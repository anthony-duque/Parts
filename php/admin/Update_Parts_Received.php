<?php

    class Parts_Stats {
        
        public $unordered   = 0;
        public $ordered     = 0;
        public $returned    = 0;
        public $received    = 0;
        public $percent     = 0;

        function __construct($parts){

            $unordered = 0;
            $ordered   = 0;
            $received  = 0;
            $returned  = 0;

            foreach($parts as $part){

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

            $percent_recvd = 0;

            if ($totalParts == 0){
                $percent_recvd = 100;
            } else {
                $percent_recvd = ($received / $totalParts) * 100;
            }

            $this->unordered    = $unordered;
            $this->ordered      = $ordered;
            $this->received     = $received;
            $this->returned     = $returned;
            $this->percent      = $percent_recvd;

        }   // function __construct()
    }


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

        public $repair_id;
        public $ro_num;
        public $parts = [];
        public $parts_unordered;
        public $parts_ordered;
        public $parts_received;
        public $parts_returned;
        public $parts_percent_received;

        private $db_conn;

        function __construct($repair_id, $roNum, $dbConn){

            $this->db_conn                  = $dbConn;
            $this->repair_id                = $repair_id;
            $this->ro_num                   = $roNum;
            $this->parts                    = $this->Get_Parts_List_For_Repair($dbConn);

            $parts_stats                    = new Parts_Stats($this->parts);

            $this->parts_unordered          = $parts_stats->unordered;
            $this->parts_ordered            = $parts_stats->ordered;
            $this->parts_received           = $parts_stats->received;
            $this->parts_returned           = $parts_stats->returned;
            $this->parts_percent_received   = $parts_stats->percent;

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

    $rows_str = "";

    foreach($shops as $shop){

        foreach($shop->repairs as $repair){

            $rows_str .= " ROW($repair->repair_id, $repair->parts_unordered, $repair->parts_ordered," .
                            " $repair->parts_received, $repair->parts_returned, $repair->parts_percent_received),";

        }   // foreach($shop->repairs as $repair)
    }   // foreach($shops as $shop)

    $rows_str = rtrim($rows_str, ',');

    $sql = <<<strSQL
            UPDATE repairs AS r
            JOIN (
             VALUES 
                $rows_str
            ) AS n(id, parts_unordered, parts_ordered, parts_received, parts_returned, parts_percent_received) 
                ON r.id = n.id
            SET
                r.parts_unordered = n.parts_unordered, 
                r.parts_ordered   = n.parts_ordered, 
                r.parts_received  = n.parts_received,
                r.parts_returned  = n.parts_returned, 
                r.parts_percent_received = n.parts_percent_received;
    strSQL;

    if ($db_conn->query($sql) === TRUE) {

        echo "<br/>" . $db_conn->affected_rows . " repair records parts stats updated successfully.<br/>";

    }else{

        echo "<br/>Error: " . $sql . "<br> - " . $db_conn->error;
        exit;

    }  // if ($db_conn->query($sql) === TRUE)	
  
}
///////////////////////////////////////
?>