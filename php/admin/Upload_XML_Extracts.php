<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../Utility_Scripts.php');

require('Upload_XML_Prod_Sched.php');
require('Upload_XML_Parts_Status.php');

require('Create_Labels_CSV.php');

$companyID = $_GET["companyID"];       // set the company cookie

const TARGET_DIR    = "../../extract_files/";  // destination folder on the server

$repairsFileName    = "Production_Schedule_Report_" . $companyID . ".xml";      // Daily Out destination file name
$partsFileName      = "Parts_Status_Report_" . $companyID . ".xml";   // Parts Status destination file name

    // Process the Daily Out extract first
try{

    $extractFile = TARGET_DIR . $repairsFileName;
    $upload_OK = move_uploaded_file($_FILES["Repairs_XML_$companyID"]["tmp_name"], $extractFile);

    if ($upload_OK){

        $repairList = Get_Repairs_From_XML($companyID, $extractFile);

        $shopList = Check_Shop_List_Against_DB($repairList->shops);

        Delete_Old_Repair_Records($repairList);

        Write_Repairs_To_Database($repairList, $shopList);

        echo "<br/> Repair Order records upload successful!";

    }   // if ($upload_OK)

} catch(Exception $e){

    echo "There was an error uploading the " . basename($_FILES["Repairs_XML_$companyID"]["name"]);
    echo "<br/>Error details: " . $e->getMessage();

}   // catch(Exception $e)


    // Process Parts Status extract file
try{

    echo "Entered here";
    $extractFile = TARGET_DIR . $partsFileName;
    $upload_OK = move_uploaded_file($_FILES["Parts_XML_$companyID"]["tmp_name"], $extractFile);

//    Debug_Load_Error($extractFile);
//    exit;

    if ($upload_OK){

        $repairsList = Get_Repairs_List_From_DB($companyID);

        $partsList = Get_Parts_List_From_XML($companyID, $repairsList, $extractFile);

    //    Display_Parts_List($partsList);

        Write_New_Parts_Records_To_Database($partsList, $repairsList);

        echo "<br/> Parts Status upload successful!";
    } else {

        echo "Upload failed!<br>";

        // Replace 'file' with the actual 'name' attribute of your HTML <input type="file">
        if (isset($_FILES["Parts_XML_$companyID"])) {
            $file = $_FILES["Parts_XML_$companyID"];

            // Step 1: Check the global upload error code
            if ($file['error'] !== UPLOAD_ERR_OK) {
                switch ($file['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        echo "Error: The file is too large."; // Configured in php.ini or HTML form
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        echo "Error: The file was only partially uploaded.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        echo "Error: No file was uploaded.";
                        break;
                    default:
                        echo "Error code: " . $file['error']; // Covers other codes (4, 6, 7, 8)
                        break;
                }
            } else {
                // Step 2: Attempt to move the file if step 1 passes
                $targetDir = __DIR__ . "/uploads/";
                $targetFile = $targetDir . basename($file['name']);

                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    echo "Success: File uploaded and moved successfully.";
                } else {
                    // If it returns false here, check your PHP warnings (Permissions/Paths)
                    echo "Error: Failed to move the file. Check server permissions or directory paths.";
                }
            }
        }

        echo $_FILES["Parts_XML_$companyID"]['error'];
    }
} catch(Exception $e){
    echo "There was an error uploading the " . basename($_FILES["Parts_XML_$companyID"]["name"]);
//    header("Location: ./Upload_Extracts.html");
}


function Debug_Load_Error($filePath){
// Enable internal error handling
libxml_use_internal_errors(true);

// Attempt to load your second XML file
$xml2 = simplexml_load_file("../../extract_files/Part_Status_Report.xml");

if ($xml2 === false) {
    echo "<strong>Failed loading the second XML file:</strong><br>";
    foreach (libxml_get_errors() as $error) {
        echo "Error on line {$error->line}, column {$error->column}: {$error->message}<br>";
    }
    libxml_clear_errors();
} else {
    echo "Second file loaded successfully!";
}}
/*
require('../db_open.php');

$tsql = "CALL sp_Update_XML_Upload(?)";
$stmt = $conn->prepare($tsql);
$stmt->bind_param("i", $companyID);

if ($stmt->execute() === TRUE) {
    Create_Labels_File();
} else {
    echo "Error: " . $tsql . "<br>" . $conn->error;
}

$conn = null;
*/
?>
<br/><br/>
<input type='button' value="Back to Admin Menu" onclick='window.location.href="../../html/admin/Admin.html";'>