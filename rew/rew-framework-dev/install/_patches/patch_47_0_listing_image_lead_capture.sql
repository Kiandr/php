ALTER TABLE `rewidx_system` ADD COLUMN `registration_on_more_pics` enum('true','false') NOT NULL default 'false' AFTER `registration_required`;
