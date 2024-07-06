<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const RO_NUM           	= 0;
const OWNER            	= 1;
const VEHICLE          	= 2;
const VEHICLE_COLOR    	= 3;
const LICENSE_PLATE    	= 4;
const PARTS_RCVD       	= 5;
const VEHICLE_IN    	= 6;
const CURRENT_PHASE    	= 7;
const SCHEDULED_OUT    	= 8;
const TECHNICIAN       	= 9;
const ESTIMATOR        	= 10;

require('Utility_Scripts.php');
require('Daily_Out_Upload.php');

const TARGET_DIR    = "../extract_files/";  // destination folder on the server

    // Process the Daily Out extract first
const D_OUT_FNAME   = "Daily_Out.csv";      // Daily Out destination file name

try{

    $extractFile = TARGET_DIR . D_OUT_FNAME;
    $upload_OK = move_uploaded_file($_FILES["DailyOutCSV"]["tmp_name"], $extractFile);

    if ($upload_OK){

        Upload_Daily_Out_CSV($extractFile);
        echo "File upload successful!";
    }
}
catch(Exception $e){
    echo "The was an error uploading the " . basename($_FILES["DailyOutCSV"]["name"]);
    header("Location: ./Extracts_Upload.html");
}

const P_STAT_FNAME  = "Parts_Status.csv";   // Parts Status destination file name

?>
