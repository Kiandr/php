-- Add `users`.`auto_rotate`
ALTER TABLE `users` ADD `auto_rotate` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true' AFTER `notify_favs` ;