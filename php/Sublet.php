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
      $ro_Num = $_GET["roNum"];
      $loc_ID = $_GET["locationID"];

      $car_sublets = Process_GET($ro_Num, $loc_ID);
      echo json_encode($car_sublets);
      break;

   default:
      break;

} // switch()


class Sublet{

    public $part_description;
    public $vendor_name;
    public $received_quantity;

    function __construct($rec){
        $this->part_description     = $rec["Part_Description"];
        $this->vendor_name          = $rec["Vendor_Name"];
        $this->received_quantity    = $rec["Received_Qty"];
    }
}   // Sublet{}


function Process_GET($roNum, $locID){

    $carSublets = [];

    $sqlQuery = <<<strSQL
            SELECT Part_Description, Vendor_Name, Received_Qty
            FROM PartsStatusExtract
            WHERE Part_Type = 'Sublet' AND RO_Num = $roNum AND Loc_ID = $locID
            ORDER BY Received_Qty
        strSQL;

//        echo $sqlQuery;
    require('db_open.php');

    try{

        $s = mysqli_query($conn, $sqlQuery);

        while($r = mysqli_fetch_assoc($s)){
            array_push($carSublets, new Sublet($r));
        }   //while{}

    } catch (Exception $e){

        echo "Fetching Car Sublets failed." . $e->getMessage();
    }   // catch()

    finally {
        $conn = null;
        return $carSublets;
    }   // try-catch

}   // Process_GET()



?>
