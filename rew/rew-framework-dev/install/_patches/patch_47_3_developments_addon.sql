-- Table structure for table `developments`
CREATE TABLE IF NOT EXISTS `developments` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `agent_id` mediumint(8) unsigned NOT NULL DEFAULT '1',
    `link` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `title` varchar(100) NOT NULL DEFAULT '',
    `subtitle` varchar(255) DEFAULT NULL,
    `description` longtext,
    `is_enabled` enum('Y','N') NOT NULL DEFAULT 'N',
    `is_featured` enum('Y','N') NOT NULL DEFAULT 'N',
    `community_id` mediumint(8) unsigned DEFAULT NULL,
    `idx_feed` varchar(100) DEFAULT NULL,
    `idx_criteria` longtext,
    `idx_listings` enum('Y','N') NOT NULL DEFAULT 'Y',
    `idx_snippet_id` int(10) unsigned DEFAULT NULL,
    `page_title` tinytext,
    `meta_keywords` tinytext,
    `meta_description` tinytext,
    `about_heading` varchar(100) DEFAULT NULL,
    `website_url` varchar(150) DEFAULT NULL,
    `completion_status` varchar(100) DEFAULT NULL,
    `completion_date` varchar(255) DEFAULT NULL,
    `completion_is_partial` enum('Y','N') DEFAULT NULL,
    `num_stories` int(10) DEFAULT NULL,
    `num_units` int(10) DEFAULT NULL,
    `unit_min_price` int(10) DEFAULT NULL,
    `unit_max_price` int(10) DEFAULT NULL,
    `unit_styles` varchar(100) DEFAULT NULL,
    `common_features` varchar(200) DEFAULT NULL,
    `construction` varchar(200) DEFAULT NULL,
    `parking` varchar(200) DEFAULT NULL,
    `views` varchar(200) DEFAULT NULL,
    `address` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `state` varchar(100) DEFAULT NULL,
    `zip` varchar(20) DEFAULT NULL,
    `order` int(10) NOT NULL DEFAULT '0',
    `timestamp_created` timestamp NULL DEFAULT NULL,
    `timestamp_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `link` (`link`),
    KEY `agent_id` (`agent_id`),
    KEY `community_id` (`community_id`),
    KEY `is_enabled` (`is_enabled`),
    KEY `is_featured` (`is_featured`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Table structure for table `developments_tags`
CREATE TABLE IF NOT EXISTS `developments_tags` (
    `development_id` int(10) unsigned NOT NULL,
    `tag_name` varchar(100) NOT NULL,
    `tag_order` int(10) unsigned DEFAULT NULL,
    `created_ts` timestamp NULL DEFAULT NULL,
    `updated_ts` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`development_id`,`tag_name`),
    KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Constraints for table `developments`
ALTER TABLE `developments`
    ADD CONSTRAINT `developments_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `developments_ibfk_2` FOREIGN KEY (`community_id`) REFERENCES `featured_communities` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
;

-- Constraints for table `developments_tags`
ALTER TABLE `developments_tags`
    ADD CONSTRAINT `developments_tags_ibfk_1` FOREIGN KEY (`development_id`) REFERENCES `developments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
;