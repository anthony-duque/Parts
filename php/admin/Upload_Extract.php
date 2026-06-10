<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const TARGET_DIR        = "../../extract_files/";   // destination folder on the server
const EXTRACT_FILENAME  = "Extract_CompanyID.csv";  // Daily Out destination file name



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