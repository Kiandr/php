<?php

if (empty(Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'])) {
    return;
}

// Get Page Object
$page = $this->getContainer()->getPage();

// Landing Page Type
$this->config['type'] = (!empty($this->config['type'])) ? $this->config['type'] : 'radio';
$type = $this->config['type'];

// Load Audio JS
$page->addJavascript((__DIR__) . '/audio/audio.js');

if (!empty($type)) {
    // DB Connection
    $db = DB::get();

    // Set Cache Index Using Last Updated Timestamp
    $last_updated = $db->fetch("SELECT MAX(`timestamp_updated`) as `max` FROM `landing_pods`;");
    $index = $last_updated['max'] . $this->uid;

    // Check Cache
    $cached = Cache::getCache($index);
    if (!is_null($cached)) {
        // Use Cache
        $pods = $cached;
    } else {
        // Fetch Content Pods
        $result = $db->prepare("SELECT `name`, `markup` FROM `landing_pods` WHERE `active` = 'true' AND `type` = :type ORDER BY `order`;");
        $result->execute(array('type' => $type));
        $pods = $result->fetchAll();

        // Check if The Pods Are Set Up
        if (!empty($pods)) {
            // Process Pods
            foreach ($pods as $key => $pod) {
                // Handle Custom Pods
                if (preg_match('/^custom\-/', $pod['name'])) {
                    $pods[$key]['markup'] = '<section class="pod customSection"><div class="wrap">' . $pod['markup'] . '</div></section>';

                // Handle Standard Pods
                } else {
                    // Snippet Matches - This will only effect snippets included directly in the markup
                    // Snippets are not supported within the user controlled portion of the pods at this time
                    preg_match_all("!(#([a-zA-Z0-9_-]+)#)!", $pods[$key]['markup'], $matches);
                    if (!empty($matches)) {
                        // Loop through Snippets
                        foreach ($matches[1] as $match) {
                            // Load Snippet
                            $snippet = rew_snippet($match, false);

                            // Replace Snippet Contents
                            if (preg_match('/#form\-/', $match)) { // Form Snippets, Wrap In <div></div> (To Fix IE9 Issue with .rewfw)
                                $pods[$key]['markup'] = str_replace($match, '<div>' . $snippet . '</div>', $pods[$key]['markup']);
                            } else {
                                $pods[$key]['markup'] = str_replace($match, $snippet, $pods[$key]['markup']);
                            }
                        }
                    }

                    $query = $db->prepare("SELECT * FROM `landing_pods_fields` WHERE `pod_name` = :pod_name ORDER BY `order` ASC;");
                    $query->execute(array('pod_name' => $pod['name']));
                    $pods[$key]['fields'] = $query->fetchAll();

                    // Retrieve Pod Fields
                    foreach ($pods[$key]['fields'] as $field_key => $field) {
                        // Video Embed Links
                        if ($field['type'] == 'video') {
                            $value = '';

                            // Build Video Div
                            if (!empty($field['value'])) {
                                $value .= '<div class="videoContainer photo-effect">';
                                $value .= (stripos($field['value'], 'iframe') === false) ? '<iframe src="' . htmlspecialchars($field['value']) . '" frameborder="0" allowfullscreen=""></iframe>' : $field['value'];
                                $value .= '</div>';
                            }

                            // Replace Markup Tags with Pod Content
                            $pods[$key]['markup'] = str_replace('{' . $field['name'] . '}', $value, $pods[$key]['markup']);
                        } else {
                            // Fetch Image Upload - ID is Stored in Pod
                            if ($field['type'] == 'img') {
                                // Fetch Image Filename
                                $image = $db->fetch("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` = :type AND `id` = :id LIMIT 1;", array(
                                    'type'  => 'landing_' . $type . '_image',
                                    'id'    => $field['value'],
                                ));
                                $field['value'] = '/thumbs/' . $field['hint'] . (!empty($image) ? '/uploads/' . htmlspecialchars($image['file']) : '/uploads/agents/na.png');

                            // Audio Upload Player
                            } else if ($field['type'] == 'audio') {
                                // Find radio advertisements
                                $ads = array();
                                $find_upload = $db->prepare("SELECT `file` FROM `cms_uploads` WHERE `id` = :id;");
                                $value = !empty($field['value']) ? unserialize($field['value']) : $field['value'];
                                if (!empty($value) && is_array($value)) {
                                    foreach ($value as $upload => $ad) {
                                        $find_upload->execute(array('id' => $upload));
                                        if ($audio = $find_upload->fetchColumn()) {
                                            $ad['audio'] = $audio;
                                            //$image = $ad['image'];
                                            //if (!empty($image)) {
                                            //	$find_upload->execute(array('id' => $image));
                                            //	$ad['image'] = $find_upload->fetchColumn();
                                            //}
                                            $ads[] = $ad;
                                        }
                                    }
                                    $audio_output = '';
                                    if (!empty($ads)) {
                                        ob_start();
                                        require $page->locateTemplate('idx', 'misc', 'radio', 'audio-list');
                                        $audio_output = ob_get_clean();
                                    }
                                    $field['value'] = $audio_output;
                                }
                            } else if ($field['type'] == 'tabbed') {
                                // Tabbed Data
                                $tabs = unserialize($field['value']);
                                $tab_output = '';
                                $tab_output_contents = '';

                                if (!empty($tabs)) {
                                    // Build Tabs Output
                                    $tab_output .= '<div class="tabset"><ul>';
                                    foreach ($tabs as $count => $tab) {
                                        $tab_output .= '<li class="' . (($count == 0) ? 'current' : (($count == (count($tabs) - 1)) ? 'last' : '')) . '"><a href="#tab' . $count . '">' . htmlspecialchars($tab['title']) . '</a></li>';
                                        $tab_output_contents .= '<div id="tab' . $count . '" class="tab-contents' . (($count > 0) ? ' hidden' : '') . '">' . $tab['content'] . '</div>';
                                    }
                                    $tab_output .= '</ul></div>';

                                    // Build Tabbed Content Sections Output
                                    $tab_output .= $tab_output_contents;
                                }

                                // Update Output
                                $field['value'] = $tab_output;
                            }

                            // Use Default Values For Empty Text Pod Fields
                            if (empty($field['value'])) {
                                $field['value'] = $field['default'];
                            }

                            // Replace Markup Tags with Pod Content
                            if (!empty($field['value'])) {
                                $pods[$key]['markup'] = str_replace('{' . $field['name'] . '}', $field['value'], $pods[$key]['markup']);
                            }
                        }
                    }

                    // "As Heard On" Logos
                    if (stripos($pods[$key]['markup'], '{as-heard-on}') >= 0) {
                        // Build Output
                        $aho_output = '';

                        // Audio Uploads
                        $query = $db->prepare("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` = :type ORDER BY `order` ASC;");
                        $query->execute(array(
                                'type' => 'landing_' . $type . '_aho'
                        ));
                        $aho = $query->fetchAll();

                        if (!empty($aho)) {
                            ob_start();
                            require $page->locateTemplate('idx', 'misc', 'radio', 'as-heard-on');
                            $aho_output = ob_get_clean();
                        }

                        $pods[$key]['markup'] = str_replace('{as-heard-on}', $aho_output, $pods[$key]['markup']);
                    }
                }
            }
        }

        // Save Cache
        if (!empty($pods)) {
            Cache::setCache($index, $pods);
        }
    }
}
