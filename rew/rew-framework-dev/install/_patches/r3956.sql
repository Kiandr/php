--
-- Add Columns:
-- `default_info`.`facebook_apikey`
-- `default_info`.`facebook_secret`
-- `default_info`.`google_apikey`
-- `default_info`.`google_secret`
-- `default_info`.`microsoft_apikey`
-- `default_info`.`microsoft_secret`
--
ALTER TABLE `default_info`
	ADD `facebook_apikey` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `auto_rotate_frequency` ,
	ADD `facebook_secret` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `facebook_apikey`,
	ADD `google_apikey` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `facebook_secret` ,
	ADD `google_secret` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `google_apikey`,
	ADD `microsoft_apikey` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `google_secret` ,
	ADD `microsoft_secret` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `microsoft_apikey`
;

--
-- Add Columns:
-- `users`.`network_facebook`
-- `users`.`network_google`
-- `users`.`network_microsoft`
--
ALTER TABLE  `users`
	ADD `network_facebook` LONGTEXT NOT NULL AFTER `search_maximum_price` ,
	ADD `network_google` TEXT NOT NULL AFTER `network_facebook` ,
	ADD `network_microsoft` LONGTEXT NOT NULL AFTER `network_google`
;

--
-- Add 'Social Connect' Auto-Responder
--
REPLACE INTO `auto_responders` (`id`, `title`, `from_name`, `from_email`, `cc_email`, `bcc_email`, `subject`, `document`, `tempid`, `is_html`, `active`) VALUES
(12, 'Social Connect', 'Not Set Up', 'notsetup@realestatewebmasters.com', '', '', 'Welcome to the site! [Social Connect]', '<p>Hi {first_name} {last_name},</p>\r\n<p>Thanks for visiting our site! Please let us know how we can help you with your home search. Would you like to view homes in person anytime soon?</p>\r\n<p>Thanks</p>', NULL, 'true', 'N');