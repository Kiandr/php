-- Add imported flag to users table
ALTER TABLE `users` ADD `imported` ENUM( 'Y', 'N' ) NULL DEFAULT 'N' AFTER `auto_search` ;