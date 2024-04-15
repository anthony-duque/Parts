<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$extractFile = $_POST["DailyOutCSV"];

if (trim($extractFile) === ''){
	echo "No extract file specified.";
	header("Location: ./Upload_DailyOut.html");
}
const FILENAME = "../extract_files/Daily_Out.csv";

const RO_NUM           = 0;
const OWNER            = 1;
const VEHICLE_IN       = 2;
const VEHICLE          = 3;
const ESTIMATOR        = 4;
const CURRENT_PHASE    = 6;
const PARTS_RCVD       = 9;
const TECHNICIAN       = 13;

require('db_open.php');

	// Refresh the table of Repairs with Current RepairOrders

	$tsql = "DELETE FROM Repairs";

	//echo $tsql;

	if ($conn->query($tsql) === TRUE) {
		echo "<br/>Repairs Table cleared.<br/>";
	} else {
	  echo "Error: " . $tsql . "<br> - " . $conn->error;
	  exit;
	}

	if (($handle = fopen(FILENAME, "r")) === FALSE) {
		echo "Error in opening " . FILENAME;
		exit;
	}

    $tsql = "INSERT INTO Repairs " .
            "(RONum, Owner, Vehicle, Estimator, " .
            "Vehicle_In, PartsReceived, CurrentPhase, Technician) " .
            "VALUES ";

	$row = 0;	// record counter

	while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {

        $second_field = trim($data[1]);

        if (($second_field == '') || ($second_field == 'Owner')) {
            continue;
        }

        ++$row;

        if ($data[VEHICLE_IN] > ''){
            $dateObj = date_create($data[VEHICLE_IN]);
            $mySqlDate = "'" . date_format($dateObj, "Y-m-d H:i:s") . "'";
        } else {
            $mySqlDate = 'NULL';
        }

		$ro_num 		= $data[RO_NUM];
		$owner 			= str_replace("'", "\'", $data[OWNER]);
		$vehicle 		= str_replace("'", "\'", $data[VEHICLE]);
		$vehicle_in 	= $mySqlDate;
		$estimator		= str_replace("'", "\'", $data[ESTIMATOR]);
		$current_phase 	= $data[CURRENT_PHASE];
		$parts_received = $data[PARTS_RCVD];
		$technician     = str_replace("'", "\'", $data[TECHNICIAN]);

        $values = "(". $ro_num .", '". $owner . "', '" . $vehicle . "', '" . $estimator . "', " .
                  $vehicle_in . ", " . $parts_received . ", '" . $current_phase . "', '" . $technician . "')";

        $insert_sql = $tsql . $values;

		if ($conn->query($insert_sql) === TRUE) {
	      echo $ro_num . " uploaded<br/>";
	    } else {
	      echo "Error: " . $insert_sql . "<br>" . $conn->error;
	    }
    }

    fclose($handle);
	$conn = null;

/*
    $tsql = str_replace(")(", "),(", $tsql);
    $tsql = $tsql . ';';
    echo $tsql;
    exit;

    $servername = "localhost";
    $username = "root";
    $password = "Al@d5150";
    $dbname = "CarStar";

    // Create connection
    $conn = new mysqli($servername, $us	$row = 0;
ername, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    } else {
        echo("Connection successful!");
    }

    $sql = "INSERT INTO MyGuests (firstname, lastname, email)
    	VALUES ('John', 'Doe', 'john@example.com')";
*/

?>
Total Records Read: <?= $row ?>
