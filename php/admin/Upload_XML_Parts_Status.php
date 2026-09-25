<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../Utility_Scripts.php');
  
    const TARGET_DIR            = "../../extract_files/";  // destination folder on the server
    const PARTS_STAT_FILE_NAME  = "Part Status_Report.xml";      // Part Status destination file name

    $company_ID = $_GET["companyID"];       // set the company cookie

/*  Start of Main Routine  */

    $repairsList = Get_Repairs_List_From_DB($company_ID);

    $partsList = Get_Parts_List_From_XML($company_ID, $repairsList);

//    Display_Parts_List($partsList);

    Write_New_Parts_Records_To_Database($partsList, $repairsList);

/*  End of Main Routine  */

    class Repair {

        public $repair_id;
        public $ro_num;
        public $location_id;
    
        function __construct($repairRecord){

            $this->repair_id = $repairRecord['repair_id'];
            $this->ro_num = $repairRecord['ro_num'];
            $this->location_id = $repairRecord['location_id'];

        }   // function __construct(...)
    }   // class Repair{}


    class Part{

        public $ro_num;
        public $line;
        public $part_number;
        public $part_description;
        public $part_type;
        public $vendor_name;
        public $ro_quantity;
        public $po_number;
        public $ordered_quantity;
        public $order_date;
        public $expected_delivery;
        public $received_quantity;
        public $invoice_date;
        public $returned_quantity;
        public $location_id;
        public $repair_id;

        function __construct($partsRecord, $repair_ID_Lookup){

            $this->ro_num           = Cleanup_Text($partsRecord->repair_order_number);

            if (empty($partsRecord->repair_facility_number)) {
                $this->location_id  = Cleanup_Text($partsRecord->franchise_id);
            } else {
                $this->location_id  = Cleanup_Text($partsRecord->repair_facility_number);
            }

            $repair = array_filter($repair_ID_Lookup, function($repair){
                return (($repair->ro_num === $this->ro_num) && ($repair->location_id === $this->location_id));
            });

            $this->repair_id        = $repair ? reset($repair)->repair_id : null;

            if (empty($partsRecord->estimate_line_number)) {
                $line = 'NULL';
            } else{
                $line = $partsRecord->estimate_line_number;
            }

            $this->line              = $line;

            $this->part_number       = Cleanup_Text($partsRecord->part_number);
            $this->part_description  = Cleanup_Text($partsRecord->part_description);
            $this->part_type         = Cleanup_Text($partsRecord->part_type_name);
            $this->vendor_name       = Cleanup_Text($partsRecord->vendor_name);
            $this->ro_quantity       = Cleanup_Text($partsRecord->part_quantity);
            $this->po_number         = Cleanup_Text($partsRecord->po_number);
            $this->ordered_quantity  = Cleanup_Text($partsRecord->order_quantity);
            $this->order_date        = Get_SQL_date($partsRecord->order_datetime);
            $this->expected_delivery = Get_SQL_date($partsRecord->expected_delivery_date);
            $this->received_quantity = Cleanup_Text($partsRecord->receive_quantity);
            $this->invoice_date      = Get_SQL_date($partsRecord->invoice_date);
            $this->returned_quantity = Cleanup_Text($partsRecord->return_quantity);

        }   // function __construct(...)

    }   // class PartStatus{}


    function Display_Parts_List($partsList){

        echo "<br/><br/>Parts List:<br/>";
        echo "<table border='1'>";
        echo "<br/>Total Parts: " . count($partsList);
        echo "<table border='1'>".
            "<tr>" .
                "<th>Location ID</th>" .
                "<th>RO Number</th>" .
                "<th>Repair ID</th>" .
                "<th>Line</th>" .
                "<th>Part Description</th>" .
                "<th>Part Number</th>" .
                "<th>Part Type</th>" .
                "<th>Vendor Name</th>" .
                "<th>RO Quantity</th>" .
                "<th>PO Number</th>" .
                "<th>Ordered Quantity</th>" .
                "<th>Order Date</th>" .
                "<th>Expected Delivery</th>" .
                "<th>Received Quantity</th>" .
                "<th>Invoice Date</th>" .
                "<th>Returned Quantity</th>" .
            "</tr>";

        foreach ($partsList as $part) {

            if ($part->repair_id === null) {
                echo "<tr style='background-color: #ffcccc;'>"; // Highlight in red if repair_id is null
            } else {
                echo "<tr>";
            }
            echo "  <td>" . $part->location_id . "</td>";
            echo "  <td>" . $part->ro_num . "</td>";
            echo "  <td>" . $part->repair_id . "</td>";
            echo "  <td>" . $part->line . "</td>";
            echo "  <td>" . $part->part_description . "</td>";
            echo "  <td>" . $part->part_number . "</td>";
            echo "  <td>" . $part->part_type . "</td>";
            echo "  <td>" . $part->vendor_name . "</td>";
            echo "  <td>" . $part->ro_quantity . "</td>";
            echo "  <td>" . $part->po_number . "</td>";
            echo "  <td>" . $part->ordered_quantity . "</td>";
            echo "  <td>" . $part->order_date . "</td>";
            echo "  <td>" . $part->expected_delivery . "</td>";
            echo "  <td>" . $part->received_quantity . "</td>";
            echo "  <td>" . $part->invoice_date . "</td>";
            echo "  <td>" . $part->returned_quantity . "</td>";
            echo "</tr>";
        }

        echo "</table>";

    }   // function Display_Parts_List($partsList)


    function Get_Repairs_List_From_DB($company_ID){

        require('../db_open.php');

        $repairsList = [];

        $tsql = <<<strSQL
                    SELECT s.location_id, r.ro_num, r.id AS repair_id  
                    FROM repairs r INNER JOIN shops s
                        ON r.shop_id = s.id
                    WHERE s.company_id = $company_ID
                    ORDER BY s.location_id, r.ro_num;
        strSQL;

        $repairs = $conn->query($tsql);

        if ($repairs->num_rows > 0) {

            while ($repair = $repairs->fetch_assoc()) {
                $repairsList[] = new Repair($repair);
            }

        } else {
            echo "No repair records found for the specified company ID.";
        }

        $conn->close();

        return $repairsList;

    }   // function Get_Repairs_List_From_DB($company_ID)


    function Get_Parts_List_From_XML($company_ID, $repair_ID_Lookup){

        $extractFile = TARGET_DIR . PARTS_STAT_FILE_NAME;

        // Load the XML file
        $xml = simplexml_load_file($extractFile);

        if ($xml === false) {
            die("Error: Failed to load or parse the Part Status XML file.");
        }

        $partsList = [];

        foreach ($xml->data->repairOrderLine as $rol) {

            if (empty($rol->repair_order_number)) {
                continue;
            }

            $part = new Part($rol, $repair_ID_Lookup);
            $partsList[] = $part;

        }   // foreach ($xml->data->repairOrderLine as $rol)

        return $partsList;

    }   // function Get_Parts_List_From_XML($company_ID)


    function Form_Insert_SQL($partsList){

        $tsql = <<<strSQL
                INSERT INTO parts_status
                    (line, part_number, part_description, part_type, 
                    vendor_name, ro_qty, 
                    po_number, ordered_qty, 
                    order_date, expected_delivery, 
                    received_qty, invoice_date, 
                    returned_qty, repair_id)
                VALUES
        strSQL;

        $values = '';

        foreach ($partsList as $part) {

            if (empty($part->ro_num) || empty($part->repair_id)){

                continue;   // skip records without an RO or repair id
    
            }else{

                $values .= "(" . $part->line . ", '" . $part->part_number . "', '" .
                            $part->part_description . "', '" . $part->part_type . "', '" . 
                            $part->vendor_name . "', " . $part->ro_quantity . ", '" . 
                            $part->po_number . "', " . $part->ordered_quantity . ", " . 
                            $part->order_date . ", " . $part->expected_delivery . ", " . 
                            $part->received_quantity . ", " . $part->invoice_date . "," . 
                            $part->returned_quantity .", " . $part->repair_id . "),";

            }   // if(empty()...)

        }   // foreach ($xml->data->repairOrderLine as $rol)

        $values = rtrim($values, ',');

        $insert_sql = $tsql . $values;

        return $insert_sql;

    }   // function Form_Insert_SQL()


    function Write_New_Parts_Records_To_Database($parts_list){

        require ('../db_open.php');

        $sql = Form_Insert_SQL($parts_list);

        if ($conn->query($sql) === TRUE){

            echo "Successfully inserted $conn->affected_rows parts status records into the database.";

        }else{

            echo "Error: " . $sql . "<br>" . $conn->error;
            exit;

        }   // if ($conn->query($sql) === TRUE)

        $conn->close();    

    }   // function Write_New_Parts_Records_To_Database()
?>