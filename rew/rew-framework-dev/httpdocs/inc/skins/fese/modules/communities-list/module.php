<?php

// Config: Thumbnail size (default: 500x500)
$config_thumbs = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : false;

// Config: Exclude community ids
$config_exclude = !empty($this->config['exclude']) ? $this->config['exclude'] : false;
$config_exclude = is_int($config_exclude) ? $config_exclude : false;

// CMS database
$db = DB::get('cms');

// Community list
$communities = [];

// Prepare query to fetch community page
$find_page = $db->prepare("SELECT `file_name` FROM `pages` WHERE `page_id` = ? LIMIT 1;");

// Prepare DB query to fetch community photos
$find_photo = $db->prepare(sprintf(
    "SELECT `file` FROM `%s` WHERE `type` = 'community' AND `row` = ? ORDER BY `order` ASC LIMIT 1;",
    Settings::getInstance()->TABLES['UPLOADS']
));

// Load communities that have a page to link
$queryString = "SELECT `id`, `page_id`, `title` FROM `featured_communities` WHERE `page_id` > 0"
    . " AND `is_enabled` = 'Y'"
    . ($config_exclude ? ' AND `id` != ?' : '')
. " ORDER BY `order` ASC;";
$query = $db->prepare($queryString);
$query->execute([$config_exclude]);
foreach ($query->fetchAll() as $community) {
    // Load community photo
    $community['image'] = null;
    $find_photo->execute([$community['id']]);
    if ($image = $find_photo->fetchColumn()) {
        $community['image'] = $config_thumbs
            ? sprintf('/thumbs/%s/uploads/%s', $config_thumbs, $image)
            : sprintf('/uploads/%s', $image);
    }

    // Find community page to link to
    $find_page->execute([$community['page_id']]);
    if ($page = $find_page->fetch()) {
        $community['url'] = sprintf('/%s.php', $page['file_name']);
    }

    // Add community to list
    $communities[] = $community;
}
