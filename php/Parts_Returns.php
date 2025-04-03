<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

    echo json_encode(Get_Pending_Returns());

    class Part_Return{

        public $ro_num;
        public $part_number;
        public $part_description;
        public $part_type;
        public $vendor;
        public $invoice_num;
        public $amount;
        public $return_date;
        public $return_reason;
        public $pickup_date;

        function __construct($rec){

            $this->ro_num             = $rec["RO_Num"];
            $this->part_number        = $rec["Part_Number"];
            $this->part_description   = $rec["Part_Description"];
            $this->part_type          = $rec["Part_Type"];
            $this->vendor             = $rec["Vendor_Name"];
            $this->invoice_num        = $rec["Invoice_Number"];
            $this->amount             = $rec["Amount"];
            $this->return_date        = GetDisplayDate($rec["Return_Date"]);
            $this->return_reason      = $rec["Reason"];
            $this->pickup_date        = GetDisplayDate($rec["Vendor_Pickup_Date"]);

        }   // __construct()

    }   // part_return{}

    function Get_Pending_Returns(){

        $returns = [];
        $sql =  <<<strSQL
                    SELECT RO_Num, Return_Date, Vendor_Pickup_Date,
                        Part_Number, Part_Description, Part_Type,
                        Amount, Invoice_Number, Reason, Vendor_Name
                    FROM Parts_Returns
                    ORDER BY RO_Num, Vendor_Name, Part_Number ASC;
                strSQL;

        require('db_open.php');

        try {

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($returns, new Part_Return($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Pending Returns failed.";
        }   // try-catch

        return $returns;
    }   // Get_Pending_Returns()
?>
