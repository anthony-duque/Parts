<?php

require('db_open.php');

$loc_IDs = $_COOKIE["locationID"];       // set the location cookie for use in the utility scripts
$last_upload_date = '';

$sql = <<<strSQL
    SELECT last_data_upload
    FROM Location_IDs li
    WHERE id IN ($loc_IDs)
    LIMIT 1;
strSQL;

try{

    $s = mysqli_query($conn, $sql);
    $r = mysqli_fetch_assoc($s);
    $last_upload_date = $r["last_data_upload"];

} catch (Exception $e){

    echo "Fetching 'Last Update' value failed." . $e->getMessage();
}   // catch()

finally {
    $conn = null;
    echo $last_upload_date;
}   // class{}

?>
