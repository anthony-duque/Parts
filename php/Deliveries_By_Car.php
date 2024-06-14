<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('db_open.php');

    $allCars = Get_All_Cars($conn);
    echo json_encode($allCars);

        // Deliveries by Vendor - Cars - Parts
    class Vendor {

        public $vendor_name;
        public $parts = [];

        function __construct($rec){
            $this->vendor_name = $rec["Vendor_Name"];
        }   // Vendor()

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
    }   // Part{}


    function Get_All_Cars($dbConn){

        $sql = <<<strSQL

                    SELECT DISTINCT p.RO_Num, r.Owner, r.Vehicle
                    FROM PartsStatusExtract p INNER JOIN Repairs r
                        ON p.RO_Num = r.RONum
                    WHERE Invoice_Date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)

                strSQL;

        try{

            $cars = [];
            $s = mysqli_query($dbConn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $car = new Car($r);
                array_push($cars, $car);
            }


        } catch(Exception $e){

            echo "Fetching cars failed." . $e->getMessage();
            $dbConn = null;

        } finally {

            return $cars;

        }   // try-catch{}
    }   // Get_All_Cars()

?>
