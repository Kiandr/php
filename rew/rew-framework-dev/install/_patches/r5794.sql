ALTER TABLE `rewidx_details`
	ADD `price_history` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true' AFTER `socialnetwork` ,
	ADD `status_history` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true' AFTER `price_history`
; 