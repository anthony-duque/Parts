<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const TARGET_DIR        = "../../extract_files/";   // destination folder on the server
const EXTRACT_FILENAME  = "Extract_CompanyID.csv";  // Daily Out destination file name

const SHOP_ID       = 0;
const SHOP_NAME     = 1;
const RO_NUM        = 2;
const ESTIMATOR     = 3;
const TECHNICIAN    = 4;
const OWNER         = 5;
const VEHICLE_COLOR = 6;
const LICENSE_PLATE = 7;
const VIN           = 8;
const VEHICLE_YEAR  = 9;
const VEHICLE_MAKE  = 10;
const VEHICLE_MODEL = 11;
const CURRENT_PHASE = 12;
const DATE_IN       = 13;
const TARGET_DATE   = 14;
const LINE_NO       = 15;
const OP_CODE       = 16;
const PART_DESC     = 17;
const PART_NO       = 18;
const PART_TYPE     = 19;
const PART_STATUS   = 20;
const VENDOR_NAME   = 21;
const PART_PRICE    = 22;
const RO_QTY        = 23;
const ORDER_DATE    = 24;
const ORDERED_QTY   = 25;
const RECEIVED_QTY  = 26;
const RECEIVED_DATE = 27;
const RETURNED_QTY  = 28;

$companyID = $_GET["companyID"];    // get the company ID from the query string

try{

    $extractFile = TARGET_DIR . str_replace("CompanyID", $companyID, EXTRACT_FILENAME);
    $upload_OK = move_uploaded_file($_FILES["ExtractCSV"]["tmp_name"], $extractFile);

    if ($upload_OK){

        Process_Extract($extractFile, $companyID);
        echo "<br/> Extract upload successful!";
    }

} catch(Exception $e){

    echo "There was an error uploading the " . basename($_FILES["ExtractCSV"]["name"]);
    echo "<br/>Error details: " . $e->getMessage();
    header("Location: ../../html/admin/Upload_Extract.html");

}


function Process_Extract($extract_file, $companyID){

    // Open the extract file for reading
    if (($handle = fopen($extract_file, "r")) === FALSE) {
        echo "Error in opening " . $extract_file;
        exit;
    }



    require('../db_open.php');

    $tsql = "CALL sp_Process_Extract('$companyID')";
    if ($conn->query($tsql) === TRUE) {
        echo "<br/><br/>Extract processed successfully.<br/>";
    } else {
      echo "Error: " . $tsql . "<br> - " . $conn->error;
      exit;
    }

}

?>