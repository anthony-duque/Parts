<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$method     = $_SERVER['REQUEST_METHOD'];
$locationID = $_GET["locationID"];

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
      $production_stages = Process_GET($locationID);
      echo json_encode($production_stages);
      break;

   default:
      break;

} // switch()

////////////////////////////


class Stage {

    public     $description;
    public     $order_no;

    function __construct($rec){
        $this->description  = $rec["Description"];
        $this->order_no     = $rec["Order_no"];
    }

}   // Stage{}


function Process_GET($locID){

    $stages = [];

    $sqlQuery = <<<strSQL
                SELECT Description, Order_no
                FROM Stage_Headings
                WHERE Loc_ID = $locID
                ORDER BY Order_no
            strSQL;

    require('db_open.php');

    $s = mysqli_query($conn, $sqlQuery);

    while($r = mysqli_fetch_assoc($s)){
        array_push($stages, new Stage($r));
    }   // while()

    $conn = null;
    return $stages;

}   //  Process_GET()


?>
