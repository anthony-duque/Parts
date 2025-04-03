<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

    echo json_encode(Get_Pending_Returns());

    class Part_Return{

        public $ro_num;
        public $owner;
        public $vehicle;
        public $vendor;
        public $return_num;
        public $pickup_date;
        public $return_form;

        function __construct($rec){

            $this->ro_num       = $rec["RO"];
            $this->owner        = toProperCase($rec["Owner"]);
            $this->vehicle      = toProperCase($rec["Vehicle"]);
            $this->vendor       = toProperCase($rec["Vendor"]);
            $this->return_num   = $rec["Return_Number"];
            $this->pickup_date  = GetDisplayDate($rec["Pickup_Date"]);
            $this->return_form  = '';

        }   // __construct()

    }   // part_return{}

    function Get_Pending_Returns(){

        $returns = [];
        $sql =  <<<strSQL
                    SELECT RO, SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                        Pickup_Date, Vehicle, Vendor, Return_Number
                    FROM Pending_Returns
                    ORDER BY Vendor, RO;
                strSQL;

        require('db_open.php');

        try {

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($returns, new Part_Return($r));
            }   //while{}

        } catch(Exception $e){
            echo "Fetching Pending Returns failed.";
        } finally{
            $conn = null;
            return $returns;
        }   // try-catch

    }   // Get_Pending_Returns()

?>
