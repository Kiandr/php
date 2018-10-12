-- Drop Old `calendar_attendees` Table
DROP TABLE `calendar_attendees`;

-- Create New `calendar_attendees` Table
CREATE TABLE IF NOT EXISTS `calendar_attendees` (
	`type` enum('Agent','Lead') NOT NULL,
	`user` mediumint(8) unsigned NOT NULL,
	`event` mediumint(8) unsigned NOT NULL,
	PRIMARY KEY  (`event`,`user`,`type`),
	KEY `event` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- Constraints for table `calendar_attendees`
ALTER TABLE `calendar_attendees`
	ADD CONSTRAINT `calendar_attendees_ibfk_1` FOREIGN KEY (`event`) REFERENCES `calendar_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;