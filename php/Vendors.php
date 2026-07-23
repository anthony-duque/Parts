<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Allow-Control-Origin: *");
    $method = $_SERVER['REQUEST_METHOD'];   // See if it is a GET, POST, DELETE, etc
    $companyID = $_GET["companyID"];   // Get

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
           $vendors = ProcessGET($companyID);
//           print_r($vendors);

           if (json_encode($vendors) === null){
               echo "JSON error: " . json_last_error_msg();
           } else {
               echo json_encode($vendors, JSON_INVALID_UTF8_SUBSTITUTE);
           }

           break;
    }   // switch()


    class Vendor{

        public $name;
        public $oem;
        public $phone_number;
        public $address;
        public $city;
        public $state;
        public $zipcode;
        public $email;
        public $location_ID;
 //       public $location;

        function __construct($rec){
            $this->name         = $rec["name"];
            $this->oem          = $rec["oem"];
            $this->phone_number = $rec["phone_number"];
            $this->address      = $rec["address"];
            $this->city         = $rec["city"];
            $this->state        = $rec["state"];
            $this->zipcode      = $rec["zipcode"];
            $this->email        = $rec["email"];
            $this->location_ID  = $rec["location_id"];
//            $this->location     = $rec["location"];
        }
    }

    function ProcessGET($company_id){


        require('db_open.php');

        $sql = <<<strSQL
                SELECT
                    v.name, v.oem, v.phone_number,
                    v.address, v.city, v.state, v.zipcode,
                    v.email, v.location_id
                FROM vendors v INNER JOIN locations li
                    ON v.location_id = li.id
                WHERE li.company_id = $company_id
                ORDER BY name
            strSQL;

        $vendorList = [];

        try{

            $eachVendor = null;
            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $eachVendor = new Vendor($r);
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