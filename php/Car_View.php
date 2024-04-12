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
        $part->part_number      = $partRec["Part_Number"];
        $part->part_description = $partRec["Part_Description"];
        $part->vendor_name      = $partRec["Vendor_Name"];

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

        try{

            $parts = [];

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

//                echo $r["Part_Description"] . "<br/>";

                $part = CreatePartsList($r);
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
                array_push($parts, $part);
            }

            $Car = new Car();
            $Car->ro_num = $roNum;
            $Car->partsList = $parts;

            return $Car;

        } catch(Exception $e){

            echo "Fetching repairs failed." . $e->getMessage();

        } finally {
            //echo "reached finally";
            $conn = null;
        }   // try-catch{}
//        }   // if-else {}
    }   // ProcessGET()
?>
