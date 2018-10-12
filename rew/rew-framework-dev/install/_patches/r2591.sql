-- Update `featured_communities`
ALTER TABLE `featured_communities`
    ADD `subtitle` VARCHAR( 100 ) NOT NULL AFTER `title`,
    ADD `order` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `snippet`,
    ADD INDEX ( `order` ),
    DROP `images`
;