<?php

    $ro_num = $_GET["roNum"];

    $CarParts = ProcessGET($ro_num);

    echo json_encode($CarParts);

    class Car{
        public $ro_num;
//        public $owner;
//        public $vehicle;
//        public $estimator;
//        public $technician;
        public $partsList = [];
    }

    class Part{
        public $part_number;
        public $part_description;
        public $vendor_name;
    };

    function CreatePartsList($partRec){

        $part = new Part();
        $part->part_number      = $partRec["part_number"];
        $part->part_description = $partRec["part_description"];
        $part->vendor_name      = $partRec["vendor_name"];

        return $partRec;
    }   // CreateCarEstimator


    function ProcessGET($roNum){

        require('db_open.php');

//        if ($id > ''){
//            ;// Get just one record
//        } else {

        $sql = "SELECT Part_Number, Part_Description, Vendor_Name " .
                " FROM PartsStatusExtract" .
                " WHERE RO_Num = " . $roNum;

        $sql = $sql;

        try{

            $parts = [];

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                echo $r["Part_Description"] . "<br/>";
/*
                if ($r["Technician"] === $repair->technician){
                    $repair->cars[] = CreateCar($r);
                } else {
                    array_push($repairs, $repair);
                    $repair = new Repair();
                    $repair->technician = $r["Technician"];
                    $repair->cars[] = CreateCar($r);
                }
*/
            }
//            array_push($repairs, $repair);

//            return $repairs;

        } catch(Exception $e){

            echo "Fetching repairs failed." . $e->getMessage();

        } finally {
            //echo "reached finally";
            $conn = null;
        }   // try-catch{}
//        }   // if-else {}
    }   // ProcessGET()
?>
