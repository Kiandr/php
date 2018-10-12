-- ---------------------------------------------------
-- Add `users`.`manual` to Track Manually Added Leads
-- ---------------------------------------------------
ALTER TABLE `users`
	ADD `manual` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no' AFTER `forms` 
;

-- ---------------------------------------------------
-- Update `users`.`manual` for Manually Added Leads
-- ---------------------------------------------------
UPDATE
	`users` `l`
LEFT JOIN
	`history_users` `u` ON `l`.`id` = `u`.`user` AND `u`.`type` = 'Lead' 
LEFT JOIN
	`history_users` `a` ON `u`.`event` = `a`.`event` AND `a`.`type` = 'Agent' 
LEFT JOIN
	`history_events` `e` ON `u`.`event` = `e`.`id` AND `e`.`type` = 'Create' AND `e`.`subtype` = 'Lead'
SET
	`l`.`manual` = 'yes'
WHERE 
	`u`.`user` IS NOT NULL
	AND `a`.`user` IS NOT NULL
	AND `e`.`id` IS NOT NULL
;