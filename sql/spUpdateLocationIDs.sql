CREATE PROCEDURE spUpdateLocationIDs()
BEGIN

		/* Insert new shops in the Location (Shop) Lookup Table */
	INSERT INTO Location_IDs
	(Location)
	SELECT DISTINCT r.Location
	FROM Repairs r LEFT JOIN Location_IDs li
		ON r.Location = li.Location
	WHERE li.id IS NULL;


		/* Associate each repair with a shop */
	UPDATE Repairs r INNER JOIN Location_IDs li
	SET r.Loc_ID = li.id
	WHERE r.Location = li.Location;


		/* Associate each part with a shop */
	UPDATE PartsStatusExtract pse INNER JOIN Location_IDs li
	SET pse.Loc_ID = li.id
	WHERE pse.Location = li.Location;


		/* Decipher the status for each part */
	UPDATE PartsStatusExtract
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
	DELETE FROM Car_Stage
	WHERE id IN
		(SELECT * FROM (SELECT ps.id
						FROM Car_Stage ps LEFT JOIN Repairs r
							ON ps.ro_Num = r.RONum AND ps.loc_ID = r.Loc_ID
						WHERE r.id IS NULL) AS p
		);


		/* Insert new cars in the Production Stage table */
	INSERT INTO Car_Stage
		(ro_Num, loc_ID, stage_ID)
	SELECT r.RONum, r.Loc_ID,
		CASE
			WHEN UPPER(r.CurrentPhase) = '[SCHEDULED]'
				THEN 0
			WHEN SUBSTRING_INDEX(r.CurrentPhase, " ", 1) REGEXP '[0-9]'
				THEN FLOOR(SUBSTRING_INDEX(r.CurrentPhase, " ", 1))
			ELSE
				0
		END AS stageID
	FROM Repairs r LEFT JOIN Car_Stage ps
		ON r.RONum = ps.ro_Num AND r.Loc_ID = ps.loc_ID
	WHERE ps.id IS NULL
			AND r.CurrentPhase <> '[Completed]'
			AND Vehicle_In < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
	ORDER BY r.RONum;


		/* Associate each car with a shop in Scheduled_In_VIN table */
	UPDATE Scheduled_In_VIN siv INNER JOIN Location_IDs li
	SET siv.Loc_ID = li.id
	WHERE UPPER(siv.Location) = UPPER(li.Location);

END
