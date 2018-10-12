ALTER TABLE  `pages`
	ADD  `template` VARCHAR (100) NOT NULL AFTER  `summary`,
	ADD  `variables` LONGTEXT NOT NULL AFTER  `template`
;

ALTER TABLE  `default_info`
	ADD  `template` VARCHAR (100) NOT NULL AFTER  `summary`,
	ADD  `variables` LONGTEXT NOT NULL AFTER  `template`
;