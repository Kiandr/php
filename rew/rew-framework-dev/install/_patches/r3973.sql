-- Created `history_data` Table
CREATE TABLE `history_data` (
	`event` INT(10) UNSIGNED NOT NULL,
	`data` LONGTEXT NOT NULL,
	UNIQUE (`event`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Constraints for `history_data`
ALTER TABLE `history_data`
	ADD CONSTRAINT `history_data_ibfk_1` FOREIGN KEY (`event`) REFERENCES `history_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
;

-- Populate `history_data` Table
INSERT INTO `history_data` (
	SELECT `id`, `data` FROM `history_events`
);

-- Remove `history_events`.`data`
ALTER TABLE `history_events` DROP `data`;