-- Add `position_me_data_id` to the `users` table for PositionMe ID data caching
ALTER TABLE `users` 
ADD `position_me_data_id` int(11) DEFAULT NULL AFTER `network_yahoo`;