<?php

try {
    // DB connection
    $db = DB::get();

    // Radio ads
    $radio = array();
    $count = 0;
    $limit = 4;

    // Prepare query to find radio audio & images
    $find_upload = $db->prepare("SELECT `file` FROM `cms_uploads` WHERE `type` = :type AND `id` = :id LIMIT 1;");

    // Load radio files
    $find_audio = $db->prepare("SELECT `value` FROM `landing_pods_fields` WHERE `pod_name` = 'ad-player' AND `type` = 'audio' AND `name` = :name LIMIT 1;");
    $find_audio->execute(array('name' => 'audio-files'));
    if ($audio = $find_audio->fetchColumn()) {
        // Process audio files
        $audio = unserialize($audio);
        if (!empty($audio) && is_array($audio)) {
            foreach ($audio as $upload => $ad) {
                // Find audio file
                $find_upload->execute(array('type' => 'landing_radio_audio', 'id' => $upload));
                if ($mp3 = $find_upload->fetchColumn()) {
                    // Limit reached
                    if (++$count > $limit) {
                        break;
                    }

                    // Radio ad
                    $title = $ad['title'];
                    $image = $ad['image'];
                    if (!empty($image)) {
                        $find_upload->execute(array('type' => 'landing_radio_image', 'id' => $image));
                        $image = $find_upload->fetchColumn();
                    }

                    // Radio ad
                    $radio[] = array(
                        'title' => $title,
                        'image' => $image,
                        'audio' => $mp3
                    );
                }
            }
        }
    }

// Error occurred
} catch (Exception $e) {
    //Log::error($e);
}
