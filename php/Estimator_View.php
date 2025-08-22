<?php

    require('Utility_Scripts.php');

    $repairs = ProcessGET();

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

            $this->ro_num           = $rec["RONum"];
            $this->owner            = ucwords(strtolower($rec["Owner"]));
            $this->vehicle          = $rec["Vehicle"];
            $this->vehicle_color    = $rec["Vehicle_Color"];
            $this->vehicle_in       = $rec["Vehicle_In"];
            $this->technician       = $rec["Technician"];
            $this->current_phase    = $rec["CurrentPhase"];
            $this->parts_unordered  = 0;
            $this->parts_waiting    = 0;
            $this->parts_received   = 0;
            $this->parts_returned   = 0;
            $this->parts_percent    = 0;
            $this->scheduled_out    = GetDisplayDate($rec["Scheduled_Out"]);
            $this->scheduled_out    = substr($this->scheduled_out, 0, 5);
            $this->location         = $rec["Location"];
            $this->loc_ID           = $rec["Loc_ID"];
            $this->insurance        = $rec["Insurance"];

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


    class Estimator_Repairs{

        public $estimator;
        public $location_ID;
        public $cars = [];

        function __construct($rec){
            $this->name    = $rec["Estimator"];
            $this->location_ID  = $rec["Loc_ID"];             
        }   // Repair($rec)

    };  // Repair{}


    function GetAllRepairs($dbConn){

        $repairs = [];

        $sql = <<<strSQL
                    SELECT SUBSTRING_INDEX(Estimator, ' ', 1) AS Estimator,
                    RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                    Vehicle, LCASE(Vehicle_Color) AS Vehicle_Color,
                    Technician, Vehicle_In, CurrentPhase, Scheduled_Out,
                    Location, Loc_ID, Insurance
                    FROM Repairs
                    WHERE Estimator > '' AND RONum <> 1004
                    ORDER BY Estimator, PartsReceived DESC
                strSQL;

        try{

            $s = mysqli_query($dbConn, $sql);
            $est = "";

            while($r = mysqli_fetch_assoc($s)){

                if ($r["Estimator"] !== $est){

                    if ($est !== ''){
                        array_push($repairs, $repair);
                    }

                    $est = $r["Estimator"];
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
                    SELECT RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty, Part_Status
                    FROM PartsStatusExtract
                    WHERE Part_Number NOT IN ('Sublet', 'Remanufactured')
                        AND (Line > 0)
                        AND (Part_Number > '' OR Vendor_Name > '')
                        AND Vendor_Name NOT LIKE '**%'
                        AND Part_Number NOT LIKE 'Aftermarket%'
                        AND (Part_Type <> 'Sublet')
                        AND RO_Num = $roNum
                        AND Loc_ID = $locID
                    ORDER BY Ordered_Qty ASC
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


    function ProcessGET(){

        require('db_open.php');

        $allRepairs = GetAllRepairs($conn);

        foreach($allRepairs as $repair){    // for each car assigned to an estimator
            foreach($repair->cars as $car){ // get the parts list
                $car->parts = GetAllParts($conn, $car->ro_num, $car->loc_ID);
            }
        }

        ComputePartsReceived($allRepairs);

        return $allRepairs;

    }   // ProcessGET()
?>
