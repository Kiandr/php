ALTER TABLE `agents` ADD `network_microsoft` LONGTEXT NOT NULL AFTER `network_google` ;
ALTER TABLE `agents` ADD `google_calendar_sync` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `partners` ;
ALTER TABLE `agents` ADD `microsoft_calendar_sync` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `google_calendar_sync` ;

ALTER TABLE `default_info` ADD `calendar_notifications` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true' AFTER `yahoo_secret` ;

ALTER TABLE `calendar_events` ADD `google_event_id` VARCHAR( 255 ) NOT NULL AFTER `priority` ;
ALTER TABLE `calendar_events` ADD `microsoft_event_id` VARCHAR( 255 ) NOT NULL AFTER `google_event_id` ;

ALTER TABLE `users_reminders` ADD `google_event_id` VARCHAR( 255 ) NOT NULL AFTER `sent` ;
ALTER TABLE `users_reminders` ADD `microsoft_event_id` VARCHAR( 255 ) NOT NULL AFTER `google_event_id` ;