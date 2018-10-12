-- Change defaults for notifications to 'yes'
ALTER TABLE `users`
	CHANGE `notify_favs` `notify_favs` ENUM( 'yes', 'no' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'yes',
	CHANGE `notify_searches` `notify_searches` ENUM( 'yes', 'no' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'yes';