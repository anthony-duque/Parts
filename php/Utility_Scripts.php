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

?>
