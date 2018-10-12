-- Remove "Features" from LEC6 pages that don't need them
UPDATE `pages` SET `features` = '' WHERE `agent` = 1 AND (`is_link` = 't' OR `file_name` IN ('404', 'error', 'help', 'privacy-policy', 'unsubscribe'));

-- Turn off the slideshow for pages that don't need it
UPDATE `pages` SET `hide_slideshow` = 't' WHERE `agent` = 1 AND `file_name` IN ('404', 'error', 'help', 'privacy-policy', 'unsubscribe');