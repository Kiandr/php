-- Store original message and tags for delayed emails
ALTER TABLE `delayed_emails`
	ADD `message` LONGTEXT NULL DEFAULT NULL AFTER `mailer` ,
	ADD `tags` LONGTEXT NULL DEFAULT NULL AFTER `message`
;