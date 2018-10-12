-- Add `users_messages`.`user_alert`
ALTER TABLE `users_messages`
	ADD `user_alert` ENUM('Y', 'N') NOT NULL DEFAULT 'N' AFTER `sent_from`;