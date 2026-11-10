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

            $this->vehicle_in       = Get_SQL_date($ro->vehicle_in_datetime);
            $this->current_phase    = Cleanup_Text($ro->repair_phase_name);
            $this->scheduled_out    = Get_SQL_date($ro->vehicle_out_datetime);
            $this->technician       = Cleanup_Text($ro->body_technician_display_name);
            $this->estimator        = Cleanup_Text($ro->service_writer_display_name);
            $this->insurance        = Cleanup_Text($ro->master_carrier_name);

        }   // function __construct($ro_num, ...)
    }   // class repairOrderLine{}


    class Repairs{

        public $company_id;
        public $repairs = [];
        public $shops = [];

        function __construct($companyID, $repairsFile){
   
                // Load the XML file
            $xml = simplexml_load_file($repairsFile);

            if ($xml === false) {
                die("<br/>Error: Failed to load or parse the Production Schedule XML file.");
            }

            $location_ids = [];

                // Loop through the XML and execute the insertion
            foreach ($xml->data->repairOrder as $ro) {

                if (empty($ro->repair_order_number)) {
                    continue;
                }
                //echo "<br/>Processing Repair Order: " . $ro->repair_order_number;

                $repair = new Repair($ro);

                if (!in_array($repair->location_id, $location_ids)) {
                    $location_ids[] = $repair->location_id;
                    $this->shops[] = new Shop($repair->location_id, $repair->shop_name, $companyID);
                }

                $this->repairs[] = $repair;

            }   // foreach ($xml->data->repairOrder as $ro)

            $this->company_id = $companyID;
        }


        public function Perform_UPSERT_on_new_load($shopList, $db_conn){

            $insert_sql = <<<strSQL

                    INSERT INTO repairs
                        (shop_id, ro_num, owner, vehicle, vehicle_color,
                        license_plate, parts_received, vehicle_in, current_phase,
                        scheduled_out, technician, estimator, insurance)
                    VALUES
            strSQL;

            $strValues = '';

            foreach($this->repairs as $r)
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

        }   // function Perform_UPSERT_on_new_load($this, $shopList, $db_conn)
        
    }   // class Repairs{}

?>