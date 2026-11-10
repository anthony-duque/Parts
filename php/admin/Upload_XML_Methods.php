<?php

class Upload_XML_Methods {


        //   Delete repairs from the database that is 
        // not on the new load.
        //
        //      1) Get the list of repairs in the database
        //          - if nothing was fetched, therefore this is a new load.
        //              just write all the new records to the database and you're done.

        //         if records were fetched:

        //      2) Compare the repairs on the database with the new load
        //          - If a repair on the database doesn't exist in the new load 
        //              delete it.
        //          - If it exists, update it.

    public static function Delete_Old_Repair_Records($repairsObj, $db_conn, $repairsOldList)
    {
        $rep_IDs_to_delete = [];     // Will contain the repair id's to be deleted from the database

            // Cycle through the repairs found in the database
        foreach($repairsOldList as $rep_in_db)
        {
            $repID = $rep_in_db["id"];
            $locID = $rep_in_db["location_id"];
            $roNum = $rep_in_db["ro_num"];

            $foundRepair = array_filter(        // check if this repair is still in the new load
                                $repairsObj->repairs, 
                                function($r) use ($locID, $roNum){
                                            return ($r->location_id == $locID && $r->ro_num == $roNum);
                                }
                            );

            if (!$foundRepair)   // repair in db not found in the new load
            {
                $rep_IDs_to_delete[] = $repID;      // add the repair id to the list to be deleted
            }
        }

        if (count($rep_IDs_to_delete) > 0)
        {

            $deleted_rep_ids = implode(',', $rep_IDs_to_delete);
            $sql = "DELETE FROM repairs WHERE id IN (" . $deleted_rep_ids . ")";
            $db_conn->query($sql);
            echo "Old repairs deleted from the database: $deleted_rep_ids";
        }

    }   // function Delete_Old_Repair_Records()


    public static function Check_Shop_List_Against_DB($shops, $db_conn) {

        foreach ($shops as $shop) {

            $location_id = $shop->location_id;
            $name = $shop->name;

            $tsql = "SELECT id FROM shops WHERE location_id = '$location_id' AND company_id = $shop->company_id";

            $result = $db_conn->query($tsql);

            if ($result->num_rows == 0) {

                    // Insert the shop into the shops table
                $insert_sql = "INSERT INTO shops " . 
                                "(location_id, name, company_id) " . 
                            "VALUES ('$location_id', '$name', $shop->company_id)";

                $db_conn->query($insert_sql);
                $shop->shop_id = $db_conn->insert_id;   // insert the primary key generated for the new shop

                echo "<br/>Shop with location_id: $location_id and name: $name added to the database.";
                    // for future development: email admin that a new shop was added to this company
            } else {

                $row = $result->fetch_assoc();
                $shop->shop_id = $row['id'];

            }   // if ($result->num_rows == 0)

        }   // foreach ($shops as $shop)

        return $shops;

    }   //  Check_Shop_List_Against_DB($company_ID, $shops)


    public static function Display_Repairs_List($repList)
    {
        $header =   "<br/>" .
                    "<table border=1>" .
                    "<thead>" .
                    "  <tr>" .
                    "      <th>Shop ID</th>" .
                    "      <th>Location ID</th>" .
                    "      <th>Shop name</th>" .
                    "      <th>RO Number</th>" .
                    "      <th>Owner</th>" .
                    "      <th>Vehicle</th>" .
                    "  </tr>" .
                    "</thead>";
        echo $header;

        foreach($repList as $r)
        {
            $repairRow = 
                "  <tr>" .
                "      <td>$r->shop_id</td>" .
                "      <td>$r->location_id</td>" .
                "      <td>$r->shop_name</td>" .
                "      <td>$r->ro_num</td>" .
                "      <td>$r->owner</td>" .
                "      <td>$r->vehicle</td>" .
                "  </tr>";
            echo $repairRow;
        }

    }   // function Display_Repairs_List()


    public static function Debug_Load_Error($filePath){
        
            // Enable internal error handling
        libxml_use_internal_errors(true);

        // Attempt to load your second XML file
        $xml2 = simplexml_load_file($filePath);

        if ($xml2 === false) {
            echo "<br/><strong>Failed loading the XML file: " . basename($filePath) . "</strong><br>";
            foreach (libxml_get_errors() as $error) {
                echo "<br/>Error on line {$error->line}, column {$error->column}: {$error->message}<br>";
            }
            libxml_clear_errors();
        } else {
            echo "<br/>XML file " . basename($filePath) . " loaded successfully!";
        }
    }


    public static function Populate_Shop_IDs($repair_list, $shop_lookup)
    {
        foreach($repair_list->repairs as $repair)
        {
            $shopFound = array_search($repair->location_id, array_column($shop_lookup, 'location_id'));

            $shopID = $shopFound !== false ? $shop_lookup[$shopFound]->shop_id : null;

            $repair->shop_id = $shopID;
        }

        return $repair_list->repairs;
    }

}   // class Upload_XML_Methods{}

?>