<?php

require('Utility_Scripts.php');

    class Part{
        public $ro_num;
        public $part_number;
        public $part_description;
        public $vendor_name;
        public $ordered_quantity;
        public $received_quantity;
        public $returned_quantity;
        public $ro_quantity;
        public $expected_delivery;
        public $order_date;
    };

    function CreatePartEntry($partRec){

        $part = new Part();

        $part->ro_num           = $partRec["RO_Num"];
        $part->part_number      = $partRec["Part_Number"];
        $part->part_description = $partRec["Part_Description"];

        $part->vendor_name      = strtolower($partRec["Vendor_Name"]);
        $part->vendor_name      =  ucwords($part->vendor_name);

        $part->ro_quantity      = $partRec["RO_Qty"];
        $part->ordered_quantity = $partRec["Ordered_Qty"];
        $part->received_quantity = $partRec["Received_Qty"];
        $part->returned_quantity = $partRec["Returned_Qty"];
        $part->expected_delivery = $partRec["Expected_Delivery"];
        $part->expected_delivery = $partRec["Order_Date"];

        return $part;
    }   // CreateCarEstimator

    $allParts = GetPartsList();
    echo json_encode($allParts);

    function GetPartsList(){

        require('db_open.php');

        $sql = "SELECT RO_Num, Part_Number, Part_Description, Vendor_Name, " .
                " RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty, " .
                " Expected_Delivery, Order_Date " .
                " FROM PartsStatusExtract " .
                " WHERE (Line > 0) AND (Part_Number > '' OR Vendor_Name > '')";

        // echo $sql;
        $parts = [];

        try{

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = CreatePartEntry($r);
                array_push($parts, $part);
            }   // while()

        } catch(Exception $e){

            echo "Getting parts list failed." . $e->getMessage();

        }

        finally {

            $conn = null;
            return $parts;
        }   // try-catch{}

    }   // GetPartsList()

?>
