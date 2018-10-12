<?php

// Directory Paths
define('DIR_INCLUDE', $_SERVER['DOCUMENT_ROOT']. '/idx/inc/php/');
define('DIR_FEATURED_IMAGES', $_SERVER['DOCUMENT_ROOT']. '/uploads/featured/');

// URL Strcture
define('URL', Settings::getInstance()->SETTINGS['URL']);
define('URL_IDX', URL . 'idx/');
define('URL_FEATURED_IMAGES', URL . 'uploads/featured/');

// MySQL Tables
define('TABLE_USERS', 'users');
define('TABLE_AGENTS', 'agents');
define('TABLE_MESSAGES', 'users_messages');
define('TABLE_SAVED_LISTINGS', 'users_listings');
define('TABLE_SAVED_SEARCHES', 'users_searches');
define('TABLE_VIEWED_LISTINGS', 'users_viewed_listings');
define('TABLE_VIEWED_SEARCHES', 'users_viewed_searches');
define('TABLE_FEATURED_LISTINGS', 'featured_listings');
define('TABLE_FEATURED_OFFICES', 'featured_offices');
