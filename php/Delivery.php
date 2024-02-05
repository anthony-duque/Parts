<?php

    header("Access-Allow-Control-Origin: *");
    $method = $_SERVER['REQUEST_METHOD'];   // See if it is a GET, POST, DELETE, etc

     switch($method){

        case 'POST':
//            echo 'POST';
            $rcvdJson = file_get_contents('php://input');
            $data = json_decode($rcvdJson);
            ProcessPOST($data);
            //echo $rcvdJson;
            break;

        case "GET":
  //         echo 'GET';
  //         $qString = $_GET["id"];
           $patientList = ProcessGET();
           echo json_encode($deliveryList);
           break;

        default:
            break;
    }

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

   }


?>
