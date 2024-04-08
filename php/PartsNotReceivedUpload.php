<?php

const FILENAME = "../extract_files/Parts_Not_Received.csv";

const RO_NUM          	= 0;
const LINE            	= 3;
const PART_NUMBER     	= 4;
const PART_TYPE    		= 5;
const PART_DESCRIPTION	= 6;
const ORDERED_QUANTITY	= 8;
const EXPECTED_DELIVERY = 10;
const VENDOR_NAME 		= 12;

require('db_open.php');

	// Refresh the table of Parts Not Ordered

	$tsql = "DELETE FROM PartsNotReceived";

	//echo $tsql;

	if ($conn->query($tsql) === TRUE) {
		echo "Parts Not Received Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br>" . $conn->error;
	  exit;
	}

	if (($handle = fopen(FILENAME, "r")) === FALSE) {
		echo "Error small in opening " . FILENAME;
		exit;
	}

    $tsql = "INSERT INTO PartsNotReceived " .
            "(RONum, Line, Part_Number, Part_Type, Part_Description, " .
			"Ordered_Quantity, Expected_Delivery, Vendor_Name) " .
            "VALUES ";

	$row = 0;	// record counter

	while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {

        $second_field = trim($data[3]);
			// To delete any lines before the column names
        if (($second_field == '') || ($second_field == 'Line')) {
            continue;
        }

        ++$row;

		if ($data[EXPECTED_DELIVERY] > ''){
            $dateObj = date_create($data[EXPECTED_DELIVERY]);
            $mySqlDate = "'" . date_format($dateObj, "Y-m-d H:i:s") . "'";
        } else {
            $mySqlDate = 'NULL';
        }

		$ro_num 			= $data[RO_NUM];
		$line 				= $data[LINE];
		$part_number 		= str_replace("'", "\'", $data[PART_NUMBER]);
		$part_type     		= str_replace("'", "\'", $data[PART_TYPE]);
		$part_description	= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $data[PART_DESCRIPTION]);
		$part_description	= str_replace("'", "\'",  $part_description);
		$ordered_quantity 	= $data[ORDERED_QUANTITY];
		$expected_delivery	= $mySqlDate;
		$vendor_name		= str_replace("'", "\'", $data[VENDOR_NAME]);

        $values = "(". $ro_num .", ". $line . ", '" . $part_number . "', '" .
                  $part_type . "', '" . $part_description . "', " . $ordered_quantity .
				  ", " . $expected_delivery . ", '" . $vendor_name . "')";

        $insert_sql = $tsql . $values;

		if ($conn->query($insert_sql) === TRUE) {
	      echo $ro_num . " inserted<br/>";
	    } else {
	      echo "Error: " . $insert_sql . "<br>" . $conn->error;
	    }
    }

    fclose($handle);
	$conn = null;

?>
Total records uploaded: <?= $row ?>
