<?php

    require('Utility_Scripts.php');

    $repairs = ProcessGET();

    echo json_encode($repairs);

    class Part{

        public $ro_quantity;
        public $ordered_quantity;
        public $received_quantity;
        public $returned_quantity;

        function __construct($rec){

            $this->ro_quantity       = $rec["RO_Qty"];
            $this->ordered_quantity  = $rec["Ordered_Qty"];
            $this->received_quantity = $rec["Received_Qty"];
            $this->returned_quantity = $rec["Returned_Qty"];

        }   // Part()
    }   // Part{}


    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $estimator;
        public $parts = [];
        public $parts_unordered;
        public $parts_waiting;
        public $parts_received;
        public $parts_percent;

        function __construct($rec){

            $this->ro_num           = $rec["RONum"];
            $this->owner            = ucwords(strtolower($rec["Owner"]));
            $this->vehicle          = $rec["Vehicle"];
            $this->estimator        = $rec["Estimator"];
            $this->parts_unordered  = 0;
            $this->parts_waiting    = 0;
            $this->parts_received   = 0;
            $this->parts_percent    = 0;

        }   // Car($rec)
    }   // Car{}

    class Technician_Repairs{

        public $technician;
        public $cars = [];

        function __construct($rec){

            $this->technician    = $rec["Technician"];
        }   // Repair($rec)
    };  // Repair{}


    function GetAllParts($dbConn, $roNum){

        $allParts = [];

        $sql =  <<<strSQL
                    SELECT RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty
                    FROM PartsStatusExtract
                    WHERE (Part_Number <> 'Remanufactured')
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


    function GetAllRepairs($dbConn){

        $repairs = [];

        $sql = <<<strSQL
                    SELECT SUBSTRING_INDEX(Technician, ' ', 1) AS Technician,
                        RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                        Vehicle, Estimator
                    FROM Repairs
                    WHERE Technician > ''
                    ORDER BY Technician, PartsReceived DESC
                strSQL;

        try{

            $s = mysqli_query($dbConn, $sql);
            $tech = "";

            while($r = mysqli_fetch_assoc($s)){

                if ($r["Technician"] !== $tech){
                    if ($tech !== ''){
                        array_push($repairs, $repair);
                    }
                    $tech = $r["Technician"];
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
