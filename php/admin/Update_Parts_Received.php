<?php

    class Part_XML{
        
        public $ro_quantity;
        public $ordered_quantity;
        public $received_quantity;
        public $returned_quantity;
        public $part_status;

        function __construct($rec){

            $this->ro_quantity       = $rec["ro_qty"];
            $this->ordered_quantity  = $rec["ordered_qty"];
            $this->received_quantity = $rec["received_qty"];
            $this->returned_quantity = $rec["returned_qty"];
            $this->part_status       = $rec["status"];

        }   // function __construct($partsRecord)
    }   // class Part{}


    class Repair_XML{

        private $db_conn;
        public $repair_id;
        public $ro_num;
        public $parts = [];
        public $parts_received_percent;

        function __construct($repair_id, $roNum, $dbConn){

            $this->db_conn                  = $dbConn;
            $this->repair_id                = $repair_id;
            $this->ro_num                   = $roNum;
            $this->parts                    = $this->Get_Parts_List_For_Repair($dbConn);
            $this->parts_received_percent   = $this->Compute_Parts_Received();

        }   // function __construct($repair_id, $dbConn)

        function Get_Parts_List_For_Repair(){

            $partsList = [];

            $sql = <<<strSQL

                    SELECT ro_qty, ordered_qty, received_qty,
                        returned_qty, status
                    FROM parts
                    WHERE repair_id = $this->repair_id
                            AND number NOT IN ('Sublet', 'Remanufactured')
                            AND UPPER(type) NOT IN ('SUBLET')
                            AND (line > 0)
                    ORDER BY line;
            strSQL;

            $s = mysqli_query($this->db_conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                array_push($partsList, new Part_XML($r));

            }   //while{}

            return $partsList;

        }   // function Get_Parts_List_For_Repair()


        function Compute_Parts_Received(){

            $unordered = 0;
            $ordered   = 0;
            $received  = 0;
            $returned  = 0;

            foreach($this->parts as $part){

                switch($part->part_status){

                    case "NOT_ORDERED":
                        // ++$unordered;
                        $unordered += $part->ro_quantity;
                        break;

                    case "ORDERED":
                        // ++$ordered;
                        $ordered += $part->ordered_quantity;
                        break;

                    case "RETURNED":
                        // ++$returned;
                        $returned += $part->returned_quantity;
                        break;

                    case "RECEIVED":
                    default:
                        // ++$received;
                        $received += $part->received_quantity;
                        break;
                }   // switch($part->part_status)
            }   // foreach($this->parts as $part)

            $totalParts = $unordered + $ordered + $received + $returned;

            $partsReceivedPercent = 0;

            if ($totalParts == 0){
                $partsReceivedPercent = 100;
            } else {
                $partsReceivedPercent = ($received / $totalParts) * 100;
            }

            return $partsReceivedPercent;
        }   // function Compute_Parts_Received()

    }   // class Repair{}


    class Shop_XML{

        private $db_conn;
        public $shop_id;
        public $repairs = [];

        function __construct($shop_id, $dbConn){

            $this->db_conn = $dbConn;
            $this->shop_id = $shop_id;
            $this->repairs = $this->Get_Repairs_For_Shop();

        }   // function __construct($shop_id, $db_conn)


        function Get_Repairs_For_Shop(){

            $repairsList = [];

            $sql = <<<strSQL

                    SELECT id, ro_num
                    FROM repairs
                    WHERE shop_id = $this->shop_id
                    ORDER BY id;
            strSQL;

            $s = mysqli_query($this->db_conn, $sql);

            while($r = mysqli_fetch_assoc($s)){

                array_push($repairsList, new Repair_XML($r['id'], $r['ro_num'], $this->db_conn));

            }   //while{}

            return $repairsList;
        }   // function Get_Repairs_For_Shop($shop_id, $db_conn)

    }   // class Shop{}

///////////////////////////////////////

function Update_Parts_Received($company_id, $db_conn){

//    $company_id = $_GET["companyID"];

    $sql = <<<strSQL
                SELECT id AS shop_id
                FROM shops
                WHERE company_id = $company_id
    strSQL;

    $shops = [];

    $s = mysqli_query($db_conn, $sql);

    while($r = mysqli_fetch_assoc($s)){

        $shop = new Shop_XML($r['shop_id'], $db_conn);
        array_push($shops, $shop);

    }   // while()

    $sql = "UPDATE repairs SET parts_received = CASE id ";

    $cases = "";
    $repair_ids = [];

    foreach($shops as $shop){
        foreach($shop->repairs as $repair){

            $cases .= "WHEN " . $repair->repair_id . " THEN " . $repair->parts_received_percent . "\n";
            $repair_ids[] = $repair->repair_id;

        }   // foreach($shop->repairs as $repair)
    }   // foreach($shops as $shop)

    $sql .= $cases . "ELSE parts_received END WHERE id IN (" . implode(",", $repair_ids) . ");";

    if ($db_conn->query($sql) === TRUE) {

        echo "<br/>" . count($repair_ids) . " Repair records parts received updated successfully.<br/>";

    }else{

        echo "<br/>Error: " . $sql . "<br> - " . $db_conn->error;
        exit;

    }  // if ($db_conn->query($sql) === TRUE)	
    
}
///////////////////////////////////////

?>