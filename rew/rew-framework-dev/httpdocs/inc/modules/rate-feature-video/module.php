<?php

// @todo: support for non-YouTube videos (such as videmo)

// Skin instance
$skin = $this->getContainer()->getPage()->getSkin();

// Module configuration
$heading    = $this->config('heading');
$subheading = $this->config('subheading');
$linkUrl    = $this->config('linkUrl');
$linkText   = $this->config('linkText');

try {
    // DB connection
    $db = DB::get();

    // YouTube embed ID
    $video_id = false;

    // Load video from RATE landing page
    $query = $db->prepare("SELECT `value` FROM `landing_pods_fields` WHERE `pod_name` = 'testimonials' AND `name` = :name LIMIT 1;");
    $query->execute(array('name' => 'video-1'));
    if ($video = $query->fetchColumn()) {
        // Extract YouTube Embed ID
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match)) {
            $video_id = $match[1];
        }
        
        if (stripos($video, 'vimeo') !== false) {
            $video_id = $video;
        }
    }

// Error occurred
} catch (Exception $e) {
    //Log::error($e);
}
