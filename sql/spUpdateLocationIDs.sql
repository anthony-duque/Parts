CREATE DEFINER=`root`@`localhost` PROCEDURE `CarStar`.`spUpdateLocationIDs`()
BEGIN

	INSERT INTO Location_IDs
	(Location)
	SELECT DISTINCT r.Location
	FROM Repairs r LEFT JOIN Location_IDs li
		ON r.Location = li.Location
	WHERE li.id IS NULL;

	UPDATE Repairs r INNER JOIN Location_IDs locID
	SET r.Loc_ID = locID.id
	WHERE r.Location = locID.Location;

	UPDATE PartsStatusExtract pse INNER JOIN Location_IDs li
	SET pse.Loc_ID = li.id
	WHERE pse.Location = li.Location;

	DELETE FROM Car_Stage
	WHERE id IN
		(SELECT * FROM (SELECT ps.id
						FROM Car_Stage ps LEFT JOIN Repairs r
							ON ps.ro_Num = r.RONum AND ps.loc_ID = r.Loc_ID
						WHERE r.id IS NULL) AS p
		);

	INSERT INTO Car_Stage
		(ro_Num, loc_ID, stage_ID)
	SELECT r.RONum, r.Loc_ID, FLOOR(SUBSTRING_INDEX(r.CurrentPhase, " ", 1)) AS stageID
	FROM Repairs r LEFT JOIN Car_Stage ps
		ON r.RONum = ps.ro_Num AND r.Loc_ID = ps.loc_ID
	WHERE ps.id IS NULL AND r.CurrentPhase NOT LIKE '[%%]'
	ORDER BY r.RONum;

	DELETE FROM Tech_Car_Priority
	WHERE id IN
		(SELECT * FROM (SELECT tcp.id
						FROM Tech_Car_Priority tcp LEFT JOIN Repairs r
							ON tcp.RO_Num = r.RONum AND tcp.LocationID = r.Loc_ID
						WHERE r.id IS NULL) AS p
		);

END
