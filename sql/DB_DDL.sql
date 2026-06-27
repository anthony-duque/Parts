-- CREATE DATABASE PartsApp_DB;
-- USE PartsApp_DB;


CREATE TABLE `adhoc_table` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='A special table that will hold values that does not belong to any of the other tables.';


CREATE TABLE `car_stage` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ro_num` int unsigned NOT NULL,
  `loc_id` smallint unsigned NOT NULL,
  `stage_id` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Tracks the production stage of cars.';


-- PartsApp_DB.companies definition

CREATE TABLE `companies` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `company_code` varchar(30) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `pass_code` varchar(15) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `account_signup_date` date DEFAULT NULL COMMENT 'When the company signed up for the app.',
  `active_start_date` date DEFAULT NULL COMMENT 'When account was renewed.',
  `active_end_date` date DEFAULT NULL COMMENT 'When account will expire.',
  `contact_person` varchar(30) DEFAULT NULL COMMENT 'Contact Person in the company',
  `email` varchar(60) DEFAULT NULL COMMENT 'Company Email',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Company_UNIQUE` (`company_code`)
);


CREATE TABLE `departments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dept_code` varchar(15) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Department Lookup Table';


CREATE TABLE `Employee_Table` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(15) NOT NULL,
  `firstName` varchar(15) DEFAULT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `cellNumber` bigint DEFAULT NULL,
  `cellService` varchar(20) DEFAULT NULL,
  `deptCode` char(12)  DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `notify` tinyint(1) DEFAULT '1',
  `notif_preference` varchar(10) DEFAULT NULL,
  `locID` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='List of Shop Staff with their name, cell number and departments.';


CREATE TABLE `Location_Table` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `loc_code` varchar(15) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Location Lookup Table';


CREATE TABLE `location_ids` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(50) DEFAULT NULL,
  `company_id` smallint unsigned NOT NULL,
  `last_data_upload` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Location ID for each shop.';


CREATE TABLE `material_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) COMMENT='Material Types Lookup Table';


CREATE TABLE `materials` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `part_number` varchar(15) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `reorder_quantity` tinyint DEFAULT NULL,
  `brand` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`part_number`),
  KEY `type` (`type`),
  CONSTRAINT `materials_type_FK` FOREIGN KEY (`type`) REFERENCES `material_types` (`code`)
) COMMENT='List of materials that technicians can order.';


-- PartsApp_DB.parts_status definition

CREATE TABLE `parts_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `part_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `part_description` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `part_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ro_qty` smallint DEFAULT NULL,
  `vendor_name` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `po_number` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ordered_qty` smallint DEFAULT NULL,
  `expected_delivery` date DEFAULT NULL,
  `received_qty` smallint DEFAULT NULL,
  `returned_qty` smallint DEFAULT NULL,
  `line` smallint DEFAULT NULL,
  `ro_num` varchar(15) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `invoice_date` datetime DEFAULT NULL,
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `loc_id` tinyint unsigned DEFAULT '0',
  `part_status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `repair_code` varchar(20) DEFAULT NULL,
  `part_price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75567 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Dump table for extract file Parts_Status.csv.';

CREATE TABLE `parts_returns` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `ro_num` mediumint unsigned NOT NULL,
  `return_date` date NOT NULL,
  `vendor_pickup_date` date DEFAULT NULL,
  `part_number` varchar(30) NOT NULL,
  `part_description` varchar(100) NOT NULL,
  `part_type` varchar(20) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `invoice_number` varchar(15) NOT NULL,
  `reason` varchar(25) DEFAULT NULL,
  `vendor_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Tracks parts that have been returned to vendors.';


CREATE TABLE `pending_returns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `RO` smallint unsigned NOT NULL,
  `Owner` varchar(50) DEFAULT NULL,
  `Vehicle` varchar(75) DEFAULT NULL,
  `Vendor` varchar(50) NOT NULL,
  `Return_Number` varchar(12) NOT NULL,
  `Pickup_Date` date DEFAULT NULL COMMENT 'Vendor Pickup Date',
  PRIMARY KEY (`id`)
);


-- PartsApp_DB.repairs definition

CREATE TABLE `repairs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ro_num` varchar(15) NOT NULL,
  `owner` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vehicle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vehicle_in` datetime DEFAULT NULL,
  `technician` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `current_phase` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `parts_received` float DEFAULT NULL,
  `estimator` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `vehicle_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `license_plate` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `scheduled_out` datetime DEFAULT NULL,
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `loc_id` tinyint unsigned DEFAULT '0' COMMENT 'Location ID',
  `insurance` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `vin` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3263 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='List of active vehicles (pre-ordered and cars in shop).';


CREATE TABLE `scheduled_in_vin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `Scheduled_In` date DEFAULT NULL COMMENT 'Scheduled In Date',
  `RO_Num` mediumint unsigned NOT NULL COMMENT 'Repair Order Number',
  `VIN` varchar(17) DEFAULT NULL COMMENT 'Vehicle Identification Number',
  `RO_Hours` float DEFAULT '0' COMMENT 'Number of hours to fix the vehicle.',
  `Assigned_Hours` float DEFAULT '0' COMMENT 'Number of assigned hours.',
  `Estimate_Amt` float DEFAULT '0' COMMENT 'Estimate amount the vehicle repair would cost.',
  `Total_Loss` tinyint(1) DEFAULT '0',
  `Location` varchar(50) DEFAULT NULL COMMENT 'Shop Location',
  `Loc_ID` smallint unsigned DEFAULT '0' COMMENT 'Location ID set depending on the value in Location_ID table.',
  PRIMARY KEY (`id`)
) COMMENT='Scheduled In, VIN, RO Hours, Assigned Hours, Estimate Amount, Total Loss';


CREATE TABLE `stage_headings` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `Description` varchar(50) NOT NULL,
  `Order_no` tinyint NOT NULL,
  `Loc_ID` tinyint unsigned NOT NULL COMMENT 'Location ID',
  PRIMARY KEY (`id`)
) COMMENT='Production Stage Headings for each location.';


CREATE TABLE `Tech_Car_Priority` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `Technician` char(15) NOT NULL,
  `RO_Num` int unsigned NOT NULL,
  `Priority` tinyint unsigned NOT NULL,
  `LocationID` tinyint unsigned NOT NULL,
  `Dept_Code` varchar(12) NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Tracks the priority of cars assigned to each technician.';


CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `oem` tinyint(1) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `email` varchar(35) DEFAULT NULL,
  `location_ID` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'Shop ID depending on Location table.',
  `shop_location` varchar(50) DEFAULT NULL COMMENT 'Shop Location',
  `opt_oem` tinyint(1) DEFAULT '0' COMMENT 'Does vendor sell Opt OEM parts?',
  `aftermarket` tinyint(1) DEFAULT NULL COMMENT 'Does vendor sell aftermarket parts?',
  `preferred` tinyint(1) DEFAULT '0' COMMENT 'Is this a preferred vendor?',
  `electronic` tinyint(1) DEFAULT '0' COMMENT 'Can we order electronically to this vendor?',
  `vendor_ID` mediumint unsigned DEFAULT '0' COMMENT 'Vendor ID assigned by CCC One',
  PRIMARY KEY (`id`)
) COMMENT='Lists current vendor info including phone, address, and email address.';


CREATE TABLE `cell_email_lookup` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cellName` varchar(12) NOT NULL,
  `emailFormat` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='A lookup table that lists the equivalent email address of a cell service.';