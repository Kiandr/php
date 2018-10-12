<?php

// DB Connection
$db = DB::get('blog');

// Find blog author
$query = $db->prepare("SELECT `a`.*, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`, `t`.`time_diff`, `t`.`daylight_savings` FROM `" . TABLE_BLOG_AUTHORS . "` `a` LEFT JOIN `" . LM_TABLE_TIMEZONES . "` `t` ON `a`.`timezone` = `t`.`id` WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(`a`.`first_name`, ' ', `a`.`last_name`), '.', ''), '/', ''), ')', ''), '(', ''), '-', ' '), '  ', ' ') LIKE REPLACE(:author, '-', ' ')" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `a`.`id` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " LIMIT 1;");
$query->execute(array('author' => '%' . $_GET['author'] . '%'));
$author = $query->fetch();
if (empty($author)) {
    // Send 404 Header
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
} else {
    // Author Link
    $author['name'] = $author['first_name'] . ' ' . $author['last_name'];
    $author['link'] = sprintf(URL_BLOG_AUTHOR, Format::slugify($author['name']));

    // Count Author Blog Entries
    $query = $db->prepare("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `agent` = :author AND `published` = 'true' AND `timestamp_published` < NOW() ORDER BY `timestamp_published` DESC;");
    $query->execute(array('author' => $author['id']));
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

        // Query to find comments & pingbacks
        $find_comments = $db->prepare("SELECT SUM(`total`) AS `total` FROM ((SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_PINGS . "` WHERE `published` = 'true' AND `entry` = :entry) UNION ALL (SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true' AND `entry` = :entry)) AS `t`;");

        // Select Author's Blog Entries
        $entries = array();
        $result = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW() AND `agent` = :author ORDER BY `timestamp_published` DESC" . $sql_limit . ";");
        $result->execute(array('author' => $author['id']));
        while ($entry = $result->fetch()) {
            // Entry Author
            $entry['author'] = $author;

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

    // Author Meta Information
    $page_title = $blog_settings['page_title'] . ' - ' . $author['first_name'] . ' ' . $author['last_name'] . "'s Blog";
    $page_title = $page_title . (isset($_GET['p']) && $_GET['p'] > 0 ? ' | Page #' . (int) $_GET['p'] : '');
}
