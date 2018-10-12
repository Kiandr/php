-- Add `default_info`.`auto_rotate_unassign`
ALTER TABLE `default_info`
	ADD `auto_rotate_unassign` ENUM('true', 'false') NOT NULL DEFAULT 'false' AFTER `auto_rotate_frequency`;