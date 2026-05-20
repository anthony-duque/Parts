<?php

    require('Utility_Scripts.php');

    $locationID = $_GET["locID"];
    $repairs = ProcessGET($locationID);

    echo json_encode($repairs);

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


    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $vehicle_color;
        public $estimator;
        public $parts = [];
        public $parts_unordered;
        public $parts_waiting;
        public $parts_received;
        public $parts_returned;
        public $parts_percent;
        public $scheduled_out;
        public $location;
        public $loc_ID;
        public $insurance;

        function __construct($rec){

            $this->ro_num           = $rec["ro_num"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->vehicle          = $rec["vehicle"];
            $this->vehicle_color    = $rec["vehicle_color"];
            $this->estimator        = $rec["estimator"];
            $this->parts_unordered  = 0;
            $this->parts_waiting    = 0;
            $this->parts_received   = 0;
            $this->parts_returned   = 0;
            $this->parts_percent    = 0;
            $this->scheduled_out    = GetDisplayDate($rec["scheduled_out"]);
            $this->scheduled_out   = substr($this->scheduled_out, 0, 5);
            $this->location         = $rec["location"];
            $this->loc_ID           = $rec["loc_id"];
            $this->insurance        = $rec["insurance"];

        }   // Car($rec)
    }   // Car{}

    class Technician_Repairs{

        public $name;
        public $location_ID;
        public $cars = [];

        function __construct($rec){
            $this->name         = $rec["technician"];
            $this->location_ID  = $rec["loc_id"]; 
        }   // Repair($rec)
    };  // Repair{}


    function GetAllParts($dbConn, $roNum, $locID){

        $allParts = [];

        $sql =  <<<strSQL

                    SELECT ro_qty, ordered_qty, received_qty,
                        returned_qty, location, loc_id, part_status

                    FROM parts_status

                    WHERE part_number NOT IN ('Sublet', 'Remanufactured')
                        AND (line > 0)
                        AND (part_number > '' OR vendor_name > '')
                        AND vendor_name NOT LIKE '**%'
                        AND part_type NOT IN ('Sublet')
                        AND ro_num = $roNum
                        AND loc_id = $locID

                    ORDER BY ordered_qty ASC;

                strSQL;
        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($allParts, new Part($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching List of Cars failed.";
        }   // try-catch

        return $allParts;
    }   // GetAllParts()


    function GetAllRepairs($dbConn, $locID){

        $repairs = [];

        if ($locID > 0){
            $loc_condition = " AND loc_id = $locID ";
        } else {
            $loc_condition = " ";
        }

        $sql = <<<strSQL
                    SELECT SUBSTRING_INDEX(technician, ' ', 1) AS technician,
                        ro_num, SUBSTRING_INDEX(owner, ',', 1) AS owner,
                        vehicle, estimator, scheduled_out,
                        LOWER(vehicle_color) as vehicle_color,
                        location, loc_id, insurance
                    FROM repairs
                    WHERE technician > '' $loc_condition
                    ORDER BY technician, parts_received DESC
                strSQL;

        try{

            $s = mysqli_query($dbConn, $sql);
            $tech = "";

            while($r = mysqli_fetch_assoc($s)){

                if ($r["technician"] !== $tech){
                    if ($tech !== ''){
                        array_push($repairs, $repair);
                    }
                    $tech = $r["technician"];
                    $repair = new Technician_Repairs($r);
                }

                array_push($repair->cars, new Car($r));
            }   // while()

            array_push($repairs, $repair);

        } catch(Exception $e){

            echo "Fetching repairs failed." . $e->getMessage();

        } finally {

            $dbConn = null;
            return $repairs;

        }   // try-catch{}
    }   // GetAllRepairs()


    function ProcessGET($loc_ID){

        require('db_open.php');

        $allRepairs = GetAllRepairs($conn, $loc_ID);

        foreach($allRepairs as $repair){    // for each car assigned to an estimator
            foreach($repair->cars as $car){ // get the parts list
                $car->parts = GetAllParts($conn, $car->ro_num, $car->loc_ID);
            }
        }

        ComputePartsReceived($allRepairs);

        $conn = null;

        return $allRepairs;

    }   // ProcessGET()

?>