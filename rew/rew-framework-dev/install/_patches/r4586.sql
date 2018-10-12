-- Add `default_info`.`gapikey`
ALTER TABLE `default_info`
	ADD `gapikey` TINYTEXT NOT NULL AFTER `agent` ;