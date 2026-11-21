<?php

require('Utility_Scripts.php');

    $ro_num = $_GET["roNum"];
    $locationID = $_GET["locationID"];

    $CarParts = ProcessGET($ro_num, $locationID);

    echo json_encode($CarParts);

    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $vehicle_color;
        public $license_plate;
        public $vehicle_in;
        public $scheduled_out;
        public $estimator;
        public $technician;
        public $location;
        public $insurance;
        public $vin;
        public $stage;
        public $partsList   = [];
        public $subletList  = [];

        function __construct($rec){

            $this->ro_num           = $rec["ro_num"];
            $this->owner            = $rec["owner"];
            $this->vehicle          = $rec["vehicle"];
            $this->vehicle_color    = toProperCase($rec["vehicle_color"]);
            $this->license_plate    = $rec["license_plate"];
            $this->vehicle_in       = GetDisplayDate($rec["vehicle_in"]);
            $this->scheduled_out    = GetDisplayDate($rec["scheduled_out"]);
            $this->estimator        = $rec["estimator"];
            $this->technician       = toProperCase($rec["technician"]);
            $this->location         = $rec["location"];
            $this->insurance        = $rec["insurance"];
            $this->vin              = $rec["vin"];
            $this->stage            = $rec["stage"];

        }   // Car()

    }   // Car{}


    class Part{

        public $line_num;
        public $part_number;
        public $part_description;
        public $vendor_name;
        public $ordered_quantity;
        public $order_date;
        public $received_quantity;
        public $invoice_date;
        public $returned_quantity;
        public $ro_quantity;
        public $expected_delivery;
        public $part_status;

        function __construct($rec){

            $this->line_num             = $rec["line"];
            $this->part_number          = $rec["part_number"];
            $this->part_description     = $rec["part_description"];

            $this->vendor_name          = strtolower($rec["vendor_name"]);
            $this->vendor_name          =  ucwords($this->vendor_name);

            $this->ro_quantity          = $rec["ro_qty"];
            $this->ordered_quantity     = $rec["ordered_qty"];
            $this->order_date           = GetDisplayDate($rec["order_date"]);
            $this->received_quantity    = $rec["received_qty"];
            $this->invoice_date         = GetDisplayDate($rec["invoice_date"]);
            $this->returned_quantity    = $rec["returned_qty"];
            $this->expected_delivery    = GetDisplayDate($rec["expected_delivery"]);
            $this->part_status          = $rec["part_status"];

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


    function GetPartsList($ro, $locID, $dbConn){

        $sql = <<<strSQL

                SELECT line, part_number, part_description, order_date,
                        vendor_name, ro_qty, ordered_qty, received_qty,
                        returned_qty, expected_delivery, invoice_date,
                        status
                FROM parts
                WHERE number NOT IN ('Sublet', 'Remanufactured')
                    AND line > 0
                    AND (part_Number > '' OR vendor_name > '')
                    AND vendor_name NOT LIKE '**%'
                    AND type NOT IN ('Sublet')
                    AND repair_id = $repair_id
                ORDER BY ordered_qty ASC;

            strSQL;
        try{

            $parts = [];

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = new Part($r);
                array_push($parts, $part);

            }   // while()

            return $parts;

        } catch(Exception $e){

            echo "Fetching RO parts failed." . $e->getMessage();

        }   // try-catch{}
    }   // GetPartsList()


    function GetSubletList($ro_num, $loc_id, $dbConn){

        $sublets = [];

        $sql =  <<<strSQL

                    SELECT part_description, vendor_name,
                        received_qty
                    FROM parts
                    WHERE type = 'Sublet'
                        AND repair_id = $repair_id
                    ORDER BY received_qty

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


    function ProcessGET($roNum, $locID){

        require('db_open.php');

        $sql = <<<strSQL
                    SELECT 
                        r.ro_num, r.owner, r.vehicle, r.estimator, r.technician,
                        r.vehicle_color, r.license_plate, r.vehicle_in, r.scheduled_out,
                        s.name, r.shop_id, r.insurance, r.vin, st.description AS stage,
                        r.parts_ordered, r.parts_unordered, r.parts_returned, r.parts_received,
                        r.parts_percent_received
                    FROM 
                        repairs r INNER JOIN shops s
                        	ON r.shop_id = s.id
                        INNER JOIN shop_stage_headings ssh 
                        	ON r.shop_id = ssh.shop_id
                        	AND r.stage_sequence = ssh.sequence
                        INNER JOIN stages st
                        	ON st.code = ssh.stage_code
                    WHERE r.id = $repair_id
                strSQL;

        try{

            $s = mysqli_query($conn, $sql);
            $r = mysqli_fetch_assoc($s);

            $car = new Car($r);
            $car->partsList = GetPartsList($roNum, $locID, $conn);
            $car->subletList = GetSubletList($roNum, $locID, $conn);

        } catch(Exception $e){

            echo "Fetching RO details failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $car;

        }   // try-catch{}
    }   // ProcessGET($roNum)

?>
