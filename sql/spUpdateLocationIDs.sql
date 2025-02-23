CREATE PROCEDURE spUpdateLocationIDs()
BEGIN

	DELETE FROM Location_IDs;

	INSERT INTO Location_IDs
	(Location)
	SELECT DISTINCT Location
	FROM Repairs;

	UPDATE Repairs r INNER JOIN Location_IDs locID
	SET r.Loc_ID = locID.id
	WHERE r.Location = locID.Location;

	UPDATE PartsStatusExtract pse INNER JOIN Location_IDs li
	SET pse.Loc_ID = li.id
	WHERE pse.Location = li.Location;

END
