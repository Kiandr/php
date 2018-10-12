<?php

// @todo: support for non-YouTube videos (such as videmo)

try {
    // DB connection
    $db = DB::get();

    // Load video fields
    $fields = array('video-1-title', 'video-1', 'video-2-title', 'video-2', 'video-3-title', 'video-3', 'video-4-title', 'video-4');
    $where_in = implode(', ', array_fill(0, count($fields), '?'));
    $query = $db->prepare("SELECT `name`, `value` FROM `landing_pods_fields` WHERE `pod_name` = 'testimonials' AND `name` IN(" . $where_in . ") ORDER BY `name` ASC;");
    $query->execute($fields);
    $fields = array();
    while ($field = $query->fetch()) {
        $fields[$field['name']] = $field['value'];
    }

    // Only allow youtube videos to be embeded
    foreach ($fields as $name => $field) {
        if (in_array($name, array('video-1', 'video-2', 'video-3', 'video-4'))) {
            // Extract YouTube Embed ID
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $field, $match)) {
                $videos[$name] = array('title' => $fields[$name . '-title'], 'id' => $match[1], 'type' => 'youtube');
            }
        
            if (preg_match('~^https://(?:(?:www|player)\.)?vimeo\.com/(?:(?:clip|video/))?(\d+)~', $field, $match)) {
                $hash = unserialize(@file_get_contents('http://vimeo.com/api/v2/video/' . $match[1] . '.php'));
                
                if (!empty($hash)) {
                    $videos[$name] = array('title' => $fields[$name . '-title'], 'id' => $match[1], 'url' => $hash[0]['thumbnail_large'], 'type' => 'vimeo');
                }
            }
        }
    }

// Error occurred
} catch (Exception $e) {
    //Log::error($e);
}
