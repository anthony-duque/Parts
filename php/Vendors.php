<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Allow-Control-Origin: *");
    $method = $_SERVER['REQUEST_METHOD'];   // See if it is a GET, POST, DELETE, etc

     switch($method){
/*
        case 'POST':
//            echo 'POST';
            $rcvdJson = file_get_contents('php://input');
            //echo $rcvdJson;
            $data = json_decode($rcvdJson);
            ProcessPOST($data);
            break;
*/
        case "GET":
        default:
           $vendors = ProcessGET();
           echo json_encode($vendors);
           break;
    }   // switch()


    function ProcessGET(){

        class Vendor{

            public $id;
            public $name;
            public $oem;
            public $phone_number;
            public $address;
            public $city;
            public $state;
            public $zipcode;
            public $email;
            public $location_ID;
            public $location;

            function __construct($rec){
                $this->id           = $rec["id"];
                $this->name         = $rec["name"];
                $this->oem          = $rec["oem"];
                $this->phone_number = $rec["phone_number"];
                $this->address      = $rec["address"];
                $this->city         = $rec["city"];
                $this->state        = $rec["state"];
                $this->zipcode      = $rec["zipcode"];
                $this->email        = $rec["email"];
                $this->location_ID  = $rec["location_id"];
                $this->location     = $rec["shop_location"];
            }
        }

        require('db_open.php');

        $sql = <<<strSQL
                SELECT
                    id, name, oem, phone_number,
                    address, city, state, zipcode,
                    email, location_id, shop_location
                FROM vendors
                ORDER BY name
            strSQL;

        $vendorList = [];

        try{

            $eachVendor = null;
            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $eachVendor = new Vendor($r);
//                echo "Vendor: " . $eachVendor->name . "<br/>";
                $vendorList[] = $eachVendor;
            }

        } catch(Exception $e){

            echo "Fetching Vendors failed." . $e->getMessage();

        } finally {
//            var_dump($vendorList);
            $conn = null;
            return $vendorList;
        }   // try-catch{}

    }   // ProcessGET()

?>
