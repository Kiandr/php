ALTER TABLE `agents` CHANGE `image` `image` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `agents` ADD `website` VARCHAR( 200 ) NOT NULL AFTER `agent_id` ;