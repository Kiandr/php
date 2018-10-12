ALTER TABLE `campaigns` DROP `cc_email`, DROP `bcc_email`;
ALTER TABLE `campaigns` ADD `sender` ENUM( 'admin', 'agent', 'custom' ) NOT NULL DEFAULT 'agent' AFTER `description` ;
UPDATE `campaigns` SET `sender` = 'agent', `sender_name` = '', `sender_email` = '' WHERE `sender_email` = 'notsetup@realestatewebmasters.com';
UPDATE `campaigns` SET `sender` = 'custom' WHERE `sender_email` != '' AND `sender_name` != '';