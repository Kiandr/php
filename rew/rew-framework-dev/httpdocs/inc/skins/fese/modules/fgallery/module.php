<?php

// Gallery slides
$gallery = [];
$images = $this->config('images');
if (!is_array($images)) {
    return;
}
$host = Http_host::getHost();
if (count($images) > 1) {
    foreach ($images as $img) {
        if (empty($img)) {
            continue;
        }
        $url = $img;
        $w = 640;
        $h = 480;

        // Handle thumbnails
        $path = '/uploads/';
        if (($upload = strpos($img, $path)) !== false) {
            $thumb = strpos($img, '/thumbs/') === 0;
            $local = strpos($img, $host) !== false;
            if ($thumb || $local) {
                $url = substr($img, $upload);
            }
        }

        // Gallery photo
        $gallery[] = [
            'src' => $url,
            'w' => $w,
            'h' => $h
        ];
    }
}

// # of images
$numImages = count($images);

// Update instance configuration
// to pass data to module.js.php
$this->config('gallery', $gallery);

// PhotoSwipe assets
$page = $this->getPage();
$page->addStylesheet(__DIR__ . '/lib/photoswipe.css', 'photoswipe');
$page->addStylesheet(__DIR__ . '/lib/default-skin/default-skin.css', 'photoswipe');
$page->addJavascript(__DIR__ . '/lib/photoswipe.min.js', 'photoswipe');
$page->addJavascript(__DIR__ . '/lib/photoswipe-ui-default.min.js', 'photoswipe');
