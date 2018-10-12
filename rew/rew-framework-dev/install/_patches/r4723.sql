-- Add `users`.`keywords`
ALTER TABLE `users`
	ADD `keywords` VARCHAR(100) NOT NULL AFTER `referer`;

-- Add `users_sessions`.`keywords`
ALTER TABLE `users_sessions`
	ADD `keywords` VARCHAR(100) NOT NULL AFTER `referer`;