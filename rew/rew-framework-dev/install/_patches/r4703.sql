-- Update `auto_responders` to Handle Sender Settings
ALTER TABLE `auto_responders` ADD `from` ENUM('admin', 'agent', 'custom') NOT NULL DEFAULT 'agent' AFTER `title`;
UPDATE `auto_responders` SET `from` = 'agent', `from_name` = '', `from_email` = '' WHERE `from_email` = 'notsetup@realestatewebmasters.com';
UPDATE `auto_responders` SET `from` = 'custom' WHERE `from_email` != '' AND `from_name` != '';
UPDATE `auto_responders` SET `from` = 'admin', `from_name` = '', `from_email` = '' WHERE `from_name` = (SELECT CONCAT(`first_name`, ' ', `last_name`) FROM `agents` WHERE `id` = 1) AND `from_email` = (SELECT `email` FROM `agents` WHERE `id` = 1);