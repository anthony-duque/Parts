<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Process_Extract_File.php');
require('Check_For_New_Shops.php');

const TARGET_DIR        = "../../extract_files/";   // destination folder on the server
const EXTRACT_FILENAME  = "Extract_CompanyID.csv";  // Daily Out destination file name

$companyID = $_GET["companyID"];    // get the company ID from the query string

try{

    $extractFile = TARGET_DIR . str_replace("CompanyID", $companyID, EXTRACT_FILENAME);
    $upload_OK = move_uploaded_file($_FILES["ExtractCSV"]["tmp_name"], $extractFile);

    if ($upload_OK){

<<<<<<< HEAD
        $shop_names = Process_Extract($extractFile, $companyID);
        echo "<br/> Extract upload successful!";

        Check_For_New_Shops($shop_names, $companyID);
    }
=======
        if (Process_Extract($extractFile, $companyID) == true) {
            echo "<br/> Extract upload successful!";
        } else {
            echo "<br/>Error occurred while processing the extract file.";
        }
        
    } else {
        echo "<br/>Error uploading the file. Please try again.";
    }   // 
>>>>>>> 7f9728f3c45004f9e42f758bd172aa90d048e283

} catch(Exception $e){

    echo "There was an error uploading the " . basename($_FILES["ExtractCSV"]["name"]);
    echo "<br/>Error details: " . $e->getMessage();
    header("Location: ../../html/admin/Upload_Extract.html");

}
?>