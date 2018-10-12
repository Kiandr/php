<?php

// Process Search
if (!empty($_GET['search']) || !empty($_GET['search_category'])) {
    // DB Connection
    $db = DB::get('directory');

    // Keywords to search for
    $keywords = array($_GET['search']);
    if (strtolower(substr($_GET['search'], -1)) == 's') {
        if (strtolower(substr($_GET['search'], -2)) == "'s") {
            $keywords[] = substr($_GET['search'], 0, -2) . 's';
            $keywords[] = substr($_GET['search'], 0, -2);
        } else {
            $keywords[] = substr($_GET['search'], 0, -1) . '\'s';
        }
    } else {
        $keywords[] = $_GET['search'] . 's';
    }

    // Search directory categories
    $cat_search = implode(' OR ', array_map(function ($keyword) use ($db) {
        return "`title` LIKE " . $db->quote('%' . $keyword . '%');
    }, $keywords));

    // Search directory listings
    $listing_search = implode(' OR ', array_map(function ($keyword) use ($db) {
        return "("
            . "`business_name` LIKE " . $db->quote('%' . $keyword . '%')
            . " OR `address` LIKE " . $db->quote('%' . $keyword . '%')
            . " OR `description` LIKE " . $db->quote('%' . $keyword . '%')
        . ")";
    }, $keywords));

    // Search cms pages
    $cms_search = implode(' OR ', array_map(function ($keyword) use ($db) {
        return "("
            . "`page_title` LIKE " . $db->quote('%' . $keyword . '%')
            . " OR `meta_tag_desc` LIKE " . $db->quote('%' . $keyword . '%')
            . " OR `meta_tag_keywords` LIKE " . $db->quote('%' . $keyword . '%')
        . ")";
    }, $keywords));

    // Matched Categories
    $categories = array();
    $result = $db->query("SELECT `link`, `title` FROM `directory_categories` WHERE " . $cat_search . " ORDER BY `order` ASC, `title` ASC;");
    $categories = $result->fetchAll();
    $category_count = $result->rowCount();

    // Count Category Entries
    $count = $db->query("SELECT COUNT(`id`) AS `total` FROM `directory_listings` WHERE (" . $listing_search . ") AND `pending` = 'N' ORDER BY `business_name` ASC;");
    $listing_count = $count->fetchColumn();

    // Directory Entries Found
    $listings = array();
    if (!empty($listing_count)) {
        // SQL Limit
        if ($listing_count > DIRECTORY_PAGE_LIMIT) {
            $limitvalue = (($_GET['p'] - 1) * DIRECTORY_PAGE_LIMIT);
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
            $sql_limit  = " LIMIT " . $limitvalue . ", " . DIRECTORY_PAGE_LIMIT;
        }

        // Generate Pagination
        $pagination = generate_page_bar($listing_count, $_GET['p'], DIRECTORY_PAGE_LIMIT);

        // Set Prev/Next
        if (!empty($pagination['prev'])) {
            $page->info('link.prev', $pagination['prev']['url']);
        }
        if (!empty($pagination['next'])) {
            $page->info('link.next', $pagination['next']['url']);
        }

        // Find "Pagination" TPL
        $pagination_tpl = $page->locateTemplate('directory', 'misc', 'pagination');

        // Entries
        $entries = array();

        // Select Category Blog Entries
        $result = $db->query("SELECT * FROM `directory_listings` WHERE (" . $listing_search . ") AND `pending` = 'N' ORDER BY `featured`,`business_name` ASC" . $sql_limit . ";");
        while ($listing = $result->fetch()) {
            $listings[] = entry_parse($listing);
        }
    }

    // CMS Pages Found
    $cms_pages = array();

    // Check Homepage
    $result = $db->query("SELECT `page_title` FROM `default_info` WHERE (" . $cms_search . ") AND `agent` = 1 LIMIT 1;");
    $cms_page = $result->fetch();
    if (!empty($cms_page)) {
        $cms_pages[] = array('value' => '/', 'title' => $cms_page['page_title']);
    }

    // Check Internal Pages
    $result = $db->query("SELECT `file_name`, `page_title` FROM `pages` WHERE (" . $cms_search . ") AND `is_link` != 't' AND `hide_sitemap` != 't' AND `agent` = 1 ORDER BY `page_title` ASC;");
    while ($cms_page = $result->fetch()) {
        $cms_pages[] = array('value' => '/' . $cms_page['file_name'] . '.php', 'title' => $cms_page['page_title']);
    }

    // Count pages found
    $cms_pages_count = count($cms_pages);

    // Category Meta Information
    $page_title = Format::htmlspecialchars($_GET['search']) . ' in the ' . Format::htmlspecialchars($directory_settings['directory_name']);
}
