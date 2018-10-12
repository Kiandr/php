--
-- Improve binary tree indexes (performance increase)
--
ALTER TABLE `history_events` DROP INDEX `type`, ADD INDEX `type` (`subtype`, `type`);
ALTER TABLE `history_users` DROP INDEX `user`, ADD INDEX `user` (`user`, `type`);
