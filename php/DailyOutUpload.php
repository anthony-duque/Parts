<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	const FILENAME = "../extract_files/Daily_Out_Report.csv";

	const RO_NUM           = 0;
	const OWNER            = 1;
	const VEHICLE_IN       = 2;
	const VEHICLE          = 3;
	const ESTIMATOR        = 4;
	const CURRENT_PHASE    = 6;
	const PARTS_RCVD       = 9;
	const TECHNICIAN       = 13;

$row = 0;

if (($handle = fopen(FILENAME, "r")) !== FALSE) {

    $tsql = "INSERT INTO Repairs " .
            "(RONum, Owner, Vehicle, Estimator," .
            " Vehicle_In, PartsReceived, CurrentPhase, Technician) " .
            "VALUES ";

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

        $tsql = $tsql . $values;

    }

    fclose($handle);

    $tsql = str_replace(")(", "),(", $tsql);
    $tsql = $tsql . ';';
    echo $tsql;
    exit;

    $servername = "localhost";
    $username = "root";
    $password = "Al@d5150";
    $dbname = "CarStar";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    } else {
        echo("Connection successful!");
    }

    /*    $sql = "INSERT INTO MyGuests (firstname, lastname, email)
    VALUES ('John', 'Doe', 'john@example.com')";
    */

    if ($conn->query($tsql) === TRUE) {
      echo $row . " vendors successfully uploaded!";
    } else {
      echo "Error: " . $tsql . "<br>" . $conn->error;
    }

    $conn->close();

}
?>
Total Records Read: <?= $row ?>
