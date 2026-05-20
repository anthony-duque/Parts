<?php

require 'Utility_Scripts.php';

class Part{

    public $number;
    public $description;
    public $type;
    public $part_status;
    public $quantity;
    public $line_num;
    public $order_date;
    public $eta_date;

    function __construct($rec){
        $this->number       = $rec["part_number"];
        $this->description  = $rec["part_description"];
        $this->type         = $rec["part_type"];
        $this->part_status  = $rec["part_status"];
        $this->quantity     = $rec["ro_qty"];
        $this->line_num     = $rec["line"];
        $this->order_date   = GetDisplayDate($rec["order_date"]);
        $this->eta_date     = GetDisplayDate($rec["expected_delivery"]);
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
        $this->roNum        = $rec["ro_num"];
        $this->vehicle      = $rec["vehicle"];
        $this->vehicle_in   = $rec["vehicle_in"];
        $this->current_phase = $rec["current_phase"];
        $this->owner        = $rec["owner"];
        $this->estimator    = $rec["estimator"];
        $this->locID        = $rec["loc_id"];
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
        $this->name     = $rec["vendor_name"];
        $this->locID    = $rec["loc_id"];
        $this->locName  = $rec["location"];
        $this->phoneNum = $rec["phone_number"];
        $this->email    = $rec["email"];
    }   // __construct()

}   // Vendor{}


require('db_open.php');

    $sql = <<<strSQL

            SELECT pse.vendor_name, r.loc_id, li.location, r.estimator,
                r.ro_num, r.vehicle, r.owner, r.vehicle_in, r.current_phase,
            	pse.part_number, pse.part_description, pse.part_type,
                pse.ro_qty,	pse.ordered_qty, pse.order_date, pse.part_status,
                v.phone_number, v.email, siv.VIN, pse.expected_delivery, pse.line
            FROM repairs r INNER JOIN parts_status pse
               		ON r.ro_num = pse.ro_num AND r.loc_id = pse.loc_id
                LEFT JOIN scheduled_in_vin siv
                    ON r.ro_num = siv.ro_num AND r.loc_id = siv.loc_id
               INNER JOIN location_ids li
               		ON r.loc_id = li.id
               LEFT JOIN vendors v
               		ON pse.vendor_name = v.name AND r.loc_id = v.location_id
            WHERE
                TRIM(r.estimator) > '' AND
                pse.line > 0 AND 
                LENGTH(pse.part_number) > 0 AND
                TRIM(pse.vendor_name) NOT LIKE '*%IN%HOUSE%' AND
                pse.part_type NOT IN ('Sublet', 'FIX ME', 'Stock', 'Glass', 'Re-Manufactured') AND
                pse.part_status IN ('NOT_ORDERED', 'ORDERED')
            ORDER BY pse.vendor_name,r.loc_id, r.estimator, r.ro_num;

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
            if (($vendorName == $r["vendor_name"]) && ($locID == $r["loc_id"])){

                if ($roNum != $r["ro_num"]){

                    array_push($vendor->cars, $car);    // push the car from previous RO

                    $roNum      = $r["ro_num"];  // the new RO
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

                $vendorName = $r["vendor_name"];
                $locID      = $r["loc_id"];

                $vendor     = new Vendor($r);   // create a new vendor
                                                // when it's different from the last one
                $roNum      = $r["ro_num"];
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
