--
-- Table structure for table `saved_search_logger`
--

CREATE TABLE IF NOT EXISTS `saved_search_logger` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(10) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
