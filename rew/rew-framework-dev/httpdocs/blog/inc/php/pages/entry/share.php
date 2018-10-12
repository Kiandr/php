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

    // Entry Meta Information
    $page_title = __('Share with your Friends - %s', $entry['title']);
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
