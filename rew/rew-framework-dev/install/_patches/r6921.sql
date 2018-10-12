-- Add a binary column to store user GUIDs

ALTER TABLE  `users` ADD  `guid` BINARY( 16 ) NOT NULL AFTER  `id`;

-- These functions convert a UUID() to and from binary
-- http://stackoverflow.com/a/7168916
-- The functions also re-arrange the bits to improve index performance
-- The trigger ensures that all new rows will have a GUID automatically

DELIMITER $$

DROP FUNCTION IF EXISTS `GuidToBinary`$$

CREATE FUNCTION `GuidToBinary`(
	$DATA VARCHAR(36)
) RETURNS BINARY(16)
	DETERMINISTIC
BEGIN
	DECLARE $Result BINARY(16) DEFAULT NULL;
	IF $DATA IS NOT NULL THEN
		SET $DATA = REPLACE($DATA,'-','');
		SET $Result = CONCAT(
			UNHEX(SUBSTRING($DATA,7,2)),
			UNHEX(SUBSTRING($DATA,5,2)),
			UNHEX(SUBSTRING($DATA,3,2)),
			UNHEX(SUBSTRING($DATA,1,2)),
			UNHEX(SUBSTRING($DATA,11,2)),
			UNHEX(SUBSTRING($DATA,9,2)),
			UNHEX(SUBSTRING($DATA,15,2)),
			UNHEX(SUBSTRING($DATA,13,2)),
			UNHEX(SUBSTRING($DATA,17,16)));
	END IF;
	RETURN $Result;
END$$

DROP FUNCTION IF EXISTS `ToGuid`$$

CREATE FUNCTION `ToGuid`(
	$DATA BINARY(16)
) RETURNS CHAR(36) CHARSET utf8 DETERMINISTIC
BEGIN
	DECLARE $Result CHAR(36) DEFAULT NULL;
	IF $DATA IS NOT NULL THEN
		SET $Result = CONCAT(
			HEX(SUBSTRING($DATA,4,1)),
			HEX(SUBSTRING($DATA,3,1)),
			HEX(SUBSTRING($DATA,2,1)),
			HEX(SUBSTRING($DATA,1,1)),
			'-', 
			HEX(SUBSTRING($DATA,6,1)),
			HEX(SUBSTRING($DATA,5,1)),
			'-',
			HEX(SUBSTRING($DATA,8,1)),
			HEX(SUBSTRING($DATA,7,1)),
			'-',
			HEX(SUBSTRING($DATA,9,2)),
			'-',
			HEX(SUBSTRING($DATA,11,6)));
	END IF;
	RETURN $Result;
END$$

DROP TRIGGER IF EXISTS `before_insert_users`$$

CREATE
	TRIGGER `before_insert_users` BEFORE INSERT ON `users` 
	FOR EACH ROW SET new.guid = GuidToBinary( UUID ( ) );
$$

DELIMITER ;

UPDATE `users` SET `guid` = (SELECT GuidToBinary( UUID ( ) ) );

ALTER TABLE  `users` ADD UNIQUE (`guid`) ;