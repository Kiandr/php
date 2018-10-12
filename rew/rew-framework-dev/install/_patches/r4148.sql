--
-- Table structure for table `flyers`
--

CREATE TABLE IF NOT EXISTS `flyers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `agent_id` mediumint(8) unsigned NOT NULL,
  `mls_number` varchar(20) NOT NULL,
  `idx` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `html` text NOT NULL,
  `post_data` text NOT NULL,
  `timestamp_submitted` timestamp NULL default NULL,
  `timestamp_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `main` (`agent_id`,`mls_number`,`idx`),
  KEY `agent_id` (`agent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Constraints for table `flyers`
--
ALTER TABLE `flyers`
  ADD CONSTRAINT `flyers_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;