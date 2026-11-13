<?php

class Car{

    public $repair_id;
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
    public $shop_id;
    public $insurance;
    public $stage_sequence;

    function Get_Sublet_List($dbConn){

        $sublets = [];

        $sql =  <<<strSQL

                    SELECT description, vendor_name, received_qty
                    FROM parts
                    WHERE type = 'Sublet'
                            AND repair_id = $this->repair_id
                    ORDER BY received_qty;

                strSQL;

        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($sublets, new Sublet($r));
            }   //while{}

        } catch(Exception $e){
            echo "<br/>Fetching Sublet List failed.(" . $e->getMessage() . ")";
        }   // try-catchRONum

        return $sublets;
    }   // Get_Sublet_List()


    function Get_Parts_List($dbConn){

        $allParts = [];

        $sql =  <<<strSQL

                    SELECT ro_qty, ordered_qty, received_qty, returned_qty, status

                    FROM parts

                    WHERE number NOT IN ('Sublet', 'Remanufactured')
                        AND (Line > 0)
                        AND (number > '' OR vendor_name > '')
                        AND vendor_name NOT LIKE '**%'
                        AND number NOT LIKE 'Aftermarket%'
                        AND type NOT IN ('FIX ME','Sublet')
                        AND repair_id = $this->repair_id
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

        $this->repair_id        = $rec["id"];
        $this->ro_num           = $rec["ro_num"];
        $this->owner            = toProperCase($rec["owner"]);
        $this->vehicle          = toProperCase($rec["vehicle"]);
        $this->vehicle_in       = $rec["vehicle_in"];
        $this->technician       = toProperCase($rec["technician"]);
        $this->current_phase    = $rec["current_phase"];
        $this->estimator        = toProperCase($rec["estimator"]);
        $this->vehicle_color    = $rec["vehicle_color"];
        $this->parts_unordered  = 0;
        $this->parts_waiting    = 0;
        $this->parts_received   = 0;
        $this->parts_returned   = 0;
        $this->parts_percent    = 0;
        $this->scheduled_out    = GetDisplayDate($rec["scheduled_out"]);
        $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
        $this->shop_id          = $rec["shop_id"];
        $this->insurance        = $rec["insurance"];
        $this->stage_sequence   = $rec["stage_sequence"];
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
        $this->part_status       = $rec["status"];

    }   // Part()
}   // Part{}


class Sublet{

    public $part_description;
    public $vendor_name;
    public $received_quantity;

    function __construct($rec){
        $this->part_description     = $rec["description"];
        $this->vendor_name          = $rec["vendor_name"];
        $this->received_quantity    = $rec["received_qty"];
    }
}   // Sublet{}

?>
