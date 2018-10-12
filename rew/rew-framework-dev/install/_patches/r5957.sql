--
-- Create `auth` Tables
--
CREATE TABLE IF NOT EXISTS `auth` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` enum('Agent','Associate','Lender') NOT NULL,
  `username` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `last_logon` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Create `associates` Tables
--
CREATE TABLE IF NOT EXISTS `associates` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `auth` mediumint(8) unsigned NOT NULL,
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `office_phone` varchar(25) NOT NULL default '',
  `home_phone` varchar(25) NOT NULL default '',
  `cell_phone` varchar(25) NOT NULL default '',
  `fax` varchar(25) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `city` varchar(100) NOT NULL default '',
  `state` varchar(100) NOT NULL default '',
  `zip` varchar(100) NOT NULL default '',
  `timezone` tinyint(3) unsigned NOT NULL default '6',
  `default_filter` varchar(100) NOT NULL default 'all',
  `default_timeframe` varchar(100) NOT NULL default 'today',
  `page_limit` tinyint(3) unsigned NOT NULL default '20',
  `columns` varchar(200) NOT NULL default 'email,phone,notes,status,forms,calls,emails,listings,searches,agent,lender',
  `signature` longtext NOT NULL,
  `add_sig` enum('Y','N') NOT NULL default 'N',
  `last_logon` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `auth` (`auth`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Create `lenders` Tables
--
CREATE TABLE IF NOT EXISTS `lenders` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `auth` mediumint(8) unsigned NOT NULL,
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `office_phone` varchar(25) NOT NULL default '',
  `home_phone` varchar(25) NOT NULL default '',
  `cell_phone` varchar(25) NOT NULL default '',
  `fax` varchar(25) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `city` varchar(100) NOT NULL default '',
  `state` varchar(100) NOT NULL default '',
  `zip` varchar(100) NOT NULL default '',
  `timezone` tinyint(3) unsigned NOT NULL default '6',
  `default_filter` varchar(100) NOT NULL default 'all',
  `default_timeframe` varchar(100) NOT NULL default 'today',
  `page_limit` tinyint(3) unsigned NOT NULL default '20',
  `columns` varchar(200) NOT NULL default 'email,phone,notes,status,forms,calls,emails,listings,searches,agent',
  `last_logon` timestamp NOT NULL default '0000-00-00 00:00:00',
  `auto_assign_admin` enum('true','false') NOT NULL default 'false',
  `auto_assign_optin` enum('true','false') NOT NULL default 'false',
  `auto_assign_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `auth` (`auth`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Add 'auto_assign_lenders' to `default_info` for Lender Auto-Assignment
--
ALTER TABLE `default_info`
	ADD `auto_assign_lenders` ENUM('true', 'false') NOT NULL DEFAULT 'false' AFTER `auto_rotate_unassign`
;

--
-- Add 'Associate' and 'Lender' to `history_users`.`type` ENUM to Track Lender History
--
ALTER TABLE `history_users`
	CHANGE `type` `type` ENUM( 'Agent', 'Associate', 'Lender', 'Lead') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
;

--
-- Add `users_notes`.`associate` Column to Track ISA Notes
-- Add `users_notes`.`lender` Column to Track Lender's Notes
--
ALTER TABLE `users_notes`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent_id`,
	ADD `lender` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `associate`,
	ADD INDEX (`associate`),
	ADD INDEX (`lender`)
;

--
-- Add `delayed_emails`.`associate` Column to Track ISA Delayed Emails
-- Add `delayed_emails`.`lender` Column to Track Lender's Delayed Emails
--
ALTER TABLE `delayed_emails`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent`,
	ADD `lender` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `associate`,
	ADD INDEX (`associate`),
	ADD INDEX (`lender`)
;

-- Add `groups`.`associate` Column to Track ISA Groups
--
ALTER TABLE `groups`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent_id`,
	ADD INDEX (`associate`)
;

--
-- Add `users_reminders`.`associate` Column to Track ISA Reminders
--
ALTER TABLE `users_reminders`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent`,
	ADD INDEX (`associate`)
;

--
-- Add `users_listings`.`associate` Column to Track ISA Recommended Listings
--
ALTER TABLE `users_listings`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent_id`,
	ADD INDEX (`associate`)
;

--
-- Add `users_searches`.`associate` Column to Track ISA Saved Searches 
-- Add `users_searches`.`suggested` Column to Flag Suggested Searches
-- 
ALTER TABLE `users_searches`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent_id`,
	ADD `suggested` ENUM('true', 'false') NOT NULL DEFAULT 'false' AFTER `idx`,
	ADD INDEX (`associate`),
	ADD INDEX (`suggested`)
;

--
-- Update Existing Suggested Searches
--
UPDATE `users_searches` SET `suggested` = IF(`agent_id` IS NOT NULL, 'true', 'false'); 

--
-- Add `users_viewed_searches`.`associate` Column to Track ISA Saved Searches
--
ALTER TABLE `users_viewed_searches`
	ADD `associate` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent_id`,
	ADD INDEX (`associate`)
;

--
-- Update `agents`.`columns` to include 'lender' in defaults
--
ALTER TABLE `agents`
	CHANGE `columns` `columns` VARCHAR( 200 ) NOT NULL DEFAULT 'email,phone,groups,notes,status,forms,calls,emails,listings,searches,agent,lender,visits,origin'
;

-- Add `users`.`lender` Column to store Lead's Assign Lender
--
ALTER TABLE `users`
	ADD `lender` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `agent`,
	ADD INDEX (`lender`)
;

--
-- Add `agents`.`auth` Column
--
ALTER TABLE `agents`
	ADD `auth` MEDIUMINT(8) UNSIGNED NOT NULL AFTER `id`,
	ADD INDEX (`auth`)
;

--
-- Add Agent Data to `auth` Table
--
INSERT INTO `auth` (SELECT
    NULL AS `id`,
    'Agent' AS `type`,
    `username`,
    `password`,
    `last_logon`,
    `timestamp` AS `timestamp_created`,
    `timestamp` AS `timestamp_updated`
FROM `agents`);

--
-- Update `agents`.`auth`
--
UPDATE `agents` SET `auth` = (
	SELECT `id` FROM `auth` WHERE `type` = 'Agent' AND `username` = `agents`.`username` AND `password` = `agents`.`password`
);

--
-- Remove `username` and `password` from `agents` Table
--
ALTER TABLE `agents`
	DROP `username`,
	DROP `password`,
	DROP `last_logon`
;

--
-- Constraints for `auth` Column 
--
ALTER TABLE `agents` ADD CONSTRAINT `agents_ibfk_3` FOREIGN KEY (`auth`) REFERENCES `auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `lenders` ADD CONSTRAINT `lenders_ibfk_1` FOREIGN KEY (`auth`) REFERENCES `auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `associates` ADD CONSTRAINT `associates_ibfk_1` FOREIGN KEY (`auth`) REFERENCES `auth` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- Constraints for `associates` 
--
ALTER TABLE `groups` ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`associate`) REFERENCES `associates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for `lenders` 
--
ALTER TABLE `users` ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`lender`) REFERENCES `lenders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;