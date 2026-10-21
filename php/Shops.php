<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$method = $_SERVER['REQUEST_METHOD'];
$shopID = $_GET["shopID"] ?? null;
$companyID = $_GET["companyID"] ?? null;

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data, $shopID);
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
      var_dump($jsonData);
      ProcessPUT($jsonData);
      break;

   case "GET":  // get shops

        if ($shopID) {
            $shops = Process_GET_By_Shop($shopID);
        } else if ($companyID) {
            $shops = Process_GET_By_Company($companyID);
        } else {
            $shops = [];
        }

        echo json_encode($shops);
        break;

   default:
      break;

} // switch()

////////////////////////////


class Shop {

    public $id;
    public $name;

    function __construct($rec){
        $this->id = $rec["id"];
        $this->name = $rec["name"];
    }

}   // Shop{}


function Process_GET_By_Shop($sID){

    $shops = [];

    $sql = <<<strSQL
                SELECT id, name
                FROM shops
                WHERE id = $sID
            strSQL;

    require('db_open.php');

    $s = mysqli_query($conn, $sql);

    while($r = mysqli_fetch_assoc($s)){
        array_push($shops, new Shop($r));
    }   // while()

    $conn->close();
    return $shops;

}   //  Process_GET()


function Process_GET_By_Company($cID){

    $shops = [];

    $sql = <<<strSQL
                SELECT id, name
                FROM shops
                WHERE company_id = $cID
            strSQL;

    require('db_open.php');

    $s = mysqli_query($conn, $sql);

    while($r = mysqli_fetch_assoc($s)){
        array_push($shops, new Shop($r));
    }   // while()

    $conn->close();
    return $shops;

}   //  Process_GET()


function ProcessPOST($shopData, $shop_ID){

    // Implementation for POST request

}   //  Process_POST()

?>