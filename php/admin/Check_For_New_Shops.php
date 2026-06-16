<?php

function Check_For_New_Shops($shop_names, $companyID) {

/*
Check unique shop names in the upload.

		- if there is no shop id values in the session variables:
			-> write the shop names in location_ids table.

		- if there is shop ids in the session variable:
			-> fetch the shops from the location_ids table
			-> check these against the shop names found in the extract
				-> if they match, proceed with load
				-> if they don't match, have the user map the old shop names with the new ones 
*/
    require('../db_open.php');

        // Get all the shop names for this company
    $sql = "SELECT shop_name FROM location_ids ".
            "WHERE company_id = '$companyID'";

    $result = mysqli_query($conn, $sql);

    $existing_shops = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $existing_shops[] = $row['shop_name'];
    }

            // If there are no existing shops for this company yet
            // then insert all shop names from the extract file
    if (empty($existing_shops)){

        $insert_sql = "INSERT INTO location_ids " .
                    "(company_id, shop_name) " .
                    "VALUES ";

        $values = '';

        foreach ($new_shops as $shop) {
            $values .= "('$companyID', '$shop'),";
        }

        $insert_sql .= " " . rtrim($values, ',');
        
        if (mysqli_query($conn, $insert_sql)) {

            echo "Initial shops added.<br/>";

        } else {

            echo "Error adding shops: " . mysqli_error($conn) . "<br/>";

        }   // if (mysqli_query($conn, $insert_sql)){
    
    } else {

        echo "Existing shops for company ID $companyID: " . implode(", ", $existing_shops) . "<br/>";
        $new_shops = array_diff($shop_names, $existing_shops);

        if (!empty($new_shops)) {

        } else {
            echo "No new shops to add for company ID: $companyID.<br/>";
        }   // if (!empty($new_shops)){

    }   // if (empty($existing_shops)){
}

?>