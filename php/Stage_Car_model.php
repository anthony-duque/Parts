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
    public $locID;
    public $insurance;
    public $stageID;

    function Get_Sublet_List($dbConn){

        $sublets = [];

        $sql =  <<<strSQL

                    SELECT part_description, vendor_name, received_qty
        
                    FROM parts_status
        
                    WHERE part_type = 'Sublet'
                        AND ro_num = $this->ro_num AND loc_id = $this->locID

                    ORDER BY received_qty

                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($sublets, new Sublet($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Sublet List failed.";
        }   // try-catchRONum

        return $sublets;
    }   // Get_Sublet_List()


    function Get_Parts_List($dbConn){

        $allParts = [];

        $sql =  <<<strSQL

                    SELECT ro_qty, ordered_qty, received_qty, returned_qty, part_status

                    FROM parts_status

                    WHERE part_number NOT IN ('Sublet', 'Remanufactured')
                        AND (Line > 0)
                        AND (part_number > '' OR vendor_name > '')
                        AND vendor_name NOT LIKE '**%'
                        AND part_number NOT LIKE 'Aftermarket%'
                        AND part_type NOT IN ('FIX ME','Sublet')
                        AND ro_num = $this->ro_num
                        AND loc_id = $this->locID

                    ORDER BY ordered_qty ASC
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

        $this->ro_num           = $rec["ro_num"];
        $this->owner            = toProperCase($rec["owner"]);
        $this->vehicle          = toProperCase($rec["vehicle"]);
        $this->vehicle_color    = $rec["vehicle_color"];
        $this->vehicle_in       = $rec["vehicle_in"];
        $this->technician       = toProperCase($rec["technician"]);
        $this->estimator        = toProperCase($rec["estimator"]);
        $this->current_phase    = $rec["current_phase"];
        $this->parts_unordered  = 0;
        $this->parts_waiting    = 0;
        $this->parts_received   = 0;
        $this->parts_returned   = 0;
        $this->parts_percent    = 0;
        $this->scheduled_out    = GetDisplayDate($rec["scheduled_out"]);
        $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
        $this->locID            = $rec["loc_id"];
        $this->insurance        = $rec["insurance"];
        $this->stageID          = $rec["stage_id"];
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

        $this->ro_quantity       = $rec["ro_qty"];
        $this->ordered_quantity  = $rec["ordered_qty"];
        $this->received_quantity = $rec["received_qty"];
        $this->returned_quantity = $rec["returned_qty"];
        $this->part_status       = $rec["part_status"];

    }   // Part()
}   // Part{}


class Sublet{

    public $part_description;
    public $vendor_name;
    public $received_quantity;

    function __construct($rec){
        $this->part_description     = $rec["part_description"];
        $this->vendor_name          = $rec["vendor_name"];
        $this->received_quantity    = $rec["received_qty"];
    }
}   // Sublet{}

?>
