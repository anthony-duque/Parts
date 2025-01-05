<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('Utility_Scripts.php');
    require('db_open.php');

    $numDays = $_GET["numDays"];

    if ($numDays > 1){
        $dateClause = "Invoice_Date IS NOT NULL";
    } else {
        $dateClause = "DATEDIFF(CURDATE(), Invoice_Date) = $numDays";
    }

    $allVendors = Get_All_Vendors($conn, $dateClause);     // Get all cars with deiiveries (invoice_date not null)

    foreach($allVendors as $vendor){

        $vendor->Get_Cars_for_Vendor($conn, $dateClause, $vendor->name);   // Get all the vendors that delivered per car

        foreach($vendor->cars as $car){

            $car->Get_Parts_for_Car($conn, $dateClause, $vendor->name);  // Get the parts delivered by the vendor for each car

        }   // foreach($car)

    }   // foreach($allVendors)

   echo json_encode($allVendors);

        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $name;
        public $cars = [];

        function __construct($rec, $dbConn){
            $this->name = str_replace("'", "''", $rec["Vendor_Name"]);
        }   // Vendor()

        function Get_Cars_for_Vendor($dbConn, $sqlDtClause){

            $carList = [];

            $sql = <<<strSQL

            SELECT DISTINCT

                r.RONum AS RO_Num, r.Vehicle AS Vehicle, r.Owner AS Owner,
                r.Estimator AS Estimator, r.Technician AS Technician,
                r.Vehicle_In AS Vehicle_In, r.CurrentPhase AS CurrentPhase

            FROM PartsStatusExtract pse INNER JOIN Repairs r

            WHERE Vendor_Name = '$this->name'
                AND pse.RO_Num = r.RONum
                AND $sqlDtClause
                AND RO_Num <> 1004

            ORDER BY r.RONum

            strSQL;

            try{

                $s = mysqli_query($dbConn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $car = new Car($r);
                    array_push($carList, $car);
                }

            } catch(Exception $e){

                echo "Fetching cars failed." . $e->getMessage();
                $dbConn = null;

            } finally {
                $this->cars = $carList;
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
        public $parts = [];

        function __construct($rec){

            $this->ro_num           = $rec["RO_Num"];
            $this->vehicle          = $rec["Vehicle"];
            $this->owner            = ucwords(strtolower($rec["Owner"]));
            $this->estimator        = $rec["Estimator"];
            $this->technician       = $rec["Technician"];
            $this->vehicle_in       = $rec["Vehicle_In"];
            $this->current_phase    = $rec["CurrentPhase"];

        }

        function Get_Parts_for_Car($dbConn, $sqlDtClause, $vendorName){

            $sql = <<<strSQL

                SELECT
                    Part_Number,
                    Part_Description,
                    Received_Qty,
                    Invoice_Date

                FROM PartsStatusExtract

                WHERE RO_Num = $this->ro_num
                    AND Vendor_Name = '$vendorName'
                    AND $sqlDtClause

            strSQL;

            try{

                $partsList = [];
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


    function Get_All_Vendors($dbConn, $sqlDateClause){

        $sql = <<<strSQL
                    SELECT DISTINCT Vendor_Name
                    FROM PartsStatusExtract
                    WHERE Vendor_Name NOT IN (%s)
                         AND $sqlDateClause
                         AND RO_Num <> 1004
                strSQL;
        $sql = sprintf($sql, IN_HOUSE_VENDORS);
        //echo $sql;
        try {

            $vendors = [];
            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $vendor = new Vendor($r, $dbConn);
                array_push($vendors, $vendor);
            }

        } catch (Exception $e){

            echo "Fetching vendors failed." . $e->getMessage();
            $dbConn = null;

        } finally {

            return $vendors;

        }   // try-catch{}
    }   // Get_All_Cars()

?>
