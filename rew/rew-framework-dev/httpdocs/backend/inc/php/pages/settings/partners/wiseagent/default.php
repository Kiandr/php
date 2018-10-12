<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageWiseagent($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage wiseagent integrations')
    );
}

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
    // Form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Trim input
        $_POST = Format::trim($_POST);

        // Required Fields
        $required = array(
            array('value' => 'wa_api_key', 'title' => __('Wise Agent API Key')),
        );

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check Errors
        if (empty($errors)) {
            // WiseAgent API Doesn't Have an API Key Validation Endpoint - The Team Request Endpoint Returns Empty Array if the Key is Invalid
            try {
                $response = Partner_WiseAgent::getTeamMembers($_POST['wa_api_key']);
            } catch (Exception $e) {
            }

            if (empty($response)) {
                $errors[] = __('The provided Wise Agent API key is invalid');
            } else {
                // Current partners
                $partners = $authuser->info('partners');

                // Add API Key to Partners Settings
                $partners['wiseagent']['api_key'] = $_POST['wa_api_key'];

                // Update Agent API Key
                try {
                    $sql = "UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;";
                    $query = $db->prepare($sql)->execute(array(
                        'partners' => json_encode($partners),
                        'id' => $authuser->info('id')
                    ));
                } catch (Exception $e) {
                    $errors[] = __('Failed to update agent API Key. Please try again.');
                }

                // Create group if needed
                try {
                    $sql = "SELECT `id` FROM `groups` WHERE `name` = :name AND `agent_id` IS NULL AND `user` = 'false';";
                    $query = $db->prepare($sql);
                    $query->execute(array(
                        'name' => Partner_WiseAgent::GROUP_NAME,
                    ));
                    $wa_group = $query->fetchAll();
                } catch (Exception $e) {
                    $errors[] = __('Failed to check if WiseAgent group exists. Please try again.');
                }

                if (isset($wa_group) && empty($wa_group)) {
                    // Create WiseAgent group if it doesn't exist
                    try {
                        $sql = "INSERT INTO `groups` SET "
                            . "`name`			= :name, "
                            . "`description`	= 'Leads in this group will be synced with Wise Agent', "
                            . "`style`			= :style, "
                            . "`user`			= 'false', "
                            . "`agent_id`		= NULL;";
                        $query = $db->prepare($sql)->execute(array(
                            'name' => Partner_WiseAgent::GROUP_NAME,
                            'style' => Partner_WiseAgent::GROUP_STYLE,
                        ));
                    } catch (Exception $e) {
                        $errors[] = __('Failed to create WiseAgent group. Please try again.');
                    }
                }

                // Success
                if (empty($errors)) {
                    $success[] = __('Your changes have successfully been saved.');
                    $authuser->setNotices($success, $errors);
                    header('Location: ?');
                    exit;
                }
            }
        }
    }

// Update Mode
} else {
    // Form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Trim input
        $_POST = Format::trim($_POST);

        // Current partners
        $partners = $authuser->info('partners');

        // WiseAgent Settings
        $partners['wiseagent']['category'] = (!empty($_POST['wiseagent_category'])) ? htmlspecialchars($_POST['wiseagent_category']) : null;
        $partners['wiseagent']['call_list'] = ($_POST['call_list'] == 'true') ? 'true' : 'false';

        // Update Agent Partner Settings
        try {
            $sql = "UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;";
            $query = $db->prepare($sql)->execute(array(
                'partners' => json_encode($partners),
                'id' => $authuser->info('id')
            ));
        } catch (Exception $e) {
            $errors[] = __('Failed to update agent partner settings. Please try again.');
        }

        // Success
        if (empty($errors)) {
            $success[] = __('Your changes have successfully been saved.');
            $authuser->setNotices($success, $errors);
            header('Location: ?');
            exit;
        }
    }
}

// API integration status
$api_key = $authuser->info('partners.wiseagent.api_key');
$logins_valid = (!empty($api_key));

// fix empty $_POST for disable page
if (empty($_POST['wa_api_key'])) {
    $_POST['wa_api_key'] = $api_key;
}
