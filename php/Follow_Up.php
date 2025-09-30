<?php

require 'Utility_Scripts.php';

class Part{

    public $number;
    public $description;
    public $type;
    public $part_status;
    public $quantity;
    public $order_date;

    function __construct($rec){
        $this->number       = $rec["Part_Number"];
        $this->description  = $rec["Part_Description"];
        $this->type         = $rec["Part_Type"];
        $this->part_status  = $rec["Part_Status"];
        $this->quantity     = $rec["RO_Qty"];
        $this->order_date   = GetDisplayDate($rec["Order_Date"]);
    }   // construct()

}   // Part{}


class Car{

    public $roNum;
    public $vehicle;
    public $vehicle_in;
    public $current_phase;
    public $owner;
    public $estimator;
    public $locID;
    public $parts = [];
    public $vin;

    function __construct($rec){
        $this->roNum        = $rec["RONum"];
        $this->vehicle      = $rec["Vehicle"];
        $this->vehicle_in   = $rec["Vehicle_In"];
        $this->current_phase = $rec["CurrentPhase"];
        $this->owner        = $rec["Owner"];
        $this->estimator    = $rec["Estimator"];
        $this->locID        = $rec["Loc_ID"];
        $this->vin          = $rec["VIN"];
    }   // __construct()

}   // Car{}


class Vendor{

    public $name;
    public $cars = [];
    public $locID;
    public $locName;
    public $phoneNum;
    public $email;

    function __construct($rec){
        $this->name     = $rec["Vendor_Name"];
        $this->locID    = $rec["Loc_ID"];
        $this->locName  = $rec["Location"];
        $this->phoneNum = $rec["phone_number"];
        $this->email    = $rec["email"];
    }   // __construct()

}   // Vendor{}


require('db_open.php');

    $sql = <<<strSQL

            SELECT pse.Vendor_Name, r.Loc_ID, li.Location, r.Estimator,
                r.RONum, r.Vehicle, r.Owner, r.Vehicle_In, r.CurrentPhase,
            	pse.Part_Number, pse.Part_Description, pse.Part_Type,
                pse.RO_Qty,	pse.Ordered_Qty, pse.Order_Date, pse.Part_Status,
                v.phone_number, v.email, siv.VIN
            FROM Repairs r INNER JOIN PartsStatusExtract pse
               		ON r.RONum = pse.RO_Num AND r.Loc_ID = pse.Loc_ID
                LEFT JOIN Scheduled_In_VIN siv
                    ON r.RONum = siv.RO_Num AND r.Loc_ID = siv.Loc_ID
               INNER JOIN Location_IDs li
               		ON r.Loc_ID = li.id
               LEFT JOIN Vendors v
               		ON pse.Vendor_Name = v.name AND r.Loc_ID = v.location_ID
            WHERE
                TRIM(r.Estimator) > '' AND
                r.RONum <> 1004 AND
                LENGTH(pse.Part_Number) > 0 AND
                TRIM(pse.Vendor_Name) NOT LIKE '*%IN%HOUSE%' AND
                pse.Part_Type NOT IN ('Sublet', 'FIX ME', 'Stock', 'Glass', 'Re-Manufactured') AND
                pse.Part_Status IN ('NOT ORDERED', 'ORDERED')
            ORDER BY pse.Vendor_Name,r.Loc_ID, r.Estimator, r.RONum;

        strSQL;

    try {

        $locID      = 0;
        $vendorList = [];
        $carList    = [];
        $vendor     = null;
        $vendorName = "VENDOR";
        $roNum      = 0;
        $partNumber = "";
        $car        = null;
        $part       = null;

        $s = mysqli_query($conn, $sql);

        while($r = mysqli_fetch_assoc($s)){

                // for the same vendor last read
            if (($vendorName == $r["Vendor_Name"]) && ($locID == $r["Loc_ID"])){

                if ($roNum != $r["RONum"]){

                    array_push($vendor->cars, $car);    // push the car from previous RO

                    $roNum      = $r["RONum"];  // the new RO
                    $car        = new Car($r);  // a new entry in the cars

                }// if ($roNum...)

            } else {    // for a new vendor

                switch($vendorName){

                    case "VENDOR":      // this code is needed or else
                        break;          // there will be a null vendor at the top of the list

                    default:
                        array_push($vendor->cars, $car);    // push the car from previous RO
                        array_push($vendorList, $vendor);   // push the last vendor
                        break;
                }

                $vendorName = $r["Vendor_Name"];
                $locID      = $r["Loc_ID"];

                $vendor     = new Vendor($r);   // create a new vendor
                                                // when it's different from the last one
                $roNum      = $r["RONum"];
                $car        = new Car($r);      // create a new car

            }   // if (($vendorName...))-else

            $part       = new Part($r); // a new part
            array_push($car->parts, $part);

        }   //while{}

    } catch(Exception $e){
        echo "Fetching Vendor List of parts failed.";
    } finally {

        $conn = null;
        echo json_encode($vendorList);
        return $vendorList;

    }   // try-catch

?>
