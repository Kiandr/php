-- Add Columns to `users`: `last_form`, `last_call`, `last_email`, `num_calls`, `num_emails`
ALTER TABLE `users`
	ADD `last_form` TEXT NOT NULL AFTER `referer` ,
	ADD `last_call` TEXT NOT NULL AFTER `last_form` ,
	ADD `last_email` TEXT NOT NULL AFTER `last_call` ,
	ADD `num_calls` INT UNSIGNED NOT NULL AFTER `last_email` ,
	ADD `num_emails` INT UNSIGNED NOT NULL AFTER `num_calls`
;

-- Populate `users`.`num_calls`
UPDATE `users` `u` SET `u`.`num_calls` = (
	SELECT COUNT(*)
	FROM `history_users` `hu`
	LEFT JOIN `history_events` `he` ON `hu`.`event` = `he`.`id`
	WHERE `he`.`type` = 'Phone' AND `hu`.`type` = 'Lead' AND `hu`.`user` = `u`.`id`
);

-- Populate `users`.`num_emails`
UPDATE `users` `u` SET `u`.`num_emails` = (
	SELECT COUNT(*)
	FROM `history_users` `hu`
	LEFT JOIN `history_events` `he` ON `hu`.`event` = `he`.`id`
	WHERE `he`.`type` = 'Email' AND `hu`.`type` = 'Lead' AND `hu`.`user` = `u`.`id`
);

-- Populate `users`.`last_call`
UPDATE `users` `u` SET `u`.`last_call` = (
	SELECT CONCAT('{"timestamp":', UNIX_TIMESTAMP(`he`.`timestamp`), ',"type":"', `he`.`subtype`, '"}')
	FROM `history_users` `hu`
	LEFT JOIN `history_events` `he` ON `hu`.`event` = `he`.`id`
	WHERE `he`.`type` = 'Phone' AND `hu`.`type` = 'Lead' AND `hu`.`user` = `u`.`id`
	ORDER BY `he`.`timestamp` DESC
	LIMIT 1
);

-- Populate `users`.`last_email`
UPDATE `users` `u` SET `u`.`last_email` = (
	SELECT CONCAT('{"timestamp":', UNIX_TIMESTAMP(`he`.`timestamp`), ',"type":"', `he`.`subtype`, '"}')
	FROM `history_users` `hu`
	LEFT JOIN `history_events` `he` ON `hu`.`event` = `he`.`id`
	WHERE `he`.`type` = 'Email' AND `hu`.`type` = 'Lead' AND `hu`.`user` = `u`.`id`
	ORDER BY `he`.`timestamp` DESC
	LIMIT 1
);

-- Populate `users`.`last_form`
UPDATE `users` `u` SET `u`.`last_form` = (
	SELECT CONCAT('{"timestamp":', UNIX_TIMESTAMP(`f`.`timestamp`), ',"type":"', `f`.`form`, '"}')
	FROM `users_forms` `f`
	WHERE `f`.`user_id` = `u`.`id`
	ORDER BY `f`.`timestamp` DESC
	LIMIT 1
);