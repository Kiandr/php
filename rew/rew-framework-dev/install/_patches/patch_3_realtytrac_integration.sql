--
-- Table structure for table `rewrt_defaults`
--

CREATE TABLE IF NOT EXISTS `rewrt_defaults` (
  `state` varchar(100) NOT NULL,
  `view` varchar(100) NOT NULL DEFAULT '',
  `sort_by` varchar(100) NOT NULL DEFAULT '',
  `page_limit` int(10) NOT NULL DEFAULT '12',
  `panels` longtext NOT NULL,
  `criteria` longtext NOT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `rewrt_defaults`
--

INSERT INTO `rewrt_defaults` (`state`, `view`, `sort_by`, `page_limit`, `panels`, `criteria`, `timestamp_created`, `timestamp_updated`) VALUES
('', 'grid', 'DESC-Property.LastUpdated', 12, 'a:5:{s:6:"county";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:3:"zip";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:5:"value";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `rewrt_defaults` (`state`, `view`, `sort_by`, `page_limit`, `panels`, `criteria`, `timestamp_created`, `timestamp_updated`) VALUES
('sold',  'grid',  '',  '0',  'a:2:{s:9:"sold_date";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:11:"sold_amount";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}',  '',  '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `rewrt_details`
--

CREATE TABLE IF NOT EXISTS `rewrt_details` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `streetview` enum('true','false') NOT NULL DEFAULT 'true',
  `directions` enum('true','false') NOT NULL DEFAULT 'true',
  `birdseye` enum('true','false') NOT NULL DEFAULT 'true',
  `onboard` enum('true','false') NOT NULL DEFAULT 'true',
  `transaction_history` enum('true','false') NOT NULL DEFAULT 'true',
  `foreclosure_history` enum('true','false') NOT NULL DEFAULT 'true',
  `nosy_neighbor` enum('true','false') NOT NULL DEFAULT 'true',
  `nearby_solds` enum('true','false') NOT NULL DEFAULT 'true',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rewrt_details`
--

REPLACE INTO `rewrt_details` (`id`, `streetview`, `directions`, `birdseye`, `onboard`, `transaction_history`, `foreclosure_history`, `nosy_neighbor`, `nearby_solds`, `timestamp_created`, `timestamp_updated`) VALUES
(1, 'true', 'true', 'true', 'true', 'true', 'true', 'true', 'true', NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `rewrt_system`
--

CREATE TABLE IF NOT EXISTS `rewrt_system` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `states` longtext,
  `default_state` varchar(100) NOT NULL,
  `copy_register` longtext,
  `copy_login` longtext,
  `registration` varchar(10) NOT NULL,
  `registration_required` enum('true','false') NOT NULL,
  `registration_password` enum('true','false') NOT NULL,
  `registration_phone` enum('true','false') NOT NULL,
  `registration_verify` enum('true','false') NOT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rewrt_system`
--

REPLACE INTO `rewrt_system` (`id`, `registration`, `registration_required`, `registration_password`, `registration_phone`, `registration_verify`, `copy_register`, `timestamp_created`, `timestamp_updated`) VALUES
(1, 'true', 'false', 'false', 'false', 'false', '<h3>Why Join?</h3><p>Registering will grant you access to our individual public record pages which include tax information, transaction history, neighborhood information, property comparison tools, and much more. You will also gain full access to our IDX search tool including the ability to save your searches and receive new listing email alerts that are tailored to your preferences!</p>', NOW(), NOW());

-- ------------------------------

--
-- Table structure for table `users_viewed_rt_properties`
--

CREATE TABLE IF NOT EXISTS `users_viewed_rt_properties` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rtid` varchar(20) NOT NULL DEFAULT '',
  `state` varchar(100) NOT NULL DEFAULT '',
  `apn` varchar(20) NOT NULL,
  `type` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `subdivision` varchar(100) NOT NULL,
  `bedrooms` tinyint(3) unsigned NOT NULL,
  `bathrooms` decimal(8,2) unsigned NOT NULL,
  `sqft` int(10) unsigned NOT NULL,
  `estimated_value` int(10) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`rtid`,`state`),
  KEY `estimated_value` (`estimated_value`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Constraints for table `users_viewed_rt_properties`
--

ALTER TABLE `users_viewed_rt_properties`
  ADD CONSTRAINT `users_viewed_rt_properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ------------------------------

--
-- Listing View Tracker for RT
--

ALTER TABLE `users` ADD `num_rt_properties` int(10) NOT NULL DEFAULT '0' AFTER `num_saved`;
