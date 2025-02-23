<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Access-Allow-Control-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case "GET":
      $pg_info = ProcessGET();
      echo json_encode($pg_info);
      break;

   default:
      break;
}


function ProcessGET(){

    require('db_open.php');

    $last_upload_date = '';

    $sql = "SELECT value FROM Adhoc_Table WHERE name = 'LAST_UPLOAD'";

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);
        $last_upload_date = $r["value"];

    } catch (Exception $e){

        echo "Fetching 'Last Update' value failed." . $e->getMessage();

    }

    finally {

        $conn = null;

        class Page_Info{

            public $last_update;

            function __construct($l_update){
                $this->last_update = $l_update;
            }
        }

        $page_info = new Page_Info($last_upload_date);
        return $page_info;
    }

}   // ProcessGET()

?>
