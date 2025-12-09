<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');
require('Upload_Daily_Out.php');
require('Upload_Parts_Status.php');
require('Create_Labels_CSV.php');


const TARGET_DIR    = "../extract_files/";  // destination folder on the server
const D_OUT_FNAME   = "Daily_Out.csv";      // Daily Out destination file name
const P_STAT_FNAME  = "Parts_Status.csv";   // Parts Status destination file name

    // Process the Daily Out extract first
try{

    $extractFile = TARGET_DIR . D_OUT_FNAME;
    $upload_OK = move_uploaded_file($_FILES["DailyOutCSV"]["tmp_name"], $extractFile);

    if ($upload_OK){
        Upload_Daily_Out_CSV($extractFile);
        echo "<br/> Daily Out upload successful!";
    }
} catch(Exception $e){
    echo "There was an error uploading the " . basename($_FILES["DailyOutCSV"]["name"]);
 //   header("Location: ../Upload_Extracts.html");
}

    // Process Parts Status extract file
try{

    $extractFile = TARGET_DIR . P_STAT_FNAME;
    $upload_OK = move_uploaded_file($_FILES["PartsStatusCSV"]["tmp_name"], $extractFile);

    if ($upload_OK){
        Upload_Parts_Status_CSV($extractFile);
        echo "<br/> Parts Status upload successful!";
    }
} catch(Exception $e){
    echo "The was an error uploading the " . basename($_FILES["PartsStatusCSV"]["name"]);
//    header("Location: ./Upload_Extracts.html");
}

require('db_open.php');

$tsql = "UPDATE Adhoc_Table " .
         "SET value = '" . $_POST['uploadDateTime'] . "' " .
         "WHERE name = 'LAST_UPLOAD'";

if ($conn->query($tsql) === TRUE) {

    $tsql = "CALL spUpdateLocationIDs()";

    if ($conn->query($tsql) === TRUE) {
      Create_Labels_File(); //echo $ro_num . " uploaded<br/>";
    } else {
      echo "Error: " . $tsql . "<br>" . $conn->error;
    }

} else {
  echo "Error: " . $tsql . "<br>" . $conn->error;
}




$conn = null;

?>
<br/><br/>
<input type='button' value="Back to Admin Menu" onclick='window.location.href="../Admin.html";'>
