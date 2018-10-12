--
-- Table structure for table `bdx_settings`
--

CREATE TABLE IF NOT EXISTS `bdx_settings` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `settings` longtext NOT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Dumping data for table `bdx_settings`
--

REPLACE INTO `bdx_settings` (`id`, `settings`) VALUES
(1, 'a:5:{s:16:\"state_page_limit\";s:2:\"12\";s:15:\"city_page_limit\";s:2:\"12\";s:20:\"community_page_limit\";s:2:\"12\";s:18:\"listing_page_limit\";s:1:\"8\";s:26:\"similar_listing_page_limit\";s:1:\"6\";}');