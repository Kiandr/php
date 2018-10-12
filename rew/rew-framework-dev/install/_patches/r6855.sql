-- Add `users` columns
ALTER TABLE `users` ADD `source_app_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER `network_yahoo` ,
ADD `source_user_id` MEDIUMINT( 8 ) UNSIGNED NULL DEFAULT NULL AFTER `source_app_id` ,
ADD INDEX ( `source_app_id` ) ;

ALTER TABLE `users` ADD FOREIGN KEY ( `source_app_id` ) REFERENCES `api_applications` (
`id`
) ON DELETE SET NULL ON UPDATE CASCADE ;

-- Add `agents` columns
ALTER TABLE `agents` ADD `auto_assign_app_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER `auto_optout_time` ,
ADD `auto_rotate_app_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER `auto_assign_app_id` ;

ALTER TABLE `agents` ADD INDEX ( `auto_assign_app_id` ) ;
ALTER TABLE `agents` ADD INDEX ( `auto_rotate_app_id` ) ;

ALTER TABLE `agents` ADD FOREIGN KEY ( `auto_assign_app_id` ) REFERENCES `api_applications` (
`id`
) ON DELETE SET NULL ON UPDATE CASCADE ;

ALTER TABLE `agents` ADD FOREIGN KEY ( `auto_rotate_app_id` ) REFERENCES `api_applications` (
`id`
) ON DELETE SET NULL ON UPDATE CASCADE ;

-- Add `users_searches` columns
ALTER TABLE `users_searches` ADD `source_app_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER `sent` ,
ADD INDEX ( `source_app_id` ) ;

ALTER TABLE `users_searches` ADD FOREIGN KEY ( `source_app_id` ) REFERENCES `api_applications` (
`id`
) ON DELETE SET NULL ON UPDATE CASCADE ;

-- Add `api_requests` table
CREATE TABLE IF NOT EXISTS `api_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL,
  `method` varchar(20) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `get` text,
  `post` text,
  `headers` text,
  `status` enum('ok','error') NOT NULL DEFAULT 'ok',
  `response` text,
  `user_agent` varchar(255) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `duration` decimal(18,12) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for table `api_requests`
--
ALTER TABLE `api_requests`
  ADD CONSTRAINT `api_requests_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `api_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Add `api_applications` columns
ALTER TABLE `api_applications` ADD `url` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `name` ,
ADD `num_requests_ok` INT( 10 ) UNSIGNED NOT NULL AFTER `url` ,
ADD `num_requests_error` INT( 10 ) UNSIGNED NOT NULL AFTER `num_requests_ok` ;

-- Add `agents_auto_rotate` table
CREATE TABLE IF NOT EXISTS `agents_auto_rotate` (
  `source_app_id` int(10) unsigned DEFAULT NULL,
  `last_agent_id` mediumint(8) unsigned NOT NULL,
  KEY `source_app_id` (`source_app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Constraints for table `agents_auto_rotate`
--
ALTER TABLE `agents_auto_rotate`
  ADD CONSTRAINT `agents_auto_rotate_ibfk_1` FOREIGN KEY (`source_app_id`) REFERENCES `api_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
