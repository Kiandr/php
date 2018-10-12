<?php

// Directory Categories
$categories = array();

try {
    // DB Connection
    $db = DB::get('directory');

    // Find available categories & sub-categories
    $result = $db->query("SELECT `link`,`title` FROM `directory_categories` WHERE `parent` = '' ORDER BY `order` ASC, `title` ASC;");
    $subcategories = $db->prepare("SELECT `link`,`title` FROM `directory_categories` WHERE `parent` = :category ORDER BY `order` ASC, `title` ASC;");
    while ($category = $result->fetch()) {
        // Sub-categories
        $category['subcategories'] = array();
        $subcategories->execute(array('category' => $category['link']));
        while ($subcategory = $subcategories->fetch()) {
            $category['subcategories'][] = $subcategory;
        }

        // Add Category
        $categories[] = $category;
    }

// Database error
} catch (PDOException $e) {
    Log::error($e);
}
