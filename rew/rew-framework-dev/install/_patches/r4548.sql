--
-- Add Columns:
-- `default_info`.`linkedin_apikey`
-- `default_info`.`linkedin_secret`
-- `default_info`.`twitter_apikey`
-- `default_info`.`twitter_secret`
-- `default_info`.`yahoo_apikey`
-- `default_info`.`yahoo_secret`
--
ALTER TABLE `default_info`
	ADD `linkedin_apikey` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `microsoft_secret` ,
	ADD `linkedin_secret` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `linkedin_apikey`,
	ADD `twitter_apikey` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `linkedin_secret` ,
	ADD `twitter_secret` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `twitter_apikey`,
	ADD `yahoo_apikey` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `twitter_secret` ,
	ADD `yahoo_secret` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `yahoo_apikey`
;

--
-- Add Columns:
-- `users`.`network_linkedin`
-- `users`.`network_twitter`
-- `users`.`network_yahoo`
--
ALTER TABLE  `users`
	ADD `network_linkedin` LONGTEXT NOT NULL AFTER `network_microsoft` ,
	ADD `network_twitter` LONGTEXT NOT NULL AFTER `network_linkedin` ,
	ADD `network_yahoo` LONGTEXT NOT NULL AFTER `network_twitter`
;

--
-- Add Columns:
-- `users`.`oauth_facebook`
-- `users`.`oauth_google`
-- `users`.`oauth_microsoft`
-- `users`.`oauth_linkedin`
-- `users`.`oauth_twitter`
-- `users`.`oauth_yahoo`
--  
ALTER TABLE  `users`
	ADD `oauth_facebook` VARCHAR(200) NOT NULL AFTER `search_maximum_price` ,
	ADD `oauth_google` VARCHAR(200) NOT NULL AFTER `oauth_facebook` ,
	ADD `oauth_microsoft` VARCHAR(200) NOT NULL AFTER `oauth_google`,
	ADD `oauth_linkedin` VARCHAR(200) NOT NULL AFTER `oauth_microsoft`,
	ADD `oauth_twitter` VARCHAR(200) NOT NULL AFTER `oauth_linkedin`,
	ADD `oauth_yahoo` VARCHAR(200) NOT NULL AFTER `oauth_twitter`
;

--
-- Add Columns:
-- `rewidx_system`.`copy_connect`
--
ALTER TABLE `rewidx_system`
	ADD `copy_connect` LONGTEXT NOT NULL AFTER `copy_login`
;