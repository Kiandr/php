--
-- Add `default_info`.`summary`
--
ALTER TABLE `default_info` ADD `summary` LONGTEXT NOT NULL AFTER `category_html` ;

--
-- Add `pages`.`summary`
--
ALTER TABLE `pages` ADD `summary` LONGTEXT NOT NULL AFTER `category_html` ;