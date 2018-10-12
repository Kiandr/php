-- Add an index to pages.file_name if one doesn't exist

DELIMITER //

CREATE PROCEDURE addidx() BEGIN
IF NOT EXISTS(
	SELECT * FROM information_schema.`STATISTICS` 
	WHERE `TABLE_SCHEMA` = DATABASE() AND `TABLE_NAME` = 'pages' AND `COLUMN_NAME` = 'file_name'
	)
	THEN
		ALTER TABLE `pages`
		ADD INDEX ( `file_name` );
END IF;
END
//

DELIMITER ;

CALL addidx();

DROP PROCEDURE addidx;