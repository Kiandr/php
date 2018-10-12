-- Adding in new field to rewrt_system --
ALTER TABLE `rewrt_system` ADD `language` LONGTEXT NOT NULL AFTER `registration_verify`;
