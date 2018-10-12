ALTER TABLE `users_forms`
    ADD `page` LONGTEXT NOT NULL AFTER  `data`,
    ADD `reply` INT( 10 ) UNSIGNED NULL,
    ADD `read` TIMESTAMP NULL DEFAULT NULL,
    ADD INDEX ( `reply` ),
    ADD INDEX ( `read` )
;