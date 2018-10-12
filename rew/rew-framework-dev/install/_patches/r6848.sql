ALTER TABLE `users_sessions`
	CHANGE `referer` `referer` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `keywords` `keywords` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `ua` `ua` VARCHAR( 400 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `ip` `ip` INT( 10 ) UNSIGNED NULL DEFAULT NULL
;

ALTER TABLE `users_pageviews`
	CHANGE `referer_id` `referer_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL
;

ALTER TABLE `featured_communities`
	ADD UNIQUE ( `snippet` )
;

DROP TABLE
	`mobile_settings`
;