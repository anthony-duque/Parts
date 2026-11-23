<?php

    require('Utility_Scripts.php');

    $companyID = $_GET["companyID"];
    $repairs = ProcessGET($companyID);

    echo json_encode($repairs);

    class Car{

        public $repair_id;
        public $ro_num;
        public $owner;
        public $vehicle;
        public $vehicle_color;
        public $vehicle_in;
        public $current_phase;
        public $technician;
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
            $this->vehicle_in       = $rec["vehicle_in"];
            $this->technician       = $rec["technician"];
            $this->current_phase    = $rec["current_phase"];
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


    class Estimator_Repairs{

        public $name;
        public $location_ID;
        public $cars = [];

        function __construct($rec){
            $this->name    = $rec["estimator"];
            $this->location_ID  = $rec["shop_id"];             
        }   // Repair($rec)

    };  // Repair{}


    function GetAllRepairs($dbConn, $companyID){

        $repairs = [];

        $sql = <<<strSQL
                SELECT r.id AS repair_id, SUBSTRING_INDEX(r.estimator, ' ', 1) AS estimator,
                    r.ro_num, SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                    r.vehicle, LCASE(r.vehicle_color) AS vehicle_color,
                    r.technician, r.vehicle_in, r.current_phase, r.scheduled_out,
                    s.name, r.shop_id, r.insurance, r.parts_received,
                    r.parts_unordered, r.parts_ordered, r.parts_returned,
                    r.parts_percent_received
                FROM repairs r INNER JOIN shops s 
                    ON r.shop_id = s.id
                WHERE r.estimator > '' AND s.company_id = $companyID
                ORDER BY estimator, parts_percent_received DESC;
            strSQL;

        try{

            $s = mysqli_query($dbConn, $sql);
            $est = "";

            while($r = mysqli_fetch_assoc($s)){

                if ($r["estimator"] !== $est){

                    if ($est !== ''){
                        array_push($repairs, $repair);
                    }

                    $est = $r["estimator"];
                    $repair = new Estimator_Repairs($r);
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


    function GetAllParts($dbConn, $repairID){

        $allParts = [];

        $sql =  <<<strSQL
                    SELECT ro_qty, ordered_qty, received_qty, returned_qty, status        
                    FROM parts        
                    WHERE number NOT IN ('Sublet', 'Remanufactured')
                        AND (line > 0)
                        AND (number > '' OR vendor_name > '')
                        AND vendor_name NOT LIKE '**%'
                        AND type NOT IN ('Sublet')
                        AND repair_id = $repairID        
                    ORDER BY received_qty DESC
                strSQL;
        try {

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($allParts, new Part($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching List of Cars failed. " . $e->getMessage();
        }   // try-catch

        return $allParts;
    }   // GetAllParts()


    function ProcessGET($company_ID){

        require('db_open.php');

        $allRepairs = GetAllRepairs($conn, $company_ID);

        foreach($allRepairs as $repair){    // for each car assigned to an estimator
            foreach($repair->cars as $car){ // get the parts list
                $car->parts = GetAllParts($conn, $car->repair_id);
            }
        }

        return $allRepairs;

    }   // ProcessGET()
?>
