-- Add `featured_communities`.`is_enabled` ENUM
ALTER TABLE `featured_communities`
	ADD `is_enabled` ENUM('Y', 'N') NOT NULL DEFAULT 'N' AFTER `description`;

-- Enable existing featured community records
UPDATE `featured_communities` SET `is_enabled` = 'Y';
