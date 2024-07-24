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

exit;


function ProcessGET(){

    require('db_open.php');

    class Page_Info{

        public $last_update;

        function __construct($l_update){
            $this->last_update = $l_update;
        }
    }

    $last_update = '';

    $sql = "SELECT value FROM Adhoc_Table WHERE name = 'LAST_UPLOAD'";

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);
        $last_update = $r["value"];

    } catch (Exception $e){

        echo "Fetching 'last update' value failed." . $e->getMessage();

    }

    finally {

        $conn = null;
        $page_info = new Page_Info($last_update);
        return $page_info;
    }

}   // ProcessGET()

?>
