-- Added `rewidx_system`.`language` to store IDX Meta Information
ALTER TABLE `rewidx_system`
	ADD `language` LONGTEXT NOT NULL AFTER `savedsearches_message`
;