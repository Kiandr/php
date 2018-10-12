<?php

/**
 * Parse Directory Listing
 *  - Build Address String
 *  - Get Logo & Thumbnails
 *  - Set Primary Image
 *  - Format Website Link
 *  - Handle No-Follow for Anchors
 *  - Build Details URL
 *
 * @param array $entry
 * @return array
 */
function entry_parse($entry)
{

    // Address
    if (!empty($entry['city'])) {
        $entry['address'] .= ', ' . $entry['city'];
    }
    if (!empty($entry['state']) && !empty($entry['city'])) {
        $entry['address'] .= ', ' . $entry['state'];
    }
    if (!empty($entry['zip']) && !empty($entry['city'])) {
        $entry['address'] .= ', ' . $entry['zip'];
    }
    $entry['address'] = ltrim($entry['address'], ', ');

    // DB Connection
    $db = DB::get('directory');

    // Images
    $result = $db->prepare("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `row` = :row AND `type` = :type ORDER BY `order` ASC;");

    // Logo
    $result->execute(array('row' => $entry['id'], 'type' => 'directory_logo'));
    $logo = $result->fetchColumn();
    if (!empty($logo)) {
        $entry['logo'] = '/uploads/' . $logo;
    }

    // Thumbnails
    $result->execute(array('row' => $entry['id'], 'type' => 'directory'));
    $thumbnails = $result->fetchAll();
    if (!empty($thumbnails)) {
        $entry['thumbnails'] = array_map(function ($image) {
            return '/uploads/' . $image['file'];
        }, $thumbnails);
    }

    // Main Image (Logo, Thumbnail, No Image)
    $entry['image'] = !empty($entry['logo']) ? $entry['logo'] : false;
    if (empty($entry['image']) && !empty($entry['thumbnails'])) {
        $entry['image'] = $entry['thumbnails'][0];
    }
    $entry['image'] = !empty($entry['image']) ? $entry['image'] : '/img/404.gif';

    // Website URL
    if (!empty($entry['website'])) {
        // Escape HTML
        $entry['website'] = htmlspecialchars($entry['website']);
        // Prepend HTTP:// If Needed
        if (!preg_match('#http://#', $entry['website'])) {
            $entry['website'] = 'http://' . $entry['website'];
        }
        // Turn into Anchor Link
        if ($entry['website_link'] == 'Y') {
            $entry['website'] = '<a href="' . $entry['website'] . '" target="_blank"' . ($entry['no_follow'] == 'Y' ? ' rel="nofollow"' : '') . '>' . preg_replace('#^http://#', '', rtrim($entry['website'], '/')) . '</a>';
        }
    }

    // Append rel="nofollow" to Anchors
    if ($entry['no_follow'] == 'Y') {
        $entry['description'] = preg_replace("/<\s*a\s+(.+?)>/i", '<a $1 rel="nofollow">', $entry['description']);
    }

    // URL Details
    if (!empty($entry['primary_category'])) {
        $entry_dir = $entry['primary_category'];
    } elseif (!empty($_GET['category'])) {
        $entry_dir = $_GET['category'];
    } else {
        $entry_cats = explode(',', $entry['categories']);
        $entry_dir = $entry_cats[0];
    }
    $entry['url_details'] = sprintf(URL_DIRECTORY_LISTING, $entry_dir, $entry['link']);

    // Return Entry
    return $entry;
}
