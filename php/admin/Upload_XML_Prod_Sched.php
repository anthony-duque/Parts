<?php

    class Shop{
    
        public $shop_id;        // determined in Check_Shop_List_Against_DB() function
        public $company_id;
        public $location_id;
        public $name;

        function __construct($id, $name, $companyID){
            $this->company_id   = $companyID;
            $this->location_id  = $id;
            $this->name         = $name;
        }
    }   // class Shop{}


    class Repairs{

        public $company_id;
        public $repairs;
        public $shops;

        function __construct($companyID, $repairList, $shopList){
   
            $this->company_id = $companyID;
            $this->repairs = $repairList;
            $this->shops = $shopList;
        }
    }   // class Repairs{}


    class Repair{

        public $shop_id;
        public $location_id;
        public $shop_name;
        public $ro_num;
        public $owner;
        public $vehicle;
        public $vehicle_color;
        public $license_plate;
        public $parts_received;
        public $vehicle_in;
        public $current_phase;
        public $scheduled_out;
        public $technician;
        public $estimator;
        public $insurance;

        function __construct($ro){

                // use franchise_id if repair_facility_number is empty, otherwise use repair_facility_number
            if (empty($ro->repair_facility_number)) {
                $this->location_id     = Cleanup_Text($ro->franchise_id);
            } else {
                $this->location_id     = Cleanup_Text($ro->repair_facility_number);
            }

            $this->shop_name        = Cleanup_Text($ro->repair_facility_name);

            $this->ro_num           = Cleanup_Text($ro->repair_order_number);
            $this->owner            = Cleanup_Text($ro->owner_name);
            $this->vehicle          = Cleanup_Text($ro->vehicle_year_make_model);
            $this->vehicle_color    = Cleanup_Text($ro->vehicle_exterior_paint_color);
            $this->license_plate    = Cleanup_Text($ro->vehicle_license_number);

            if (empty($ro->parts_received_percent)) {
                $this->parts_received = 0;
            } else {
                $this->parts_received = $ro->parts_received_percent;
            }

            $this->vehicle_in           = Get_SQL_date($ro->vehicle_in_datetime);
            $this->current_phase        = Cleanup_Text($ro->repair_phase_name);
            $this->scheduled_out        = Get_SQL_date($ro->vehicle_out_datetime);
            $this->technician           = Cleanup_Text($ro->body_technician_display_name);
            $this->estimator            = Cleanup_Text($ro->service_writer_display_name);
            $this->insurance            = Cleanup_Text($ro->master_carrier_name);

        }   // function __construct($ro_num, ...)
    }   // class repairOrderLine{}


    function Check_Shop_List_Against_DB($shops, $db_conn) {

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


    function Populate_Shop_IDs($repair_list, $shop_lookup)
    {
        foreach($repair_list->repairs as $repair)
        {
            $shopFound = array_search($repair->location_id, array_column($shop_lookup, 'location_id'));

            $shopID = $shopFound !== false ? $shop_lookup[$shopFound]->shop_id : null;

            $repair->shop_id = $shopID;
        }

        return $repair_list->repairs;
    }


    function Display_Repairs_List($repList)
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


    function Get_Repairs_From_XML($company_ID, $repairsFile) {
    
            // Load the XML file
        $xml = simplexml_load_file($repairsFile);

        if ($xml === false) {
            die("<br/>Error: Failed to load or parse the Production Schedule XML file.");
        }

        $location_ids = [];
        $shops = [];
        $repairs = [];

            // Loop through the XML and execute the insertion
        foreach ($xml->data->repairOrder as $ro) {

            if (empty($ro->repair_order_number)) {
                continue;
            }
            //echo "<br/>Processing Repair Order: " . $ro->repair_order_number;

            $repair = new Repair($ro);

            if (!in_array($repair->location_id, $location_ids)) {
                $location_ids[] = $repair->location_id;
                $shops[] = new Shop($repair->location_id, $repair->shop_name, $company_ID);
            }

            $repairs[] = $repair;

        }   // foreach ($xml->data->repairOrder as $ro)

        return new Repairs($company_ID, $repairs, $shops);

    }   // function Get_Repairs_From_XML($company_ID, &$shops)



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

    function Delete_Old_Repair_Records($repairsObj, $db_conn, $repairsOldList)
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


    function Perform_UPSERT_on_new_load($repsObj, $shopList, $db_conn)
    {
        $insert_sql = <<<strSQL

                INSERT INTO repairs
                    (shop_id, ro_num, owner, vehicle, vehicle_color,
                    license_plate, parts_received, vehicle_in, current_phase,
                    scheduled_out, technician, estimator, insurance)
                VALUES
        strSQL;

        $strValues = '';

        foreach($repsObj->repairs as $r)
        {
                // look up the shop_id for this repair from the shop lookup list
            $shopFound = array_search($r->location_id, array_column($shopList, 'location_id'));

            $shop_id = $shopFound !== false ? $shopList[$shopFound]->shop_id : null;

            $strValues .= <<<strSQL
                            ($r->shop_id, '$r->ro_num', '$r->owner', '$r->vehicle', '$r->vehicle_color', 
                            '$r->license_plate', $r->parts_received, $r->vehicle_in, '$r->current_phase', 
                            $r->scheduled_out, '$r->technician', '$r->estimator', '$r->insurance'),
                        strSQL;       
        }
        
        $strValues = rtrim($strValues, ',') . " AS new_repair ";  // Remove the trailing comma

        $upsert_sql = $insert_sql . $strValues . " ON DUPLICATE KEY UPDATE " .
                        "shop_id = new_repair.shop_id, " .
                        "ro_num = new_repair.ro_num, " .
                        "owner = new_repair.owner, " .
                        "vehicle = new_repair.vehicle, " .
                        "vehicle_color = new_repair.vehicle_color, " .
                        "license_plate = new_repair.license_plate, " .
                        "parts_received = new_repair.parts_received, " .
                        "vehicle_in = new_repair.vehicle_in, " .
                        "current_phase = new_repair.current_phase, " .
                        "scheduled_out = new_repair.scheduled_out, " .
                        "technician = new_repair.technician, " .
                        "estimator = new_repair.estimator, " .
                        "insurance = new_repair.insurance;";

//        echo "<br/>$upsert_sql<br/>";

        if ($db_conn->query($upsert_sql) === TRUE) {

            echo "<br/>Repair records inserted/updated successfully.<br/>";

        }else{

            echo "<br/>Error: " . $upsert_sql . "<br> - " . $db_conn->error;
            exit;

        }  // if ($db_conn->query($tsql) === TRUE)	

    }   // function Perform_UPSERT_on_new_load($repsObj, $shopList, $db_conn)

?>