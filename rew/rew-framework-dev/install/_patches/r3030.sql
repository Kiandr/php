-- Insert `calendar_types`
INSERT INTO `calendar_types` (`agent`, `title`, `timestamp_created`, `timestamp_updated`) (
    SELECT
        `r`.`agent`, `r`.`type`, NOW(), NOW()
    FROM
        `users_reminders` `r`
    LEFT JOIN
        `calendar_types` `t` ON `r`.`type` = `t`.`title`
    WHERE
        `t`.`id` IS NULL
        AND `r`.`type` NOT IN (SELECT `id` FROM `calendar_types`)
    GROUP BY
        `r`.`type`
);

-- Set `users_reminders`.`type`
UPDATE
    `users_reminders` `r`
LEFT JOIN
    `calendar_types` `t` ON `r`.`type` = `t`.`title`
SET
    `r`.`type` = `t`.`id`
WHERE
    `t`.`id` IS NOT NULL
;

-- Update `users_reminders`
ALTER TABLE `users_reminders`
    CHANGE `type` `type` MEDIUMINT( 8 ) UNSIGNED NOT NULL,
    ADD `share` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `details`,
    ADD INDEX ( `type` ),
    ADD INDEX ( `share` ),
    ADD INDEX ( `timestamp` )
;