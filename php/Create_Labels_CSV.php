<?php

function Create_Labels_File(){

    define ("LABELS_CSV_FILE", "../extract_files/Labels.csv");

    //require('Utility_Scripts.php');

    if (($csvFile = fopen(LABELS_CSV_FILE, "w")) === FALSE) {
    	echo "Error in opening " . LABELS_CSV_FILE;
    	exit;
    }

    $tsql = <<<strSQL
                SELECT
                    RONum, Location,
                    SUBSTRING_INDEX(Estimator, ' ', 1) AS Estimator,
                    SUBSTRING_INDEX(Owner, ',', 1) AS Owner,
                    Vehicle, LCASE(Vehicle_Color) AS Vehicle_Color,
                    SUBSTRING_INDEX(Technician, ' ', 1) AS Technician,
                    DATE_FORMAT(Vehicle_In, "%M %d %Y") AS Vehicle_In
                FROM Repairs
            strSQL;

    require('db_open.php');

    $s = mysqli_query($conn, $tsql);

    while($rec = mysqli_fetch_assoc($s)){

        if ($rec["Vehicle"] > ''){
            $vehicle        = explode(" ", $rec["Vehicle"]);
            $rec["Vehicle"]   = $vehicle[1] . " " . $vehicle[2];
            $rec["Vehicle"]   = toProperCase($rec["Vehicle"]);
        }

        $rec["Technician"]  = toProperCase($rec["Technician"]);
        $rec["Estimator"]     = toProperCase($rec["Estimator"]);
        $rec["Owner"]  = toProperCase($rec["Owner"]);

        fputcsv($csvFile, $rec);
    //    array_push($this->cars, new Car($conn, $r));
    }   // while()

    fclose($csvFile);
    $conn = null;

}   // Create_Labels_CSV()

?>
