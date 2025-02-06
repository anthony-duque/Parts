<?php

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
const SHOP_LOCATION     = 11;

function Upload_Daily_Out_CSV($daily_out_extract_file){

        // Open the extract file for reading
    if (($handle = fopen($daily_out_extract_file, "r")) === FALSE) {
		echo "Error in opening " . $daily_out_extract_file;
		exit;
	}

    require('db_open.php');

		// Delete all records in Repairs table
	$tsql = "DELETE FROM Repairs";

	if ($conn->query($tsql) === TRUE) {
		echo "<br/><br/>Repairs Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br> - " . $conn->error;
	  exit;
	}

	$tsql = <<<strSQL
			INSERT INTO Repairs
         		(RONum, Owner, Vehicle, Vehicle_Color, License_Plate, PartsReceived,
            	Vehicle_In, CurrentPhase, Scheduled_Out, Technician, Estimator, Location)
	        VALUES
strSQL;

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

        $location       = "'" . Cleanup_Text($data[SHOP_LOCATION]) . "'";

        $values = "(". $ro_num . ", " . $owner . ", " . $vehicle . ", " . $vehicle_color  . ", " .
					$license_plate . ", " . $parts_received . ", " . $vehicle_in . ", " .
                  	$current_phase . ", " . $scheduled_out . ", " . $technician . ", " .
                    $estimator . ", " . $location . ")";

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

    echo "Total Repair Orders Uploaded: " . $row . "</br>";
}
?>
