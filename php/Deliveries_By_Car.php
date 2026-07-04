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

    $allCars = Get_All_Cars($conn, $dateClause);     // Get all cars with deiiveries (invoice_date not null)

    foreach($allCars as $car){

        $car->Get_Vendors_for_Car($conn, $dateClause);   // Get all the vendors that delivered per car

        foreach($car->vendors as $vendor){

            $vendor->Get_Vendor_Parts($car->ro_num, $car->location_ID, $conn, $dateClause);  // Get the parts delivered by the vendor for each car

        }   // foreach($car)
    }   // foreach($allCars)

   echo json_encode($allCars);

        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $name;
        public $parts = [];

        function __construct($rec, $dbConn){
//            $this->name = $rec["Vendor_Name"];
            $this->name = str_replace("'", "''", $rec["vendor_name"]);
        }   // Vendor()

        function Get_Vendor_Parts($ro, $loc_ID, $dbConn, $sqlDtClause){

            $partsList = [];

            $sql = <<<strSQL
                        SELECT part_number, part_description, received_qty, invoice_date
                        FROM parts_status
                        WHERE ro_num = '$ro' AND loc_id = $loc_ID
                            AND vendor_name = '$this->name'
                            AND $sqlDtClause
                    strSQL;

            try{

                $s = mysqli_query($dbConn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $part = new Part($r);
                    array_push($partsList, $part);
                }

            } catch(Exception $e){

                echo "Fetching parts per car failed." . $e->getMessage();
                $dbConn = null;

            } finally {
                $this->parts = $partsList;
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
        public $vendors = [];

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

        function Get_Vendors_for_Car($dbConn, $sqlDtClause){

            $sql = <<<strSQL
                        SELECT DISTINCT vendor_name
                        FROM parts_status
                        WHERE ro_num = '$this->ro_num'
                            AND $sqlDtClause
                            AND vendor_name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                    strSQL;

            try{

                $vendorList = [];
                $s = mysqli_query($dbConn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $vendor = new Vendor($r, $dbConn);
                    array_push($vendorList, $vendor);
                }

            } catch(Exception $e){

                echo "Fetching vendors per car failed." . $e->getMessage();
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
            $this->part_number          = $rec["part_number"];
            $this->part_description     = $rec["part_description"];
            $this->received_quantity    = $rec["received_qty"];
            $this->invoice_date         = GetDisplayDate($rec["invoice_date"]);
        }   // Part()
    }   // Part{}


    function Get_All_Cars($dbConn, $sqlDtClause){

        $sql = <<<strSQL

                    SELECT DISTINCT p.ro_num AS ro_num, SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                        r.vehicle, r.technician, r.estimator, r.vehicle_in, r.current_phase,
                        r.loc_id

                    FROM parts_status p INNER JOIN repairs r
                            ON p.ro_num = r.ro_num AND p.loc_id = r.loc_id

                    WHERE vendor_name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                        AND $sqlDtClause
                    
                        ORDER BY ro_num DESC
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
