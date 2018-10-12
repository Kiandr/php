<?php

// Require Module
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS'])) {
    return;
}

// Results view (Default: grid)
$view = !empty($this->config['view']) ? $this->config['view'] : 'grid';

// Limit (Default: 6)
$limit = !empty($this->config['limit']) && is_int($this->config['limit']) ? $this->config['limit'] : 6;

// Current $_REQUEST
$request = $_REQUEST;

// Get CMS Database
$db = DB::get('cms');

// Search View
$_REQUEST['view'] = $view;

// Page Limit
$_REQUEST['page_limit'] = $limit;

// Search All Cities
$_REQUEST['search_city'] = array();

// Search All Types
$_REQUEST['search_type'] = array();

// Search by MLS Number
$_REQUEST['search_mls'] = array();

try {
    // Select & Build Featured Listings Collection
    $mls_numbers = $db->fetchAll("SELECT `mls_number` FROM `featured_listings`;");
    foreach ($mls_numbers as $mls_number) {
        $_REQUEST['search_mls'][] = $mls_number['mls_number'];
    }
} catch (Exception $e) {
    Log::error($e);
}

// No Featured Listings
if (empty($_REQUEST['search_mls'])) {
    $_REQUEST['search_mls'][] = 'No Featured Listings';
}

// Snippet
$_REQUEST['snippet'] = true;

// Page
$page = $this->getContainer()->getPage();

// Load IDX Search Page
$search = $page->load('idx', 'search', Settings::getInstance()->IDX_FEED);

// Multi-IDX
if (!empty(Settings::getInstance()->IDX_FEEDS)) {
    $feeds = $page->container('feeds')->addModule('idx-feeds', array(
        'mode' => 'inline',
    ));
    $search['category_html'] = $feeds->display(false) . $search['category_html'];
}

// Print Output
echo $search['category_html'];

// Restore $_REQUEST
$_REQUEST = $request;

// Snippet
$_REQUEST['snippet'] = !empty($mls_numbers);
