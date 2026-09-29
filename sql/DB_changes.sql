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

ALTER TABLE PartsApp_DB.stage_headings MODIFY COLUMN description varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;
ALTER TABLE PartsApp_DB.stage_headings MODIFY COLUMN id TINYINT UNSIGNED auto_increment NOT NULL;
ALTER TABLE PartsApp_DB.stage_headings DROP COLUMN loc_id;
ALTER TABLE PartsApp_DB.stage_headings MODIFY COLUMN description varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;
ALTER TABLE PartsApp_DB.stage_headings DROP COLUMN order_no;

CREATE TABLE PartsApp_DB.company_stages (
	id SMALLINT UNSIGNED auto_increment NOT NULL,
	sequence_num TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT 'It indicates where this stage places in the production process sequence.',
	stage_heading_id TINYINT UNSIGNED NOT NULL COMMENT 'Foreign key that ties with the stage headings lookup table.',
	company_id SMALLINT UNSIGNED NOT NULL COMMENT 'Foreign key field that ties for which company this stage heading belongs to.',
	CONSTRAINT company_stages_pk PRIMARY KEY (id)
)

ALTER TABLE company_stages
ADD CONSTRAINT fk_company_stages_company_id
FOREIGN KEY (company_id)
REFERENCES companies(id)
ON DELETE CASCADE;

ALTER TABLE company_stages
ADD CONSTRAINT fk_company_stages_company_id
FOREIGN KEY (company_id)
REFERENCES companies(id)
ON DELETE CASCADE;


CREATE TABLE PartsApp_DB.repair_stage (
	id INTEGER UNSIGNED auto_increment NOT NULL,
	repair_id INT UNSIGNED NOT NULL COMMENT 'Foreign key that ties a vehicle to it''s stage.',
	stage_heading_id TINYINT UNSIGNED NULL COMMENT 'Foreign key that ties this repair to a stage heading.  SET to NULL on Cascade not DELETE.',
	CONSTRAINT PK_repair_stage PRIMARY KEY (id)
)

ALTER TABLE PartsApp_DB.repairs MODIFY COLUMN ro_num varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;

    -- unique id other than primary key to be used by repair_stage 
    -- since repair records gets deleted on every load.
ALTER TABLE PartsApp_DB.repairs
ADD CONSTRAINT UQ_repairs_shop_id_ro_num
UNIQUE INDEX (shop_id, ro_num)

    -- foreign key to tie repair_stage with the repairs table
ALTER TABLE PartsApp_DB.repair_stage
ADD CONSTRAINT FK_repair_stage_shop_id_ro_num
FOREIGN KEY (shop_id, ro_num)
REFERENCES repairs(shop_id, ro_num)
ON DELETE NO ACTION;

ALTER TABLE repair_stage
ADD CONSTRAINT fk_repair_stage_stage_heading_id
FOREIGN KEY (stage_heading_id)
REFERENCES stage_headings(id)
ON DELETE SET NULL;