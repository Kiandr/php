-- Add `agents`.`sms_email`
ALTER TABLE `agents`
	ADD `sms_email` VARCHAR(100) NOT NULL AFTER `email`;