--
-- Create `api_applications` Table
--
CREATE TABLE IF NOT EXISTS `api_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `enabled` enum('Y','N') NOT NULL DEFAULT 'Y',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
