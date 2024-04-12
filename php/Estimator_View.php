<?php

    $repairs = ProcessGET();

    echo json_encode($repairs);

    class Car{
        public $ro_num;
        public $owner;
        public $vehicle;
        public $technician;
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

        return $car;
    }   // CreateCarEstimator


    function ProcessGET(){

        require('db_open.php');

//        if ($id > ''){
//            ;// Get just one record
//        } else {

        $repairs = [];

        $sql = "SELECT id, Estimator, RONum, SUBSTRING_INDEX(Owner, ',', 1) AS Owner, " .
                "Vehicle, Technician " .
                "FROM Repairs ORDER BY Estimator, Owner";

        $sql = $sql;

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
//        }   // if-else {}
    }   // ProcessGET()
?>
