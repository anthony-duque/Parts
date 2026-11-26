<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('./Utility_Scripts.php');
    require('./Global_Consts.php');

/////////////////////////////////////////////////////////////
    
    $companyID  = $_GET["companyID"];

    $sqlDateClause = Global_Consts::getDateRangeStr();

        // Get all Repairs associated with company
    $sql = <<<strSQL

            SELECT r.id AS repair_id, r.shop_id, r.ro_num,
                    SUBSTRING_INDEX(r.owner, ',', 1) AS owner,
                    r.vehicle, r.technician, r.estimator, r.vehicle_in
            FROM repairs r
            WHERE r.id IN (
                SELECT DISTINCT r.id
                FROM repairs r INNER JOIN parts p 
                        ON r.id = p.repair_id
                    INNER JOIN shops s
                        ON r.shop_id = s.id
                WHERE vendor_name NOT IN (%s)
                    AND $sqlDateClause
                    AND s.company_id = $companyID
            );
    strSQL;

    $sql = sprintf($sql, IN_HOUSE_VENDORS);


    try {

        $allCars = [];
        $s = mysqli_query(Global_Consts::getConn(), $sql);

        while($r = mysqli_fetch_assoc($s)){
            $car = new Car($r);
            array_push($allCars, $car);
        }

    } catch (Exception $e){

        echo "Fetching cars failed." . $e->getMessage();

    } finally {

        Global_Consts::closeConn();
        echo json_encode($allCars);
    
    }   // try-catch{}


//////////////////////////////////////////////

    class Car {

        public $repair_id;
        public $ro_num;
        public $location_ID;
        public $owner;
        public $vehicle;
        public $estimator;
        public $technician;
        public $vehicle_in;
        public $vendors;

        function __construct($rec){

            $this->repair_id        = $rec["repair_id"];
            $this->ro_num           = $rec["ro_num"];
            $this->location_ID      = $rec["shop_id"];
            $this->vehicle          = $rec["vehicle"];
            $this->owner            = ucwords(strtolower($rec["owner"]));
            $this->estimator        = $rec["estimator"];
            $this->technician       = $rec["technician"];
            $this->vehicle_in       = $rec["vehicle_in"];

            $this->vendors          = $this->Get_Vendors_for_Car();
        }


        function Get_Vendors_for_Car(){

            $sqlDateClause = Global_Consts::getDateRangeStr();

            $sql = <<<strSQL

                SELECT DISTINCT vendor_name
                FROM parts p
                WHERE repair_id = $this->repair_id
                    AND $sqlDateClause
                    AND vendor_name NOT IN (%s)

            strSQL;

            $sql = sprintf($sql, IN_HOUSE_VENDORS);

            try{

                $vendorList = [];

                $s = mysqli_query(Global_Consts::getConn(), $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $vendor = new Vendor($r, $this->repair_id);
                    array_push($vendorList, $vendor);
                }

            } catch(Exception $e){

                echo "Fetching vendors per car failed." . $e->getMessage();
                Global_Consts::closeConn();

            } finally {

                return $vendorList;
            }   // try-catch{}

        }   // Get_Vendors_for_Car()
    }   // Car{}


        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $name;
        public $parts = [];

        function __construct($rec, $repairID){

            $vendor_name        = $rec["vendor_name"];
            $this->name         = str_replace("'", "''", $vendor_name);
            $this->parts        = $this->Get_Vendor_Parts($repairID);

        }   // Vendor()


        function Get_Vendor_Parts($repair_id){

            $partsList = [];

            $sqlDateClause = Global_Consts::getDateRangeStr();

            $sql = <<<strSQL

                        SELECT number, description, received_qty, invoice_date
                        FROM parts
                        WHERE repair_id = $repair_id
                            AND vendor_name = '$this->name'
                            AND $sqlDateClause

                    strSQL;

            try{

                $s = mysqli_query(Global_Consts::getConn(), $sql);

                while($r = mysqli_fetch_assoc($s)){
                    $part = new Part($r);
                    array_push($partsList, $part);
                }

            } catch(Exception $e){

                echo "Fetching parts per car failed." . $e->getMessage();
                Global_Consts::closeConn();

            } finally {

                return $partsList;

            }   // try-catch{}
        }
    }   // Vendor{}


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
