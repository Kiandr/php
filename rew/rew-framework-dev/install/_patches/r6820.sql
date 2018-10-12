-- Finally, a table to store settings in
CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(100) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Store the Dropbox app id for MoxieManager
INSERT INTO `settings` (`name`, `value`) VALUES
	('moxiemanager.dropbox.app_id', NULL)
;