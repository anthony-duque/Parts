<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

$unorderedParts = GetPartsList();

echo json_encode($unorderedParts);

class Estimator{

    public $name;
    public $cars = [];

    function __construct($rec){
        $this->name = $rec["Estimator"];
    }   // Estimator()
}   // Estimator{}


class Car{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $vehicle_in;
    public $parts = [];
    public $showParts;

    function __construct($rec){
        $this->ro_num       = $rec["RONum"];
        $this->owner        = $rec["Owner"];
        $this->vehicle      = $rec["Vehicle"];
        $this->vehicle_in   = $rec["Vehicle_In"];
        $this->showParts    = false;
    }   // Car()
}   // Car{}


class Part{

    public $part_number;
    public $part_description;
    public $line_num;
    public $ro_qty;
    public $ordered_qty;
    public $received_qty;
    public $order_date;

    function __construct($rec){

        $this->part_number      = $rec["Part_Number"];
        $this->part_description = $rec["Part_Description"];
        $this->line_num         = $rec["Line"];
        $this->ro_qty           = $rec["RO_Qty"];
        $this->ordered_qty      = $rec["Ordered_Qty"];
        $this->received_qty     = $rec["Received_Qty"];
        $this->order_date       = $rec["Order_Date"];

    }   // Part()
}   // Part{}


    // Get list of Estimators
function GetEstimators(&$estimatorList, $dbConn){

    $sql = "SELECT DISTINCT Estimator FROM Repairs WHERE LENGTH(Estimator) > 0";

    try {

        $s = mysqli_query($dbConn, $sql);
        while($r = mysqli_fetch_assoc($s)){

            $estimator = new Estimator($r);
            array_push($estimatorList, $estimator);

        }   //while{}

    } catch(Exception $e){
        echo "Fetching List of Estimators failed.";
    }   // try-catch

}   // GetEstimatorList()

    // Get all the RO's per Estimator
function Get_ROs_Per_Estimator($estimatorName, $dbConn){

    $ROList = [];

    $sql = <<<strSQL
        SELECT DISTINCT pse.RO_Num
        FROM PartsStatusExtract pse INNER JOIN Repairs r
    	   ON pse.RO_Num = r.RONum
        WHERE (RO_Qty > 0) AND (Ordered_Qty = 0)
            AND (Received_Qty = 0) AND (Part_Number > '')
            AND (Part_Type <> 'Sublet')
            AND (Part_Number NOT LIKE 'Aftermarket%')
            AND r.Estimator = '
    strSQL . $estimatorName . "'";

    try {

        $s = mysqli_query($dbConn, $sql);
        while($r = mysqli_fetch_assoc($s)){
            array_push($ROList, $r["RO_Num"]);
        }   //while{}

    } catch(Exception $e){
        echo "Fetching List of Cars failed.";
    }   // try-catch

    return $ROList;
}   // GetROsForEstimator


function GetCarInfoForRO($roNum, $dbConn){

    $sql = <<<strSQL
        SELECT RONum, Owner, Vehicle, Vehicle_In
        FROM Repairs
        WHERE RONum =
    strSQL . $roNum;

    $r = null;

    try {

        $s = mysqli_query($dbConn, $sql);
        $r = mysqli_fetch_assoc($s);

    } catch(Exception $e) {

        echo "Fetching Car Info for RO num ' . $roNum .' failed.";

    } finally {

        return $r;

    }   // try-catch{}

}   // GetCarInfoForRO()


function GetAllPartsForRO($roNum, $dbConn){

    $sql = <<<strSQL
        SELECT Part_Number, Part_Description, Line,
            RO_Qty, Ordered_Qty, Received_Qty, Order_Date
        FROM PartsStatusExtract
        WHERE (RO_Qty > 0) AND (Ordered_Qty = 0)
            AND (Received_Qty = 0) AND (Part_Number > '')
            AND (Part_Type <> 'Sublet')
            AND (Part_Number NOT LIKE 'Aftermarket%')
            AND RO_Num =
    strSQL . $roNum;

    $r = null;

    try {

        $s = mysqli_query($dbConn, $sql);

    } catch(Exception $e) {

        echo "Fetching Parts List for RO num ' . $roNum .' failed.";

    } finally {

        return $s;

    }   // try-catch{}

}   // GetAllPartsForRO()


function GetPartsList(){

    require('db_open.php');

    $estimators = [];

        // Get list of estimators
    GetEstimators($estimators, $conn);

    foreach($estimators as &$eachEstimator){

        $ros_per_est = [];  // will contain all ro's per estimator
        $ros_per_est = Get_ROs_Per_Estimator($eachEstimator->name, $conn);

            // Get Car List per Estimator
        foreach($ros_per_est as $eachRO){
            $rec = GetCarInfoForRO($eachRO, $conn);
            $car = new Car($rec);
            array_push($eachEstimator->cars, $car);
        }

            // Get Parts list for each car
        foreach($eachEstimator->cars as &$eachCar){

            $rows = GetAllPartsForRO($eachCar->ro_num, $conn);

            while($row = mysqli_fetch_assoc($rows)){

                $part = new Part($row);
                array_push($eachCar->parts, $part);

            }   // while()
        }
    }   // foreach()

    $conn = null;
//    var_dump($estimators);
    return $estimators;

}   // GetPartsList()

?>
