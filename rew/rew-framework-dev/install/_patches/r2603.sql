-- Change `default_info`.`feature_tabs` To `features`, Increase from 100 to 200.
ALTER TABLE `default_info`
    CHANGE `feature_tabs` `features` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
;

-- Add `pages`.`features`
ALTER TABLE `pages`
    ADD `features` VARCHAR( 200 ) NOT NULL AFTER `category_html`
;

-- Add `blog_settings`.`features`
ALTER TABLE `blog_settings`
    ADD `features` VARCHAR( 200 ) NOT NULL AFTER `hide_slideshow`
;

-- Add `directory_settings`.`features`
ALTER TABLE `directory_settings`
    ADD `features` VARCHAR( 200 ) NOT NULL AFTER `meta_tag_desc`
;

-- Add `featured_communities`.`page_id`
ALTER TABLE `featured_communities`
    ADD `page_id` INT( 10 ) UNSIGNED NOT NULL AFTER `snippet`
;

-- Add `default_info`.`settings`
ALTER TABLE `default_info`
    ADD `settings` LONGTEXT NOT NULL
;

-- Make groups null-able
ALTER TABLE  `groups`
    CHANGE  `agent_id`  `agent_id` MEDIUMINT( 8 ) UNSIGNED NULL DEFAULT  '1',
    ADD  `user` ENUM(  'true',  'false' ) NOT NULL DEFAULT  'true' AFTER  `style`
;

-- Add Mobile Group
INSERT INTO `groups` (`agent_id`, `name`, `description`, `style`, `user`) VALUES
(NULL, 'Mobile Leads', 'Leads captured from Mobile Form', 'i', 'false');