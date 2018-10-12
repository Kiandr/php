<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Require RATE module to be installed
if (!empty(Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'])) {
    // DB connection
    $db = DB::get();

    // Prepare database queries
    $select_field = $db->prepare("SELECT * FROM `landing_pods_fields` WHERE `pod_name` = 'testimonials' AND `name` = :name;");
    $insert_field = $db->prepare("INSERT INTO `landing_pods_fields` SET `pod_name` = 'testimonials', `name` = :name, `title` = :title, `type` = :type;");
    $update_order = $db->prepare("UPDATE `landing_pods_fields` SET `order` = :order WHERE `pod_name` = 'testimonials' AND `name` = :name;");

    // Video testimonials
    echo 'Updating RATE Video Testimonial Pod' . PHP_EOL;
    for ($count = 1; $count <= 4; $count++) {
        echo PHP_EOL . "\t" . 'Video #' . $count . PHP_EOL;

        // Add video title field
        echo PHP_EOL . "\t\t" . 'Video Title: ';
        try {
            // Field details
            $field_type = 'text';
            $field_name = 'video-' . $count . '-title';
            $field_title =  'Video ' . $count . ' Title Text';

            // Check if field already exists
            $select_field->execute(array('name' => $field_name));
            $field_exists = $select_field->fetch();
            if (empty($field_exists)) {
                // Insert new field
                $insert_field->execute(array(
                    'type' => $field_type,
                    'name' => $field_name,
                    'title' => $field_title
                ));

                // Awesome
                echo 'Created';
            } else {
                // Skip
                echo 'Exists';
            }

            // Update order
            $update_order->execute(array(
                'name' => $field_name,
                'order' => (100 + $count + 1)
            ));

        // Error occurred
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Add video embed field
        echo PHP_EOL . "\t\t" . 'Video Embed: ';
        try {
            // Field details
            $field_type = 'video';
            $field_name = 'video-' . $count;
            $field_title =  'Video ' . $count . ' Embed Link';

            // Check if field already exists
            $select_field->execute(array('name' => $field_name));
            $field_exists = $select_field->fetch();
            if (empty($field_exists)) {
                // Insert new field
                $insert_field->execute(array(
                    'type' => $field_type,
                    'name' => $field_name,
                    'title' => $field_title
                ));

                // Awesome
                echo 'Created';
            } else {
                // Skip
                echo 'Exists';
            }

            // Update order
            $update_order->execute(array(
                'name' => $field_name,
                'order' => (100 + $count + 2)
            ));

        // Error occurred
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo PHP_EOL;
    }

    //Insert radio page Page (filename = radio)
    echo PHP_EOL . 'Creating Radio Page';

    // Check if snippet exists
    $select = $db->prepare("SELECT `file_name` FROM `pages` WHERE `file_name` = :file_name;");
    $select->execute(array('file_name' => 'radio'));
    $radio_page = $select->fetch();
    if (empty($radio_page)) {
        $insert_page = $db->prepare("INSERT INTO `pages` SET `agent` = :agent, `file_name` = :file_name, `page_title` = :page_title, `link_name` = :link_name, `category_html` = :category_html, `template` = :template, `hide` = :hide, `hide_sitemap` = :hide_sitemap, `category` = :category, `is_main_cat` = :is_main_cat, `is_link` = :is_link;");
        $insert_page->execute(array('agent' => 1, 'file_name' => 'radio', 'page_title' => 'Radio', 'link_name' => 'Radio Landing Page', 'category_html' => '<p>#radio-landing-page#</p>', 'template' => '1col', 'hide' => 't', 'hide_sitemap' => 't', 'category' => 'radio', 'is_main_cat' => 't', 'is_link' => 'f'));

        // Awesome
        echo PHP_EOL . 'Created';
    } else {
        //Skip
        echo PHP_EOL . 'Exists';
    }

    echo PHP_EOL;
}
