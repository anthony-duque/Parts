<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../Utility_Scripts.php');

    $company_ID = $_GET["companyID"];       // set the company cookie

    const TARGET_DIR    = "../../extract_files/";  // destination folder on the server
    const D_OUT_FNAME   = "Production Schedule_Report.xml";      // Daily Out destination file name

    $extractFile = TARGET_DIR . D_OUT_FNAME;

        // Load the XML file
    $xml = simplexml_load_file($extractFile);

    if ($xml === false) {
        die("Error: Failed to load or parse the XML file.");
    }

        // Loop through the XML and execute the insertion

    $insertedCount = 0;
    $values = '';

    $tsql = <<<strSQL
                INSERT INTO repairs
                    (ro_num, owner, vehicle, vehicle_color, license_plate,
                    parts_received, vehicle_in, current_phase, scheduled_out,
                    technician, estimator, location, insurance)
                VALUES
    strSQL;

    foreach ($xml->data->repairOrder as $ro) {

        $ro_num         = Cleanup_Text($ro->repair_order_number);
        
        $owner          = Cleanup_Text($ro->owner_name);

        $vehicle        = Cleanup_Text($ro->vehicle_year_make_model);

        $vehicle_color  = Cleanup_Text($ro->vehicle_exterior_paint_color);

        $license_plate 	= Cleanup_Text($ro->vehicle_license_number);

        if (empty($ro->parts_received_percent)) {
            $parts_received = 0;
        } else {
            $parts_received = $ro->parts_received_percent;
        }

        $vehicle_in     = Get_SQL_date($ro->vehicle_in_datetime);

        $current_phase  = Cleanup_Text($ro->repair_phase_name);

        $scheduled_out	= Get_SQL_date($ro->vehicle_out_datetime);

        $technician     = Cleanup_Text($ro->body_technician_display_name);

        $estimator      = Cleanup_Text($ro->service_writer_display_name);
        
        $location       = Cleanup_Text($ro->repair_facility_name);

        $insurance      = Cleanup_Text($ro->master_carrier_name);

        $values .= "('" . $ro_num . "', '" . $owner . "', '" . $vehicle . "', '" . $vehicle_color  . "', " .
                    "'" . $license_plate . "', " . $parts_received . ", " . $vehicle_in . ", " .
                    "'" . $current_phase . "', " . $scheduled_out . ", '" . $technician . "', " .
                    "'" . $estimator . "', '" . $location . "', '" . $insurance . "'),";

        $insertedCount++;

    }   // foreach ($xml->data->repairOrder as $ro)

    $values = rtrim($values, ',');

    $insert_sql = $tsql . $values;

    require('../db_open.php');

		// Delete all records in Repairs table
	$tsql = "DELETE FROM repairs" . 
			" WHERE loc_id IN (SELECT id FROM locations WHERE company_id = $company_ID)";

	if ($conn->query($tsql) === TRUE) {

		echo "<br/><br/>Company-related repair records cleared.<br/>";

	} else {

		echo "Error: " . $tsql . "<br> - " . $conn->error;
		exit;

	}	// if ($conn->query($tsql) === TRUE)

    if ($conn->query($insert_sql) === TRUE) {
        echo "Successfully inserted {$insertedCount} repair records into the database.";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
        exit;
    }

    $conn = null;    
//    echo "<br/>$insert_sql<br/>";

?>