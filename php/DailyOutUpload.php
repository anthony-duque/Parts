<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const FILEPATH = "../extract_files/";

const RO_NUM           	= 0;
const OWNER            	= 1;
const VEHICLE          	= 2;
const VEHICLE_COLOR    	= 3;
const LICENSE_PLATE    	= 4;
const PARTS_RCVD       	= 5;
const VEHICLE_IN    	= 6;
const CURRENT_PHASE    	= 7;
const SCHEDULED_OUT    	= 8;
const TECHNICIAN       	= 9;
const ESTIMATOR        	= 10;

$extractFile = FILEPATH . $_POST["DailyOutCSV"];

if (trim($extractFile) === ''){
	echo "No extract file specified.";
	header("Location: ./Upload_DailyOut.html");
}

require('Utility_Scripts.php');

require('db_open.php');

		// Refresh the table of Repairs with Current RepairOrders
	$tsql = "DELETE FROM Repairs";

	if ($conn->query($tsql) === TRUE) {
		echo "<br/>Repairs Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br> - " . $conn->error;
	  exit;
	}

	if (($handle = fopen($extractFile, "r")) === FALSE) {
		echo "Error in opening " . $extractFile;
		exit;
	}

	$tsql = "INSERT INTO Repairs" .
	         	" (RONum, Owner, Vehicle, Vehicle_Color, License_Plate, PartsReceived, " .
	            " Vehicle_In, CurrentPhase, Scheduled_Out, Technician, Estimator) " .
	         "VALUES";

	$row = 0;	// record counter

	while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {

        $second_field = trim($data[1]);

        if (($second_field == '') || ($second_field == 'Owner')) {
            continue;
        }

        ++$row;

		$ro_num 		= $data[RO_NUM];

		$owner			= "'" . Cleanup_Text($data[OWNER]) . "'";

		$vehicle		= "'" . Cleanup_Text($data[VEHICLE]) . "'";

		$vehicle_color 	= "'" . Cleanup_Text($data[VEHICLE_COLOR]) . "'";

		$license_plate 	= "'" . Cleanup_Text($data[LICENSE_PLATE]) . "'";

		$parts_received = $data[PARTS_RCVD];

		$vehicle_in 	= Get_SQL_date($data[VEHICLE_IN]);

		$current_phase 	= "'" . $data[CURRENT_PHASE] . "'";

		$scheduled_out	= Get_SQL_date($data[SCHEDULED_OUT]);

		$technician     = "'" . Cleanup_Text($data[TECHNICIAN]) . "'";

		$estimator		= "'" . Cleanup_Text($data[ESTIMATOR]) . "'";

        $values = "(". $ro_num . ", " . $owner . ", " . $vehicle . ", " . $vehicle_color  . ", " .
					$license_plate . ", " . $parts_received . ", " . $vehicle_in . ", " .
                  	$current_phase . ", " . $scheduled_out . ", " . $technician . ", " . $estimator . ")";

        $insert_sql = $tsql . $values;
//		echo $insert_sql . '<br/>';

		if ($conn->query($insert_sql) === TRUE) {
	      ; //echo $ro_num . " uploaded<br/>";
	    } else {
	      echo "Error: " . $insert_sql . "<br>" . $conn->error;
	    }
    }

    fclose($handle);
	$conn = null;

?>
Total Repair Orders Uploaded: <?= $row ?>
<br/>
<input type='button' value='Back to Main Menu' onclick='location.href = "../Admin.html";'>
