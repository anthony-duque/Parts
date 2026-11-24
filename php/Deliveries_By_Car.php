<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('Utility_Scripts.php');
    require('db_open.php');

    $numDays    = $_GET["numDays"];
    $companyID  = $_GET["companyID"];

    if ($numDays > 1){
        $dateClause = "Invoice_Date IS NOT NULL";
    } else {
        $dateClause = "DATEDIFF(CURDATE(), Invoice_Date) = $numDays";
    }

        // Get all Repairs associated with company
    $allCars = Get_All_Cars($conn, $dateClause, $companyID);     // Get all cars with deiiveries (invoice_date not null)

    foreach($allCars as $car){      // for each car     

        $car->Get_Vendors_for_Car($conn, $dateClause);   // Get all the vendors that delivered per car

        foreach($car->vendors as $vendor){      // for each vendor

            $vendor->Get_Vendor_Parts($car->repair_id, $dateClause, $conn);  // Get the parts delivered by the vendor for each car

        }   // foreach($car)
    }   // foreach($allCars)

   echo json_encode($allCars);

        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $name;
        public $parts = [];

        function __construct($rec){

            $vendor_name = $rec["vendor_name"];
            $this->name = str_replace("'", "''", $vendor_name);

        }   // Vendor()


        function Get_Vendor_Parts($repairID, $sqlDtClause, $dbConn){

            $partsList = [];

            $sql = <<<strSQL
                        SELECT number, description, received_qty, invoice_date
                        FROM parts
                        WHERE repair_id = $repairID
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

        public $repair_id;
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

            $this->repair_id        = $rec["repair_id"];
            $this->ro_num           = $rec["ro_num"];
            $this->location_ID      = $rec["shop_id"];
            $this->vehicle          = $rec["vehicle"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->estimator        = $rec["estimator"];
            $this->technician       = $rec["technician"];
            $this->vehicle_in       = $rec["vehicle_in"];
            $this->current_phase    = $rec["current_phase"];
        }

        function Get_Vendors_for_Car($db_conn, $sqlDtClause){

            $sql = <<<strSQL

                        SELECT DISTINCT vendor_name
                        FROM parts p
                        WHERE repair_id = $this->repair_id
                            AND $sqlDtClause
                            AND vendor_name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                    strSQL;

            try{

                $vendorList = [];
                $s = mysqli_query($db_conn, $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $vendor = new Vendor($r);
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
            $this->part_number          = $rec["number"];
            $this->part_description     = $rec["description"];
            $this->received_quantity    = $rec["received_qty"];
            $this->invoice_date         = GetDisplayDate($rec["invoice_date"]);
        }   // Part()
    }   // Part{}


    function Get_All_Cars($dbConn, $sqlDtClause, $company_id){

        $sql = <<<strSQL

                    SELECT DISTINCT p.repair_id, r.ro_num, SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                        r.vehicle, r.technician, r.estimator, r.vehicle_in, r.current_phase,
                        r.shop_id
                    FROM parts p INNER JOIN repairs r
                            ON p.repair_id = r.id
                        INNER JOIN shops s
                            ON r.shop_id = s.id
                    WHERE vendor_name NOT IN ('**IN-HOUSE', 'ASTECH', 'AIRTIGHT AUTO GLASS', 'BIG BRAND','Jim''s Tire Center', 'PRO TECH DIAGNOSTICS')
                        AND $sqlDtClause
                        AND s.company_id = $company_id
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
