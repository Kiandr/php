<?php

// Get Database
$db = DB::get();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageRadioLandingPage');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage radio langing pages.')
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Content Pods
$update_pods = array();

// Allow images for radio ads
$radio_ad_images = Skin::hasFeature(Skin::ENABLE_RADIO_AD_IMAGES);
echo '<script> var radio_ad_images = ' . intval($radio_ad_images) . '; </script>';

// Set up Active Pod Order Based on Submit
if (!empty($_POST['pod']['active'])) {
    foreach ($_POST['pod']['active'] as $active) {
        $update_pods[$active] = array();
    }
}

// Fetch Default Content Pods
try {
    foreach($db->fetchAll("SELECT * FROM `" . TABLE_LANDING_PODS . "` WHERE `type` = 'radio' ORDER BY `order` ASC") as $result) {
        // Handle Custom Pods
        if (preg_match('/^custom\-/', $result['name'])) {
            // Make it Known
            $update_pods[$result['name']] = array();

            // Handle Standard Pods
        } else {
            // Retrieve Default Pod Fields
            try {
                foreach($db->fetchAll("SELECT * FROM `" . TABLE_LANDING_PODS_FIELDS . "` WHERE `pod_name` = :pod_name ORDER BY `order` ASC;", ["pod_name" => $result['name']]) as $fields) {
                    $update_pods[$result['name']]['fields'][$fields['name']] = $fields;
                }
            } catch (PDOException $e) {}
        }
    }
} catch (PDOException $e) {}

// Process Submit
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete Removed Custom Pods
    if (!empty($_POST['delete'])) {
        $_POST['delete'] = (is_array($_POST['delete'])) ? $_POST['delete'] : array($_POST['delete']);

        // Format IDs for Query, Unset Deleted Pods From Default Pod Setup
        foreach ($_POST['delete'] as $key => $delete) {
            unset($update_pods[$delete]);
        }
        try {
            $db->prepare("DELETE FROM `landing_pods` WHERE `type` = 'radio' AND `name` IN (" . implode(",", array_fill(0, count($_POST['delete']), '?')) . ") AND `name` LIKE 'custom-%';")
            ->execute($_POST['delete']);
        } catch (PDOException $e) {}
    }

    // Check Errors
    if (empty($errors)) {
        // Build Pod Update Query
        if (!empty($update_pods)) {
            // Reset Order
            try {
                $db->prepare("UPDATE `" . TABLE_LANDING_PODS . "` SET `order` = 0 WHERE `type` = 'radio';")->execute();
            } catch (PDOException $e) {}

            $order = 0;

            // Update Each Pod In the DB
            foreach ($update_pods as $pod_name => $pod) {
                // Pod is Active (true/false)
                $active = (!empty($_POST['pod']['active']) && in_array($pod_name, $_POST['pod']['active'])) ? 'true' : 'false';

                // Is This a Custom Pod?
                $custom = preg_match('/^custom\-/', $pod_name) ? true : false;

                // Update Pod Settings
                if (!empty($custom)) {
                    // Custom Pod, Might Need to be Created First
                    $query = "INSERT INTO `" . TABLE_LANDING_PODS . "` SET "
                        . " `name` = :name, "
                        . " `title` = 'Custom', "
                        . " `active` = :active, "
                        . " `markup` = :markup, "
                        . " `order` = :order, "
                        . " `type` = 'radio', "
                        . " `timestamp_created` = NOW(), "
                        . " `timestamp_updated` = NOW() "
                    . " ON DUPLICATE KEY UPDATE "
                        . " `active` = :active_d, "
                        . " `markup` = :markup_d, "
                        . " `order` = :order_d, "
                        . " `timestamp_updated` = NOW();";
                    $qParams = [
                        "name" => $pod_name,
                        "active" => $active,
                        "markup" => $_POST[$pod_name]['content'],
                        "order" => $order,
                        "active_d" => $active,
                        "markup_d" => $_POST[$pod_name]['content'],
                        "order_d" => $order
                    ];
                } else {
                    $query = "UPDATE `" . TABLE_LANDING_PODS . "` SET "
                        . "`active` = :active, "
                        . "`order` = :order, "
                        . "`timestamp_updated` = NOW() "
                        . " WHERE `name` = :name AND `type` = 'radio';";
                    $qParams = [
                        "active" => $active,
                        "order" => $order,
                        "name" => $pod_name
                    ];
                }
                try {
                    $db->prepare($query)->execute($qParams);
                    if (empty($custom)) {
                        if (!empty($pod['fields'])) {
                            // Update Pod Field Values
                            foreach ($pod['fields'] as $field) {
                                // User Controlled Content
                                if (isset($_POST[$pod_name][$field['name']]) && !empty($_POST[$pod_name][$field['name']])) {
                                    // Format Insert Value
                                    $insert_value = is_array($_POST[$pod_name][$field['name']]) ? serialize($_POST[$pod_name][$field['name']]) : $_POST[$pod_name][$field['name']];

                                    // Attempt to Format Video Embed Links
                                    if ($field['type'] == 'video') {
                                        if (preg_match("/vimeo\.com\/([a-zA-Z0-9-_]+)\/*$/", $insert_value, $match)) {
                                            if (!empty($match[1])) {
                                                $insert_value = 'https://player.vimeo.com/video/' . $match[1];
                                            }
                                        } else if (preg_match("/youtube.*\.com\/watch\?.*v\=([a-zA-Z0-9-_]+)/", $insert_value, $match) || preg_match("/youtu\.be\/([a-zA-Z0-9-_]+)\/*$/", $insert_value, $match)) {
                                            if (!empty($match[1])) {
                                                $insert_value = 'https://www.youtube.com/embed/' . $match[1];
                                            }
                                        }
                                    }

                                    try {
                                        $db->prepare("UPDATE `" . TABLE_LANDING_PODS_FIELDS . "` SET "
                                            . " `value` = :value "
                                            . " WHERE `pod_name` = :pod_name AND `name` = :name;")
                                        ->execute([
                                            "value" => $insert_value,
                                            "pod_name" => $pod_name,
                                            "name" => $field['name']
                                        ]);
                                    } catch (PDOException $e) {
                                        $errors[] = __('Failed to update pod field "%s" for pod: %s', $field['name'], $pod_name);
                                    }
                                }
                            }
                        }
                    }

                // Error Updating Pod
                } catch (PDOException $e) {
                    $errors[] = __("Failed to update pod: %s", $pod_name);
                }

                // Increment Order
                $order++;
            }
        }

        // Success
        $success[] = __('Landing page settings have successfully been updated.');

        // Redirect back to form
        $authuser->setNotices($success, $errors);
        header('Location: ?success');
        exit;
    }
}

