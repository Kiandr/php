--
-- Table structure for table `mobile_settings`
--

CREATE TABLE IF NOT EXISTS `mobile_settings` (
  `id` mediumint(8) NOT NULL auto_increment,
  `mbl_contact_image` varchar(255) NOT NULL,
  `mbl_contact_name` varchar(255) NOT NULL,
  `mbl_contact_title` varchar(255) NOT NULL,
  `mbl_contact_business` varchar(255) NOT NULL,
  `mbl_contact_address` varchar(255) NOT NULL,
  `mbl_contact_location` varchar(255) NOT NULL,
  `mbl_contact_cell_phone` varchar(25) NOT NULL,
  `mbl_contact_office_phone` varchar(25) NOT NULL,
  `mbl_contact_email` varchar(255) NOT NULL,
  `mbl_contact_website` varchar(255) NOT NULL,
  `mbl_registration` enum('true','false') NOT NULL default 'false',
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `mobile_settings`
--

INSERT INTO `mobile_settings` (`id`, `mbl_contact_image`, `mbl_contact_name`, `mbl_contact_title`, `mbl_contact_business`, `mbl_contact_address`, `mbl_contact_location`, `mbl_contact_cell_phone`, `mbl_contact_office_phone`, `mbl_contact_email`, `mbl_contact_website`, `mbl_registration`, `timestamp_created`, `timestamp_updated`) VALUES
(1, '', '', '', '', '', '', '', '', '', '', 'false', NOW(), NOW());
