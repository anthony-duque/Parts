CREATE PROCEDURE sp_Update_XML_Upload (IN companyID INT)
BEGIN

	UPDATE PartsApp_DB.parts_status
	SET part_status =
		CASE
	
			WHEN (received_qty = 0) AND (ordered_qty = 0) AND (ro_qty > 0)
			THEN 'NOT_ORDERED'
	
			WHEN (received_qty = returned_qty) AND (returned_qty > 0)
			THEN 'RETURNED'
	
			WHEN (received_qty = 0) AND (ordered_qty > 0)
			THEN 'ORDERED'
	
			WHEN (received_qty < ordered_qty) AND (received_qty > 0)
			THEN 'ORDERED'
	
			ELSE 'RECEIVED'
			
		END
	WHERE repair_id IN 
				(SELECT r.id 
				FROM PartsApp_DB.repairs r INNER JOIN PartsApp_DB.shops s 
					ON s.id = r.shop_id
				INNER JOIN PartsApp_DB.companies c 
					ON c.id = s.company_id  
				WHERE c.id = @companyID);

/*

	DELETE FROM car_stage
	WHERE id IN
		(SELECT * FROM (SELECT cs.id
						FROM car_stage cs LEFT JOIN repairs r
							ON cs.ro_num = r.ro_num 
								AND cs.loc_id = r.loc_id
								AND cs.company_id = r.company_ID
						WHERE r.id IS NULL) AS p
		);


	INSERT INTO car_stage
		(ro_num, loc_id, stage_id)
	SELECT r.ro_num, r.loc_id,
		CASE
			WHEN UPPER(r.current_phase) = '[SCHEDULED]'
				THEN 0
			WHEN SUBSTRING_INDEX(r.current_phase, " ", 1) REGEXP '[0-9]'
				THEN FLOOR(SUBSTRING_INDEX(r.current_phase, " ", 1))
			ELSE
				0
		END AS stageID
	FROM repairs r LEFT JOIN car_stage cs
		ON r.ro_num = cs.ro_num AND r.loc_id = cs.loc_id
	WHERE cs.id IS NULL
			AND r.current_phase <> '[Completed]'
			AND vehicle_in < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
	ORDER BY r.ro_num;

	UPDATE scheduled_in_vin siv INNER JOIN shops li
	SET siv.Loc_ID = li.id
	WHERE UPPER(siv.location) = UPPER(li.location);
*/

END