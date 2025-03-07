<?php

require('Utility_Scripts.php');

    class Part{

        public $ro_num;
        public $part_number;
        public $part_description;
        public $vendor_name;
        public $ordered_quantity;
        public $received_quantity;
        public $returned_quantity;
        public $ro_quantity;
        public $expected_delivery;
        public $order_date;
        public $invoice_date;
        public $part_status;
        public $vehicle_in;
        public $current_phase;
        public $owner;

        function __construct($rec){

            $this->ro_num               = $rec["RO_Num"];
            $this->part_number          = $rec["Part_Number"];
            $this->part_description     = $rec["Part_Description"];

            $this->vendor_name          = strtolower($rec["Vendor_Name"]);
            $this->vendor_name          =  ucwords($this->vendor_name);

            $this->ro_quantity          = $rec["RO_Qty"];
            $this->ordered_quantity     = $rec["Ordered_Qty"];
            $this->received_quantity    = $rec["Received_Qty"];
            $this->returned_quantity    = $rec["Returned_Qty"];
            $this->expected_delivery    = GetDisplayDate($rec["Expected_Delivery"]);
            $this->order_date           = GetDisplayDate($rec["Order_Date"]);
            $this->invoice_date         = GetDisplayDate($rec["Invoice_Date"]);
            $this->part_status          = ComputePartStatus(
                                            $this->ro_quantity,
                                            $this->ordered_quantity,
                                            $this->received_quantity,
                                            $this->returned_quantity
                                        );

            $this->vehicle_in           = GetDisplayDate($rec["Vehicle_In"]);
            $this->current_phase        = $rec["CurrentPhase"];
            $this->owner                = $rec["Owner"];

        }   // Part()
    }   // Part{}

    $allParts = GetPartsList();
    echo json_encode($allParts);

    function GetPartsList(){

        require('db_open.php');

        $sql = <<<strSQL
                SELECT RO_Num, Part_Number, Part_Description, Vendor_Name,
                      RO_Qty, Ordered_Qty, Received_Qty, Returned_Qty,
                      Expected_Delivery, Order_Date, Invoice_Date,
                      Vehicle_In, CurrentPhase, SUBSTRING_INDEX(r.Owner, ',', 1) AS Owner

                FROM PartsStatusExtract pse INNER JOIN Repairs r
                    ON pse.RO_Num = r.roNum
                WHERE (Line > 0) AND (Part_Number > '' OR Vendor_Name > '')
                    AND Vendor_Name NOT IN ('**in-house', 'Airtight Auto Glass',
                    'Big Brand', 'Jim''s Tire Center', 'Pro Tech Diagnostics', 'Astech')
                    AND Part_Number NOT IN ('Sublet')
                strSQL;

        $parts = [];

        try{

            $s = mysqli_query($conn, $sql);

            while($r = mysqli_fetch_assoc($s)){
                array_push($parts, new Part($r));
            }

        } catch(Exception $e){

            echo "Getting parts list failed." . $e->getMessage();

        }

        finally {

            $conn = null;
            return $parts;
        }   // try-catch{}

    }   // GetPartsList()

?>
