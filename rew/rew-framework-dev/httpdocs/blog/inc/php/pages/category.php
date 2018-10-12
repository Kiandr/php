<?php

// DB Connection
$db = DB::get('blog');

// Select Category
$query = $db->prepare("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `link` = :category LIMIT 1;");
$query->execute(array('category' => $_GET['category']));
$category = $query->fetch();
if (empty($category)) {
    // Send 404 Header
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
} else {
    // Count Category Blog Entries
    $query = $db->prepare("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW() AND FIND_IN_SET(:category, `categories`)" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " ORDER BY `timestamp_published` DESC;");
    $query->execute(array('category' => $category['link']));
    $count_entries = $query->fetchColumn();
    if (!empty($count_entries)) {
        // Generate Pagination
        $sql_limit = '';
        if ($count_entries > BLOG_PAGE_LIMIT) {
            // Generate Pagination
            $pagination = generate_page_bar($count_entries, $_GET['p'], BLOG_PAGE_LIMIT);

            // Max Page Exceeded, Show Last Page, Send 404
            if ($_GET['p'] > $pagination['pages']) {
                $_GET['p'] = $pagination['pages'];
                $pagination = generate_page_bar($count_entries, $_GET['p'], BLOG_PAGE_LIMIT);
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);

            // Invalid Page Requested, Show First Page, Send 404
            } else if (isset($_GET['p']) && $_GET['p'] < 1) {
                $_GET['p'] = 1;
                $pagination = generate_page_bar($count_entries, $_GET['p'], BLOG_PAGE_LIMIT);
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            }

            // Set Prev/Next
            if (!empty($pagination['prev'])) {
                $page->info('link.prev', $pagination['prev']['url']);
            }
            if (!empty($pagination['next'])) {
                $page->info('link.next', $pagination['next']['url']);
            }

            // SQL Limit
            $limitvalue = (($_GET['p'] - 1) * BLOG_PAGE_LIMIT);
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
            $sql_limit  = ' LIMIT ' . $limitvalue . ', ' . BLOG_PAGE_LIMIT;

            // Find "Pagination" TPL
            $pagination_tpl = $page->locateTemplate('blog', 'misc', 'pagination');

        // Page Requested, Only First Page Exists
        } elseif (isset($_GET['p']) && ($_GET['p'] > 1 || $_GET['p'] < 1)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
        }

        // Query to find author
        $find_author = $db->prepare("SELECT `a`.*, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`, `t`.`time_diff`, `t`.`daylight_savings` FROM `" . TABLE_BLOG_AUTHORS . "` `a` LEFT JOIN `" . LM_TABLE_TIMEZONES . "` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`id` = :author LIMIT 1;");

        // Query to find comments & pingbacks
        $find_comments = $db->prepare("SELECT SUM(`total`) AS `total` FROM ((SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_PINGS . "` WHERE `published` = 'true' AND `entry` = :entry) UNION ALL (SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true' AND `entry` = :entry)) AS `t`;");

        // Select Category Blog Entries
        $entries = array();
        $result = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE FIND_IN_SET(:category, `categories`) AND `published` = 'true' AND `timestamp_published` < NOW()" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " ORDER BY `timestamp_published` DESC" . $sql_limit . ";");
        $result->execute(array('category' => $category['link']));
        while ($entry = $result->fetch()) {
            // Get Blog Entry Author
            $find_author->execute(array('author' => $entry['agent']));
            $author = $find_author->fetch();
            if (!empty($author)) {
                $author['link'] = sprintf(URL_BLOG_AUTHOR, Format::slugify($author['name']));
                $entry['author'] = $author;
            }

            // Count Comments & Pingbacks
            $find_comments->execute(array('entry' => $entry['id']));
            $entry['comments'] = $find_comments->fetchColumn();

            // Format Blog Entry Preview
            $entry['body'] = preg_replace('!(#([a-zA-Z0-9_-]+)#)!', '', $entry['body']);
            $entry['body'] = Format::stripTags($entry['body'], '<img><p><br><br /><ol><ul><li><h1><h2><h3><h4><h5><b><i><strong><table><tr><td><th>');
            $entry['body'] = Format::truncate($entry['body'], 775, '&hellip;', false, true);

            // Add to Collection
            $entries[] = $entry;
        }
    }

    // Category Meta Information
    $page_title = !empty($category['page_title']) ? $category['page_title'] : $blog_settings['page_title'] . ' - ' . $category['title'];
    $page_title = $page_title . (isset($_GET['p']) && $_GET['p'] > 0 ? ' | Page #' . (int) $_GET['p'] : '');
    if (!empty($category['meta_tag_desc'])) {
        $meta_desc  = $category['meta_tag_desc'];
    }
}
