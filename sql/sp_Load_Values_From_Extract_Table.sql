CREATE DEFINER=`root`@`localhost` PROCEDURE `PartsApp_DB`.`spUpdateLocationIDs`()
BEGIN

	INSERT INTO shops
		(location)
	SELECT DISTINCT r.location
	FROM repairs r LEFT JOIN shops li
		ON r.location = li.location
	WHERE li.id IS NULL;

	UPDATE repairs r INNER JOIN shops li
	SET r.loc_id = li.id
	WHERE r.location = li.location AND r.loc_id = 0;

	UPDATE parts_status pse INNER JOIN shops li
	SET pse.loc_id = li.id
	WHERE pse.location = li.location AND r.loc_id = 0;

	UPDATE parts_status
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
		(SELECT * FROM (SELECT cs.id
						FROM car_stage cs LEFT JOIN repairs r
							ON cs.ro_num = r.ro_num AND cs.loc_id = r.loc_id
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

END