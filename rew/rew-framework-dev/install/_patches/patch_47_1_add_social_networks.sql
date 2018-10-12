--
-- Table structure for table `agents_social_networks`
--

CREATE TABLE IF NOT EXISTS `agents_social_networks` (
  `agent_id` mediumint(8) unsigned NOT NULL,
  `name` VARCHAR(64) NOT NULL,
  `url` VARCHAR(255) DEFAULT NULL,
  KEY `name` (`name`),
  PRIMARY KEY (`agent_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `agents`
--

REPLACE INTO `agents_social_networks` (`agent_id`, `name`, `url`) VALUES
(1, 'Google', NULL), (1, 'Google Plus', '#'), (1, 'YouTube', '#'), (1, 'Facebook', '#'), (1, 'LinkedIn', '#'), (1, 'Twitter', '#'), (1, 'Pinterest', '#');

--
-- Constraints for table `agents_social_networks`
--
ALTER TABLE `agents_social_networks`
  ADD CONSTRAINT `agents_social_networks_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
