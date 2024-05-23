<?php

    $repairs = ProcessGET();

    echo json_encode($repairs);

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
    }

    function GetAllParts($dbConn, $roNum){

        $allParts = [];

        $sql = <<<strSQL
            SELECT RO_Qty, Ordered_Qty, Received_Qty
            FROM PartsStatusExtract
            WHERE (RO_Qty > 0) AND (Ordered_Qty = 0)
                AND (Received_Qty = 0) AND (Part_Number > '')
                AND (Part_Type <> 'Sublet')
                AND (Part_Number NOT LIKE 'Aftermarket%')
                AND RO_Num =
        strSQL . $roNum;
/*
        try {

            $s = mysqli_query($dbConn, $sql);
            while($r = mysqli_fetch_assoc($s)){
                array_push($allParts, $r["RO_Num"]);
            }   //while{}

        } catch(Exception $e){
            echo "Fetching List of Cars failed.";
        }   // try-catch
*/
        return $allParts;
    }


    function ProcessGET(){

        require('db_open.php');

        $allRepairs = GetAllRepairs($conn);

        foreach($allRepairs as $repair){
            foreach($repair->cars as $car){
                $car->parts = GetAllParts($conn, $car->ro_num);
            }
        }

        return $allRepairs;

    }   // ProcessGET()
?>
