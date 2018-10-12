<?php

// Agent Id
$agent_id = Settings::getInstance()->SETTINGS['agent'];

// Team Id
$team_id = Settings::getInstance()->SETTINGS['team'];

// Get CMS Database
$db = DB::get('cms');

// Sitemap
$sitemap = ['#' => []];
$letters = range('A', 'Z');
foreach ($letters as $letter) {
    $sitemap[$letter] = [];
}

// CMS Pages
try {
    // Load Pages
    $pages_query = $db->prepare("SELECT `file_name` AS `link`, `link_name` AS `title` FROM `pages` WHERE `agent` <=> :agent AND `team` <=> :team AND `is_link` != 't' AND `hide_sitemap` != 't' AND `file_name` NOT IN ('404', 'error', 'unsubscribe') ORDER BY `link_name` ASC;");
    $pages_query->execute([
        'agent' => $agent_id,
        'team' => $team_id
    ]);
    $pages = $pages_query->fetchAll();

    // Add to Sitemap
    array_map(function ($page) use (&$sitemap) {

        // Letter
        $letter = preg_match('/^[a-z]/i', $page['title']) ? strtoupper(substr($page['title'], 0, 1)) : '#';

        // Page Link
        $page['link'] = '/' . $page['link'] . '.php';

        // Ignore Current Page from Sitemap
        if (Http_Uri::getUri() == $page['link']) {
            return;
        }

        // Add to Sitemap
        $sitemap[$letter][$page['title']] = $page;
    }, $pages);

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}

// CMS Links
try {
    // Load Pages
    $pages_query = $db->prepare("SELECT `file_name` AS `link`, `link_name` AS `title`, `is_link`, UPPER(SUBSTR(`link_name`, 1, 1)) AS `letter` FROM `pages` WHERE `agent` <=> :agent AND `team` <=> :team AND `hide_sitemap` != 't' AND `is_link` = 't' AND LEFT(`file_name`, 1) = '/' ORDER BY `link_name` ASC;");
    $pages_query->execute([
        'agent' => $agent_id,
        'team' => $team_id
    ]);
    $pages = $pages_query->fetchAll();

    // Add to Sitemap
    array_map(function ($page) use (&$sitemap) {

        // Letter
        $letter = preg_match('/^[a-z]/i', $page['title']) ? strtoupper(substr($page['title'], 0, 1)) : '#';

        // Add to Sitemap
        $sitemap[$letter][$page['title']] = $page;
    }, $pages);

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}

// Broker Site Only, Include Blog Entries
if ($agent_id == 1 && !empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    try {
        // Blog Common File
        include_once $_SERVER['DOCUMENT_ROOT'] . '/blog/common.inc.php';

        // Load Pages
        $pages = $db->fetchAll("SELECT `link`, `title`, UPPER(SUBSTR(`title`, 1, 1)) AS `letter` FROM `blog_entries` WHERE `published` = 'true' AND `timestamp_published` < NOW() ORDER BY `title` ASC;");

        // Add to Sitemap
        array_map(function ($page) use (&$sitemap) {

            // Letter
            $letter = preg_match('/^[a-z]/i', $page['title']) ? strtoupper(substr($page['title'], 0, 1)) : '#';

            // Page Link
            $page['link'] = sprintf(URL_BLOG_ENTRY, $page['link']);

            // Add to Sitemap
            $sitemap[$letter][$page['title']] = $page;
        }, $pages);

    // Error Occurred
    } catch (Exception $e) {
        Log::error($e);
    }
}

// Unfilter Sitemap
$sitemap = array_filter($sitemap, function($letter) {
    return !empty($letter);
});