CREATE DATABASE PartsApp_DB;
USE PartsApp_DB;


CREATE TABLE `Adhoc_Table` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='A special table that will hold values that does not belong to any of the other tables.';


CREATE TABLE `Car_Stage` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ro_Num` int unsigned NOT NULL,
  `loc_ID` smallint unsigned NOT NULL,
  `stage_ID` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Tracks the production stage of cars.';


CREATE TABLE `Department_Table` (
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


CREATE TABLE `Location_IDs` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `loc_code` varchar(15) NULL,
  `Location` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Location ID for each shop.';


CREATE TABLE `Material_Types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `Code` varchar(10) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Code` (`Code`)
) COMMENT='Material Types Lookup Table';


CREATE TABLE `Materials` (
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


CREATE TABLE `PartsStatusExtract` (
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


CREATE TABLE `Parts_Returns` (
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


CREATE TABLE `Pending_Returns` (
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


CREATE TABLE `Stage_Headings` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `Description` varchar(50) NOT NULL,
  `Order_no` tinyint NOT NULL,
  `Loc_ID` tinyint unsigned NOT NULL COMMENT 'Location ID',
  PRIMARY KEY (`id`)
) COMMENT='Production Stage Headings for each location.';


CREATE TABLE `Sublet_Status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `RO_Num` mediumint unsigned NOT NULL,
  `Loc_ID` tinyint unsigned NOT NULL,
  `Procedure` varchar(50) NOT NULL,
  `Vendor` varchar(50) DEFAULT NULL,
  `Status` tinyint(1) NOT NULL COMMENT 'Is the procedure done or not. Done = TRUE, Not Done = FALSE',
  PRIMARY KEY (`id`)
) COMMENT='Tracks the status of sublet procedures for each vehicle.';


CREATE TABLE `Tech_Car_Priority` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `Technician` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `RO_Num` int unsigned NOT NULL,
  `Priority` tinyint unsigned NOT NULL,
  `LocationID` tinyint unsigned NOT NULL,
  `Dept_Code` varchar(12) NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Tracks the priority of cars assigned to each technician.';


CREATE TABLE `Vendors` (
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