// Pages using this Module
$used_on_pages = array();

// Check Homepage
try {
    $row = $db->fetch("SELECT `agent` FROM `" . TABLE_SETTINGS . "` WHERE `agent` = '1' AND `category_html` LIKE '%#radio-landing-page#%';");

    if (!empty($row)) {
        $used_on_pages[] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/', 'text' => 'Homepage');
    }
} catch (PDOException $e) {
    $errors[] = __('Error Searching Pages using this Snippet');
}

// Locate Pages
try {
    foreach($db->fetchAll("SELECT `page_id`, `link_name` FROM `" . TABLE_PAGES . "` WHERE `agent` = '1' AND `category_html` LIKE '%#radio-landing-page#%' ORDER BY `link_name` ASC;") as $row) {
        $used_on_pages[] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/pages/edit/?id=' . $row['page_id'], 'text' => $row['link_name']);
    }
} catch (PDOException $e) {
    $errors[] = __('Error Searching Pages using this Snippet');
}

// Page Uploads
$uploads = array();
try {
    foreach($db->fetchAll("SELECT `id`, `file`, `type` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` IN ('landing_radio_audio', 'landing_radio_aho') ORDER BY `order` ASC;") as $row) {
        $uploads[$row['type']][] = $row;
    }
} catch (PDOException $e) {}

// Pods using {as-heard-on} tag
$as_heard_on_tag = array();
try {
    foreach($db->fetchAll("SELECT `title` FROM `" . TABLE_LANDING_PODS . "` WHERE `markup` LIKE '%{as-heard-on}%';") as $row ){
        $as_heard_on_tag[] = $row['title'];
    }
} catch (PDOException $e) {}
$as_heard_on_tag = '<ul><li>' . implode('</li><li>', $as_heard_on_tag) . '</li></ul>';

