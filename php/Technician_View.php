<?php

    $repairs = ProcessGET();

    echo json_encode($repairs);

    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $estimator;
        public $partsRcvd;

        function __construct($rec){
            $this->ro_num       = $rec["RONum"];
            $this->owner        = $rec["Owner"];
            $this->vehicle      = $rec["Vehicle"];
            $this->estimator    = $rec["Estimator"];
            $this->partsRcvd    = $rec["PartsReceived"];
        }   // Car($rec)

    }   // Car{}

    class Repair{

        public $technician;
        public $cars = [];

        function __construct($rec){
            $this->technician    = $rec["Technician"];
        }   // Repair($rec)

    };  // Repair{}


    function ProcessGET(){

        require('db_open.php');

        $records = null;

        $sql = "SELECT SUBSTRING_INDEX(Technician, ' ', 1) AS Technician, RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner, " .
                "Vehicle, Estimator, PartsReceived FROM Repairs " .
                "WHERE Technician > '' " .
                "ORDER BY Technician, PartsReceived DESC";

        $sql = $sql;

        try{

            $repairs = [];
            $rows = array();

            $s = mysqli_query($conn, $sql);

            $r = mysqli_fetch_assoc($s);    // get the first row
            $repair = new Repair($r);
            array_push($repair->cars, new Car($r));

            while($r = mysqli_fetch_assoc($s)){

                if ($r["Technician"] === $repair->technician){
                    array_push($repair->cars, new Car($r));
                } else {
                    array_push($repairs, $repair);
                    $repair = new Repair($r);
                    array_push($repair->cars, new Car($r));
                }
            }
            array_push($repairs, $repair);

            return $repairs;

        } catch(Exception $e){

            echo "Fetching repairs failed." . $e->getMessage();

        } finally {
            //echo "reached finally";
            $conn = null;
        }   // try-catch{}

    }   // ProcessGET()
?>
