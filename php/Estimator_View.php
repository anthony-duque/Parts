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
            $this->part_status       = ComputePartStatus(
                                            $this->ro_quantity,
                                            $this->ordered_quantity,
                                            $this->received_quantity,
                                            $this->returned_quantity
                                        );

        }   // Part()
    }   // Part{}


    class Estimator_Repairs{

        public $estimator;
        public $cars = [];

        function __construct($rec){
            $this->estimator    = $rec["Estimator"];
        }   // Repair($rec)

    };  // Repair{}


    function GetAllRepairs($dbConn){

        $repairs = [];

        $sql = <<<strSQL
                    SELECT SUBSTRING_INDEX(Estimator, ' ', 1) AS Estimator,
                    RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                    Vehicle, LCASE(Vehicle_Color) AS Vehicle_Color, Technician, Vehicle_In, CurrentPhase, Scheduled_Out
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


    function GetAllParts($dbConn, $roNum){

        $allParts = [];

        $sql =  <<<strSQL
                    SELECT RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty
                    FROM PartsStatusExtract
                    WHERE Part_Number NOT IN ('Sublet', 'Remanufactured')
                        AND (Line > 0)
                        AND (Part_Number > '' OR Vendor_Name > '')
                        AND Vendor_Name NOT LIKE '**%'
                        AND Part_Number NOT LIKE 'Aftermarket%'
                        AND (Part_Type <> 'Sublet')
                        AND RO_Num =
                strSQL . $roNum .
                " ORDER BY Ordered_Qty ASC";

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
                $car->parts = GetAllParts($conn, $car->ro_num);
            }
        }

        ComputePartsReceived($allRepairs);

        return $allRepairs;

    }   // ProcessGET()
?>
