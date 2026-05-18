CREATE PROCEDURE spUpdateLocationIDs()
BEGIN

		/* Insert new shops in the Location (Shop) Lookup Table */
	INSERT INTO location_ids
	(Location)
	SELECT DISTINCT r.Location
	FROM repairs r LEFT JOIN location_ids li
		ON r.Location = li.location
	WHERE li.id IS NULL;


		/* Associate each repair with a shop */
	UPDATE repairs r INNER JOIN location_ids li
	SET r.Loc_ID = li.id
	WHERE r.Location = li.location;


		/* Associate each part with a shop */
	UPDATE parts_status pse INNER JOIN location_ids li
	SET pse.Loc_ID = li.id
	WHERE pse.Location = li.location;


		/* Decipher the status for each part */
	UPDATE parts_status
	SET Part_Status =
			CASE

				WHEN (Received_Qty = 0) AND (Ordered_Qty = 0) AND (RO_Qty > 0)
				THEN 'NOT_ORDERED'

				WHEN (Received_Qty = Returned_Qty) AND (Returned_Qty > 0)
				THEN 'RETURNED'

				WHEN (Received_Qty = 0) AND (Ordered_Qty > 0)
				THEN 'ORDERED'

				WHEN (Received_Qty < Ordered_Qty) AND (Received_Qty > 0)
				THEN 'ORDERED'

				ELSE 'RECEIVED'
			END;


		/* Remove any car that are no longer in Production */
	DELETE FROM car_stage
	WHERE id IN
		(SELECT * FROM (SELECT cs.id
						FROM car_stage cs LEFT JOIN repairs r
							ON cs.ro_num = r.RONum AND cs.loc_id = r.Loc_ID
						WHERE r.id IS NULL) AS p
		);


		/* Insert new cars in the Production Stage table */
	INSERT INTO car_stage
		(ro_num, loc_id, stage_id)
	SELECT r.RONum, r.Loc_ID,
		CASE
			WHEN UPPER(r.CurrentPhase) = '[SCHEDULED]'
				THEN 0
			WHEN SUBSTRING_INDEX(r.CurrentPhase, " ", 1) REGEXP '[0-9]'
				THEN FLOOR(SUBSTRING_INDEX(r.CurrentPhase, " ", 1))
			ELSE
				0
		END AS stageID
	FROM repairs r LEFT JOIN car_stage cs
		ON r.RONum = cs.ro_num AND r.Loc_ID = cs.loc_id
	WHERE cs.id IS NULL
			AND r.CurrentPhase <> '[Completed]'
			AND Vehicle_In < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
	ORDER BY r.RONum;


		/* Associate each car with a shop in scheduled_in_vin table */
	UPDATE scheduled_in_vin siv INNER JOIN location_ids li
	SET siv.Loc_ID = li.id
	WHERE UPPER(siv.Location) = UPPER(li.location);

END
