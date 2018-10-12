<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// HTTP Structure
define('URL', Settings::getInstance()->SETTINGS['URL']);
define('URL_BLOG', URL . 'blog/');
define('URL_BLOG_IMAGES', URL . 'img/blog/');
define('URL_BLOG_AUTHOR', URL_BLOG . 'author/%s/');
define('URL_BLOG_ARCHIVE', URL_BLOG . '%s/');
define('URL_BLOG_CATEGORY', URL_BLOG . '%s/');
define('URL_BLOG_TAG', URL_BLOG . 'tags/%s/');
define('URL_BLOG_ENTRY', URL_BLOG . '%s.html');
define('URL_BLOG_ENTRY_PRINT', URL_BLOG . '%s/print.html');
define('URL_BLOG_ENTRY_SHARE', URL_BLOG . '%s/share.html');
define('URL_BLOG_AUTHOR_IMAGES', URL . 'uploads/agents/');
define('URL_BLOG_AUTHOR_THUMBS', URL . 'uploads/agents/thumbs/');
define('URL_BLOG_UNSUBSCRIBE', URL_BLOG . '%s.html?unsubscribe=%s');

// Path Structure
define('DIR_BLOG_AUTHOR_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/agents/');
define('DIR_BLOG_AUTHOR_THUMBS', $_SERVER['DOCUMENT_ROOT'] . '/uploads/agents/thumbs/');

// MySQL Tables
define('TABLE_BLOG_AUTHORS', 'agents');
define('TABLE_BLOG_SETTINGS', 'blog_settings');
define('TABLE_BLOG_ENTRIES', 'blog_entries');
define('TABLE_BLOG_COMMENTS', 'blog_comments');
define('TABLE_BLOG_CATEGORIES', 'blog_categories');
define('TABLE_BLOG_PINGS', 'blog_pings');
define('TABLE_BLOG_LINKS', 'blog_links');
define('TABLE_BLOG_TAGS', 'blog_tags');
define('LM_TABLE_TIMEZONES', 'timezones');

// Misc. Settings
define('BLOG_PAGE_LIMIT', 10);

// Blog Settings
$db = DB::get('blog');
$blog_settings = $db->fetch("SELECT * FROM `" . TABLE_BLOG_SETTINGS . "`;");

// Meta Information
$page_title = $blog_settings['page_title'];
$meta_desc  = $blog_settings['meta_tag_desc'];
