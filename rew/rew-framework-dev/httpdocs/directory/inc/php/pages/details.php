<?php

// DB Connection
$db = DB::get('directory');

// Select Listing
$result = $db->prepare("SELECT * FROM `directory_listings` WHERE `link` = :link;");
$result->execute(array('link' => $_GET['link']));
$entry = $result->fetch();

// Listing Not Found
if (empty($entry)) {
    // Send 404 Header
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);

// Listing Exists
} else {
    // Parse Listing (Get Thumbnails)
    $entry = entry_parse($entry);

    // URL Back to Category
    $url_back = sprintf(URL_DIRECTORY_CATEGORY, $_GET['category']);

    // Listing Category
    $result = $db->prepare("SELECT * FROM `directory_categories` WHERE `link` = :category;");
    $result->execute(array('category' => $_GET['category']));
    $category = $result->fetch();

    // Dynamic Meta Information
    $page_title = !empty($entry['page_title']) ? Format::htmlspecialchars($entry['page_title']) : Format::htmlspecialchars($entry['business_name']) . (!empty($category) ? ' | ' . Format::htmlspecialchars($category['title']) : '') . ' | ' . Format::htmlspecialchars($directory_settings['directory_name']);
    $meta_keyw  = Format::htmlspecialchars($entry['business_name']) . ', ' . ucwords(str_replace(',', ', ', $entry['categories']));
    $meta_desc  = Format::htmlspecialchars(substr($entry['description'], 0, 260));
}
