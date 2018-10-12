--
-- Table structure for table `history_data_normal`
--

CREATE TABLE IF NOT EXISTS `history_data_normal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` longtext NOT NULL,
  `hash` binary(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8 AUTO_INCREMENT=1 ;
  
ALTER TABLE `history_data` 
  ADD `norm_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  ADD INDEX `norm_id` (`norm_id`);
