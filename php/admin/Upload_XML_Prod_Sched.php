<?php

/////////////////////////////////////////////////////////////////

    class Shop{
    
        public $company_id;
        public $shop_id;
        public $location_id;
        public $name;

        function __construct($id, $name, $companyID){
            $this->company_id = $companyID;
            $this->location_id = $id;
            $this->name = $name;
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


    function Check_Shop_List_Against_DB($shops) {

        require('../db_open.php');

        foreach ($shops as $shop) {

            $location_id = $shop->location_id;
            $name = $shop->name;

            $tsql = "SELECT id FROM shops WHERE location_id = '$location_id' AND company_id = $shop->company_id";

            $result = $conn->query($tsql);

            if ($result->num_rows == 0) {

                    // Insert the shop into the shops table
                $insert_sql = "INSERT INTO shops " . 
                                "(location_id, name, company_id) " . 
                            "VALUES ('$location_id', '$name', $shop->company_id)";

                $conn->query($insert_sql);
                $shop->shop_id = $conn->insert_id;

                echo "<br/>Shop with location_id: $location_id and name: $name added to the database.";
            } else {

                $row = $result->fetch_assoc();
                $shop->shop_id = $row['id'];

            }   // if ($result->num_rows == 0)

        }   // foreach ($shops as $shop)

        $conn->close();

        return $shops;

    }   //  Check_Shop_List_Against_DB($company_ID, $shops)


    function Get_Repairs_From_XML($company_ID, $repairsFile) {
    
            // Load the XML file
        $xml = simplexml_load_file($repairsFile);

        if ($xml === false) {
            die("Error: Failed to load or parse the Production Schedule XML file.");
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


    function Repairs_Insert_SQL($repairs_object, $shopList) {

        $tsql = <<<strSQL
                    INSERT INTO repairs
                        (ro_num, owner, vehicle, vehicle_color, license_plate,
                        parts_received, vehicle_in, current_phase, scheduled_out,
                        technician, estimator, insurance, shop_id)
                    VALUES
        strSQL;

        $values = '';

        foreach ($repairs_object->repairs as $repair) {

                // find the shop_id for the repair's location_id in the shopList
            $shopFound = array_search($repair->location_id, array_column($shopList, 'location_id'));

            $shop_id = $shopFound !== false ? $shopList[$shopFound]->shop_id : null;

            if ($shop_id > 0){

                $values .= "('" . $repair->ro_num . "', '" . $repair->owner . "', '" . $repair->vehicle . "', '" . $repair->vehicle_color  . "', " .
                            "'" . $repair->license_plate . "', " . $repair->parts_received . ", " . $repair->vehicle_in . ", " .
                            "'" . $repair->current_phase . "', " . $repair->scheduled_out . ", '" . $repair->technician . "', " .
                            "'" . $repair->estimator . "', '" . $repair->insurance . "', " . $shop_id . "),";
            }   // if ($shop_id > 0)

        }   // foreach ($repairs as $repair)

        return rtrim($tsql . $values, ',');

    }   // function Form_Insert_SQL($repairs, $shopList)


    function Delete_Old_Repair_Records($repairs){

        require('../db_open.php');

            // Delete all records in Repairs table
        $tsql = "DELETE FROM repairs" . 
                " WHERE shop_id IN" . 
                "   (SELECT id " . 
                "    FROM shops " .
                "    WHERE company_id = $repairs->company_id);";

        if ($conn->query($tsql) === TRUE) {

            echo "<br/><br/>Company-related repair records cleared.<br/>";

        }else{

            echo "Error: " . $tsql . "<br> - " . $conn->error;
            exit;

        }  // if ($conn->query($tsql) === TRUE)	

        $conn = null;

    }   // function Delete_Old_Repair_Records()


    function Write_Repairs_To_Database($repairs, $shop_list) {

        require('../db_open.php');

            // Delete all records in Repairs table for the company
        $tsql = Repairs_Insert_SQL($repairs, $shop_list);
//        echo "<br/>$tsql<br/>";

        if ($conn->query($tsql) === TRUE) {
            echo "Successfully inserted $conn->affected_rows repair records into the database.";
        } else {
            echo "Error: " . $tsql . "<br>" . $conn->error;
            exit;
        }

        $conn = null;

    }   // function Write_Repairs_To_Database($insert_sql, $insertedCount)
?>