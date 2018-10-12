-- Add Columns to `cms_files`
ALTER TABLE `cms_files`
	ADD `views` INT(10) UNSIGNED NOT NULL AFTER `data`,
	ADD `share` ENUM('true', 'false') NOT NULL DEFAULT 'false' AFTER `views`,
	ADD `password` VARCHAR(40) NULL DEFAULT NULL AFTER `share`,
	ADD INDEX (`share`) 
;