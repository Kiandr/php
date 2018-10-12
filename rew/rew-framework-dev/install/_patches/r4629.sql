--
-- Remove registration settings - using IDX settings for registration
-- Backwards compatible - http://bugs.mysql.com/bug.php?id=10789
--

drop procedure if exists DropColumnIfExists;
delimiter //

create procedure DropColumnIfExists(
	IN dbName tinytext,
	IN tableName tinytext,
	IN fieldName tinytext)
begin
	IF EXISTS (
		SELECT * FROM information_schema.COLUMNS
		WHERE column_name=fieldName
		and table_name=tableName
		and table_schema=dbName
		)
	THEN
		set @ddl=CONCAT('ALTER TABLE ',dbName,'.',tableName,
			' DROP ',fieldName);
		prepare stmt from @ddl;
		execute stmt;
	END IF;
end;
//

delimiter ;

call DropColumnIfExists(Database(), 'mobile_settings', 'mbl_registration');

drop procedure DropColumnIfExists;


--
-- Adding field for Google Analytics for the m. subdomain
-- 

drop procedure if exists AddColumnUnlessExists;
delimiter //

create procedure AddColumnUnlessExists(
	IN dbName tinytext,
	IN tableName tinytext,
	IN fieldName tinytext,
	IN fieldDef text)
begin
	IF NOT EXISTS (
		SELECT * FROM information_schema.COLUMNS
		WHERE column_name=fieldName
		and table_name=tableName
		and table_schema=dbName
		)
	THEN
		set @ddl=CONCAT('ALTER TABLE ',dbName,'.',tableName,
			' ADD COLUMN ',fieldName,' ',fieldDef);
		prepare stmt from @ddl;
		execute stmt;
	END IF;
end;
//

delimiter ;

call AddColumnUnlessExists(Database(), 'mobile_settings', 'mbl_google_analytics', 'VARCHAR( 255 ) NOT NULL AFTER  `mbl_contact_website`');

drop procedure AddColumnUnlessExists;