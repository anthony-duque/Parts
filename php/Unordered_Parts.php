<?php

require('Utility_Scripts.php');

    $unorderedParts = GetPartsList();

    echo json_encode($unorderedParts);

/*
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
*/
class Estimator{

    public $name;
    public $parts = [];

    function __construct($rec){
        // Estimator
        $this->name        = $rec["Estimator"];
    }   // Estimator()
}   // Estimator{}


class Part{

    public $ro_num;
    public $owner;
    public $vehicle;
    public $part_number;
    public $part_description;
    public $line_num;
    public $ro_qty;
    public $ordered_qty;
    public $received_qty;
    public $order_date;

    function __construct($rec){

            // Vehicle
        $this->ro_num           = $rec["RONum"];
        $this->owner            = $rec["Owner"];
        $this->vehicle          = $rec["Vehicle"];

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

    function GetPartsList(){

        require('db_open.php');

        $sql =
            "SELECT r.id, SUBSTRING_INDEX(r.Estimator, ' ', 1) AS Estimator, r.RONum, SUBSTRING_INDEX(r.Owner, ',', 1) AS Owner, " .
    		"SUBSTRING_INDEX(SUBSTRING(r.Vehicle, INSTR(r.Vehicle,' ') + 1), ' ', 2) AS Vehicle, pse.Part_Description, pse.Part_Number, pse.Line, " .
                " pse.RO_Qty, pse.Ordered_Qty, pse.Received_Qty, pse.Order_Date " .
            "FROM Repairs r INNER JOIN PartsStatusExtract pse " .
            "	ON pse.RO_Num = r.RONum " .
            " WHERE (pse.RO_Qty > 0) AND (pse.Ordered_Qty = 0) AND (pse.Received_Qty = 0) AND (pse.Part_Number > '') AND (pse.Part_Type <> 'Sublet') AND (pse.Part_Number NOT LIKE 'Aftermarket%') " .
            " ORDER BY Estimator, r.Owner";
        //echo $sql;
        try{

            $estimators = [];
            $parts = [];
            $prev_estimator = '';

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $curr_estimator = $r["Estimator"];

                if ($curr_estimator !== $prev_estimator){

                    if ($prev_estimator > ''){

                        $estimator->parts = $parts;
                        array_push($estimators, $estimator);
                        $parts = [];    // empty the array
                    }

                    $prev_estimator = $curr_estimator;
                    $estimator = new Estimator($r);
                }

                $part = new Part($r);
                array_push($parts, $part);

            }   // while()

            return $estimators;

        } catch(Exception $e){

            echo "Fetching Unordered parts failed." . $e->getMessage();

        }   // try-catch{}

    }   // GetPartsList()


?>
