<?php

    $repairs = ProcessGET();

    echo json_encode($repairs);

    class Car{
        public $ro_num;
        public $owner;
        public $vehicle;
        public $estimator;
        public $partsRcvd;
    }

    class Repair{
        public $technician;
        public $cars = [];
    };

    function CreateCar($carRec){

        $car = new Car();
        $car->ro_num    = $carRec["RONum"];
        $car->owner     = $carRec["Owner"];
        $car->vehicle   = $carRec["Vehicle"];
        $car->estimator = $carRec["Estimator"];
        $car->partsRcvd = $carRec["PartsReceived"];

        return $car;
    }   // CreateCarEstimator


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
            $repair = new Repair();
            $repair->technician = $r["Technician"];
            $repair->cars[] = CreateCar($r);

            while($r = mysqli_fetch_assoc($s)){

                if ($r["Technician"] === $repair->technician){
                    $repair->cars[] = CreateCar($r);
                } else {
                    array_push($repairs, $repair);
                    $repair = new Repair();
                    $repair->technician = $r["Technician"];
                    $repair->cars[] = CreateCar($r);
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
