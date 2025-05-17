<?php

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
    $conn = null;
    echo $last_upload_date;
}   // class{}

?>
