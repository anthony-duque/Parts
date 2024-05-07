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
		$displayDate    = date_format($dateObj, "M j g:i A");
	}
	return $displayDate;
}


?>
