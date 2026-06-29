CREATE PROCEDURE spUpdateLocationIDs()
BEGIN

	INSERT INTO location_ids
		(location)
	SELECT DISTINCT r.location
	FROM repairs r LEFT JOIN location_ids li
		ON r.location  = li.location
	WHERE li.id IS NULL;

	UPDATE repairs r INNER JOIN location_ids locID
	SET r.loc_id = locID.id
	WHERE r.location = locID.location;

	UPDATE parts_status ps INNER JOIN location_ids li
	SET ps.loc_id = li.id
	WHERE ps.location = li.location;

	UPDATE parts_status ps 
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
			END;

	DELETE FROM car_stage
	WHERE id IN
		(SELECT * FROM (SELECT ps.id
						FROM car_stage ps LEFT JOIN repairs r
							ON ps.ro_num = r.ro_num AND ps.loc_id = r.loc_id
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
		ON r.ro_num = cs.ro_Num AND r.loc_id  = cs.loc_ID
	WHERE cs.id IS NULL
			AND r.current_phase <> '[Completed]'
			AND vehicle_in < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
	ORDER BY r.ro_num;

	UPDATE scheduled_in_vin siv INNER JOIN location_ids locID
	SET siv.loc_id = locID.id
	WHERE UPPER(siv.location) = UPPER(locID.location);

END
