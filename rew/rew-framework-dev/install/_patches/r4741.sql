-- Add `users_viewed_searches`.`agent_id`
ALTER TABLE `users_viewed_searches`
	ADD `agent_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `user_id`, 
	ADD INDEX (`agent_id`); 
	
-- Add `users_searches`.`agent_id`
ALTER TABLE `users_searches`
	ADD `agent_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `user_id`, 
	ADD INDEX (`agent_id`); 