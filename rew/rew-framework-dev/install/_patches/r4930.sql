-- Whoops! Rename 'cms_email' to 'sms_email'
-- This uses a procedure to check if 'cms_email' exists first.
DROP PROCEDURE IF EXISTS Patch4930;
DELIMITER //

CREATE PROCEDURE Patch4930 (IN dbName tinytext)
BEGIN
	IF EXISTS (
		SELECT * FROM information_schema.COLUMNS WHERE table_schema=dbName AND table_name='agents' AND column_name='cms_email'
	)
	THEN
		SET @ddl=CONCAT("ALTER TABLE ", dbName, ".`agents` CHANGE `cms_email` `sms_email` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");
		PREPARE STMT FROM @ddl;
		EXECUTE STMT;
	END IF;
END;
//

DELIMITER ;
CALL Patch4930(DATABASE());
DROP PROCEDURE Patch4930;