<?php

    $repairs = ProcessGET();

    echo json_encode($repairs);

    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $technician;
        public $partsRcvd;

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


    function ProcessGET(){

        require('db_open.php');

        $repairs = [];

        $sql = "SELECT SUBSTRING_INDEX(Estimator, ' ', 1) AS Estimator, " .
                "RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner, " .
                "Vehicle, Technician, PartsReceived FROM Repairs WHERE Estimator > '' " .
                "ORDER BY Estimator, PartsReceived DESC";

        try{

            $s = mysqli_query($conn, $sql);
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

            return $repairs;

        } catch(Exception $e){

            echo "Fetching repairs failed." . $e->getMessage();

        } finally {
            //echo "reached finally";
            $conn = null;
        }   // try-catch{}

    }   // ProcessGET()
?>
