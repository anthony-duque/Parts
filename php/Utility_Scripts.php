<?php

function Get_SQL_date($dateString){

	$mySqlDate = '';

	if ($dateString > ''){
		$dateObj = date_create($dateString);
		$mySqlDate = "'" . date_format($dateObj, "Y-m-d H:i:s") . "'";
	} else {
		$mySqlDate = 'NULL';
	}

	return $mySqlDate;
}	// Get_SQL_date()


/*
AT&T						10digitphonenumber@txt.att.net
Carolina West Wireless		10digit10digitnumber@cwwsms.com
Cellular One				10digitphonenumber@mobile.celloneusa.com
Illinois Valley Cellular	10digitphonenumber@ivctext.com
Inland Cellular Telephone	10digitphonenumber@inlandlink.com
Sprint						10digitphonenumber@messaging.sprintpcs.com
T-Mobile					10digitphonenumber@tmomail.net
US Cellular					10digitphonenumber@email.uscc.net
Sprint						10digitphonenumber@messaging.sprintpcs.com
T-Mobile					10digitphonenumber@tmomail.net
US Cellular					10digitphonenumber@email.uscc.net
Verizon						10digitphonenumber@vtext.com
Virgin Mobile				10digitphonenumber@vmobl.com

*/

function GetCellEmailAddress($userName){

	require('db_open.php');

	$tsql = "SELECT cellNumber, cellService WHERE userName = $userName";

	try {

		$s = mysqli_query($conn, $tsql);
		$r = mysqli_fetch_assoc($s);
		$empRec = new CarInfo($r);

	} catch(Exception $e) {

		echo "Getting employee $userName cell email address failed. " . $e->getMessage();

	} finally {

		if (strlen($empRec->cellNumber) > 0){

		}

		$conn = null;

	}   // try-catch{}


}


function Cleanup_Text($str){

	$cleanText = '';

	$cleanText	= preg_replace('/[\x00-\x1F\x80-\xFF]/', '',  $str);
	$cleanText	= str_replace("'", "\'",  $cleanText);

	return $cleanText;

}	// Cleanup_Text()


function toProperCase($str){

	$newString = '';

	$newString = strtolower($str);
	$newString = ucwords($newString);

	return $newString;

}	// toProperCase()

function GetDisplayDate($dateStr){

	$displayDate = '';

	if ($dateStr > ''){
		$dateObj            = date_create($dateStr);
		$displayDate    = date_format($dateObj, "m/d/Y");
	}
	return $displayDate;
}


function ComputePartsReceived(&$repairs){

	$unordered = 0;
	$ordered = 0;
	$received = 0;

	foreach($repairs as $repair){    // for each car assigned to an estimator
		foreach($repair->cars as $car){ // get the parts list
			foreach($car->parts as $part){ // get the parts list

				switch(true){

					case ($part->received_quantity == 0) &&
						 ($part->ordered_quantity == 0) &&
						 ($part->ro_quantity > 0):

						++$unordered;
						break;

					case ($part->received_quantity == $part->returned_quantity) &&
						 ($part->returned_quantity > 0):

					case ($part->received_quantity == 0) &&
						 (($part->ordered_quantity > 0) || ($part->ro_quantity > 0)):
						++$ordered;
						break;

					default:
						++$received;
						break;
				}
			}

			$car->parts_unordered   = $unordered;
			$car->parts_waiting     = $ordered;
			$car->parts_received    = $received;

			$totalParts = $unordered + $ordered + $received;

			if ($totalParts == 0){
				$car->parts_percent = 100;
			} else {
				$car->parts_percent = ($received / $totalParts) * 100;
			}

				// reset the counters
			$unordered = 0;
			$ordered = 0;
			$received = 0;
		}
	}
}	// ComputePartsReceived

?>
