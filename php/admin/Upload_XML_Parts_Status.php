<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../Utility_Scripts.php');

    $company_ID = $_GET["companyID"];       // set the company cookie

    const TARGET_DIR    = "../../extract_files/";  // destination folder on the server
    const P_STATUS_FNAME = "Part_Status_Report.xml";      // Part Status destination file name

    $extractFile = TARGET_DIR . P_STATUS_FNAME;

        // Load the XML file
    $xml = simplexml_load_file($extractFile);

    if ($xml === false) {
        die("Error: Failed to load or parse the Part Status XML file.");
    }

        // Loop through the XML and execute the insertion
    $insertedCount = 0;
    $values = '';

    $tsql = <<<strSQL
     		INSERT INTO parts_status
    			(ro_num, line, part_number, part_description,
                part_type, vendor_name, ro_qty, po_number, ordered_qty,
                order_date, expected_delivery, received_qty,
                invoice_date, returned_qty, location_id, company_id)
    		VALUES
    strSQL;

    foreach ($xml->data->repairOrderLine as $rol) {

            //echo "<br/>Processing Repair Order Line: " . $rol->repair_order_number;
        if (empty($rol->repair_order_number)) {
            continue;
        } else{
            $ro_number 			= "'" . Cleanup_Text($rol->repair_order_number) . "'";
//            echo "<br/>Processing Repair Order: " . $ro_number;
        }

        if (empty($rol->estimate_line_number)) {
            $line = 'NULL';
        } else{
            $line = $rol->estimate_line_number;
        }

    	$part_number		= "'" . Cleanup_Text($rol->part_number) . "'";

    	$part_description   = "'" . Cleanup_Text($rol->part_description) . "'";

    	$part_type			= "'" . Cleanup_Text($rol->part_type_name) . "'";

    	$vendor_name		=  "'" . Cleanup_Text($rol->vendor_name) . "'";

    	$ro_quantity 		= $rol->part_quantity;

        $po_number          = "'" . Cleanup_Text($rol->po_number) . "'";

    	$ordered_quantity   = $rol->order_quantity;

    	$order_date 		= Get_SQL_date($rol->order_date_time);

    	$expected_delivery  = Get_SQL_date($rol->expected_delivery_date);

    	$received_quantity 	= $rol->receive_quantity;

    	$invoice_date 		= Get_SQL_date($rol->invoice_date);

    	$returned_quantity 	= $rol->return_quantity;

        if (empty($rol->repair_facility_number)) {
            $location_id     = Cleanup_Text($rol->franchise_id);
        } else {
            $location_id     = Cleanup_Text($rol->repair_facility_number);
        }
        
        $values .= "(" . $ro_number . ", " . $line . ", " . $part_number . ", " .
                    $part_description . ", " . $part_type . ", " . $vendor_name . ", " .
                    $ro_quantity . ", " . $po_number . ", " . $ordered_quantity . ", " . $order_date . ", " .
                    $expected_delivery . ", ". $received_quantity . ", " .
                    $invoice_date . "," . $returned_quantity ."," . $location_id . "," . 
                    $company_ID . "),";

        $insertedCount++;

    }   // foreach ($xml->data->repairOrderLine as $rol)

    $values = rtrim($values, ',');

    $insert_sql = $tsql . $values;

//    echo "<br/>$insert_sql<br/>";   // test
//    exit;                           // test

    require('../db_open.php');

		// Delete all records in Parts Status table
    $tsql = "DELETE FROM parts_status" . 
            " WHERE company_id = $company_ID" . 
                " AND location_id IN " . 
                " (SELECT location_id " . 
                "  FROM shops " . 
                "  WHERE company_id = $company_ID)";

	if ($conn->query($tsql) === TRUE){

		echo "<br/><br/>Company-related parts status records cleared.<br/>";

    }else{

		echo "Error: " . $tsql . "<br> - " . $conn->error;
		exit;
	}	// if ($conn->query($tsql) === TRUE)


    if ($conn->query($insert_sql) === TRUE){

        echo "Successfully inserted {$insertedCount} parts status records into the database.";

    }else{

        echo "Error: " . $insert_sql . "<br>" . $conn->error;
        exit;
    }   // if ($conn->query($insert_sql) === TRUE)

    $conn = null;    

?>