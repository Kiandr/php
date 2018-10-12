-- Add `cms_files`.`agent` Column
ALTER TABLE `cms_files`
	ADD `agent` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `id` ,
	ADD INDEX ( `agent` ); 