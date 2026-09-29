<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$method     = $_SERVER['REQUEST_METHOD'];
//$locationID = $_GET["locationID"];

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data);
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
      ProcessPUT($jsonData);
      break;

   case "GET":  // get cars on the Paint List
      $stage_headings = Process_GET();
      echo json_encode($stage_headings);
      break;

   default:
      break;

} // switch()

////////////////////////////


class Heading {

    public     $id;
    public     $description;

    function __construct($rec){
        $this->id           = $rec["id"];
        $this->description  = $rec["description"];
    }

}   // Heading{}


function Process_GET(){

    $headings = [];

    $sqlQuery = <<<strSQL
                SELECT id, description
                FROM stage_headings
                ORDER BY description
            strSQL;

    require('db_open.php');

    $s = mysqli_query($conn, $sqlQuery);

    while($r = mysqli_fetch_assoc($s)){
        array_push($headings, new Heading($r));
    }   // while()

    $conn = null;
    return $headings;

}   //  Process_GET()

?>
