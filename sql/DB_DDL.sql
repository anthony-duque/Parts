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
  `company_code` varchar(30) NOT NULL COMMENT 'Company Code',
  `address` varchar(100) DEFAULT NULL COMMENT 'Company Address',
  `name` varchar(50) NOT NULL COMMENT 'Company Name',
  `pass_code` varchar(15) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL COMMENT 'Company Phone Number',
  `account_start_date` date DEFAULT NULL COMMENT 'Active Start Date',
  `account_end_date` date DEFAULT NULL COMMENT 'Account End Date',
  `contact_person` varchar(30) DEFAULT NULL COMMENT 'Contact Person in the company',
  `email` varchar(60) DEFAULT NULL COMMENT 'Company Email',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Company_UNIQUE` (`company_code`)
);


CREATE TABLE `company_shop` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `company_code` varchar(25) NOT NULL,
  `location_code` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='A table that links which shops belong to which companies.';


CREATE TABLE `departments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dept_code` varchar(15) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Department Lookup Table';


CREATE TABLE `Employee_Table` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `firstName` varchar(15) DEFAULT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `cellNumber` bigint DEFAULT NULL,
  `cellService` varchar(20) DEFAULT NULL,
  `deptCode` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
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
  `loc_code` varchar(15) NULL,
  `Location` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Location ID for each shop.';


CREATE TABLE `material_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `Code` varchar(10) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Code` (`Code`)
) COMMENT='Material Types Lookup Table';


CREATE TABLE `materials` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `Part_Number` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Description` varchar(30) DEFAULT NULL,
  `Unit` varchar(10) DEFAULT NULL,
  `Type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Reorder_Quantity` tinyint DEFAULT NULL,
  `Brand` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Code` (`Part_Number`),
  KEY `Type` (`Type`),
  CONSTRAINT `Materials_ibfk_1` FOREIGN KEY (`Type`) REFERENCES `Material_Types` (`Code`)
) COMMENT='List of materials that technicians can order.';


CREATE TABLE `parts_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `Part_Number` varchar(30) DEFAULT NULL,
  `Part_Description` varchar(75) DEFAULT NULL,
  `Part_Type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `RO_Qty` smallint DEFAULT NULL,
  `Vendor_Name` varchar(75) DEFAULT NULL,
  `PO_Number` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Ordered_Qty` smallint DEFAULT NULL,
  `Expected_Delivery` date DEFAULT NULL,
  `Received_Qty` smallint DEFAULT NULL,
  `Returned_Qty` smallint DEFAULT NULL,
  `Line` smallint DEFAULT NULL,
  `RO_Num` int DEFAULT NULL,
  `Order_Date` datetime DEFAULT NULL,
  `Invoice_Date` datetime DEFAULT NULL,
  `Location` varchar(50) DEFAULT NULL,
  `Loc_ID` tinyint unsigned DEFAULT '0',
  `Part_Status` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Dump table for extract file Parts_Status.csv.';


CREATE TABLE `parts_returns` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `RO_Num` mediumint unsigned NOT NULL,
  `Return_Date` date NOT NULL,
  `Vendor_Pickup_Date` date DEFAULT NULL,
  `Part_Number` varchar(30) NOT NULL,
  `Part_Description` varchar(100) NOT NULL,
  `Part_Type` varchar(20) DEFAULT NULL,
  `Amount` float DEFAULT NULL,
  `Invoice_Number` varchar(15) NOT NULL,
  `Reason` varchar(25) DEFAULT NULL,
  `Vendor_Name` varchar(50) NOT NULL,
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


CREATE TABLE `Repairs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `RONum` mediumint unsigned NOT NULL,
  `Owner` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Vehicle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Vehicle_In` datetime DEFAULT NULL,
  `Technician` varchar(30) DEFAULT NULL,
  `CurrentPhase` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `PartsReceived` float DEFAULT NULL,
  `Estimator` varchar(30) DEFAULT NULL,
  `Vehicle_Color` varchar(30) DEFAULT NULL,
  `License_Plate` varchar(12) DEFAULT NULL,
  `Scheduled_Out` datetime DEFAULT NULL,
  `Location` varchar(50) DEFAULT NULL,
  `Loc_ID` tinyint unsigned DEFAULT '0' COMMENT 'Location ID',
  `Stage_ID` int DEFAULT NULL,
  `Insurance` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='List of active vehicles (pre-ordered and cars in shop).';


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
  `Technician` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
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
  `shop_location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Shop Location',
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