// Set up Pods With Most Recent Available Settings
$pods = array('active' => array(), 'inactive' => array());
try {
    foreach ($db->fetchAll("SELECT * FROM `" . TABLE_LANDING_PODS . "` WHERE `type` = 'radio' ORDER BY `order` ASC") as $result) {
        // Store Pod in Associative Array
        $pod = $result;

        // Handle Custom Pods
        if (preg_match('/^custom\-/', $pod['name'])) {
            $pod['fields']['content'] = array(
                'pod_name'  => $pod['name'],
                'name'      => 'content',
                'title'     => __('Content'),
                'type'      => 'tinymce',
                'value'     => $pod['markup'],
            );

        // Handle Standard Pods
        } else {
            // Retrieve Pod Fields
            try {
                foreach($db->fetchAll("SELECT * FROM `" . TABLE_LANDING_PODS_FIELDS . "` WHERE `pod_name` = :pod_name ORDER BY `order` ASC;", ["pod_name" => $pod['name']]) as $fields) {
                    // Latest User Controlled Content
                    $pod['fields'][$fields['name']] = $fields;

                    // Handle Image Uploads
                    if ($fields['type'] == 'img') {
                        // Fetch Image Filename
                        try {
                            $image = $db->fetch("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'landing_radio_image' AND `id` = :id LIMIT 1;", ["id" => $fields['value']]);
                        } catch (PDOException $e) {}
                        if (!empty($image)) {
                            $pod['fields'][$fields['name']]['image'] = $image['file'];
                        }
                    }
                }
            } catch (PDOException $e) {}
        }

        // Build Pod Output
        ob_start();
        ?>

            <li id="<?=$pod['name']; ?>" class="node pod<?=(preg_match('/^custom\-/', $pod['name'])) ? ' custom' : ''; ?> <?=($pod['active'] != 'true') ? ' hide-controls' : ''; ?>">
                <div class="article">

                    <div class="p-header ttl p-title">
                        <?=$pod['title']; ?>
                    </div>

                    <div class="btns R p-controls">
                        <a class="btn btn--ghost switch-pod" style="float: right;" href="#" title="<?=($pod['active'] != 'true') ? __('Enable') : __('Disable'); ?> Pod">
                            <?=($pod['active'] != 'true') ? '<svg class="icon icon-add"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg>' : '<svg class="icon icon-minus"><use xlink:href="/backend/img/icos.svg#icon-minus"/></svg>'; ?>
                            </a>
                        <a class="btn btn--ghost edit-pod" style="float: right;" href="#" title="<?= __('Edit Pod'); ?>"><?= __('Edit'); ?></a>
                        <?php if (preg_match('/^custom\-/', $pod['name'])) { ?>
                            <a class="btn btn--ghost delete-pod" style="float: right;" href="#" title="<?= __('Delete Pod'); ?>"><svg class="icon icon-trash"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>
                        <?php } ?>
                        <input type="hidden" class="pod-status" name="pod[<?=($pod['active'] == 'true') ? 'active' : 'inactive'; ?>][]" value="<?=$pod['name']; ?>">
                        <div id="pod-form-append-<?=$pod['name']; ?>" class="hidden"></div>
                    </div>

                </div>

                <div id="edit-<?=$pod['name']; ?>" class="p-content hidden">
                    <?php
                    // Pod Doesn't Contain Editable Sections
                    if (empty($pod['fields'])) {
                        echo '<h2>' .__('No editable fields available.') .'</h2>';
                    } else {
                        // Handle Individual Editable Sections
                        foreach ($pod['fields'] as $field_name => $field) {
                            echo '<div class="field pod-field">';

                                // Form Field Output
                            switch ($field['type']) {
                                // TinyMCE Editor
                                case 'tinymce':
                                    echo '<label class="field__label">' . $field['title'] . '</label>';
                                    echo '<textarea class="w1/1 tmce" name="' . $pod['name'] . '[' . $field_name . ']">' . htmlspecialchars($field['value']) . '</textarea>';
                                    break;

                                // Image Upload
                                case 'img':
                                    echo '<label class="field__label">' . $field['title'] . '</label>';
                                    echo '<p>' . __('Recommended Dimensions: %s', $field['hint']) . '</p>';
                                    echo '<div id="' . $pod['name'] . '[' . $field_name . ']" class="uploader">';
                                    if (!empty($field['image'])) {
                                        echo '<div class="file-manager">'
                                            . '<ul>'
                                                . '<li>'
                                                    . '<div class="article">'
                                                        . '<img src="/thumbs/95x95/uploads/' . $field['image'] . '" border="0">'
                                                        . '<input type="hidden" name="uploads[]" value="' . htmlspecialchars($field['value']) . '">'
                                                    . '</div>'
                                                . '</li>'
                                            . '</ul>'
                                        . '</div>';
                                    }
                                    echo '</div>';
                                    break;

                                // Audio Upload(s)
                                case 'audio':
                                    // Audio Title Values
                                    $values = (!empty($field['value'])) ? unserialize($field['value']) : array();
                                    echo '<label class="field__label">' . $field['title'] . '</label>';
                                    echo '<div class="audio-uploader">';
                                    if (!empty($uploads['landing_radio_audio'])) {
                                        echo '<div class="file-manager">';
                                        echo '<ul>';
                                        foreach ($uploads['landing_radio_audio'] as $upload) {
                                            $ad = $values[$upload['id']];
                                            $title = $ad['title'];
                                            $image = $ad['image'];
                                            if (!empty($image)) {
                                                try {
                                                    $image = $db->fetch("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = :id LIMIT 1;", ["id" => $image]);
                                                } catch (PDOException $e) {}
                                            }
                                            echo '<li class="audio-li">'
                                                . '<div class="article">'
                                                    . '<div class="file-wrap">'
                                                        . '<label class="field__label">' . __('File') . '</label>'
                                                        . '<span>' . $upload['file'] . '</span>'
                                                    . '</div>'
                                                    . '<input type="hidden" name="uploads[]" value="' . $upload['id'] . '">'
                                                    . '<div class="title-wrap">'
                                                        . '<label class="field__label">' . __('Title') . '</label>'
                                                        . '<input class="w1/1" type="text" name="' . $pod['name'] . '[' . $field_name . '][' . $upload['id'] . '][title]" value="' . Format::htmlspecialchars($title) . '" placeholder="' . Format::ucnames(preg_replace("/\.[a-zA-Z0-9]+$/", '', str_replace(array('_', '-'), ' ', $upload['file']))) . '">'
                                                    . '</div>'
                                                    . ($radio_ad_images ?
                                                        '<div class="title-wrap">'
                                                            . '<label class="field__label">' . __('Image') . '</label>'
                                                            . '<div class="uploader" id="' . $pod['name'] . '[' . $field_name . '][' . $upload['id'] . '][image]">'
                                                            . (!empty($image) ?
                                                                '<div class="file-manager">'
                                                                    . '<ul>'
                                                                        . '<li>'
                                                                            . '<div class="article">'
                                                                                . '<img src="/thumbs/95x95/uploads/' . rawurlencode($image['file']) . '" border="0">'
                                                                                . '<input type="hidden" name="' . $pod['name'] . '[' . $field_name . '][' . $upload['id'] . '][image]" value="' . htmlspecialchars($image['id']) . '">'
                                                                            . '</div>'
                                                                        . '</li>'
                                                                    . '</ul>'
                                                                . '</div>'
                                                            : '')
                                                            . '</div>'
                                                        . '</div>'
                                                    : '')
                                                    . '<img style="display: none;">'
                                                . '</div>'
                                            . '</li>';
                                        }
                                        echo '</ul>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    break;

                                // Tabbed Content Editor
                                case 'tabbed':
                                    // Unserialize Tabbed Field Data
                                    $tabbed = (!empty($field['value'])) ? unserialize($field['value']) : array(array('title' => '', 'content' => ''));

                                    $controls = '';
                                    $tab_inputs = '';
                                    $count = 0;

                                    echo '<div class="tabs-section">';

                                        // Loop Through Tabs (Title + Content)
                                    if (!empty($tabbed)) {
                                        foreach ($tabbed as $key => $tab) {
                                            $controls .= '<div class="control' . (($count == 0) ? ' first current' : '') . '">'
                                                            . '<span class="title">' . ((!empty($tab['title'])) ? Format::truncate(htmlspecialchars($tab['title']), 8) : '') . '</span>'
                                                            . (($count > 0) ? '<a class="remove"><span class="ico"></span></a>' : '')
                                                        . '</div>';

                                            $tab_inputs .= '<div class="tab' . (($count > 0) ? ' hidden' : '') . '">'
                                                            . '<label class="field__label">' . __('Title') . '</label>'
                                                            . '<input class="w1/1 tab-title" type="text" name="' . $pod['name'] . '[' . $field_name . '][' . $key . '][title]" value="' . htmlspecialchars($tab['title']) . '">'
                                                            . '<label class="field__label">' . __('Content') . '</label>'
                                                            . '<textarea class="w1/1 tmce" name="' . $pod['name'] . '[' . $field_name . '][' . $key . '][content]">' . htmlspecialchars($tab['content']) . '</textarea>'
                                                        . '</div>';
                                            $count++;
                                        }
                                    }

                                        echo '<div class="controls">'
                                            . $controls
                                            // Button For Adding More Tabs
                                            . '<div class="add-tab' . ((count($tabbed) >= 20) ? ' hidden' : '') . '"><a href="#" id="' . $field_name . '">' . __('Add Tab') . '</a></div>'
                                        . '</div>';

                                        echo '<div class="tabs">'
                                            . $tab_inputs
                                        . '</div>';

                                        echo '</div>';
                                    break;

                                // Text Input
                                case 'text':
                                default:
                                    echo '<label class="field__label">' . $field['title'] . '</label>';
                                    echo '<input class="w1/1" type="text" name="' . $pod['name'] . '[' . $field_name . ']" value="' . htmlspecialchars($field['value']) . '" placeholder="' . $field['hint'] . '">';
                                    break;
                            }

                            echo '</div>';
                        }
                    }

                    ?>

                </div>

            </li>

        <?php
        $pod['output'] = ob_get_clean();

        // Set Pod
        $pods[($result['active'] == 'true') ? 'active' : 'inactive'][$result['name']] = $pod;
    }
} catch (PDOException $e) {}
