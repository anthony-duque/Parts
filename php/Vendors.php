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
           $vendors = ProcessGET();
           echo json_encode($vendors);
           break;

        default:
            break;
    }   // switch()


    function ProcessGET(){

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
            public $location;

            function __construct($rec){
                $this->name         = $rec["name"];
                $this->oem          = $rec["oem"];
                $this->phone_number = $rec["phone_number"];
                $this->address      = $rec["address"];
                $this->city         = $rec["city"];
                $this->state        = $rec["state"];
                $this->zipcode      = $rec["zipcode"];
                $this->email        = $rec["email"];
                $this->location_ID  = $rec["location_ID"];
                $this->location     = $rec["shop_location"];
            }
        }

        require('db_open.php');

        $sql = <<<strSQL
                SELECT
                    name, oem, phone_number,
                    address, city, state, zipcode,
                    email, location_ID, shop_location
                FROM Vendors
                ORDER BY name
            strSQL;

        $vendorList = [];

        try{

            $eachVendor = null;
            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                $eachVendor = new Vendor($r);
                array_push($vendorList, $eachVendor);
            }

        } catch(Exception $e){

            echo "Fetching Vendors failed." . $e->getMessage();

        } finally {
            //echo "reached finally";
            $conn = null;
            return $vendorList;
        }   // try-catch{}

    }   // ProcessGET()

/*
    function ProcessPOST($delivery){

        require('db_open.php');

        $tsql = "INSERT INTO Deliveries " .
                "(RONum, Location, Customer, Vehicle, Technician, Vendor, Notes) " .
                "VALUES ($delivery->RONum, '$delivery->Location', " .
                         "'$delivery->Customer', '$delivery->Vehicle', " .
                         "'$delivery->Technician', '$delivery->Vendor', " .
                         "'$delivery->Notes')";
       //echo $tsql;
       try{
           $result = $conn->query($tsql);
           echo "New Delivery added successfully!";
       } catch (PDOException $pe){
           echo "New Delivery was not added to database." . $pe->getMessage();
       }

       $conn = null;        // close the database
   }    // ProcessPOST()
*/
?>
