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

        function __construct($rec){

            $this->ro_num           = $rec["RONum"];
            $this->owner            = $rec["Owner"];
            $this->vehicle          = $rec["Vehicle"];
            $this->vehicle_color    = toProperCase($rec["Vehicle_Color"]);
            $this->license_plate    = $rec["License_Plate"];
            $this->vehicle_in       = GetDisplayDate($rec["Vehicle_In"]);
            $this->scheduled_out    = GetDisplayDate($rec["Scheduled_Out"]);
            $this->estimator        = $rec["Estimator"];
            $this->technician       = toProperCase($rec["Technician"]);

        }   // Car()
    }   // Car{}

    class Part{

        public $line_num;
        public $part_number;
        public $part_description;
        public $vendor_name;
        public $ordered_quantity;
        public $order_date;
        public $received_quantity;
        public $invoice_date;
        public $returned_quantity;
        public $ro_quantity;
        public $expected_delivery;

        function __construct($rec){

            $this->line_num = $rec["Line"];
            $this->part_number      = $rec["Part_Number"];
            $this->part_description = $rec["Part_Description"];

            $this->vendor_name      = strtolower($rec["Vendor_Name"]);
            $this->vendor_name      =  ucwords($this->vendor_name);

            $this->ro_quantity      = $rec["RO_Qty"];
            $this->ordered_quantity = $rec["Ordered_Qty"];
            $this->order_date     = GetDisplayDate($rec["Order_Date"]);
            $this->received_quantity = $rec["Received_Qty"];
            $this->invoice_date     = GetDisplayDate($rec["Invoice_Date"]);
            $this->returned_quantity = $rec["Returned_Qty"];
            $this->expected_delivery = GetDisplayDate($rec["Expected_Delivery"]);
        }   // Part()
    }   // Part{}


    function GetPartsList($ro, $dbConn){

        $sql = "SELECT Line, Part_Number, Part_Description, Order_Date, " .
                " Vendor_Name, RO_Qty, Ordered_Qty, Received_Qty, " .
                " Returned_Qty, Expected_Delivery, Invoice_Date " .
                " FROM PartsStatusExtract" .
                " WHERE RO_Num = " . $ro . " AND (Line > 0) AND (Part_Number > '' OR Vendor_Name > '')" .
                "       AND Vendor_Name NOT LIKE '**%' AND Part_Number NOT LIKE 'Aftermarket%'" .
                "       AND (Part_Type <> 'Sublet') " .
                "       AND (Part_Number <> 'Remanufactured') " .                
                " ORDER BY Ordered_Qty ASC";

        try{

            $parts = [];

            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = new Part($r);
                array_push($parts, $part);

            }   // while()

            return $parts;

        } catch(Exception $e){

            echo "Fetching RO parts failed." . $e->getMessage();

        }   // try-catch{}

    }   // GetPartsList()

    function ProcessGET($roNum){

        require('db_open.php');

        $sql = <<<strSQL
                    SELECT RONum, Owner, Vehicle, Estimator, Technician,
                        Vehicle_Color, License_Plate, Vehicle_In, Scheduled_Out
                    FROM Repairs WHERE RONum =
                strSQL;

        $sql .= $roNum;

        try{

            $s = mysqli_query($conn, $sql);
            $r = mysqli_fetch_assoc($s);

            $car = new Car($r);
            $car->partsList = GetPartsList($roNum, $conn);

        } catch(Exception $e){

            echo "Fetching RO details failed." . $e->getMessage();

        } finally {

            $conn = null;
            return $car;

        }   // try-catch{}
    }   // ProcessGET($roNum)

?>
