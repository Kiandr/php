<?php

// DB Connection
$db = DB::get('blog');

// Tag Requested
if (!empty($_GET['tag'])) {
    // Load selected tag
    $query = $db->prepare("SELECT * FROM `" . TABLE_BLOG_TAGS . "` WHERE `link` = :tag LIMIT 1;");
    $query->execute(array('tag' => $_GET['tag']));
    $tag = $query->fetch();
    if (empty($tag)) {
        // Send 404 Header
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
    } else {
        // Count Tagged Blog Entries
        $count_entries = $db->prepare("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW() AND FIND_IN_SET(:tag, `tags`)" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " ORDER BY `timestamp_published` DESC;");
        $count_entries->execute(array('tag' => $tag['title']));
        $count_entries = $count_entries->fetchColumn();
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

            // Select Tagged Blog Entries
            $entries = array();
            $result = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW() AND FIND_IN_SET(:tag, `tags`)" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " ORDER BY `timestamp_published` DESC" . $sql_limit . ";");
            $result->execute(array('tag' => $tag['title']));
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

        // Tagged Meta Information
        $page_title = 'Blog Entries Tagged: ' . $tag['title'];
        $page_title = $page_title . (isset($_GET['p']) && $_GET['p'] > 0 ? ' | Page #' . (int) $_GET['p'] : '');
        //$page_title = !empty($tag['page_title']) ? $tag['page_title'] : $tag['title'];
        //$meta_desc  = $tag['meta_tag_desc'];
    }

// Tag Cloud
} else {
    // Blog Tags
    $tags = array();
    $tags_qty = array();

    $result = $db->query("SELECT COUNT(`be`.`id`) AS `total`, `bt`.`link`, `bt`.`title` FROM `" . TABLE_BLOG_TAGS . "` `bt`, `" . TABLE_BLOG_ENTRIES . "` `be` WHERE FIND_IN_SET(`bt`.`title`, `be`.`tags`) AND `be`.`published` = 'true' AND `be`.`timestamp_published` < NOW()" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `be`.`agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . " GROUP BY `bt`.`title` ORDER BY `total` DESC;");
    while ($tag = $result->fetch()) {
        $tags_qty[$tag['link']] = $tag['total'];
        $tags[$tag['link']] = $tag;
    }

    // Require Tags
    if (!empty($tags)) {
        // change these font sizes if you will
        $max_size = 250; // max font size in %
        $min_size = 100; // min font size in %

        // get the largest and smallest array values
        $max_qty = max(array_values($tags_qty));
        $min_qty = min(array_values($tags_qty));

        // find the range of values
        $spread = $max_qty - $min_qty;
        if (0 == $spread) { // we don't want to divide by zero
            $spread = 1;
        }

        // determine the font-size increment
        // this is the increase per tag quantity (times used)
        $step = ($max_size - $min_size) / ($spread);
    }
}
