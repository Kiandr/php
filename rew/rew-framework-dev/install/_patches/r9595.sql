--
-- Table structure for table `featured_communities_tags`
--
CREATE TABLE IF NOT EXISTS `featured_communities_tags` (
	`community_id` mediumint(8) unsigned NOT NULL,
	`tag_name` varchar(100) NOT NULL,
	`tag_order` int(11) NULL DEFAULT NULL,
	`created_ts` timestamp NULL DEFAULT NULL,
	`updated_ts` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`community_id`, `tag_name`),
	KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for table `featured_communities_tags`
--
ALTER TABLE `featured_communities_tags`
	ADD CONSTRAINT `featured_communities_tags_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `featured_communities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
;