<?php

require('db_open.php');

if (!isset($_GET["repairID"]) && !isset($_GET["shopID"])){
    echo "Error: No repairID or shopID provided.";
    exit();
}   // if{}


$last_upload_date = '';
$sql = '';

switch (true){

    case isset($_GET["repairID"]):

        $repair_id = $_GET["repairID"];

        $sql = <<<strSQL
            SELECT last_data_upload
            FROM shops s INNER JOIN repairs r 
                    ON s.id = r.shop_id
            WHERE r.id = $repair_id;
        strSQL;
        break;

    case isset($_GET["shopID"]):

        $shop_id = $_GET["shopID"];

        $sql = <<<strSQL
            SELECT last_data_upload
            FROM shops s
            WHERE s.id = $shop_id;
        strSQL;
        break;

    default:
        break;
}   // switch()


try{

    if ($sql == ''){
        throw new Exception("No repairID or shopID provided.");
    }   // if{}
    else {
        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);
        $last_upload_date = $r["last_data_upload"];
    }   // else{}

} catch (Exception $e){

    echo "Fetching 'Last Update' value failed." . $e->getMessage();
}   // catch()

finally {
    $conn = null;
    echo $last_upload_date;
}   // class{}

?>