<?php

require('Utility_Scripts.php');

    class Part{

        public $repair_id;
        public $ro_num;
        public $part_number;
        public $line_no;
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
        public $locationID;

        function __construct($rec){

            $this->repair_id            = $rec["repair_id"];
            $this->ro_num               = $rec["ro_num"];
            $this->part_number          = $rec["number"];
            $this->part_description     = $rec["description"];
            $this->line_no              = $rec["line"];

            $this->vendor_name          = strtolower($rec["vendor_name"]);
            $this->vendor_name          =  ucwords($this->vendor_name);

            $this->ro_quantity          = $rec["ro_qty"];
            $this->ordered_quantity     = $rec["ordered_qty"];
            $this->received_quantity    = $rec["received_qty"];
            $this->returned_quantity    = $rec["returned_qty"];
            $this->expected_delivery    = GetDisplayDate($rec["expected_delivery"]);
            $this->order_date           = GetDisplayDate($rec["order_date"]);
            $this->invoice_date         = GetDisplayDate($rec["invoice_date"]);
            $this->part_status          = $rec["status"];

            $this->vehicle_in           = GetDisplayDate($rec["vehicle_in"]);
            $this->current_phase        = $rec["current_phase"];
            $this->owner                = $rec["owner"];
            $this->locationID           = $rec["shop_id"];

        }   // Part()
    }   // Part{}

    $allParts = GetPartsList();
    echo json_encode($allParts);

    function GetPartsList(){

        require('db_open.php');

        $sql = <<<strSQL

                SELECT p.repair_id, r.ro_num, p.number, p.description, p.vendor_name, p.line,
                      p.ro_qty, p.ordered_qty, p.received_qty, p.returned_qty, p.status,
                      p.expected_delivery, p.order_date, p.invoice_date, r.shop_id,
                      r.vehicle_in, r.current_phase, SUBSTRING_INDEX(r.owner, ',', 1) AS owner

                FROM parts p INNER JOIN repairs r
                    ON p.repair_id = r.id

                WHERE (p.line > 0) AND (p.number > '' OR p.vendor_name > '')
                    AND p.vendor_name NOT IN ('**in-house', 'Airtight Auto Glass', '*in House Stock',
                    'Big Brand', 'Jim''s Tire Center', 'Pro Tech Diagnostics', 'Astech')
                    AND p.number NOT IN ('Sublet')

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
