<?php

// Get Authorization Managers
$settingsAuth = new REW\Backend\Auth\SettingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$settingsAuth->canManageBlogs($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view blog settings')
    );
}

// Success
$success = array();
// Errors
$errors = array();

// Get Blog Settings
$db = DB::get();
$settingsQuery = $db->prepare("SELECT * FROM `" . TABLE_BLOG_SETTINGS . "`;");
$settingsQuery->execute();
$blog_settings = $settingsQuery->fetch();

/* Throw Missing Settings Exception */
if (empty($blog_settings)) {
    throw new \REW\Backend\Exceptions\MissingSettingsException();
}

// Process Submission
if (isset($_GET['submit'])) {
        // Required Fields
    $required   = array();
    $required[] = array('value' => 'blog_name', 'title' => __('Blog Name'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }
    // Check Errors
    if (empty($errors)) {
        // Sanitize Data
        $_POST['captcha'] = ($_POST['captcha'] == 't') ? 't' : 'f';

        // Build UPDATE Query
        $updateQuery = $db->prepare("UPDATE `" . TABLE_BLOG_SETTINGS . "` SET "
               . "`blog_name`			= :blog_name, "
               . "`page_title`			= :page_title, "
               . "`meta_tag_desc`		= :meta_tag_desc, "
               . "`captcha`				= :captcha, "
               . "`timestamp_updated`	= NOW();");

        $updateParams = [
                'blog_name'         => $_POST['blog_name'],
                'page_title'        => $_POST['page_title'],
                'meta_tag_desc'     => $_POST['meta_tag_desc'],
                'captcha'           => $_POST['captcha']
        ];

        // Execute Query
        if ($updateQuery->execute($updateParams)) {
            // Success
            $success[] = __('Blog Settings have successfully been saved.');

            // Get Updated Settings
            $settingsQuery = $db->prepare("SELECT * FROM `" . TABLE_BLOG_SETTINGS . "`;");
            $settingsQuery->execute();
            $blog_settings = $settingsQuery->fetch();
        // Query Error
        } else {
            $errors[] = __('An error has occurred. Blog Settings could not be saved.');
        }
    }
    // Use $_POST Data
    foreach ($blog_settings as $k => $v) {
        $blog_settings[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Array-ify
$blog_settings['features'] = is_array($blog_settings['features']) ? $blog_settings['features'] : explode(',', $blog_settings['features']);
