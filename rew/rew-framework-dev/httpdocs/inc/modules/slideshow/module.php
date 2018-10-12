<?php

try {
    // CMS Database
    $db = DB::get('cms');

    // Slideshow Images
    $slideshow = array();

    // Load Images
    $images = $db->fetchAll("SELECT `image`, `caption`, `link` FROM `slideshow_images` ORDER BY `order` ASC;");
    foreach ($images as $image) {
        // URL to Image
        $image['image'] = '/uploads/slideshow/' . $image['image'];

        // Add to Slideshow
        $slideshow[] = $image;
    }


// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
