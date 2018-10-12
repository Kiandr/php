-- Add immediately search frequency
ALTER TABLE `users_searches` MODIFY `frequency` enum('never','immediately','daily','weekly','monthly') NOT NULL default 'weekly';
