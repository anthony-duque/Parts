<?php

    require('Utility_Scripts.php');

    $companyID = $_GET["companyID"];
    $repairs = ProcessGET($companyID);

    echo json_encode($repairs);

    class Car{

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

            $this->ro_num           = $rec["ro_num"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->vehicle          = $rec["vehicle"];
            $this->vehicle_color    = $rec["vehicle_color"];
            $this->vehicle_in       = $rec["vehicle_in"];
            $this->technician       = $rec["technician"];
            $this->current_phase    = $rec["current_phase"];
            $this->parts_unordered  = 0;
            $this->parts_waiting    = 0;
            $this->parts_received   = 0;
            $this->parts_returned   = 0;
            $this->parts_percent    = 0;
            $this->scheduled_out    = GetDisplayDate($rec["scheduled_out"]);
            $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
            $this->location         = $rec["location"];
            $this->loc_ID           = $rec["loc_id"];
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
            $this->part_status       = $rec["part_status"];
        }   // Part()
    }   // Part{}


    class Estimator_Repairs{

        public $name;
        public $location_ID;
        public $cars = [];

        function __construct($rec){
            $this->name    = $rec["estimator"];
            $this->location_ID  = $rec["loc_id"];             
        }   // Repair($rec)

    };  // Repair{}


    function GetAllRepairs($dbConn, $companyID){

        $repairs = [];

        $sql = <<<strSQL
                SELECT SUBSTRING_INDEX(r.estimator, ' ', 1) AS estimator,
                    r.ro_num, SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                    r.vehicle, LCASE(r.vehicle_color) AS vehicle_color,
                    r.technician, r.vehicle_in, r.current_phase, r.scheduled_out,
                    li.location, r.loc_id, r.insurance
                FROM repairs r INNER JOIN location_ids li 
                    ON r.loc_id = li.id
                WHERE r.estimator > '' AND li.company_id = $companyID
                ORDER BY estimator, parts_received DESC;
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


    function GetAllParts($dbConn, $roNum, $locID){

        $allParts = [];

        $sql =  <<<strSQL
                    SELECT ro_qty, ordered_qty, received_qty, returned_qty, part_status        
                    FROM parts_status        
                    WHERE part_number NOT IN ('Sublet', 'Remanufactured')
                        AND (line > 0)
                        AND (part_number > '' OR vendor_name > '')
                        AND vendor_name NOT LIKE '**%'
                        AND part_type NOT IN ('Sublet')
                        AND ro_num = '$roNum'
                        AND loc_id = $locID        
                    ORDER BY ordered_qty ASC
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
                $car->parts = GetAllParts($conn, $car->ro_num, $car->loc_ID);
            }
        }

        ComputePartsReceived($allRepairs);

        return $allRepairs;

    }   // ProcessGET()
?>
