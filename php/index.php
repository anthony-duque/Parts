<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Utility_Scripts.php';

header('Access-Allow-Control-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];
 
switch($method){

   case "GET":
      $pg_info = ProcessGET($_GET["companyID"]);
      echo json_encode($pg_info);
      break;

   default:
      break;
}   // switch()


class Location{

    public $id;
    public $location;
    public $last_data_upload;

    function __construct($rec){
        $this->id = $rec["id"];
        $this->location = $rec["location"];
        $this->last_data_upload = $rec["last_data_upload"];
    }
}


function GetShopLocations($companyID){

    $locations = [];

    try{

        require('db_open.php');

        $sql = "SELECT id, location, last_data_upload " .
                "FROM locations " . 
                "WHERE company_id = " . $companyID . 
                " ORDER BY location";

        $s = mysqli_query($conn, $sql);

        while($r = mysqli_fetch_assoc($s)){
            $location = new Location($r);
            array_push($locations, $location);
        }   // while()

    } catch(Exception $e){

        echo "Fetching Shop Locations failed. " . $e->getMessage();

    } finally {

        $conn->close();
        return $locations;

    }   // finally{}

}   // GetShopLocations()


class Page_Info{

    public $locations = [];

    function __construct($companyID){

        $this->locations = GetShopLocations($companyID);
    
    }   // function()

}   // class{}


function ProcessGET($company_ID){

    $page_info = new Page_Info($company_ID);

    return $page_info;

}   // ProcessGET()

?>
