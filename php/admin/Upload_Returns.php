<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Utility_Scripts.php');

const RO_NUMBER			    = 0;
const RETURN_DATE           = 1;
const VENDOR_PICKUP_DATE    = 2;
const PART_NUMBER	        = 3;
const PART_DESCRIPTION	    = 4;
const PART_TYPE    		    = 5;
const RETURN_AMOUNT     	= 6;
const INVOICE_NUMBER 		= 7;
const RETURN_REASON         = 8;
const VENDOR_NAME           = 9;

const TARGET_DIR    = "../extract_files/";  // destination folder on the server
const D_OUT_FNAME   = "Pending_Returns.csv";      // Daily Out destination file name

try{

    $extractFile = TARGET_DIR . D_OUT_FNAME;
//    $upload_OK = move_uploaded_file($_FILES["Pending_Returns"]["tmp_name"], $extractFile);

//    if ($upload_OK){
        Upload_Returns_CSV($extractFile);
        echo "<br/> Pending Returns upload successful!";
//    }
} catch(Exception $e){

    echo "There was an error uploading the ";//. basename($_FILES["Pending_Returns"]["name"]);
    //header("Location: ./Admin.html");
}

function Upload_Returns_CSV($returns_extract_file){

        // Open the extract file and exit if not found.
    if (($handle = fopen($returns_extract_file, "r")) === FALSE) {
    	echo "Error in opening " . $returns_extract_file;
    	exit;
    }

    require('db_open.php');

    	//  Delete all records from the Parts Status table.
    $tsql = "DELETE FROM Parts_Returns";

    //echo $tsql;

    if ($conn->query($tsql) === TRUE) {
        echo "<br/><br/>Parts Returns Extract Table cleared.<br/>";
    } else {
        echo "Error: " . $tsql . "<br>" . $conn->error;
        exit;
    }

    do{
        $data = fgetcsv($handle, 500, ",");
        $first_field = trim($data[0]);
        $first_field = strtoupper($first_field);
        echo $first_field . "<br>";
    }while($first_field !== 'RO NUMBER');

    $insert_sql = '';

        $row = 0;	// record counter
        while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

            ++$row;

        	$ro_number 			= $data[RO_NUMBER];

        	$return_date 		= Get_SQL_date($data[RETURN_DATE]);

            $vendor_pickup_date = Get_SQL_date($data[VENDOR_PICKUP_DATE]);

        	$part_number		= "'" . Cleanup_Text($data[PART_NUMBER]) . "'";

        	$part_description	= "'" . Cleanup_Text($data[PART_DESCRIPTION]) . "'";

        	$part_type			= "'" . $data[PART_TYPE] . "'";

            $return_amount      = $data[RETURN_AMOUNT];

            $invoice_number		= "'" . $data[INVOICE_NUMBER] . "'";

        	$return_reason		=  "'" . Cleanup_Text($data[RETURN_REASON]) . "'";

            $vendor_name        = "'" . Cleanup_Text($data[VENDOR_NAME]) . "'";

            $values = "(" . $ro_number . ", " . $return_date . ", " .
                        $vendor_pickup_date . ", " . $part_number . ", " .
                        $part_description . ", " . $part_type . ", " .
                        $return_amount . ", " . $invoice_number . ", " .
                        $return_reason . ", " . $vendor_name . "),";

            $insert_sql .= $values;
        }

        $insert_sql = rtrim($insert_sql, ",");

        $tsql = <<<strSQL
            		INSERT INTO Parts_Returns
            			(RO_Num, Return_Date, Vendor_Pickup_Date,
                        Part_Number, Part_Description, Part_Type,
                        Amount, Invoice_Number, Reason, Vendor_Name)
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

    $sql = 'UPDATE Parts_Returns ' .
    		'SET Vendor_Name = SUBSTRING_INDEX(Vendor_Name, " - ", 1)';

    if ($conn->query($sql) === TRUE) {
      // echo "RO Num fields populated. <br/>";
        echo "Vendor Names cleaned up.";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

    fclose($handle);

    $conn = null;

    echo "Total parts returns uploaded: " . $row . "<br/>";

}

?>
