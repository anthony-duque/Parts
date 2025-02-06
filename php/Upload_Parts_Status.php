<?php

const RO_NUMBER			= 0;
const LINE           	= 1;
const PART_NUMBER		= 2;
const PART_DESCRIPTION	= 3;
const PART_TYPE    		= 4;
const VENDOR_NAME     	= 5;
const RO_QUANTITY 		= 6;
const ORDERED_QUANTITY  = 7;
const ORDER_DATE		= 8;
const EXPECTED_DELIVERY = 9;
const RECEIVED_QTY		= 10;
const INVOICE_DATE		= 11;
const RETURNED_QTY		= 12;
const SHOPLOCATION      = 13;

function Upload_Parts_Status_CSV($parts_status_extract_file){

        // Open the extract file and exit if not found.
    if (($handle = fopen($parts_status_extract_file, "r")) === FALSE) {
    	echo "Error in opening " . $parts_status_extract_file;
    	exit;
    }

    require('db_open.php');

    	//  Delete all records from the Parts Status table.
    $tsql = "DELETE FROM PartsStatusExtract";

    //echo $tsql;

    if ($conn->query($tsql) === TRUE) {
    	echo "<br/><br/>Parts Status Extract Table cleared.<br/>";
    } else {
      echo "Error: " . $tsql . "<br>" . $conn->error;
      exit;
    }


    $tsql = <<<strSQL
    		INSERT INTO PartsStatusExtract
    			(RO_Num, Line, Part_Number, Part_Description,
                Part_Type, Vendor_Name, RO_Qty, Ordered_Qty,
                Order_Date, Expected_Delivery, Received_Qty,
                Invoice_Date, Returned_Qty, Location)
    		VALUES
strSQL;

    $row = 0;	// record counter

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

        $second_field = trim($data[1]);
    	$second_field = strtoupper($second_field);

    //		echo $second_field;
        if (($second_field == '') || ($second_field === 'LINE')) {
            continue;
        }

        ++$row;

    	$ro_number 			= $data[RO_NUMBER];

    	$line 				= $data[LINE];

    	$part_number		= "'" . Cleanup_Text($data[PART_NUMBER]) . "'";

    	$part_description	= "'" . Cleanup_Text($data[PART_DESCRIPTION]) . "'";

    	$part_type			= "'" . $data[PART_TYPE] . "'";

    	$vendor_name		=  "'" . Cleanup_Text($data[VENDOR_NAME]) . "'";

    	$ro_quantity 		= $data[RO_QUANTITY];

    	$ordered_quantity 	= $data[ORDERED_QUANTITY];

    	$order_date 		= Get_SQL_date($data[ORDER_DATE]);

    	$expected_delivery	= Get_SQL_date($data[EXPECTED_DELIVERY]);

    	$received_quantity 	= $data[RECEIVED_QTY];

    	$invoice_date 		= Get_SQL_date($data[INVOICE_DATE]);

    	$returned_quantity 	= $data[RETURNED_QTY];

        $location           = "'" . Cleanup_Text($data[SHOPLOCATION]) . "'";

        $values = "(" . $ro_number . ", " . $line . ", " . $part_number . ", " .
                  $part_description . ", " . $part_type . ", " . $vendor_name . ", " .
    			  $ro_quantity . ", " . $ordered_quantity . ", " . $order_date . ", " .
    			  $expected_delivery . ", ". $received_quantity . ", " .
    			  $invoice_date . "," . $returned_quantity ."," . $location . ")";

        $insert_sql = $tsql . $values;
    //	echo $insert_sql . '<br/><br/>';

    	if ($conn->query($insert_sql) === TRUE) {
    		;
          //echo $part_number . " inserted<br/>";
        } else {
          echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }

    	// to take out the dash and numbers after the actual vendor name
    $sql = 'UPDATE PartsStatusExtract ' .
    		'SET Vendor_Name = SUBSTRING_INDEX(Vendor_Name, " - ", 1)';

    if ($conn->query($sql) === TRUE) {
      // echo "RO Num fields populated. <br/>";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

    fclose($handle);
    $conn = null;

    echo "Total parts uploaded: " . $row . "<br/>";

}
?>
