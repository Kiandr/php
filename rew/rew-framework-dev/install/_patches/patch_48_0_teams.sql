
--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_id` MEDIUMINT(8) UNSIGNED DEFAULT NULL,
  `agent_permissions` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(100) NOT NULL DEFAULT '',
  `style` CHAR(1) DEFAULT 'a',
  `description` LONGTEXT,
  `subdomain` ENUM('false','true') NOT NULL DEFAULT 'false',
  `subdomain_link` VARCHAR(100) DEFAULT NULL,
  `subdomain_idxs` VARCHAR(255) DEFAULT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `agent_id` (`agent_id`)
) ENGINE=INNODB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `team_agents`
--

CREATE TABLE `team_agents` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `agent_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `granted_permissions` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `granting_permissions` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`team_id`,`agent_id`),
  KEY `agent_id` (`agent_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `team_agent_listings`
--

CREATE TABLE `team_agent_listings` (
  `team_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  `agent_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  `listing_id` BIGINT(20) UNSIGNED NOT NULL,
  `listing_feed` VARCHAR(100) NOT NULL,
  `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`team_id`,`agent_id`,`listing_id`,`listing_feed`),
  KEY `listing` (`listing_id`,`listing_feed`),
  KEY `agent_id` (`agent_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `team_agents`
--
ALTER TABLE `team_agents`
  ADD CONSTRAINT `team_agents_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `team_agents_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Update CMS Tables to alternativly accept team id
--
ALTER TABLE `default_info`
  ADD COLUMN `team` MEDIUMINT(8) UNSIGNED DEFAULT NULL AFTER `agent`,
  DROP INDEX `PRIMARY`,
  DROP FOREIGN KEY `default_info_ibfk_1`,
  MODIFY COLUMN `agent` MEDIUMINT(8) UNSIGNED DEFAULT '1',
  ADD UNIQUE KEY `agent` (`agent`),
  ADD UNIQUE KEY `team` (`team`);

--
-- Update users to share leads
--
ALTER TABLE `users`
  ADD COLUMN `auto_rotate_team` MEDIUMINT(8) UNSIGNED DEFAULT NULL AFTER `auto_rotate`,
  ADD COLUMN `share_lead` TINYINT(4) UNSIGNED DEFAULT '1' AFTER `notify_searches`,
  ADD COLUMN `image` VARCHAR(100) NOT NULL DEFAULT '' AFTER `keywords`;

ALTER TABLE `default_info`
  ADD CONSTRAINT `default_info_ibfk_1` FOREIGN KEY (`agent`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `default_info_ibfk_2` FOREIGN KEY (`team`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `pages`
  ADD COLUMN `team` MEDIUMINT(8) UNSIGNED DEFAULT NULL AFTER `agent`,
  DROP FOREIGN KEY `pages_ibfk_1`,
  MODIFY COLUMN `agent` MEDIUMINT(8) UNSIGNED DEFAULT '1',
  ADD KEY `team` (`team`);

ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`agent`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`team`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `snippets`
  ADD COLUMN `team` MEDIUMINT(8) UNSIGNED DEFAULT NULL AFTER `agent`,
  DROP FOREIGN KEY `snippets_ibfk_1`,
  MODIFY COLUMN `agent` MEDIUMINT(8) UNSIGNED DEFAULT '1',
  ADD UNIQUE KEY `team` (`team`,`name`);

ALTER TABLE `snippets`
  ADD CONSTRAINT `snippets_ibfk_1` FOREIGN KEY (`agent`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `snippets_ibfk_2` FOREIGN KEY (`team`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
ALTER TABLE `numlinks`
  ADD COLUMN `team` MEDIUMINT(8) UNSIGNED DEFAULT NULL AFTER `agent`,
  DROP INDEX `PRIMARY`,
  DROP FOREIGN KEY `numlinks_ibfk_1`,
  MODIFY COLUMN `agent` MEDIUMINT(8) UNSIGNED DEFAULT '1',
  ADD UNIQUE KEY `agent` (`agent`),
  ADD UNIQUE KEY `team` (`team`);
  
ALTER TABLE `numlinks`  
  ADD CONSTRAINT `numlinks_ibfk_1` FOREIGN KEY (`agent`) REFERENCES `agents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `numlinks_ibfk_2` FOREIGN KEY (`team`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `rewidx_quicksearch`
  ADD COLUMN `team` MEDIUMINT(8) UNSIGNED DEFAULT NULL AFTER `agent`,
  DROP INDEX `PRIMARY`,
  MODIFY COLUMN `agent` MEDIUMINT(8) UNSIGNED DEFAULT '1',
  ADD UNIQUE KEY `agent` (`agent`,`idx`),
  ADD UNIQUE KEY `team` (`team`,`idx`);

  