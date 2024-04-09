<?php

    $repairs = ProcessGET('Estimators');
    echo json_encode($repairs);


    function ProcessGET($grouping){

        require('db_open.php');

        $records = null;

//        if ($id > ''){
//            ;// Get just one record
//        } else {

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

        $repairs = [];

        $sql = "SELECT Estimator, RONum, Owner, Vehicle, Technician " .
                "FROM Repairs ORDER BY Estimator";
        try{

            $s = mysqli_query($conn, $sql);
            $rows = array();
            $est = "";

            while($r = mysqli_fetch_assoc($s)){

                if ($r["Estimator"] !== $est){
                    $est = $r["Estimator"];
                    $repair = new Repair();
                    $repair->estimator = $est;
                    array_push($repairs, $repair);
                }
            }

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
