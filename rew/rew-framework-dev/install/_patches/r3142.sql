-- Make the URL case sensitive
ALTER TABLE `users_pages` CHANGE `url` `url` longtext BINARY NOT NULL;