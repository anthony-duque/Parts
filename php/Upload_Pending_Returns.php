<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('session_handler.php');
requireLogin();

require('Utility_Scripts.php');

const RO_NUMBER     = 0;
const OWNER         = 1;
const VEHICLE       = 2;
const VENDOR	    = 3;
const RETURN_NUMBER	= 4;
const PICKUP_DATE   = 5;

const TARGET_DIR    = "../extract_files/";  // destination folder on the server
const D_OUT_FNAME   = "Pending_Returns.csv";      // Daily Out destination file name

const FORMS_DIR    = "../images/return_forms/";  // destination folder on the server

$pendingReturns = [];

try{

    $extractFile = TARGET_DIR . D_OUT_FNAME;
    $upload_OK = move_uploaded_file($_FILES["PendingReturns"]["tmp_name"], $extractFile);

    if ($upload_OK){

        $pendingReturns = Upload_Returns_CSV($extractFile);
        //var_dump($pendingReturns);
        echo "<br/> Pending Returns uploaded successfully!";

//        echo "<br/> Deleting old Return Forms...";
        Cleanup_Returns_Folder($pendingReturns);
    }

} catch(Exception $e){

    echo "There was an error uploading extract file ". basename($_FILES["PartsReturns"]["name"]);
    //header("Location: ./Admin.html");
}


function Cleanup_Returns_Folder($return_nums){

    $returnForms = scandir(FORMS_DIR, SCANDIR_SORT_DESCENDING);

    foreach ($returnForms as $key => $filename){

        if (($filename !== '.') && ($filename !== '..')){

            $file_name = explode(".", $filename);
//            echo $file_name[0] . "<br/>";
            $fn = "'" . $file_name[0] . "'";
                // if the form is not in the pending returns
                // delete the form
            if(!in_array($fn, $return_nums)){
                echo "Deleting " . $filename . "<br/>";
                unlink(FORMS_DIR . $filename);
            }   // if (!in_array)
        }   // if (($filename))
    }   // foreach()

}   // Cleanup_Returns_Folder()


function Upload_Returns_CSV($returns_extract_file){

        // Open the extract file and exit if not found.
    if (($handle = fopen($returns_extract_file, "r")) === FALSE) {
    	echo "Error in opening " . $returns_extract_file;
    	exit;
    }

    require('db_open.php');

    	//  Delete all records from the Parts Status table.
    $tsql = "DELETE FROM Pending_Returns";

    //echo $tsql;

    if ($conn->query($tsql) === TRUE) {
        echo "<br/><br/>Pending Returns Table cleared.<br/>";
    } else {
        echo "Error: " . $tsql . "<br>" . $conn->error;
        exit;
    }

    do{
        $data = fgetcsv($handle, 500, ",");
        $first_field = trim($data[0]);
        $first_field = strtoupper($first_field);
//        echo $first_field . "<br>";
    }while($first_field !== 'RO NUMBER');

    $insert_sql = '';
    $returnNums = [];

    $row = 0;	// record counter
    while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

        ++$row;

    	$ro_number 		= $data[RO_NUMBER];

        $pickup_date    = Get_SQL_date($data[PICKUP_DATE]);

    	$owner		    = "'" . Cleanup_Text($data[OWNER]) . "'";

    	$vendor	        = "'" . Cleanup_Text($data[VENDOR]) . "'";

        $return_number  = "'" . $data[RETURN_NUMBER] . "'";

        $vehicle        = "'" . Cleanup_Text($data[VEHICLE]) . "'";

        $values = "(" . $ro_number . ", " . $pickup_date . ", " .
                    $owner . ", " . $vendor . ", " .
                    $return_number . ", " . $vehicle . "),";

        $insert_sql .= $values;

        array_push($returnNums, $return_number);
    }

    $insert_sql = rtrim($insert_sql, ",");
//    echo $insert_sql;

    $tsql = <<<strSQL
		INSERT INTO Pending_Returns
			(RO, Pickup_Date, Owner, Vendor, Return_Number, Vehicle)
		VALUES
strSQL;

//        echo $tsql . $insert_sql . '<br/><br/>';

	if ($conn->query($tsql . $insert_sql) === TRUE) {
		;
      //echo $part_number . " inserted<br/>";
    } else {
      echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }

    	// to take out the dash and numbers after the actual vendor name

    $sql = 'UPDATE Pending_Returns ' .
    		'SET Vendor = SUBSTRING_INDEX(Vendor, " - ", 1)';

    if ($conn->query($sql) === TRUE) {
      // echo "RO Num fields populated. <br/>";
        echo "Pending Returns - Vendor Names cleaned up.<br/>";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

    fclose($handle);

    $conn = null;

    echo "Total Pending Returns uploaded: " . $row . "<br/>";

    return $returnNums;
}
?>
