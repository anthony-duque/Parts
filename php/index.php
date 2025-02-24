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
}   // switch()


function GetShopLocations($dbConn){

    class Location{

        public $id;
        public $location;

        function __construct($rec){
            $this->id = $rec["id"];
            $this->location = $rec["Location"];
        }
    }

    $locations = [];

    try{

        $sql = "SELECT id, Location FROM Location_IDs";
        $s = mysqli_query($dbConn, $sql);

        while($r = mysqli_fetch_assoc($s)){
            $location = new Location($r);
            array_push($locations, $location);
        }   // while()

    } catch(Exception $e){

        echo "Fetching Shop Locations failed. " . $e->getMessage();

    } finally {
        return $locations;
    }   // finally{}

}   // GetShopLocations()


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
    }   // catch()

    finally {

        class Page_Info{

            public $last_update;
            public $locations = [];

            function __construct($l_update, $db_conn){
                $this->last_update = $l_update;
                $this->locations = GetShopLocations($db_conn);
            }   // function()

        }   // class{}

        $page_info = new Page_Info($last_upload_date, $conn);
        $conn = null;

        return $page_info;
    }

}   // ProcessGET()

?>
