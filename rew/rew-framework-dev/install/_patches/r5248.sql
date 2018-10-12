-- Add `agents`.`columns_agents` to Store Columns to Display on Manage Agents
ALTER TABLE `agents`
	ADD `columns_agents` VARCHAR( 200 ) NOT NULL DEFAULT 'leads,login' AFTER `columns` 
;