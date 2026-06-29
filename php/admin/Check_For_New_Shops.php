<?php

function All_Shops_Match($shop_names, $companyID) {

/*
Check unique shop names in the upload.

		- if there are no existing shops for this company yet:
			-> write the shop names in location_ids table.

		- if there are existing shops for this company:
			-> fetch the shops from the location_ids table
			-> check these against the shop names found in the extract
				-> if they match, proceed with load
				-> if they don't match, have the user map the old shop names with the new ones 
*/

    $all_shops_match = false;

    require('../db_open.php');

        // Get all the shop names for this company
    $sql = "SELECT location FROM location_ids ".
            "WHERE company_id = $companyID";

    $result = mysqli_query($conn, $sql);

    $existing_shops = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $existing_shops[] = $row['location'];
    }
    
            // If there are no existing shops for this company yet
            // then insert all shop names from the extract file
    if (empty($existing_shops)){

        $insert_sql = "INSERT INTO location_ids " .
                    "(company_id, location) " .
                    "VALUES ";

        $values = '';

        foreach ($shop_names as $shop) {
            $values .= "('$companyID', '$shop'),";
        }

        $insert_sql .= " " . rtrim($values, ',');
        
        if (mysqli_query($conn, $insert_sql)) {

            echo "Initial shops added.<br/>";
            $all_shops_match = true;

        } else {

            echo "Error adding shops: " . mysqli_error($conn) . "<br/>";

        }   // if (mysqli_query($conn, $insert_sql)){
    
    } else {

        echo "Existing shops for company ID $companyID: " . implode(", ", $existing_shops) . "<br/>";
        $new_shops = array_diff($shop_names, $existing_shops);

        if (empty($new_shops)) {

            echo "All shops match for company ID: $companyID.<br/>";
            $all_shops_match = true;
        }
    
    }   // if (empty($existing_shops)){

    if ($all_shops_match){
        
        echo "Loading values from the extract table to repairs and parts tables.<br/>";

        $stmt = $conn->prepare("CALL sp_Load_Values_From_Extract_Table(?)");
        $stmt->bind_param("i", $companyID);
        $stmt->execute();

    }   // if($all_shops_match())

    $conn->close();

    return $all_shops_match;

}

?>