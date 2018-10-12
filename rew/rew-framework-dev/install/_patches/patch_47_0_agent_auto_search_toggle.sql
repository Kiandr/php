-- Backend Auto/Smart Search Agent Toggle
ALTER TABLE `agents` ADD `auto_search` enum('true','false') NOT NULL default 'true' AFTER `auto_optout_time`;
