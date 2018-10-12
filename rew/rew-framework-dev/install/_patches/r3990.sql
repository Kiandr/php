--
-- Table structure for table `rewidx_comments`
--

CREATE TABLE IF NOT EXISTS `rewidx_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` enum('Agent','Lead') NOT NULL,
  `user` mediumint(8) unsigned default NULL,
  `idx` varchar(20) NOT NULL default '',
  `mls_number` varchar(20) NOT NULL default '',
  `comment` longtext NOT NULL,
  `published` enum('true','false') NOT NULL default 'false',
  `subscribed` enum('true','false') NOT NULL default 'false',
  `timestamp_published` timestamp NULL default NULL,
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user` (`type`,`user`),
  KEY `mls_number` (`mls_number`),
  KEY `idx` (`idx`),
  KEY `published` (`published`),
  KEY `subscribed` (`subscribed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Add 'IDX Listing Comment' to `users`.`forms` 
--
ALTER TABLE `users`
	CHANGE `forms` `forms` SET( 'Contact Form', 'Approve Form', 'Buyer Form', 'Seller Form', 'IDX Registration', 'IDX Inquiry', 'Mobile IDX Registration', 'IDX Listing Comment' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
;