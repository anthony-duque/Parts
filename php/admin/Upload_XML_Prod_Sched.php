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
    }   // class Repairs{}

?>