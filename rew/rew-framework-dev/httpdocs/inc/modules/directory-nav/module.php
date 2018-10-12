<?php

// Require Directory Settings
require_once Settings::getInstance()->DIRS['ROOT'] . 'directory/common.inc.php';

// Get Directory Database
$db = DB::get('directory');

// Directory Categories
$categories = $db->fetchAll("SELECT `link`, `title` FROM `directory_categories` WHERE `parent` = '' ORDER BY `order` ASC");
foreach ($categories as $k => $category) {
    $subcategories = $db->fetchAll("SELECT `link`, `title` FROM `directory_categories` WHERE `parent` = " . $db->quote($category['link']) . " ORDER BY `order` ASC");
    if (!empty($subcategories)) {
        $category['subpages'] = array();
        foreach ($subcategories as $subcategory) {
            $tertiaries = $db->fetchAll("SELECT `link`, `title` FROM `directory_categories` WHERE `parent` = " . $db->quote($subcategory['link']) . " ORDER BY `order` ASC");
            if (!empty($tertiaries)) {
                $subcategory['subpages'] = array();
                foreach ($tertiaries as $tertiary) {
                    $tertiary['link'] = sprintf(URL_DIRECTORY_CATEGORY, $tertiary['link']);
                    $subcategory['subpages'][] = $tertiary;
                }
            }
            $subcategory['link'] = sprintf(URL_DIRECTORY_CATEGORY, $subcategory['link']);
            $category['subpages'][] = $subcategory;
        }
    }
    $category['link'] = sprintf(URL_DIRECTORY_CATEGORY, $category['link']);
    $categories[$k] = $category;
}

// Directory Navigation
$navigation[] = array('title' => 'Categories', 'pages' => array_merge(array(
    array('link' => URL_DIRECTORY, 'title' => 'All Categories')
), $categories));
