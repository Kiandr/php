<?php

// Require Blog Settings
require_once Settings::getInstance()->DIRS['ROOT'] . 'blog/common.inc.php';

// Get Blog Database
$db = DB::get('blog');

// Navigation
$navigation = array();

try {
    // Blog Categories
    $query = "SELECT `link`, `title`, `parent` FROM `blog_categories` ORDER BY `parent` ASC, `order` ASC";
    if ($categories = $db->fetchAll($query)) {
        // Build Main vs Sub Categories
        foreach ($categories as $k => $category) {
            // Link URL
            $link = $category['link'];
            $category['link'] = sprintf(URL_BLOG_CATEGORY, $link);

            // Main Category
            if (empty($category['parent'])) {
                $category['subpages'] = array();
                $categories[$link] = $category;

            // Sub Category
            } else {
                $categories[$category['parent']]['subpages'][] = $category;
            }
            unset($categories[$k]);
        }
    }

    // Check Categories
    $categories = is_array($categories) ? $categories : array();

    // Category Navigation
    $navigation[] = array('title' => 'Blog Navigation', 'pages' => array_merge(array(
        array('link' => URL_BLOG, 'title' => 'All Categories')
    ), $categories));

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}

try {
    // Blog Links
    $query = "SELECT `link`, `title`, `target` FROM `blog_links` ORDER BY `order` ASC";
    if ($links = $db->fetchAll($query)) {
        $navigation[] = array('title' => 'Blogroll', 'pages' => $links);
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}

try {
    // Agent Subdomain (Only show this Agent's Blog Entries)
    $sql_agent = (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "");

    // Blog Archive
    $query = "SELECT DATE_FORMAT(`timestamp_published`, '%Y-%m') AS `link`, DATE_FORMAT(`timestamp_published`, '%M %Y') AS `title` FROM `blog_entries` WHERE `published` = 'true' AND `timestamp_published` < NOW() " . $sql_agent. " GROUP BY YEAR(`timestamp_published`), MONTH(`timestamp_published`) ORDER BY `timestamp_published` DESC";
    if ($archives = $db->fetchAll($query)) {
        $navigation[] = array('title' => 'Archives', 'pages' => array_map(function ($archive) {
            $archive['link'] = sprintf(URL_BLOG_ARCHIVE, $archive['link']);
            return $archive;
        }, $archives));
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
