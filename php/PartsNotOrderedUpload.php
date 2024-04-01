<?php

const FILENAME = "../extract_files/Parts_Not_Ordered.csv";

const RO_NUM          	= 0;
const LINE            	= 1;
const PART_NUMBER     	= 2;
const PART_DESCRIPTION	= 3;
const PART_TYPE    		= 4;

require('db_open.php');

	// Refresh the table of Parts Not Ordered

	$tsql = "DELETE FROM PartsNotOrdered";

	//echo $tsql;

	if ($conn->query($tsql) === TRUE) {
		echo "Parts Not Ordered Table cleared.";
	} else {
	  echo "Error: " . $tsql . "<br>" . $conn->error;
	  exit;
	}

	if (($handle = fopen(FILENAME, "r")) === FALSE) {
		echo "Error in opening " . FILENAME;
		exit;
	}

    $tsql = "INSERT INTO PartsNotOrdered " .
            "(RONum, Line, Part_Number, Part_Description, Part_Type) " .
            "VALUES ";

	$row = 0;	// record counter

	while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {

        $second_field = trim($data[1]);

        if (($second_field == '') || ($second_field == 'Line')) {
            continue;
        }

        ++$row;

		$ro_num 			= $data[RO_NUM];
		$line 				= $data[LINE];
		$part_number 		= str_replace("'", "\'", $data[PART_NUMBER]);
		$part_description	= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $data[PART_DESCRIPTION]);
		$part_description	= str_replace("'", "\'",  $part_description);
		$part_type     		= str_replace("'", "\'", $data[PART_TYPE]);

        $values = "(". $ro_num .", ". $line . ", '" . $part_number . "', '" .
                  $part_description . "', '" . $part_type . "')";

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
