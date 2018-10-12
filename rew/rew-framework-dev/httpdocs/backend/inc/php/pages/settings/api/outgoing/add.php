<?php

// Get Authorization Managers
$settingsAuth = new REW\Backend\Auth\SettingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$settingsAuth->canManageApi($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage the API')
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

    // Common Required Fields
    $required = array(
        array('value' => 'name',    'title' => __('Name')),
        array('value' => 'enabled', 'title' => __('Active')),
        array('value' => 'type',    'title' => __('Destination Type')),
    );

    // Type-specific Required Fields
    if ($_POST['type'] === 'rew') {
        // Fields
        $required[] = array('value' => 'rew_url', 'title' => __('REW URL'));
        $required[] = array('value' => 'rew_api_key', 'title' => __('REW API Key'));

        // Events
        if (empty($_POST['rew_events']) || !is_array($_POST['rew_events'])) {
            $errors[] = __('You must pick at least one Event to push to the destination');
        }

        // Format URL
        if (!empty($_POST['rew_url'])) {
            $url = $_POST['rew_url'];
            if (strpos($url, 'http') !== 0) {
                $url = 'http://' . $url; // Prepend protocol
            }
            $url_parsed = parse_url($url);

            // Require valid URL
            if (empty($url_parsed) || filter_var($url, FILTER_VALIDATE_URL) === false) {
                $errors[] = __('The specified destination URL is not valid.');
            } else {
                // Override URL
                $_POST['rew_url'] = rtrim($url_parsed['scheme'] . '://' . $url_parsed['host'] . $url_parsed['path'], '/');

                // Validate API Key
                if (!empty($_POST['rew_api_key'])) {
                    $api = new Partner_REW(array(
                        'url_api_endpoint' => $_POST['rew_url'] . '/api/crm/v1',
                        'api_key' => $_POST['rew_api_key'],
                    ));

                    // Attempt API request
                    if (!$api->getAgents()) {
                        $errors[] =  __('The API key could not be verified. Make sure you have created an API Application on %s', $_POST['rew_url']);
                    }
                }
            }
        }
    } else if ($_POST['type'] === 'custom') {
        // Fields
        $required[] = array('value' => 'custom_url', 'title' => __('Third-Party URL'));

        // Events
        if (empty($_POST['custom_events']) || !is_array($_POST['custom_events'])) {
            $errors[] = __('You must pick at least one Event to push to the destination');
        } else if (is_array($_POST['custom_events'])) {
            $enabled = false;
            foreach ($_POST['custom_events'] as $value => $data) {
                if ($data['enabled'] === 'Y') {
                    $enabled = true;

                    // Require URL for enabled event
                    if (empty($data['url'])) {
                        $errors[] = __('You must enter a destination URL for all enabled events');
                        break;
                    }
                }
            }
            if (empty($enabled)) {
                $errors[] = __('You must pick at least one Event to push to the destination');
            }
        }

        // Format URL
        if (!empty($_POST['custom_url'])) {
            $url = $_POST['custom_url'];
            if (strpos($url, 'http') !== 0) {
                $url = 'http://' . $url; // Prepend protocol
            }
            $url_parsed = parse_url($url);

            // Require valid URL
            if (empty($url_parsed) || filter_var($url, FILTER_VALIDATE_URL) === false) {
                $errors[] = __('The specified destination URL is not valid.');
            } else {
                // Override URL
                $_POST['custom_url'] = $url_parsed['scheme'] . '://' . $url_parsed['host'] . $url_parsed['path'];
            }
        }
    }

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] =  __('%s is a required field.', $require['title']);
        }
    }

    // Validate destination type
    if (!empty($_POST['type']) && !in_array($_POST['type'], array('rew', 'custom'))) {
        $errors[] = __('The specified Destination Type is not supported.');
    }

    // Partners config
    $outgoing_config = $authuser->info('partners.outgoing_api');
    $outgoing_config = is_array($outgoing_config) ? $outgoing_config : array();

    // Check duplicate name
    $duplicate = false;
    if (!empty($outgoing_config['destinations'])) {
        foreach ($outgoing_config['destinations'] as $config) {
            if (strtolower($config['name']) === strtolower($_POST['name'])) {
                $duplicate = true;
                break;
            }
        }
    }
    if ($duplicate) {
        $errors[] = __('There is already a destination with that name.');
    }

    // Check Errors
    if (empty($errors)) {
        // Enum values
        $enabled = $_POST['enabled'] === 'Y' ? 'Y' : 'N';

        // Build destination
        $destination = array();
        if ($_POST['type'] === 'rew') {
            $destination = array(
                'name'      => $_POST['name'],
                'type'      => Hook_REW_OutgoingAPI::DESTINATION_TYPE_REW,
                'url'       => $_POST['rew_url'],
                'api_key'   => $_POST['rew_api_key'],
                'events'    => $_POST['rew_events'],
                'enabled'   => $enabled,
            );
        } else if ($_POST['type'] === 'custom') {
            $destination = array(
                'name'      => $_POST['name'],
                'type'      => Hook_REW_OutgoingAPI::DESTINATION_TYPE_CUSTOM,
                'url'       => $_POST['custom_url'],
                'events'    => $_POST['custom_events'],
                'enabled'   => $enabled,
            );
        }

        // Append
        $outgoing_config['destinations'][] = $destination;

        // Merge changes
        $partners = array_merge($authuser->info('partners'), array(
            'outgoing_api' => $outgoing_config,
        ));

        // Build query
        $sql = "UPDATE `agents` SET `partners` = '" . mysql_real_escape_string(json_encode($partners)) . "' WHERE `id` = '" . mysql_real_escape_string($authuser->info('id')) . "';";
        if (mysql_query($sql)) {
            // Success
            $success[] = __('Outgoing Destination has been successfully created.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect
            header('Location: ../');
            exit;
        }
    }
}

// POST defaults
if (!isset($_POST['enabled'])) {
    $_POST['enabled'] = 'Y';
}
if (!isset($_POST['type'])) {
    $_POST['type'] = 'rew';
}
