-- Backend Auto/Smart Search Global Setting
ALTER TABLE `default_info` ADD `auto_generated_searches` enum('true','false') NOT NULL default 'false' AFTER `auto_assign_lenders`;
