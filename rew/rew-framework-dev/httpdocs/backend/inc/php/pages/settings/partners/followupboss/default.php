<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageFollowupboss($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage followupboss integrations')
    );
}

// Partner instance
$api = new Partner_FollowUpBoss();

// Form action
$form_action = '?';
if (isset($_GET['setup'])) {
    $form_action = '?setup';
}

// Success
$success = array();

// Errors
$errors = array();

// Setup mode
if (isset($_GET['setup'])) {
    // Defaults
    $_POST['api_key'] = isset($_POST['api_key']) ? $_POST['api_key'] : $authuser->info('partners.followupboss.api_key');

    // Form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Trim input
        foreach ($_POST as $k => $v) {
            if (is_string($v)) {
                $_POST[$k] = trim($v);
            }
        }

        // Required Fields
        $required   = array(
            array('value' => 'api_key', 'title' => __('API Key')),
        );

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Set API credentials
            $api->setOptions(array(
                'api_key' => $_POST['api_key'],
            ));

            // Test validity
            $tasks = $api->getTasks();
            if ($err = $api->getLastError()) {
                $errors[] = __('The provided Follow Up Boss API key is invalid.');
                return;
            }

            // Current partners
            $partners = $authuser->info('partners');

            // Merge changes
            $partners = array_merge($partners, array(
                'followupboss' => array(
                    'api_key' => $_POST['api_key'],
                ),
            ));

            // Build query
            $sql = "UPDATE `agents` SET `partners` = '" . mysql_real_escape_string(json_encode($partners)) . "' WHERE `id` = '" . mysql_real_escape_string($authuser->info('id')) . "';";
            if (mysql_query($sql)) {
                // Success
                $success[] = __('Your changes have successfully been saved.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect
                header('Location: ?');
                exit;
            }
        }
    }
}

// API integration status
$logins_valid = false;
$api_key = $authuser->info('partners.followupboss.api_key');
if (!empty($api_key)) {
    // Set options
    $api->setOptions(array(
        'api_key' => $api_key,
    ));

    // Test validity
    $tasks = $api->getTasks();
    if (!($err = $api->getLastError())) {
        $logins_valid = true;
    }
}
