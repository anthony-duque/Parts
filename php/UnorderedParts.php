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

/*

    class Car{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $parts = [];

        function __construct($rec){
            $this->ro_num           = $rec["RONum"];
            $this->owner            = $rec["Owner"];
            $this->vehicle          = $rec["Vehicle"];
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

class Estimator{

    public $name;
    public $vehicles = [];

    function __construct($rec){
        // Estimator
        if (trim($rec["Estimator"]) > ''){
            $this->name = $rec["Estimator"];
        } else {
            $this->name = "Unassigned";
        }
    }   // Estimator()
}   // Estimator{}


class Vehicle{

    public $description;
    public $ro_num;
    public $owner;
    public $parts = [];

    function __construct($rec){
        // Vehicle
        $this->description  = $rec["Vehicle"];
        $this->ro_num       = $rec["RONum"];
        $this->owner        = $rec["Owner"];
    }   // Vehicle()
}   // Vehicle{}


class Part{

    public $part_number;

    public $part_description;
    public $line_num;
    public $ro_qty;
    public $ordered_qty;
    public $received_qty;
    public $order_date;

    function __construct($rec){

            // Part
        $this->part_number      = $rec["Part_Number"];
        $this->part_description = $rec["Part_Description"];
        $this->line_num         = $rec["Line"];
        $this->ro_qty           = $rec["RO_Qty"];
        $this->ordered_qty      = $rec["Ordered_Qty"];
        $this->received_qty     = $rec["Received_Qty"];
        $this->order_date       = $rec["Order_Date"];

    }   // Part()
}   // Part{}
*/
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


function GetPartsList(){

    require('db_open.php');

    $estimators = [];

        // Get list of estimators
    GetEstimators($estimators, $conn);

    $conn = null;
    return $estimators;

/*
    $sql =
        "SELECT SUBSTRING_INDEX(r.Estimator, ' ', 1) AS Estimator, r.RONum, SUBSTRING_INDEX(r.Owner, ',', 1) AS Owner, " .
		"SUBSTRING_INDEX(SUBSTRING(r.Vehicle, INSTR(r.Vehicle,' ') + 1), ' ', 2) AS Vehicle, pse.Part_Description, pse.Part_Number, pse.Line, " .
            " pse.RO_Qty, pse.Ordered_Qty, pse.Received_Qty, pse.Order_Date " .
        "FROM Repairs r INNER JOIN PartsStatusExtract pse " .
        "	ON pse.RO_Num = r.RONum " .
        " WHERE (pse.RO_Qty > 0) AND (pse.Ordered_Qty = 0) AND (pse.Received_Qty = 0) AND (pse.Part_Number > '') AND (pse.Part_Type <> 'Sublet') AND (pse.Part_Number NOT LIKE 'Aftermarket%') " .
        " ORDER BY Estimator, r.Owner";
    //echo $sql;

    try{

        $parts = [];
        $prev_estimator = '';
        $estimator = null;
        $prev_vehicle = '';

        $s = mysqli_query($conn, $sql);

        while($r = mysqli_fetch_assoc($s)){

            $curr_estimator = $r["Estimator"];

            if ($curr_estimator !== $prev_estimator){

                if ($prev_estimator > ''){

                    $estimator->parts = $parts;
                    array_push($estimators, $estimator);
                    $parts = [];    // empty the parts bucket every car
                }

                $prev_estimator = $curr_estimator;
                $estimator = new Estimator($r);
            }

            $part = new Part($r);
            array_push($parts, $part);

        }   // while()

        $estimator->parts = $parts;
        array_push($estimators, $estimator);
*/

//    } catch(Exception $e){

//        echo "Fetching Unordered parts failed." . $e->getMessage();

//    }   // try-catch{}


}   // GetPartsList()

?>
