<?php

class Car{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $vehicle_color;
    public $vehicle_in;
    public $current_phase;
    public $technician;
    public $estimator;
    public $parts = [];
    public $sublets = [];
    public $parts_unordered;
    public $parts_waiting;
    public $parts_received;
    public $parts_returned;
    public $parts_percent;
    public $scheduled_out;
    public $locationID;
    public $insurance;
    public $stageID;

    function Get_Sublet_List($dbConn){

        $sublets = [];

        $sql =  <<<strSQL
                    SELECT Part_Description, Vendor_Name, Received_Qty
                    FROM PartsStatusExtract
                    WHERE Part_Type = 'Sublet'
                    AND RO_Num = $this->ro_num AND Loc_ID = $this->locationID
                    ORDER BY Received_Qty
                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($sublets, new Sublet($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Sublet List failed.";
        }   // try-catch

        return $sublets;
    }   // Get_Sublet_List()


    function Get_Parts_List($dbConn){

        $allParts = [];

        $sql =  <<<strSQL
                    SELECT RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty, Part_Status
                    FROM PartsStatusExtract
                    WHERE Part_Number NOT IN ('Sublet', 'Remanufactured')
                        AND (Line > 0)
                        AND (Part_Number > '' OR Vendor_Name > '')
                        AND Vendor_Name NOT LIKE '**%'
                        AND Part_Number NOT LIKE 'Aftermarket%'
                        AND Part_Type NOT IN ('FIX ME','Sublet')
                        AND RO_Num = $this->ro_num
                        AND Loc_ID = $this->locationID
                    ORDER BY Ordered_Qty ASC
                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($allParts, new Part($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching List of Parts failed.";
        }   // try-catch

        return $allParts;
    }   // GetAllParts()


    function __construct($dbConn, $rec){

        $this->ro_num           = $rec["RONum"];
        $this->owner            = toProperCase($rec["Owner"]);
        $this->vehicle          = toProperCase($rec["Vehicle"]);
        $this->vehicle_color    = $rec["Vehicle_Color"];
        $this->vehicle_in       = $rec["Vehicle_In"];
        $this->technician       = toProperCase($rec["Technician"]);
        $this->estimator        = toProperCase($rec["Estimator"]);
        $this->current_phase    = $rec["CurrentPhase"];
        $this->parts_unordered  = 0;
        $this->parts_waiting    = 0;
        $this->parts_received   = 0;
        $this->parts_returned   = 0;
        $this->parts_percent    = 0;
        $this->scheduled_out    = GetDisplayDate($rec["Scheduled_Out"]);
        $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
        $this->locationID       = $rec["Loc_ID"];
        $this->insurance        = $rec["Insurance"];
        $this->stageID          = $rec["stage_ID"];
        $this->parts            = $this->Get_Parts_List($dbConn);
        $this->sublets          = $this->Get_Sublet_List($dbConn);

    }   // Car($rec)
}   // Car{}


class Part{

    public $ro_quantity;
    public $ordered_quantity;
    public $received_quantity;
    public $returned_quantity;
    public $part_status;

    function __construct($rec){

        $this->ro_quantity       = $rec["RO_Qty"];
        $this->ordered_quantity  = $rec["Ordered_Qty"];
        $this->received_quantity = $rec["Received_Qty"];
        $this->returned_quantity = $rec["Returned_Qty"];
        $this->part_status       = $rec["Part_Status"];

    }   // Part()
}   // Part{}


class Sublet{

    public $part_description;
    public $vendor_name;
    public $received_quantity;

    function __construct($rec){
        $this->part_description     = $rec["Part_Description"];
        $this->vendor_name          = $rec["Vendor_Name"];
        $this->received_quantity    = $rec["Received_Qty"];
    }
}   // Sublet{}

?>
