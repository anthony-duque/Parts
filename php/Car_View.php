<?php

require('Utility_Scripts.php');

    $ro_num = $_GET["roNum"];

    $CarParts = ProcessGET($ro_num);

    echo json_encode($CarParts);

    class Car{
        public $ro_num;
        public $owner;
        public $vehicle;
        public $vehicle_color;
        public $license_plate;
        public $vehicle_in;
        public $scheduled_out;
        public $estimator;
        public $technician;
        public $partsList = [];
    }

    class Part{
        public $line_num;
        public $part_number;
        public $part_description;
        public $vendor_name;
        public $ordered_quantity;
        public $received_quantity;
        public $returned_quantity;
        public $ro_quantity;
        public $expected_delivery;
    };

    function CreatePartEntry($partRec){

        $part = new Part();
        $part->line_num = $partRec["Line"];
        $part->part_number      = $partRec["Part_Number"];
        $part->part_description = $partRec["Part_Description"];

        $part->vendor_name      = strtolower($partRec["Vendor_Name"]);
        $part->vendor_name      =  ucwords($part->vendor_name);

        $part->ro_quantity      = $partRec["RO_Qty"];
        $part->ordered_quantity = $partRec["Ordered_Qty"];
        $part->received_quantity = $partRec["Received_Qty"];
        $part->returned_quantity = $partRec["Returned_Qty"];
        $part->expected_delivery = $partRec["Expected_Delivery"];

        return $part;
    }   // CreateCarEstimator

    function GetPartsList($ro, $dbConn){

        $sql = "SELECT Line, Part_Number, Part_Description, Vendor_Name, " .
                " RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty, " .
                " Expected_Delivery" .
                " FROM PartsStatusExtract" .
                " WHERE RO_Num = " . $ro . " AND (Line > 0) AND (Part_Number > '' OR Vendor_Name > '')" .
                " ORDER BY Ordered_Qty ASC";
        //console.log($sql);
        try{

            $parts = [];

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = CreatePartEntry($r);
                array_push($parts, $part);
            }   // while()

            return $parts;

        } catch(Exception $e){

            echo "Fetching RO parts failed." . $e->getMessage();

        }   // try-catch{}

    }   // GetPartsList()


    function Get_RO_Info(&$car, $dbConn){

        $sql = "SELECT RONum, Owner, Vehicle, Estimator, Technician, " .
                "Vehicle_Color, License_Plate, Vehicle_In, Scheduled_Out " .
                "FROM Repairs WHERE RONum = " . $car->ro_num;

        try{

            $s = mysqli_query($dbConn, $sql);
            $r = mysqli_fetch_assoc($s);

            $car->owner         = toProperCase($r["Owner"]);

            $car->vehicle       = $r["Vehicle"];

            $car->vehicle_color = toProperCase($r["Vehicle_Color"]);

            $car->license_plate = $r["License_Plate"];

            $car->vehicle_in        = "";
            if ($r["Vehicle_In"] > ''){
                $dateObj            = date_create($r["Vehicle_In"]);
                $car->vehicle_in    = date_format($dateObj, "M j g:i A");
            }

            $car->scheduled_out     = "";
            if ($r["Scheduled_Out"] > ''){
                $dateObj            = date_create();
                $car->scheduled_out = date_format($dateObj, "M j g:i A");
            }

            $car->estimator     = $r["Estimator"];

            $car->technician    = toProperCase($r["Technician"]);

        } catch(Exception $e){

            echo "Fetching RO details failed." . $e->getMessage();

        }   // try-catch{}

    }   // Get_RO_Info()


    function ProcessGET($roNum){

        require('db_open.php');

        $Car = new Car();
        $Car->ro_num = $roNum;
        Get_RO_Info($Car, $conn);

        $Car->partsList = GetPartsList($roNum, $conn);

        $conn = null;

        return $Car;
//        }   // if-else {}
    }   // ProcessGET()
?>
