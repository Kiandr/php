<?php

// DB Connection
$db = DB::get('directory');

// Search Criteria
$search_where = array();

// Search by category
if (!empty($_GET['search_category']) && is_array($_GET['search_category'])) {
    $cat_where = array();
    foreach ($_GET['search_category'] as $category) {
        $category = trim($category);
        if (empty($category)) {
            continue;
        }
        $cat_where[] = 'FIND_IN_SET(' . $db->quote($category) . ', `categories`)';
    }
    if (!empty($cat_where)) {
        $search_where[] = '(' . implode(' OR ', $cat_where) . ')';
    }
}

// Search by keyword
if (!empty($_GET['search_keyword'])) {
    $key_where = '';
    $inclusions = array();
    $exclusions = array();

    // Process Keywords
    $keywords = explode(',', $_GET['search_keyword']);
    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if (empty($keyword)) {
            continue;
        }
        if (preg_match('/exclude\-/', $keyword)) {
            $exclusions[] = preg_replace('/exclude\-/', '', $keyword);
        } else {
            $inclusions[] = $keyword;
        }
    }

    // Include keywords
    if (!empty($inclusions)) {
        $search_where[] = '(' . implode(' OR ', array_map(function ($keyword) use ($db) {
             return "("
                . "FIND_IN_SET(" . $db->quote($keyword) . ", `categories`)"
                . " OR `business_name` LIKE " . $db->quote('%' . $keyword . '%')
                . " OR `page_title` LIKE " . $db->quote('%' . $keyword . '%')
            . ")";
        }, $inclusions)) . ')';
    }

    // Exclude keywords
    if (!empty($exclusions)) {
        $search_where[] = '(' . implode(' OR ', array_map(function ($keyword) use ($db) {
            return "("
                . "NOT FIND_IN_SET(" . $db->quote($keyword) . ", `categories`)"
                . " AND `business_name` NOT LIKE " . $db->quote('%' . $keyword . '%')
                . " AND `page_title` NOT LIKE '%" . $db->quote('%' . $keyword . '%')
            . ")";
        }, $exclusions)) . ')';
    }
}

// Not Pending
$search_where[] = "`pending` = 'N'";

// SQL WHERE
$search_where = !empty($search_where) ? ' WHERE ' . implode(' AND ', $search_where) : '';

// Count Category Blog Entries
$result = $db->query("SELECT COUNT(`id`) AS `total` FROM `directory_listings`" . $search_where . ";");
$count_entries = $result->fetchColumn();

// Directory Entries Found
if (!empty($count_entries)) {
    // SQL Limit
    $limit = ($_GET['page_limit'] > 0) ? $_GET['page_limit'] : DIRECTORY_PAGE_LIMIT;
    if ($count_entries > $limit) {
        $limitvalue = (($_GET['p'] - 1) * $limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . $limit;
    }

    // Generate Pagination
    $pagination = generate_page_bar($count_entries, $_GET['p'], $limit);

    // Find "Pagination" TPL
    $pagination_tpl = $page->locateTemplate('directory', 'misc', 'pagination');

    // Sort By
    if (!empty($_GET['sort_by'])) {
        $order = explode('-', $_GET['sort_by']);
        $sort_order = '`' . $order[1] . '` ' . $order[0];
    } else {
        $sort_order = '`featured`,`business_name` ASC';
    }

    // Entries
    $entries = array();

    // Directory listings
    $entries = array();
    $result = $db->query("SELECT * FROM `directory_listings`" . $search_where . " ORDER BY " . $sort_order . $sql_limit . ";");
    while ($entry = $result->fetch()) {
        $entries[] = entry_parse($entry);
    }
}
