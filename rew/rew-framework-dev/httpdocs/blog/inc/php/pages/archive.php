<?php

// Archive Date
$date = strtotime($_GET['year'] . '-' . $_GET['month'] . '-01');

// DB Connection
$db = DB::get('blog');

// Count Blog Entries
$query = $db->prepare("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW() AND DATE_FORMAT(`timestamp_published`, '%Y-%m') = :date " . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " ORDER BY `timestamp_published` DESC;");
$query->execute(array('date' => date('Y-m', $date)));
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

    // Select Archived Blog Entries
    $entries = array();
    $result = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true'  AND `timestamp_published` < NOW() AND DATE_FORMAT(`timestamp_published`, '%Y-%m') = :date" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " ORDER BY `timestamp_published` DESC" . $sql_limit . ";");
    $result->execute(array('date' => date('Y-m', $date)));
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

// Dynamic Page Title
$page_title = $blog_settings['page_title'] . ' - Blog Archive: ' . date('F, Y', $date);
$page_title = $page_title . (isset($_GET['p']) && $_GET['p'] > 0 ? ' | Page #' . (int) $_GET['p'] : '');
