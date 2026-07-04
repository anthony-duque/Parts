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
        public $location_ID;
        public $cars = [];

        function __construct($rec, $dbConn){
            $this->name = str_replace("'", "''", $rec["vendor_name"]);
            $this->location_ID = $rec["loc_id"];
        }   // Vendor()

        function Get_Cars_for_Vendor($dbConn, $sqlDtClause){

            $carList = [];

            $sql = <<<strSQL

                SELECT DISTINCT
                    r.ro_num, r.vehicle, r.owner,
                    r.estimator, r.technician,
                    r.vehicle_in, r.current_phase,
                    r.loc_id

                FROM parts_status pse INNER JOIN repairs r

                WHERE vendor_name = '$this->name'
                    AND pse.ro_num = r.ro_num
                    AND pse.loc_id = r.loc_id
                    AND $sqlDtClause

                ORDER BY r.ro_num
            strSQL;

            try{

                $s = mysqli_query($dbConn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $car = new Car($r);
                    array_push($carList, $car);
                }

            } catch(Exception $e){

                echo "Fetching cars per vendor failed." . $e->getMessage();
                $dbConn = null;

            } finally {
                $this->cars = $carList;
            }   // try-catch{}
        }

    }   // Vendor{}


    class Car {

        public $ro_num;
        public $location_ID;
        public $owner;  
        public $vehicle;
        public $estimator;
        public $technician;
        public $vehicle_in;
        public $current_phase;
        public $parts = [];

        function __construct($rec){

            $this->ro_num           = $rec["ro_num"];
            $this->location_ID      = $rec["loc_id"];
            $this->vehicle          = $rec["vehicle"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->estimator        = $rec["estimator"];
            $this->technician       = $rec["technician"];
            $this->vehicle_in       = $rec["vehicle_in"];
            $this->current_phase    = $rec["current_phase"];

        }

        function Get_Parts_for_Car($dbConn, $sqlDtClause, $vendorName){

            $sql = <<<strSQL

                SELECT
                    part_number,
                    part_description,
                    received_qty,
                    invoice_date

                FROM parts_status

                WHERE ro_num = '$this->ro_num'
                    AND vendor_name = '$vendorName'
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
            $this->part_number          = $rec["part_number"];
            $this->part_description     = $rec["part_description"];
            $this->received_quantity    = $rec["received_qty"];
            $this->invoice_date         = GetDisplayDate($rec["invoice_date"]);
        }   // Part()
    }   // Part{}


    function Get_All_Vendors($dbConn, $sqlDateClause){

        $sql = <<<strSQL

                    SELECT DISTINCT pse.vendor_name, pse.loc_id 

                    FROM parts_status pse INNER JOIN repairs r
                        ON pse.ro_num = r.ro_num

                    WHERE pse.vendor_name NOT IN (%s)
                         AND $sqlDateClause

                    ORDER BY pse.vendor_name

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