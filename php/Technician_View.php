<?php

    
    require('Utility_Scripts.php');

    $companyID = $_GET["companyID"];
    $repairs = ProcessGET($companyID);

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
            $this->part_status       = $rec["status"];

        }   // Part()
    }   // Part{}


    class Car{

        public $repair_id;
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

            $this->repair_id        = $rec["repair_id"];
            $this->ro_num           = $rec["ro_num"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->vehicle          = $rec["vehicle"];
            $this->vehicle_color    = $rec["vehicle_color"];
            $this->estimator        = $rec["estimator"];
            $this->parts_unordered  = $rec["parts_unordered"];
            $this->parts_waiting    = $rec["parts_ordered"];
            $this->parts_received   = $rec["parts_received"];
            $this->parts_returned   = $rec["parts_returned"];
            $this->parts_percent    = $rec["parts_percent_received"];

            $this->scheduled_out    = GetDisplayDate($rec["scheduled_out"]);
            $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
            
            $this->location         = $rec["name"];
            $this->loc_ID           = $rec["shop_id"];
            $this->insurance        = $rec["insurance"];

        }   // Car($rec)
    }   // Car{}

    class Technician_Repairs{

        public $name;
        public $location_ID;
        public $cars = [];

        function __construct($rec){
            $this->name         = $rec["technician"];
            $this->location_ID  = $rec["shop_id"]; 
        }   // Repair($rec)
    };  // Repair{}


    function GetAllParts($dbConn, $repairID){

        $allParts = [];

        $sql =  <<<strSQL

                    SELECT p.ro_qty, p.ordered_qty, p.received_qty,
                        p.returned_qty, s.name, r.shop_id, p.status
                    FROM parts p INNER JOIN repairs r
                            ON p.repair_id = r.id
                        INNER JOIN shops s
                            ON r.shop_id = s.id
                    WHERE p.number NOT IN ('Sublet', 'Remanufactured')
                        AND (p.line > 0)
                        AND (p.number > '' OR p.vendor_name > '')
                        AND p.vendor_name NOT LIKE '**%'
                        AND p.type NOT IN ('Sublet')
                        AND p.repair_id = $repairID
                    ORDER BY p.ordered_qty ASC;
                strSQL;
        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($allParts, new Part($r));
            }   //while{}

        } catch(Exception $e){
            echo "\n Fetching List of parts failed for $repairID. {$e->getMessage()}";
        }   // try-catch

        return $allParts;
    }   // GetAllParts()


    function GetAllRepairs($dbConn, $companyID){

        $repairs = [];

        $sql = <<<strSQL

                SELECT r.id AS repair_id, SUBSTRING_INDEX(r.technician, ' ', 1) AS technician,
                    r.ro_num, SUBSTRING_INDEX(r.owner, ',', 1) AS owner, r.parts_unordered,
                    r.vehicle, r.estimator, r.scheduled_out, r.parts_ordered, r.parts_received,
                    LOWER(vehicle_color) as vehicle_color, r.parts_returned,
                    s.name, r.shop_id, r.insurance, r.parts_percent_received
                FROM repairs r INNER JOIN shops s 
                    ON r.shop_id = s.id 
                WHERE technician > '' AND s.company_id = $companyID
                ORDER BY technician, parts_percent_received DESC;
        
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


    function ProcessGET($company_ID){

        require('db_open.php');

        $allRepairs = GetAllRepairs($conn, $company_ID);

        foreach($allRepairs as $repair){    // for each car assigned to a technician
            foreach($repair->cars as $car){ // get the parts list
                $car->parts = GetAllParts($conn, $car->repair_id);
            }
        }

        $conn = null;

        return $allRepairs;

    }   // ProcessGET()

?>