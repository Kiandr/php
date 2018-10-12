-- ------------------------
-- Add column to track agent's testimonials
-- `testimonials`.`agent_id`
-- ------------------------

ALTER TABLE `testimonials`
	ADD `agent_id` MEDIUMINT( 8 ) UNSIGNED NULL DEFAULT NULL AFTER `id` ,
	ADD INDEX ( `agent_id` )
;

ALTER TABLE `testimonials`
	ADD  FOREIGN KEY (`agent_id`) REFERENCES `agents`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
;