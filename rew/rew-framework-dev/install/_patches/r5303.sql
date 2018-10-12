-- Add Columns to Toggle Agent Opt-Out
ALTER TABLE `agents`
	ADD `auto_optout` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `auto_rotate`,
	ADD `auto_optout_time` TIMESTAMP NOT NULL AFTER `auto_optout`
;

-- Add Columns to Store Agent Opt-Out Settings
ALTER TABLE `default_info`
	ADD `auto_optout` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `auto_rotate_unassign` ,
	ADD `auto_optout_time` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '60' AFTER `auto_optout` ,
	ADD `auto_optout_actions` LONGTEXT NOT NULL AFTER `auto_optout_time`
;