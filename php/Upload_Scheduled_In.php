<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

const LOCATION          = 0;
const SCHEDULED_IN      = 1;
const RO_NUMBER         = 2;
const VIN	            = 3;
const RO_HOURS          = 4;
const ASSIGNED_HOURS    = 5;
const ESTIMATE_AMT      = 6;
const TOTAL_LOSS        = 7;

const TARGET_DIR    = "../extract_files/";      // destination folder on the server
const D_OUT_FNAME   = "Scheduled_In_VIN.csv";   // Scheduled In VIN file name
const HEADER_ROWS   = 8;    // skip 8 rows before the records
try{

    $extractFile = TARGET_DIR . D_OUT_FNAME;
    $upload_OK = move_uploaded_file($_FILES["ScheduledInVIN"]["tmp_name"], $extractFile);

//    echo $upload_OK;
    if ($upload_OK){

        Upload_Sched_In_CSV($extractFile);
        echo "<br/> Scheduled In VIN's uploaded successfully!";

    }

} catch(Exception $e){

    echo "There was an error uploading extract file ". basename($_FILES["ScheduledInVIN"]["name"]);
    //header("Location: ./Admin.html");
}


function Update_Location_IDs($dbConn){

    $tsql = <<<strSQL
        UPDATE Scheduled_In_VIN siv INNER JOIN Location_IDs locID
    	SET siv.Loc_ID = locID.id
    	WHERE UPPER(siv.Location) = UPPER(locID.Location);
    strSQL;

    try{

        if ($dbConn->query($tsql) === TRUE) {
            echo "<br/><br/>Scheduled In Table Location IDs updated.<br/>";
        } else {
            echo "Scheduled In Table Location IDs NOT updated:<br/>(" . $tsql . ")<br>" . $conn->error;
        }

    } catch(Exception $e){
        echo "Error: " . $tsql . "<br>" . $e->getMessage();
    }   // try-catch{}

    return;

}   // Update_Location_IDs()


function Upload_Sched_In_CSV($sched_In_File){

    $handle = null;     // file handle

        // Open the extract file and exit if not found.
    if (($handle = fopen($sched_In_File, "r")) === FALSE) {
        echo "Error in opening " . $sched_In_File;
        exit;
    }   // if (($handle...))

    require('db_open.php');

        //  Delete all records from the Scheduled_In_VIN table.
    $tsql = "DELETE FROM Scheduled_In_VIN";

    if ($conn->query($tsql) === TRUE) {
        echo "<br/><br/>Scheduled In Table cleared.<br/>";
    } else {
        echo "Error: " . $tsql . "<br>" . $conn->error;
        exit;
    }   // if ($conn...)

    $carList = [];
    $insert_sql = '';
    $row = 0;

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

        ++$row;

        if($row > HEADER_ROWS){   // skip the first 8 rows

            $location   = "'" . $data[LOCATION] . "'";
            $sched_in   = Get_SQL_date($data[SCHEDULED_IN]);
            $ro_number  = $data[RO_NUMBER];
            $vin        = "'" . $data[VIN] . "'";
            $ro_hours   = $data[RO_HOURS];
            $ass_hours  = $data[ASSIGNED_HOURS];
            $est_amount = $data[ESTIMATE_AMT];
            $total_loss = strtoupper($data[TOTAL_LOSS]);

            $values = "(" . $location . ", " . $sched_in . ", " .
                $ro_number . ", " . $vin . ", " . $ro_hours . ", " .
                $ass_hours . ", " . $est_amount . ", " . $total_loss . "),";

            $insert_sql .= $values;

        }   // if($row...)

    }   // while()

    fclose($handle);

    $insert_sql = rtrim($insert_sql, ",");  // remove the extra comma at the end

    $tsql = <<<strSQL
        INSERT INTO Scheduled_In_VIN
                (Location, Scheduled_In, RO_Num, VIN, RO_Hours,
                Assigned_Hours, Estimate_Amt, Total_Loss)
                VALUES $insert_sql
    strSQL;

//    echo $tsql;

    if ($conn->query($tsql) === TRUE) {
        echo ($row - HEADER_ROWS) . " lines inserted.";
        Update_Location_IDs($conn);
      //echo $part_number . " inserted<b$r/>";
    } else {
      echo "Error: " . $tsql . "<br>" . $conn->error;
    }

    $conn = null;

}   // Upload_Sched_In_CSV()

?>
