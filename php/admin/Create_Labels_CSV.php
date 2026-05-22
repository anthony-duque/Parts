<?php

function Create_Labels_File(){

    define ("LABELS_CSV_FILE", "../../extract_files/Labels.csv");

    //require('Utility_Scripts.php');

    if (($csvFile = fopen(LABELS_CSV_FILE, "w")) === FALSE) {
    	echo "Error in opening " . LABELS_CSV_FILE;
    	exit;
    }

    $tsql = <<<strSQL
                SELECT
                    ro_num, location,
                    SUBSTRING_INDEX(estimator, ' ', 1) AS estimator,
                    SUBSTRING_INDEX(owner, ',', 1) AS owner,
                    vehicle, LCASE(vehicle_color) AS vehicle_color,
                    SUBSTRING_INDEX(technician, ' ', 1) AS technician,
                    DATE_FORMAT(vehicle_in, "%M %d %Y") AS vehicle_in
                FROM repairs
            strSQL;

    require('../db_open.php');

    $s = mysqli_query($conn, $tsql);

    while($rec = mysqli_fetch_assoc($s)){

        if ($rec["vehicle"] > ''){
            $vehicle        = explode(" ", $rec["vehicle"]);
            $rec["vehicle"]   = $vehicle[1] . " " . $vehicle[2];
            $rec["vehicle"]   = toProperCase($rec["vehicle"]);
        }

        $rec["technician"]  = toProperCase($rec["technician"]);
        $rec["estimator"]     = toProperCase($rec["estimator"]);
        $rec["owner"]  = toProperCase($rec["owner"]);

        fputcsv($csvFile, $rec);
    //    array_push($this->cars, new Car($conn, $r));
    }   // while()

    fclose($csvFile);
    $conn = null;

}   // Create_Labels_CSV()

?>
