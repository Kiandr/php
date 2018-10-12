-- Add `users`.`notify_searches`
ALTER TABLE `users`
	ADD `notify_searches` ENUM('yes', 'no') NOT NULL DEFAULT 'no' AFTER `notify_favs`; 