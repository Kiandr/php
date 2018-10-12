-- Add `timestamp_idx` column to maintain the last update time that was used in the search query
-- The `timestamp_sent` column is now freed up to accurately reflect when the listing update email was sent

ALTER TABLE `users_searches` ADD `timestamp_idx` TIMESTAMP NULL DEFAULT NULL AFTER `source_app_id`;

-- Update timestamp_idx to hold the existing timestamp_sent value to ensure that the search frequency isn't affected
UPDATE `users_searches` SET `timestamp_idx` = `timestamp_sent`;
