<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Include Needed Files
include_once __DIR__ . '/inc/php/functions/funcs.Directory.php';

// HTTP Structure
define('URL', Settings::getInstance()->SETTINGS['URL']);
define('URL_DIRECTORY', URL . 'directory/');
define('URL_DIRECTORY_CATEGORY', URL_DIRECTORY . '%s/');
define('URL_DIRECTORY_LISTING', URL_DIRECTORY . '%s/%s.html');
define('URL_DIRECTORY_SEARCH', URL_DIRECTORY . 'search.html');

// MySQL Tables
define('TABLE_DIRECTORY_SETTINGS', 'directory_settings');
define('TABLE_DIRECTORY_ENTRIES', 'directory_listings');
define('TABLE_DIRECTORY_CATEGORIES', 'directory_categories');

// Misc. Settings
define('DIRECTORY_PAGE_LIMIT', 10);

// Directory Settings
$db = DB::get('directory');
$directory_settings = $db->fetch("SELECT * FROM `directory_settings` LIMIT 1;");

// Meta Information
$page_title = (!empty($directory_settings['page_title'])) ? $directory_settings['page_title'] : $directory_settings['directory_name'];
$meta_desc  = $directory_settings['meta_tag_desc'];
$meta_keyw  = $directory_settings['meta_tag_keywords'];
$hide_slideshow = $directory_settings['hide_slideshow'];
