<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const FILEPATH = "../extract_files/";

const RO_INFO			= 0;
const LINE            	= 1;
const PART_NUMBER		= 2;
const PART_DESCRIPTION	= 3;
const PART_TYPE    		= 4;
const RO_QUANTITY 		= 5;
const VENDOR_NAME     	= 7;
const PO_NUM 			= 8;
const ORDERED_QUANTITY  = 9;
const EXPECTED_DELIVERY = 11;
const RECEIVED_QTY		= 12;
const RETURNED_QTY		= 13;
const RO_STATUS			= 16;

$extractFile = FILEPATH . $_POST["PartsStatusCSV"];

if (trim($extractFile) === ''){
	echo "No extract file specified.";
	header("Location: ./Upload_PartsStatus.html");
}

require('db_open.php');

	// Refresh the table of Parts Not Ordered

	$tsql = "DELETE FROM PartsStatusExtract";

	//echo $tsql;

	if ($conn->query($tsql) === TRUE) {
		echo "Parts Status Extract Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br>" . $conn->error;
	  exit;
	}

	if (($handle = fopen($extractFile, "r")) === FALSE) {
		echo "Error small in opening " . FILENAME;
		exit;
	}

    $tsql = "INSERT INTO PartsStatusExtract " .
            "(RO_Info, Line, Part_Number, Part_Description, Part_Type, " .
			"RO_Qty, Vendor_Name, PO_Number, Ordered_Qty, " .
			"Expected_Delivery, Received_Qty, Returned_Qty, RO_Status) " .
            "VALUES ";

	$row = 0;	// record counter


	while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {


        $second_field = trim($data[1]);
		$second_field = strtoupper($second_field);

        if (($second_field == '') || ($second_field === 'LINE')) {
            continue;
        }

        ++$row;

		$ro_info	= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $data[RO_INFO]);
		$ro_info	= str_replace("'", "\'",  $ro_info);
		$ro_info		= "'" . $ro_info . "'";

		$line 				= $data[LINE];

		$part_number		= "'" . $data[PART_NUMBER] . "'";

		$part_description	= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $data[PART_DESCRIPTION]);
		$part_description	= str_replace("'", "\'",  $part_description);
		$part_description	= "'" . $part_description . "'";

		$part_type			= "'" . $data[PART_TYPE] . "'";

		$ro_quantity 		= $data[RO_QUANTITY];

		$vendor_name	= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $data[VENDOR_NAME]);
		$vendor_name	= str_replace("'", "\'",  $vendor_name);
		$vendor_name		= "'" . $vendor_name . "'";

		$PO_Number			= "'" . $data[PO_NUM] . "'";

		$ordered_quantity 	= $data[ORDERED_QUANTITY];

		if (trim($data[EXPECTED_DELIVERY]) > ''){
            $dateObj = date_create($data[EXPECTED_DELIVERY]);
            $mySqlDate = "'" . date_format($dateObj, "Y-m-d H:i:s") . "'";
        } else {
            $mySqlDate = 'NULL';
        }

		$expected_delivery	= $mySqlDate;

		$received_quantity 	= $data[RECEIVED_QTY];
		$returned_quantity 	= $data[RETURNED_QTY];
		$ro_status 			= "'" . $data[RO_STATUS] . "'";

        $values = "(" . $ro_info . ", " . $line .", ". $part_number . ", " .
                  $part_description . ", " . $part_type . ", " . $ro_quantity . ", " .
				  $vendor_name . ", " . $PO_Number . ", " . $ordered_quantity . ", " .
				  $expected_delivery . ", ". $received_quantity . ", " .
				  $returned_quantity . "," . $ro_status . ")";

        $insert_sql = $tsql . $values;
		//echo $insert_sql . '<br/><br/>';

		if ($conn->query($insert_sql) === TRUE) {
	      echo $part_number . " inserted<br/>";
	    } else {
	      echo "Error: " . $insert_sql . "<br>" . $conn->error;
	    }
    }

	$sql = 'UPDATE PartsStatusExtract ' .
			'SET RO_Num = CONVERT(SUBSTRING_INDEX(RO_Info, " (", 1), UNSIGNED)';

	if ($conn->query($sql) === TRUE) {
      echo "RO Num fields populated. <br/>";
    } else {
      echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }

    fclose($handle);
	$conn = null;

?>
Total records uploaded: <?= $row ?>
