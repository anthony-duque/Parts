<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){

       case 'POST':;
          $json = file_get_contents('php://input');
          $data = json_decode($json);
//          echo $json;
          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
          echo 'PUT';
//          ProcessPUT();
          break;

       case "GET":
            echo "GET";
//          $materialsList = ProcessGET();
//          echo json_encode($materialsList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }


    function ProcessPOST($order){

        foreach($order->materials as $eachMaterial){
            echo $eachMaterial->description;
            // form the table that enumerates the materials
        }

    }   // ProcessPOST()

//phpinfo();
?>
