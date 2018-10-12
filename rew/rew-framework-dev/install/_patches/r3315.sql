-- Add `users_notes`.`share`
ALTER TABLE `users_notes`
    ADD `share` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `type`,
    ADD INDEX ( `share` );
;