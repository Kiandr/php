<?php

// DB Connection
$db = DB::get('blog');

// Select Entry
$query = $db->prepare("SELECT * FROM " . TABLE_BLOG_ENTRIES . " WHERE `published` = 'true' AND `timestamp_published` < NOW() AND `link` = :entry" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . ";");
$query->execute(array('entry' => $_GET['entry']));
$entry = $query->fetch();
if (empty($entry)) {
    // Send 404 Header
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
} else {
    try {
        // Select Author
        $query = $db->prepare("SELECT a.*, t.`time_diff`, t.`daylight_savings` FROM " . TABLE_BLOG_AUTHORS . " a LEFT JOIN " . LM_TABLE_TIMEZONES . " t ON a.`timezone` = t.`id` WHERE a.`id` = :author;");
        $query->execute(array('author' => $entry['agent']));
        $author = $query->fetch();
        if (!empty($author)) {
            $author['name'] = $author['first_name'] . ' ' . $author['last_name'];
            $author['link'] = Format::slugify($author['name']);
        }

    // Database error
    } catch (PDOException $e) {
        Log::error($e);
    }

    try {
        // Count Comments & Pingbacks
        $query = $db->prepare("SELECT SUM(`total`) AS `total` FROM ((SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_PINGS . "` WHERE `published` = 'true' AND `entry` = :entry) UNION ALL (SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true' AND `entry` = :entry)) AS `t`");
        $query->execute(array('entry' => $entry['id']));
        $count_comments = $query->fetch();

        // Check Comments & Pingbacks
        if (!empty($count_comments['total'])) {
            $comments = $db->prepare("(SELECT `id`, `agent`, `entry`, `website`, `timestamp_created`, CONCAT('[...]', `excerpt`, '[...]') AS `comment`, `page_title` AS `name` FROM `" . TABLE_BLOG_PINGS . "` WHERE `published` = 'true' AND `entry` = :entry) UNION ALL (SELECT `id`, `agent`, `entry`, `website`, `timestamp_created`, `comment`, `name` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true' AND `entry` = :entry) ORDER BY `timestamp_created` ASC");
            $comments->execute(array('entry' => $entry['id']));
        }

    // Database error
    } catch (PDOException $e) {
        unset($comments);
        Log::error($e);
    }

    try {
        // Blog Tags
        if (!empty($entry['tags'])) {
            $tags = array();
            $find_tag = $db->prepare("SELECT COUNT(`be`.`id`) AS `total`, `bt`.`link`, `bt`.`title` FROM `" . TABLE_BLOG_TAGS . "` `bt`, `" . TABLE_BLOG_ENTRIES . "` `be` WHERE `bt`.`title` LIKE :tag AND FIND_IN_SET(`bt`.`title`, `be`.`tags`) AND `be`.`published` = 'true' AND `be`.`timestamp_published` < NOW() GROUP BY `bt`.`title` ORDER BY `total` DESC");
            foreach (explode(',', $entry['tags']) as $tag) {
                if (empty($tag)) {
                    continue;
                }
                $find_tag->execute(array('tag' => '%' . $tag . '%'));
                $tag = $find_tag->fetch();
                if (!empty($tag)) {
                    $tags[] = $tag;
                }
            }
        }

    // Database error
    } catch (PDOException $e) {
        Log::error($e);
    }

    // Entry Meta Information
    $page_title = $entry['title'];
    $meta_desc  = $entry['meta_tag_desc'];

    // Blog Entry Snippets
    preg_match_all("!(#([a-zA-Z0-9_-]+)#)!", $entry['body'], $matches);
    if (!empty($matches)) {
        foreach ($matches[1] as $match) {
            $snippet = rew_snippet($match, false);
            $entry['body'] = str_replace($match, $snippet, $entry['body']);
        }
    }
}
