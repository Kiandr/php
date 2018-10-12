--
-- Table structure changes for `users`
--
ALTER TABLE `users`
	ADD `last_text` TEXT NOT NULL AFTER `last_form`,
	ADD `num_texts_outgoing` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `last_email`,
	ADD `num_texts_incoming` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `num_texts_outgoing`,
	ADD `num_texts` INT( 10 ) UNSIGNED NULL DEFAULT '0' AFTER `num_texts_incoming`,
	ADD `opt_texts` ENUM('in','out') NULL DEFAULT NULL AFTER `opt_searches`,
	ADD INDEX (`num_texts_outgoing`),
	ADD INDEX (`num_texts_incoming`),
	ADD INDEX (`num_texts`),
	ADD INDEX (`opt_texts`)
;

--
-- Table structure for table `twilio_autoresponder`
--
CREATE TABLE IF NOT EXISTS `twilio_autoresponder` (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`agent_id` mediumint(8) unsigned NOT NULL,
	`body` text,
	`media` text,
	`active` tinyint(1) unsigned DEFAULT '0',
	`created_ts` timestamp NULL DEFAULT NULL,
	`updated_ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `agent_id` (`agent_id`),
	KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Table structure for table `twilio_verified`
--
CREATE TABLE IF NOT EXISTS `twilio_verified` (
	`phone_number` varchar(20) NOT NULL,
	`optout` timestamp NULL DEFAULT NULL,
	`verified` timestamp NULL DEFAULT NULL,
	`created_ts` timestamp NULL DEFAULT NULL,
	`updated_ts` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`phone_number`),
	KEY `optout` (`optout`),
	KEY `verified` (`verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `twilio_verified_user`
--
CREATE TABLE IF NOT EXISTS `twilio_verified_user` (
	`phone_number` varchar(20) NOT NULL,
	`user_id` mediumint(8) unsigned NOT NULL,
	PRIMARY KEY (`phone_number`,`user_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `twilio_verified_user`
--
ALTER TABLE `twilio_verified_user`
	ADD CONSTRAINT `twilio_verified_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `twilio_verified_user_ibfk_1` FOREIGN KEY (`phone_number`) REFERENCES `twilio_verified` (`phone_number`) ON DELETE CASCADE ON UPDATE CASCADE
;

--
-- Constraints for table `twilio_autoresponder`
--
ALTER TABLE `twilio_autoresponder`
	ADD CONSTRAINT `twilio_autoresponder_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
;