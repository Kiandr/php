-- Make more room (for JSON) in `agents`.`agent_id`
ALTER TABLE `agents` CHANGE `agent_id` `agent_id` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';