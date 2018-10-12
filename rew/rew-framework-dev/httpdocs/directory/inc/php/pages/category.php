<?php

// DB Connection
$db = DB::get('directory');

// Require category
$result = $db->prepare("SELECT * FROM `directory_categories` WHERE `link` = :category;");
$result->execute(array('category' => $_GET['category']));
$category = $result->fetch();
if (empty($category)) {
    // Send 404 Header
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
} else {
    // Categories
    $cats = array();
    $cats[] = $category['link'];
    $sub_sql = $db->prepare("SELECT `link` FROM `directory_categories` WHERE `parent` = :category AND `link` != '' ORDER BY `order` ASC, `title` ASC;");
    $sub_sql->execute(array('category' => $category['link']));
    while ($sub_row = $sub_sql->fetch()) {
        $cats[] = $sub_row['link'];
        $tert_sql = $db->prepare("SELECT `link` FROM `directory_categories` WHERE `parent` = :subcategory AND `link` != '' ORDER BY `order` ASC, `title` ASC;");
        $tert_sql->execute(array('subcategory' => $sub_row['link']));
        while ($tert_row = $tert_sql->fetch()) {
            $cats[] = $tert_row['link'];
        }
    }

    // Find listings in category
    $cat_where = implode(' OR ', array_map(function ($cat) {
        return "FIND_IN_SET('" . $cat . "', `categories`)";
    }, $cats));

    // Count Category Blog Entries
    $result = $db->query("SELECT COUNT(`id`) AS `total` FROM `directory_listings` WHERE (" . $cat_where . ") AND `pending` = 'N' ORDER BY `business_name` ASC;");
    $count_entries = $result->fetchColumn();

    // Directory Entries Found
    if (!empty($count_entries)) {
        // SQL Limit
        if ($count_entries > DIRECTORY_PAGE_LIMIT) {
            $limitvalue = (($_GET['p'] - 1) * DIRECTORY_PAGE_LIMIT);
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
            $sql_limit  = " LIMIT " . $limitvalue . ", " . DIRECTORY_PAGE_LIMIT;
        }

        // Generate Pagination
        $pagination = generate_page_bar($count_entries, $_GET['p'], DIRECTORY_PAGE_LIMIT);

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
        $entries = array();
        $result = $db->query("SELECT * FROM `directory_listings` WHERE (" . $cat_where . ") AND `pending` = 'N' ORDER BY `featured`, `business_name` ASC" . $sql_limit . ";");
        while ($entry = $result->fetch()) {
            $entries[] = entry_parse($entry);
        }
    }

    // Refine Categories
    $refine_categories = array();
    if (!empty($category['link'])) {
        // Sub-categories
        $categories = $db->prepare("SELECT `c`.`link`, `c`.`title`, GROUP_CONCAT(DISTINCT `s`.`link` SEPARATOR ',') AS `subcategories` FROM `directory_categories` `c` LEFT JOIN `directory_categories` `s` ON `c`.`link` = `s`.`parent` WHERE `c`.`parent` = :category GROUP BY `c`.`id` ORDER BY `c`.`order` ASC, `c`.`title` ASC;");
        $categories->execute(array('category' => $category['link']));
        while ($subcategory = $categories->fetch()) {
            // Find listings in this category (or it's sub-categories)
            $subcategories = array_filter(explode(',', $subcategory['subcategories']));
            $refine_where = implode(' OR ', array_map(function ($subcat) {
                return "FIND_IN_SET('" . $subcat . "', `categories`)";
            }, array_merge(array($subcategory['link']), $subcategories)));

            // Count # of listings
            $count = $db->query("SELECT COUNT(`id`) AS total FROM `directory_listings` WHERE (" . $refine_where . ") AND `pending` = 'N';");
            $subcategory['count'] = $count->fetchColumn();

            // Add sub-category
            $refine_categories[] = $subcategory;
        }
    }

    // Related Categories
    $related_categories = array();
    if (!empty($category['related_categories'])) {
        $categories = $db->prepare("SELECT `c`.`link`, `c`.`title`,"
            . " GROUP_CONCAT(DISTINCT `s`.`link` SEPARATOR ',') AS `categories`,"
            . " GROUP_CONCAT(DISTINCT `t`.`link` SEPARATOR ',') AS `subcategories`"
            . " FROM `directory_categories` `c`"
            . " LEFT JOIN `directory_categories` `s` ON `c`.`link` = `s`.`parent`"
            . " LEFT JOIN `directory_categories` `t` ON `s`.`link` = `t`.`parent`"
            . " WHERE FIND_IN_SET(`c`.`link`, :categories)"
            . " GROUP BY `c`.`id`"
            . " ORDER BY `c`.`order` ASC, `c`.`title` ASC"
        . ";");
        $categories->execute(array('categories' => $category['related_categories']));
        while ($related = $categories->fetch()) {
            // Find listings in this category (or it's sub-categories)
            $subcategories = array_filter(explode(',', $related['categories']));
            $refine_where = implode(' OR ', array_map(function ($subcat) {
                return "FIND_IN_SET('" . $subcat . "', `categories`)";
            }, array_merge(array($related['link']), $subcategories, array_filter(explode(',', $related['subcategories'])))));

            // Count # of listings
            $count = $db->query("SELECT COUNT(`id`) AS total FROM `directory_listings` WHERE (" . $refine_where . ") AND `pending` = 'N';");
            $related['count'] = $count->fetchColumn();

            // Add related category
            $related_categories[] = $related;
        }
    }

    // Category Meta Information
    $page_title = !empty($category['page_title']) ? Format::htmlspecialchars($category['page_title']) : Format::htmlspecialchars($category['title']) . ' | ' . Format::htmlspecialchars($directory_settings['directory_name']);
    $meta_keyw  = $category['meta_tag_keywords'];
    $meta_desc  = $category['meta_tag_desc'];
}
