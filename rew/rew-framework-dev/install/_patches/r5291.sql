-- Add `users_sessions`.`ua` to store User-Agent
ALTER TABLE `users_sessions`
	ADD `ua` VARCHAR( 400 ) NOT NULL AFTER `keywords` ,
	ADD INDEX ( `ua` )
;