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
            $this->name = str_replace("'", "''", $rec["Vendor_Name"]);
        }   // Vendor()

        function Get_Vendor_Parts($ro, $loc_ID, $dbConn, $sqlDtClause){

            $partsList = [];

            $sql = <<<strSQL
                        SELECT part_number, part_description, received_qty, invoice_date
                        FROM parts_status
                        WHERE ro_num = $ro AND loc_id = $loc_ID
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

                echo "Fetching parts failed." . $e->getMessage();
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

            $this->ro_num           = $rec["RO_Num"];
            $this->location_ID      = $rec["Loc_ID"];
            $this->vehicle          = $rec["Vehicle"];
            $this->owner            = ucwords(strtolower($rec["Owner"]));
            $this->estimator        = $rec["Estimator"];
            $this->technician       = $rec["Technician"];
            $this->vehicle_in       = $rec["Vehicle_In"];
            $this->current_phase    = $rec["CurrentPhase"];
        }

        function Get_Vendors_for_Car($dbConn, $sqlDtClause){

            $sql = <<<strSQL
                        SELECT DISTINCT vendor_name
                        FROM parts_status
                        WHERE ro_num = $this->ro_num
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


    function Get_All_Cars($dbConn, $sqlDtClause){

        $sql = <<<strSQL
                    SELECT DISTINCT p.ro_num AS RO_Num, SUBSTRING_INDEX(r.owner, ',', 1) AS Owner,
                        r.vehicle, r.technician, r.estimator, r.vehicle_in, r.current_phase,
                        r.loc_id
                    FROM parts_status p INNER JOIN repairs r
                            ON p.ro_num = r.ro_num AND p.loc_id = r.loc_id
                    WHERE vendor_name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                        AND $sqlDtClause
                        AND ro_num <> 1004
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
