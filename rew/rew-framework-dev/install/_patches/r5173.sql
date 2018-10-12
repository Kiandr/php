-- Added `pages`.`hide_sitemap` to exclude Pages from CMS/XML Sitemaps
ALTER TABLE `pages`
	ADD `hide_sitemap` ENUM( 't', 'f' ) NOT NULL DEFAULT 'f' AFTER `hide`
;

-- Exclude certain Pages from CMS/XML Sitemaps
UPDATE `pages` SET `hide_sitemap` = 't' WHERE `file_name` IN ('test', 'forms', 'unsubscribe', '404', 'error');