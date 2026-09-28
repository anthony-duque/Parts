ALTER TABLE PartsApp_DB.repairs MODIFY COLUMN loc_id MEDIUMINT UNSIGNED DEFAULT 0 NULL COMMENT 'Location ID';
ALTER TABLE PartsApp_DB.parts_status MODIFY COLUMN loc_id MEDIUMINT UNSIGNED DEFAULT 0 NULL;
ALTER TABLE PartsApp_DB.locations ADD loc_id MEDIUMINT UNSIGNED NOT NULL COMMENT 'Equivalent to CCC One''s repair_facility_number.';
ALTER TABLE PartsApp_DB.shops CHANGE loc_id shop_id VARCHAR(15) NOT NULL COMMENT 'Equivalent to CCC One''s repair_facility_number.';
ALTER TABLE PartsApp_DB.shops MODIFY COLUMN shop_id VARCHAR(15) NOT NULL COMMENT 'Equivalent to CCC One''s repair_facility_number.';
ALTER TABLE PartsApp_DB.shops CHANGE location_id location_id varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Equivalent to CCC One''s repair_facility_number.';
ALTER TABLE PartsApp_DB.parts_status CHANGE location location_id varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL;
ALTER TABLE PartsApp_DB.parts_status MODIFY COLUMN location_id varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL;
ALTER TABLE PartsApp_DB.parts_status CHANGE loc_id company_id mediumint unsigned DEFAULT 0 NULL;
ALTER TABLE PartsApp_DB.parts_status CHANGE location location_id varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL;
ALTER TABLE PartsApp_DB.parts_status MODIFY COLUMN location_id varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL;
ALTER TABLE PartsApp_DB.parts_status MODIFY COLUMN invoice_date DATE NULL;
ALTER TABLE PartsApp_DB.repairs MODIFY COLUMN id MEDIUMINT UNSIGNED auto_increment NOT NULL;
ALTER TABLE PartsApp_DB.repairs DROP COLUMN location_id;
ALTER TABLE PartsApp_DB.parts_status DROP COLUMN location_id;
ALTER TABLE PartsApp_DB.parts_status CHANGE company_id repair_id INTEGER UNSIGNED DEFAULT 0 NULL;

ALTER TABLE parts_status
ADD CONSTRAINT fk_parts_status_repair_id
FOREIGN KEY (repair_id)
REFERENCES repairs(id)
ON DELETE CASCADE;

ALTER TABLE repairs
ADD CONSTRAINT fk_repairs_shop_id
FOREIGN KEY (shop_id)
REFERENCES shops(id)
ON DELETE CASCADE;

ALTER TABLE shops
ADD CONSTRAINT fk_shops_company_id
FOREIGN KEY (company_id)
REFERENCES companies(id)
ON DELETE CASCADE;
