
--
-- Table structure for table `action_plans`
--

CREATE TABLE `action_plans` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `day_adjust` set('0','1','2','3','4','5','6') NOT NULL DEFAULT '1,2,3,4,5',
  `style` char(1) NOT NULL DEFAULT 'r',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `actionplan_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Other','Email','Call','Text','Group','Listing','Search') NOT NULL,
  `performer` enum('Agent','Lender','Associate') NOT NULL DEFAULT 'Agent',
  `automated` enum('Y','N') NOT NULL DEFAULT 'N',
  `info` text NOT NULL,
  `offset` smallint(6) unsigned NOT NULL,
  `time` time NOT NULL DEFAULT '00:00:00',
  `expire` smallint(6) NOT NULL DEFAULT '1',
  `parent_id` mediumint(8) unsigned DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `actionplan_id` (`actionplan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_emails`
--

CREATE TABLE `tasks_emails` (
  `task_id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `body` text,
  `doc_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_groups`
--

CREATE TABLE `tasks_groups` (
  `task_id` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`task_id`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_texts`
--

CREATE TABLE `tasks_texts` (
  `task_id` mediumint(8) unsigned NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_action_plans`
--

CREATE TABLE `users_action_plans` (
  `actionplan_id` mediumint(8) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `timestamp_assigned` timestamp NULL DEFAULT NULL,
  `timestamp_completed` timestamp NULL DEFAULT NULL,
  KEY `actionplan_user_ibfk_1` (`actionplan_id`),
  KEY `actionplan_user_ibfk_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_tasks`
--

CREATE TABLE `users_tasks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL,
  `task_id` mediumint(8) unsigned NOT NULL,
  `actionplan_id` mediumint(8) NOT NULL,
  `status` enum('Pending','Completed','Skipped','Expired') NOT NULL DEFAULT 'Pending',
  `performer` enum('Agent','Associate','Lender','System') DEFAULT NULL,
  `performer_id` mediumint(8) unsigned DEFAULT NULL,
  `type` enum('Other','Email','Call','Text','Group','Listing','Search') NOT NULL DEFAULT 'Other',
  `name` varchar(100) DEFAULT NULL,
  `timestamp_scheduled` timestamp NULL DEFAULT NULL,
  `timestamp_due` timestamp NULL DEFAULT NULL,
  `timestamp_resolved` timestamp NULL DEFAULT NULL,
  `timestamp_expire` timestamp NULL DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id_task_id` (`user_id`, `task_id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_tasks_notes`
--

CREATE TABLE `users_tasks_notes` (
    `id` int(11) unsigned NOT NULL auto_increment,
    `user_id` mediumint(8) unsigned NOT NULL,
    `user_task_id` int(11) UNSIGNED NOT NULL,
    `note` text NOT NULL,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (`id`),
    KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`actionplan_id`) REFERENCES `action_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks_emails`
--
ALTER TABLE `tasks_emails`
  ADD CONSTRAINT `tasks_emails_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks_groups`
--
ALTER TABLE `tasks_groups`
  ADD CONSTRAINT `tasks_groups_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks_texts`
--
ALTER TABLE `tasks_texts`
  ADD CONSTRAINT `tasks_texts_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_action_plans`
--
ALTER TABLE `users_action_plans`
  ADD CONSTRAINT `users_action_plans_ibfk_1` FOREIGN KEY (`actionplan_id`) REFERENCES `action_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_action_plans_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_tasks`
--
ALTER TABLE `users_tasks`
  ADD CONSTRAINT `users_tasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_tasks_notes`
--
ALTER TABLE `users_tasks_notes`
  ADD CONSTRAINT `users_tasks_notes_ibfk_1` FOREIGN KEY (`user_task_id`) REFERENCES `users_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
