--
-- Table structure for table `users_listings_dismissed`
--
CREATE TABLE IF NOT EXISTS `users_listings_dismissed` (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
	`mls_number` varchar(20) NOT NULL DEFAULT '',
	`table` varchar(100) NOT NULL DEFAULT '',
	`idx` varchar(100) NOT NULL DEFAULT '',
	`type` varchar(100) NOT NULL,
	`city` varchar(100) NOT NULL,
	`subdivision` varchar(100) NOT NULL,
	`bedrooms` tinyint(3) unsigned NOT NULL,
	`bathrooms` decimal(8,2) unsigned NOT NULL,
	`sqft` int(10) unsigned NOT NULL,
	`price` int(10) unsigned NOT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `user_id` (`user_id`,`mls_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for table `users_listings_dismissed`
--
ALTER TABLE `users_listings_dismissed`
	ADD CONSTRAINT `users_listings_dismissed_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
;

--
-- Add `users`.`num_dismissed` to cache number of dismissed listings
--
ALTER TABLE `users`
	ADD `num_dismissed` INT(10) UNSIGNED NULL DEFAULT '0' AFTER `num_favorites`,
	ADD INDEX (`num_dismissed`)
;