<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case 'POST':;

      $json = file_get_contents('php://input');
      $data = json_decode($json);
//      ProcessPOST($data);
      break;

   case "PUT":    // Could read from input and query string

      echo 'PUT';
      $putData = fopen("php://input", "r");
      $rawJson = "";

      while($data = fread($putData, 1024)){
          $rawJson .= $data;
      }
      fclose($putData);

      $jsonData = json_decode($rawJson);
      //var_dump($jsonData);

//      ProcessPUT($jsonData);
      break;

   case "GET":  // get cars on the Paint List
      Process_GET();
      break;

   default:
      break;

} // switch()


class Sublet{

    public $part_description;
    public $vendor_name;

    function __construct($rec){
        $this->part_description = $rec["Part_Description"];
        $this->vendor_name      = $rec["Vendor_Name"];
    }
}   // Sublet{}


function



?>
