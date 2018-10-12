-- Add `users`.`contact_method`
ALTER TABLE `users`
	ADD `contact_method` ENUM('email', 'phone', 'text') NULL DEFAULT NULL AFTER `phone_fax`,
	ADD INDEX (`contact_method`)
;