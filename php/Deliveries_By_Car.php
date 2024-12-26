<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('Utility_Scripts.php');
    require('db_open.php');

    $numDays = $_GET["numDays"];

    $allCars = Get_All_Cars($conn, $numDays);     // Get all cars with deiiveries (invoice_date not null)

    foreach($allCars as $car){

        $car->Get_Vendors_for_Car($conn, $numDays);   // Get all the vendors that delivered per car

        foreach($car->vendors as $vendor){

            $vendor->Get_Vendor_Parts($car->ro_num, $conn, $numDays);  // Get the parts delivered by the vendor for each car

        }   // foreach($car)
    }   // foreach($allCars)

   echo json_encode($allCars);

        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $name;
        public $parts = [];

        function __construct($rec, $dbConn){
            $this->name = $rec["Vendor_Name"];
        }   // Vendor()

        function Get_Vendor_Parts($ro, $dbConn, $days){

            $partsList = [];

            $sql = <<<strSQL
                        SELECT Part_Number, Part_Description, Received_Qty, Invoice_Date
                        FROM PartsStatusExtract
                        WHERE RO_Num = $ro AND Vendor_Name = '$this->name'
                            AND Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
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
        public $estimator;
        public $technician;
        public $vehicle_in;
        public $current_phase;
        public $vendors = [];

        function __construct($rec){
            $this->ro_num           = $rec["RO_Num"];
            $this->vehicle          = $rec["Vehicle"];
            $this->owner            = ucwords(strtolower($rec["Owner"]));
            $this->estimator        = $rec["Estimator"];
            $this->technician       = $rec["Technician"];
            $this->vehicle_in       = $rec["Vehicle_In"];
            $this->current_phase    = $rec["CurrentPhase"];
        }

        function Get_Vendors_for_Car($dbConn, $days){

            $sql = <<<strSQL
                        SELECT DISTINCT Vendor_Name
                        FROM PartsStatusExtract
                        WHERE RO_Num = $this->ro_num
                             AND Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
                             AND Vendor_Name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                    strSQL;

            try{

                $vendorList = [];
                $s = mysqli_query($dbConn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $vendor = new Vendor($r, $dbConn);
                    array_push($vendorList, $vendor);
                }

            } catch(Exception $e){

                echo "Fetching vendors failed." . $e->getMessage();
                $dbConn = null;

            } finally {

                $this->vendors = $vendorList;
            }   // try-catch{}

        }   // Get_Vendors_for_Car()

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
            $this->invoice_date         = GetDisplayDate($rec["Invoice_Date"]);
        }   // Part()
    }   // Part{}


    function Get_All_Cars($dbConn, $days){

        $sql = <<<strSQL
                    SELECT DISTINCT p.RO_Num, SUBSTRING_INDEX(r.Owner, ',', 1) AS Owner,
                            r.Vehicle, r.Technician, r.Estimator, r.Vehicle_In, r.CurrentPhase                    FROM PartsStatusExtract p INNER JOIN Repairs r
                        ON p.RO_Num = r.RONum
                    WHERE Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
                        AND Vendor_Name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                strSQL;

//        echo $sql;

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
