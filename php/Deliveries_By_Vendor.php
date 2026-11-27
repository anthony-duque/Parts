<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('./Utility_Scripts.php');
    require('./Global_Consts.php');

////////////////////////////////////////////////////

    $companyID  = $_GET["companyID"];

    $dateStrFilter = Global_Consts::getDateRangeStr();

    $sql = <<<strSQL

                SELECT DISTINCT p.vendor_name, r.shop_id 

                FROM parts p INNER JOIN repairs r
                        ON p.repair_id = r.id
                    INNER JOIN shops s
                        ON r.shop_id = s.id

                WHERE p.vendor_name NOT IN (%s)
                        AND $dateStrFilter
                        AND s.company_id = $companyID

                ORDER BY p.vendor_name

            strSQL;

    $sql = sprintf($sql, IN_HOUSE_VENDORS);

//    echo $sql;
//    exit;
    
    try {

        $vendors = [];
        $s = mysqli_query(Global_Consts::getConn(), $sql);

        while($r = mysqli_fetch_assoc($s)){
            $vendor = new Vendor($r);
            array_push($vendors, $vendor);
        }

    } catch (Exception $e){

        echo "Fetching vendors failed." . $e->getMessage();

    } finally {

        Global_Consts::closeConn();

    }   // try-catch{}

   echo json_encode($vendors);

   ////////////////////////////////////////////


        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public  $name;
        public  $shop_id;
        public  $cars = [];

        function __construct($rec){

            $this->name     = str_replace("'", "''", $rec["vendor_name"]);
            $this->shop_id  = $rec["shop_id"];
            $this->cars     = $this->Get_Cars_for_Vendor();

        }   // Vendor()


        function Get_Cars_for_Vendor(){

            $carList = [];

            $sqlDateClause = Global_Consts::getDateRangeStr();

            $sql = <<<strSQL

                SELECT
                    r.id AS repair_id, r.shop_id,
                    r.ro_num, r.vehicle, r.owner,
                    r.estimator, r.technician,
                    r.vehicle_in

                FROM repairs r

                WHERE r.id IN (

                    SELECT DISTINCT r.id 
                    FROM repairs r INNER JOIN parts p
                        ON r.id = p.repair_id
                    WHERE 
                        r.shop_id       = $this->shop_id AND 
                        p.vendor_name   = '$this->name' AND
                        $sqlDateClause
                );
            strSQL;

            try{

                $s = mysqli_query(Global_Consts::getConn(), $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $car = new Car($r, $this->name);
                    array_push($carList, $car);
                }

            } catch(Exception $e){

                echo "Fetching cars per vendor failed." . $e->getMessage();
                Global_Consts::closeConn();

            } finally {

                return $carList;
            }   // try-catch{}
        }

    }   // Vendor{}


    class Car {

        public $repair_id;
        public $ro_num;
        public $shop_id;
        public $owner;  
        public $vehicle;
        public $estimator;
        public $technician;
        public $vehicle_in;
        public $parts = [];

        function __construct($rec, $vendorName){

            $this->repair_id        = $rec["repair_id"];
            $this->ro_num           = $rec["ro_num"];
            $this->shop_id          = $rec["shop_id"];
            $this->vehicle          = $rec["vehicle"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->estimator        = $rec["estimator"];
            $this->technician       = $rec["technician"];
            $this->vehicle_in       = $rec["vehicle_in"];
            $this->parts            = $this->Get_Parts_for_Car($vendorName);

        }   // __construct()

        function Get_Parts_for_Car($vendor_name){

            $sqlDateClause = Global_Consts::getDateRangeStr();

            $sql = <<<strSQL

                SELECT
                    number,
                    description,
                    received_qty,
                    invoice_date

                FROM parts

                WHERE repair_id = $this->repair_id
                    AND vendor_name = '$vendor_name'
                    AND $sqlDateClause

            strSQL;

            try{

                $partsList = [];
                $s = mysqli_query(Global_Consts::getConn(), $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $part = new Part($r);
                    array_push($partsList, $part);
                }

            } catch(Exception $e){

                echo "Fetching parts failed." . $e->getMessage();
                Global_Consts::closeConn();

            } finally {

                return $partsList;
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

?>