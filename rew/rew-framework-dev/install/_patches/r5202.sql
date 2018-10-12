-- Add Column `campaigns`.`starts` to store Campaign Start Date
ALTER TABLE `campaigns`
	ADD `starts` DATE NULL DEFAULT NULL AFTER `sender_email`
;