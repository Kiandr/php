<?php

// Get Authorization Managers
$settingsAuth = new REW\Backend\Auth\SettingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$settingsAuth->canManageApi($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to add a new API endpoint')
    );
}

// Form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim input
    foreach ($_POST as $k => $v) {
        if (is_string($v)) {
            $_POST[$k] = trim($v);
        }
    }

    // Required Fields
    $required = array(
        array('value' => 'name',    'title' => __('Name')),
        array('value' => 'enabled', 'title' => __('Active')),
    );

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] =  __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Enum values
        $enabled = $_POST['enabled'] === 'Y' ? 'Y' : 'N';

        // Random key
        $api_key = hash('sha256', uniqid('', true) . $_SERVER['HTTP_HOST']);

        // Build query
        $sql = "INSERT INTO `api_applications` SET "
                    . "`name`		= '" . mysql_real_escape_string($_POST['name']) . "', "
                    . "`api_key`	= '" . mysql_real_escape_string($api_key) . "', "
                    . "`enabled`	= '" . mysql_real_escape_string($enabled) . "';";

        // Execute
        if (mysql_query($sql)) {
            // Insert ID
            $insert_id = mysql_insert_id();

            // Success
            $success[] = __('API Application has been successfully created.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect
            header('Location: ../app/edit/?id=' . $insert_id);
            exit;
        } else {
            // Query error
            $errors[] = __('The application could not be saved.');
        }
    }
}

// POST defaults
if (!isset($_POST['enabled'])) {
    $_POST['enabled'] = 'Y';
}
