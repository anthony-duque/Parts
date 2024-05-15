<?php

require('Utility_Scripts.php');

    $unorderedParts = GetPartsList();

    echo json_encode($unorderedParts);

    class Part{

        public $estimator;
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

        function __construct($partRec){

            $this->estimator        = $partRec["Estimator"];
            $this->ro_num           = $partRec["RONum"];
            $this->owner            = $partRec["Owner"];
            $this->vehicle          = $partRec["Vehicle"];
            $this->part_number      = $partRec["Part_Number"];
            $this->part_description = $partRec["Part_Description"];
            $this->line_num         = $partRec["Line"];
            $this->ro_qty           = $partRec["RO_Qty"];
            $this->ordered_qty      = $partRec["Ordered_Qty"];
            $this->received_qty     = $partRec["Received_Qty"];
            $this->order_date       = $partRec["Order_Date"];

        }   // constructor()
    }   // Part{}

    function GetPartsList(){

        require('db_open.php');

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

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                $part = new Part($r);
                array_push($parts, $part);
            }   // while()

            return $parts;

        } catch(Exception $e){

            echo "Fetching Unordered parts failed." . $e->getMessage();

        }   // try-catch{}

    }   // GetPartsList()


?>
