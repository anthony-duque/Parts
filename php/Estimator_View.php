<?php

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
        public $technician;
        public $partsRcvd;
        public $parts = [];

        function __construct($rec){

            $this->ro_num       = $rec["RONum"];
            $this->owner        = $rec["Owner"];
            $this->vehicle      = $rec["Vehicle"];
            $this->technician   = $rec["Technician"];
            $this->partsRcvd    = $rec["PartsReceived"];

        }   // Car($rec)
    }   // Car{}


    class Repair{

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
                    Vehicle, Technician, PartsReceived
                    FROM Repairs WHERE Estimator > ''
                    ORDER BY Estimator, PartsReceived DESC
                strSQL;

        try{

            $s = mysqli_query($dbConn, $sql);
            $rows = array();
            $est = "";

            while($r = mysqli_fetch_assoc($s)){

                if ($r["Estimator"] !== $est){
                    if ($est !== ''){
                        array_push($repairs, $repair);
                    }
                    $est = $r["Estimator"];
                    $repair = new Repair($r);
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

        $sql =  "SELECT RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty " .
                "FROM PartsStatusExtract " .
                "WHERE RO_Num = " . $roNum . " AND (Line > 0) AND (Part_Number > '' OR Vendor_Name > '') " .
                    "AND Vendor_Name NOT LIKE '**%' " .
                "ORDER BY Ordered_Qty ASC";

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

        return $allRepairs;

    }   // ProcessGET()
?>
