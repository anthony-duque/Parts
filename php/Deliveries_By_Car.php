<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('db_open.php');

    $allCars = Get_All_Cars($conn);     // Get all cars with deiiveries (invoice_date not null)

    foreach($allCars as $car){

        $car->vendors = Get_Vendors_for_Car($car->ro_num, $conn);

        foreach($car->vendors as $vendor){
            $vendor->Get_Vendor_Parts($car->ro_num,$conn);
        }   // foreach($car)
    }   // foreach($allCars)

   echo json_encode($allCars);

        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $name;
        public $parts = [];

        function __construct($rec){
            $this->name = $rec["Vendor_Name"];
        }   // Vendor()

        function Get_Vendor_Parts($ro, $dbConn){

            $partsList = [];

            $sql = <<<strSQL
                        SELECT Part_Number, Part_Description, Received_Qty, Invoice_Date
                        FROM PartsStatusExtract
                        WHERE RO_Num = $ro AND Vendor_Name = '$this->name'
                            AND Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    strSQL;

            try{

                $s = mysqli_query($dbConn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $part = new Part($r);
                    array_push($partsList, $part);
                }

            } catch(Exception $e){

                echo "Fetching parts failed." . $e->getMessage();
                $dbConn = null;

            } finally {
                $this->parts = $partsList;
            }   // try-catch{}
        }

    }   // Vendor{}


    class Car {

        public $ro_num;
        public $owner;
        public $vehicle;
        public $vendors = [];

        function __construct($rec){
            $this->ro_num   = $rec["RO_Num"];
            $this->vehicle  = $rec["Vehicle"];
            $this->owner    = $rec["Owner"];
        }
    }   // Car{}


    class Part{

        public $part_number;
        public $part_description;
        public $received_quantity;
        public $invoice_date;

        function __construct($rec){
            $this->part_number          = $rec["Part_Number"];
            $this->part_description     = $rec["Part_Description"];
            $this->received_quantity    = $rec["Received_Qty"];
            $this->invoice_date         = $rec["Invoice_Date"];
        }   // Part()
    }   // Part{}


    function Get_Vendors_for_Car($ro, $dbConn){

        $sql = <<<strSQL
                    SELECT DISTINCT Vendor_Name
                    FROM PartsStatusExtract
                    WHERE RO_Num = $ro
                         AND Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                         AND Vendor_Name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                strSQL;

        try{

            $vendors = [];
            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $vendor = new Vendor($r);
                array_push($vendors, $vendor);
            }

        } catch(Exception $e){

            echo "Fetching vendors failed." . $e->getMessage();
            $dbConn = null;

        } finally {

            return $vendors;
        }   // try-catch{}

    }   // Get_Vendors_for_Car()


    function Get_All_Cars($dbConn){

        $sql = <<<strSQL

                    SELECT DISTINCT p.RO_Num, r.Owner, r.Vehicle
                    FROM PartsStatusExtract p INNER JOIN Repairs r
                        ON p.RO_Num = r.RONum
                    WHERE Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                strSQL;

        try {

            $cars = [];
            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $car = new Car($r);
                array_push($cars, $car);
            }

        } catch (Exception $e){

            echo "Fetching cars failed." . $e->getMessage();
            $dbConn = null;

        } finally {

            return $cars;
        }   // try-catch{}
    }   // Get_All_Cars()

?>
