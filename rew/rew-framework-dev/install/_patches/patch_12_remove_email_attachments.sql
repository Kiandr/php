-- Adds a category field to define whether a file is for email or general website use.

ALTER TABLE `cms_files` ADD `category` VARCHAR( 100 ) NULL AFTER `id` ,
ADD INDEX ( `category` ) ;
