<?php

    $repairs = ProcessGET();

    echo json_encode($repairs);

    class Car{
        public $ro_num;
        public $owner;
        public $vehicle;
        public $technician;
        public $partsRcvd;
    }

    class Repair{
        public $estimator;
        public $cars = [];
    };

    function CreateCar($carRec){

        $car = new Car();
        $car->ro_num = $carRec["RONum"];
        $car->owner = $carRec["Owner"];
        $car->vehicle = $carRec["Vehicle"];
        $car->technician = $carRec["Technician"];
        $car->partsRcvd = $carRec["PartsReceived"];

        return $car;
    }   // CreateCarEstimator


    function ProcessGET(){

        require('db_open.php');

        $repairs = [];

        $sql = "SELECT Estimator, RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner, " .
                "Vehicle, Technician, PartsReceived " .
                "FROM Repairs ORDER BY Estimator, Owner";

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
                    $repair = new Repair();
                    $repair->estimator = $est;
                }
                $repair->cars[] = CreateCar($r);
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
