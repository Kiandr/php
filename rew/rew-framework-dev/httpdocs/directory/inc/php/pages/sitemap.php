<?php

// DB Connection
$db = DB::get('directory');

// Display Categories
if ($directory_settings['sitemap'] == 'cat') {
    // Main categories
    $sitemap_cats = array();
    $categories = $db->prepare("SELECT * FROM `directory_categories` WHERE `parent` = :parent ORDER BY `order` ASC, `title` ASC;");
    $categories->execute(array('parent' => ''));
    while ($category = $categories->fetch()) {
        // Sub-categories
        $category['subcategories'] = array();
        $subcategories = $db->prepare("SELECT * FROM `directory_categories` WHERE `parent` = :parent ORDER BY `order` ASC, `title` ASC;");
        $subcategories->execute(array('parent' => $category['link']));
        while ($subcategory = $subcategories->fetch()) {
            // Sub-sub-categories
            $subcategory['subcategories'] = array();
            $subsubcategories = $db->prepare("SELECT * FROM `directory_categories` WHERE `parent` = :parent ORDER BY `order` ASC, `title` ASC;");
            $subsubcategories->execute(array('parent' => $subcategory['link']));
            while ($subsubcategory = $subsubcategories->fetch()) {
                $subcategory['subcategories'][] = $subsubcategory;
            }

            // Add sub-category
            $category['subcategories'][] = $subcategory;
        }

        // Add category
        $sitemap_cats[] = $category;
    }
}

// Listings
$entries = array();

// Category Sitemap (Show 10 Random Listings)
if ($directory_settings['sitemap'] == 'cat') {
    $sql_order = " ORDER BY RAND()";
    $sql_limit = " LIMIT 10";

// Listing Sitemap
} else {
    // Count Listings
    $count = $db->query("SELECT COUNT(`id`) AS `total` FROM `directory_listings` WHERE `pending` = 'N';");
    $count = $count->fetchColumn();

    // SQL Order
    $sql_order = " ORDER BY `business_name`";

    // SQL Limit
    $limit = 75;
    if ($count > $limit) {
        $limitvalue = (($_GET['p'] - 1) * $limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . $limit;
    }

    // Generate Pagination
    $pagination = generate_page_bar($count, $_GET['p'], $limit);

    // Set Prev/Next
    if (!empty($pagination['prev'])) {
        $page->info('link.prev', $pagination['prev']['url']);
    }
    if (!empty($pagination['next'])) {
        $page->info('link.next', $pagination['next']['url']);
    }

    // Find "Pagination" TPL
    $pagination_tpl = $page->locateTemplate('directory', 'misc', 'pagination');
}

// Directory listings
$listings = array();
$result = $db->query("SELECT * FROM `directory_listings` WHERE `pending` = 'N'" . $sql_order . $sql_limit . ";");
while ($listing = $result->fetch()) {
    $listings[] = entry_parse($listing);
}
