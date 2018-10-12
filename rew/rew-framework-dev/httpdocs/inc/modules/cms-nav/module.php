<?php

// Hide Sub-Pages
$hide_subpages = (!empty($this->config['hide_subpages']) ? true : false);

// Break out current section
$breakout = !empty($this->config['breakout']) ? $this->config['breakout'] : false;

// Only show sub-pages
$category = !empty($this->config['category']) ? $this->config['category'] : false;

// CSS Class Name (Default: 'module nav')
$class = !empty($this->config['class']) ? $this->config['class'] : 'module nav';

// Currently active category
$active = $this->getContainer()->getPage()->info('category');

// CMS Links
$links = array();

try {
    // Get CMS Database
    $db = DB::get('cms');

    // Generate Query
    $page_query = $db->prepare("SELECT `page_id`, `file_name`, `link_name`, `category`, `is_link`, `is_main_cat`, IF (`is_link` = 't', `footer`, '') AS `target`"
        . " FROM `pages`"
        . " WHERE `agent` <=> :agent AND `team` <=> :team" . " AND `hide` = 'f'"
        . (!empty($category) ? " AND `category` = :category" : "")
        . (!empty($hide_subpages) ? " AND `is_main_cat` = 't'" : "")
        . " ORDER BY `is_main_cat` ASC, (`category_order` * 100) + `subcategory_order` ASC;");

    // Generate Parameters
    $page_params = [
        'agent' => Settings::getInstance()->SETTINGS['agent'],
        'team' => Settings::getInstance()->SETTINGS['team']
    ];
    if (!empty($category)) {
        $page_params['category'] = $category;
    }

    // Execute Query
    $page_query->execute($page_params);
    if ($pages = $page_query->fetchAll($query)) {
        // Build Navigation Collection
        foreach ($pages as $page) {
            // External Link
            if ($page['is_link'] == 't') {
                $page['link'] = $page['file_name'];
                if ($page['is_main_cat'] == 't') {
                    $page['category'] = md5($page['page_id'].time());
                }

            // CMS Page
            } else {
                $page['link'] = '/' . $page['file_name'] . '.php';
            }

            // Main Page
            if ($page['is_main_cat'] == 't') {
                $links[$page['category']] = array('title' => $page['link_name'], 'link' => $page['link'], 'target' => $page['target'], 'subpages' => array());

            // Sub Page
            } else {
                // Require Category
                if (!isset($links[$page['category']])) {
                    continue;
                }

                // Only Show Sub-Pages if on Main Page
                if ($active == $page['category']) {
                    $links[$page['category']]['subpages'][] = array('title' => $page['link_name'], 'link' => $page['link'], 'target' => $page['target']);
                }
            }
        }
    }

    // Select Number of Main Navigation Links
    if (empty($category)) {
        $numlinks = $db->fetch("SELECT `num_links` FROM `numlinks` WHERE `agent` <=> " . $db->quote(Settings::getInstance()->SETTINGS['agent']) . " AND `team` <=> " . $db->quote(Settings::getInstance()->SETTINGS['team']) . " LIMIT 1;");
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}

// CMS Navigation
$navigation = array();

// Split Navigation
if (isset($numlinks['num_links']) && is_numeric($numlinks['num_links']) && (count($links) > $numlinks['num_links'])) {
    if (!empty($numlinks['num_links'])) {
        $navigation[] = array('class' => $class, 'title' => 'Navigation', 'pages' => array_slice($links, 0, $numlinks['num_links']));
        $navigation[] = array('class' => $class, 'title' => 'Communities', 'pages' => array_slice($links, $numlinks['num_links']));
    } else {
        $navigation[] = array('class' => $class, 'title' => 'Communities', 'pages' => $links);
    }
} elseif (!empty($category)) {
    $category = $links[$category];
    if (!empty($category)) {
        $navigation[] = array('class' => $class, 'title' => $category['title'], 'pages' => $category['subpages']);
    }
} else {
    $navigation[] = array('class' => $class, 'title' => 'Navigation', 'pages' => $links);
}

// Break out current section to top
if (!empty($breakout)) {
    foreach ($navigation as $k => $section) {
        foreach ($section['pages'] as $category => $links) {
            if ($active === $category) {
                $curr = $navigation[$k]['pages'][$category];
                if (!empty($curr['subpages'])) {
                    unset($navigation[$k]['pages'][$category]);
                    array_unshift($navigation, array(
                        'class' => $class . '  current',
                        'title' => $curr['title'],
                        'link'  => $curr['link'],
                        'pages' => $curr['subpages']
                    ));
                }
            }
        }
    }
}
