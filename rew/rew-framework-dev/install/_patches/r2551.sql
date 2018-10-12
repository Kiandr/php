-- Add `phone_cell` and `phone_cell_status`
ALTER TABLE `users`
	ADD `phone_cell` VARCHAR( 100 ) NOT NULL AFTER `phone_home_status` ,
	ADD `phone_cell_status` VARCHAR( 255 ) NOT NULL AFTER `phone_cell`
;