<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require('../Utility_Scripts.php');

    const TARGET_DIR    = "../../extract_files/";  // destination folder on the server
    const P_STATUS_FNAME = "Production Schedule_Report.xml";      // Production Schedule destination file name

    $company_ID = $_GET["companyID"];       // set the company cookie

    class Shop{

        public $location_id;
        public $name;

        function __construct($id, $name){
            $this->location_id = $id;
            $this->name = $name;
        }

    }   // class Shop{}


    class Repairs{

        public $company_id;
        public $insert_sql;
        public $insertedCount;
        public $shops;

        function __construct($companyID, $sql, $count, $shopList){
   
            $this->company_id = $companyID;
            $this->insert_sql = $sql;
            $this->insertedCount = $count;
            $this->shops = $shopList;
        }
    }   // class Repairs{}

    /*   Main Routine  */

    $shops = [];

    $repairs = Get_Repairs_From_XML($company_ID);

    Check_Shop_List_Against_DB($repairs);

    Write_Repairs_To_Database($repairs);

    /*  End of Main Routine  */

    function Check_Shop_List_Against_DB($repairs) {

        require('../db_open.php');

        foreach ($repairs->shops as $shop) {

            $location_id = $shop->location_id;
            $name = $shop->name;

            $tsql = "SELECT id FROM shops WHERE location_id = '$location_id' AND company_id = $repairs->company_id";

            $result = $conn->query($tsql);

            if ($result->num_rows == 0) {

                    // Insert the shop into the shops table
                $insert_sql = "INSERT INTO shops " . 
                                "(location_id, name, company_id) " . 
                            "VALUES ('$location_id', '$name', $repairs->company_id)";

                $conn->query($insert_sql);

                echo "<br/>Shop with location_id: $location_id and name: $name added to the database.";
            }

        }   // foreach ($shops as $shop)

        $conn->close();

    }   //  Check_Shop_List_Against_DB($company_ID, $shops)


    function Get_Repairs_From_XML($company_ID) {
    
        $extractFile = TARGET_DIR . P_STATUS_FNAME;

            // Load the XML file
        $xml = simplexml_load_file($extractFile);

        if ($xml === false) {
            die("Error: Failed to load or parse the Production Schedule XML file.");
        }

        $insertedCount = 0;
        $values = '';

        $location_ids = [];
        $shops = [];

        $tsql = <<<strSQL
                    INSERT INTO repairs
                        (ro_num, owner, vehicle, vehicle_color, license_plate,
                        parts_received, vehicle_in, current_phase, scheduled_out,
                        technician, estimator, insurance, company_id, location_id)
                    VALUES
        strSQL;

            // Loop through the XML and execute the insertion
        foreach ($xml->data->repairOrder as $ro) {

            if (empty($ro->repair_order_number)) {
                continue;
            }
            //echo "<br/>Processing Repair Order: " . $ro->repair_order_number;

            $ro_num         = Cleanup_Text($ro->repair_order_number);
            
            $owner          = Cleanup_Text($ro->owner_name);

            $vehicle        = Cleanup_Text($ro->vehicle_year_make_model);

            $vehicle_color  = Cleanup_Text($ro->vehicle_exterior_paint_color);

            $license_plate 	= Cleanup_Text($ro->vehicle_license_number);

            if (empty($ro->parts_received_percent)) {
                $parts_received = 0;
            } else {
                $parts_received = $ro->parts_received_percent;
            }

            $vehicle_in     = Get_SQL_date($ro->vehicle_in_datetime);

            $current_phase  = Cleanup_Text($ro->repair_phase_name);

            $scheduled_out	= Get_SQL_date($ro->vehicle_out_datetime);

            $technician     = Cleanup_Text($ro->body_technician_display_name);

            $estimator      = Cleanup_Text($ro->service_writer_display_name);

            $name           = Cleanup_Text($ro->repair_facility_name);

            if (empty($ro->repair_facility_number)) {
                $location_id     = Cleanup_Text($ro->franchise_id);
            } else {
                $location_id     = Cleanup_Text($ro->repair_facility_number);
            }

            if (!in_array($location_id, $location_ids)) {
                $location_ids[] = $location_id;
                $shops[] = new Shop($location_id, $name);
            }

            $insurance      = Cleanup_Text($ro->master_carrier_name);

            $values .= "('" . $ro_num . "', '" . $owner . "', '" . $vehicle . "', '" . $vehicle_color  . "', " .
                        "'" . $license_plate . "', " . $parts_received . ", " . $vehicle_in . ", " .
                        "'" . $current_phase . "', " . $scheduled_out . ", '" . $technician . "', " .
                        "'" . $estimator . "', '" . $insurance . "', " . $company_ID . ", '" . $location_id . "'),";

            $insertedCount++;

        }   // foreach ($xml->data->repairOrder as $ro)

        $values = rtrim($values, ',');
        $insert_stmt = $tsql . $values;

        return new Repairs($company_ID, $insert_stmt, $insertedCount, $shops);

    }   // function Get_Repairs_From_XML($company_ID, &$shops)


    function Write_Repairs_To_Database($repairs) {

        require('../db_open.php');

            // Delete all records in Repairs table
        $tsql = "DELETE FROM repairs" . 
                " WHERE company_id = $repairs->company_id" .
                "   AND location_id IN" . 
                "   (SELECT location_id " . 
                "    FROM shops " .
                "    WHERE company_id = $repairs->company_id);";

        if ($conn->query($tsql) === TRUE) {

            echo "<br/><br/>Company-related repair records cleared.<br/>";

        }else{

            echo "Error: " . $tsql . "<br> - " . $conn->error;
            exit;

        }  // if ($conn->query($tsql) === TRUE)	

        
        if ($conn->query($repairs->insert_sql) === TRUE){

            // echo "<br/>$repairs->insert_sql<br/>";
            if ($conn->query($repairs->insert_sql) === TRUE) {
                echo "Successfully inserted {$repairs->insertedCount} repair records into the database.";
            } else {
                echo "Error: " . $repairs->insert_sql . "<br>" . $conn->error;
                exit;
            }
        }   // if ($conn->query($repairs->insert_sql) === TRUE)

        $conn = null;

    }   // function Write_Repairs_To_Database($insert_sql, $insertedCount)
?>