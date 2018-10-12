-- Add Columns to Store Network Account Information
ALTER TABLE `agents`
	ADD `network_facebook` LONGTEXT NOT NULL ,
	ADD `network_linkedin` LONGTEXT NOT NULL ,
	ADD `network_twitter` LONGTEXT NOT NULL ,
	ADD `network_google` LONGTEXT NOT NULL
;