ALTER TABLE `directory_categories`
	ADD INDEX (`link`),
	ADD INDEX (`parent`)
;

ALTER TABLE `directory_listings`
	ADD UNIQUE (`link`),
	ADD INDEX (`pending`),
	ADD INDEX (`session_id`)
;
