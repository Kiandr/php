<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$settingsAuth = new REW\Backend\Auth\SettingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$settingsAuth->canManageSettings($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view site settings')
    );
}

// Success
$success = array();

// Errors
$errors = array();


// Get container
$container = Container::getInstance();

// Get Skin/Theme
$skin = $container->get(REW\Core\Interfaces\SkinInterface::class);

// Get consent message
$consentMessage = $skin->getDefaultConsentMessage();

// Site Database
$db = DB::get();

// Load Settings
$settings = $db->prepare("SELECT * FROM `default_info` WHERE `agent` = :agent;");
$settings->execute(array('agent' => 1));
$settings = $settings->fetch();

/* Throw Missing Settings Exception */
if (empty($settings)) {
    throw new \REW\Backend\Exceptions\MissingSettings\MissingIdxSystemException();
}

// Decode Mail settings
$settings['mail_settings'] = is_array($settings['mail_settings']) ? $settings['mail_settings'] : json_decode($settings['mail_settings'], true);
$settings['mail_settings']['provider'] = isset($_POST['mail_provider']) ? $_POST['mail_provider'] : $settings['mail_settings']['provider'];

// Process Submit
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate Mail settings
    if ($_POST['mail_provider'] === 'mandrill') {
        if (empty($_POST['mandrill_username'])) {
            $errors[] = __('Mandrill SMTP Username is required');
        }
        if (empty($_POST['mandrill_password'])) {
            $errors[] = __('Mandrill SMTP Password is required');
        }
    }

    if ($_POST['mail_provider'] === 'sendgrid') {
        if (empty($_POST['sendgrid_username'])) {
            $errors[] = __('SendGrid Username is required');
        }
        if (empty($_POST['sendgrid_password'])) {
            $errors[] = __('SendGrid Password is required');
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Require ENUM
        $_POST['auto_assign'] = ($_POST['auto_assign'] == 'true') ? 'true' : 'false';
        $_POST['auto_rotate'] = ($_POST['auto_rotate'] == 'true') ? 'true' : 'false';
        $_POST['auto_optout'] = ($_POST['auto_optout'] == 'true') ? 'true' : 'false';
        $_POST['auto_rotate_unassign'] = ($_POST['auto_rotate_unassign'] == 'true') ? 'true' : 'false';
        $_POST['auto_assign_lenders'] = ($_POST['auto_assign_lenders'] == 'true') ? 'true' : 'false';
        $_POST['auto_generated_searches'] = ($_POST['auto_generated_searches'] == 'true') ? 'true' : 'false';
        $_POST['anti_spam_optin'] = ($_POST['anti_spam_optin'] == 'in') ? 'in' : 'out';
        $_POST['anti_spam_sms_optin'] = ($_POST['anti_spam_sms_optin'] == 'in') ? 'in' : 'out';
        $_POST['shark_tank'] = ($_POST['shark_tank'] == 'true') ? 'true' : 'false';

        // Serialize Arrays
        $_POST['scoring'] = serialize($_POST['scoring']);
        $_POST['auto_optout_actions'] = serialize($_POST['auto_optout_actions']);
        $_POST['auto_rotate_days'] = !empty($_POST['auto_rotate_days']) && is_array($_POST['auto_rotate_days']) ? implode(',', $_POST['auto_rotate_days']) : '';
        $_POST['auto_optout_days'] = !empty($_POST['auto_optout_days']) && is_array($_POST['auto_optout_days']) ? implode(',', $_POST['auto_optout_days']) : '';

        // Valid mail providers
        $mail_providers = array();
        if (!empty(Settings::getInstance()->MODULES['REW_MAIL_MANDRILL'])) {
            $mail_providers[] = 'mandrill';
        }
        if (!empty(Settings::getInstance()->MODULES['REW_MAIL_SENDGRID'])) {
            $mail_providers[] = 'sendgrid';
        }

        // Selected mail provider
        $mail_provider = !empty($_POST['mail_provider']) && in_array($_POST['mail_provider'], $mail_providers) ? $_POST['mail_provider'] : '';

        // Mail settings
        $mail_settings = array(
            'provider' => $mail_provider,
            'mandrill' => array(
                'username' => $_POST['mandrill_username'],
                'password' => $_POST['mandrill_password'],
            ),
            'sendgrid' => array(
                'username' => $_POST['sendgrid_username'],
                'password' => $_POST['sendgrid_password'],
            ),
        );

        try {
            // Prepare UPDATE Query
            $update = $db->prepare("UPDATE `default_info` SET "
                . " `scoring`					= :scoring, "
                . "`auto_assign`				= :auto_assign,"
                . "`auto_rotate`				= :auto_rotate,"
                . "`auto_optout`				= :auto_optout,"
                . "`auto_generated_searches`	= :auto_generated_searches,"
                // Auto-Rotation Settings
                . (($_POST['auto_rotate'] == 'true' && isset($_POST['auto_rotate_hours']) && isset($_POST['auto_rotate_frequency']))
                    ? "`auto_rotate_days`		= :auto_rotate_days,"
                    . "`auto_rotate_hours`		= :auto_rotate_hours,"
                    . "`auto_rotate_frequency`	= :auto_rotate_frequency,"
                    . "`auto_rotate_unassign`	= :auto_rotate_unassign,"
                    : '')
                // Auto-Opt-Out Settings
                . (($_POST['auto_optout'] == 'true' && isset($_POST['auto_rotate_hours']) && isset($_POST['auto_optout_time']) && isset($_POST['auto_optout_actions']))
                    ? "`auto_optout_days`		= :auto_optout_days,"
                    . "`auto_optout_hours`		= :auto_optout_hours,"
                    . "`auto_optout_time`		= :auto_optout_time,"
                    . "`auto_optout_actions`	= :auto_optout_actions,"
                    : '')
                // Shark Tank Settings
                . (!empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])
                    ? "`shark_tank`				= :shark_tank,"
                    : '')
                // Lender Auto-Assignment
                . (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])
                    ? "`auto_assign_lenders`	= :auto_assign_lenders,"
                    : '')
                // Mail Settings
                . (!empty(Settings::getInstance()->MODULES['REW_MAIL_MANDRILL']) || !empty(Settings::getInstance()->MODULES['REW_MAIL_SENDGRID'])
                    ? "`mail_settings`			= :mail_settings,"
                    : '')
                // Calendar Settings
                . (isset($_POST['calendar_notifications'])
                    ? "`calendar_notifications`		= :calendar_notifications,"
                    : '')
                . " `timestamp_updated`			= NOW()"
            . " WHERE `agent` = 1;");

            // Query Parameters
            $params = array(
                'scoring'       => $_POST['scoring'],
                'auto_assign'   => $_POST['auto_assign'],
                'auto_rotate'   => $_POST['auto_rotate'],
                'auto_optout'   => $_POST['auto_optout'],
                'auto_generated_searches' => $_POST['auto_generated_searches']
            );

            // Auto-Rotation Settings
            if ($_POST['auto_rotate'] == 'true' && isset($_POST['auto_rotate_hours']) && isset($_POST['auto_rotate_frequency'])) {
                $params = array_merge($params, array(
                    'auto_rotate_days'      => $_POST['auto_rotate_days'],
                    'auto_rotate_hours'     => $_POST['auto_rotate_hours'],
                    'auto_rotate_frequency' => $_POST['auto_rotate_frequency'],
                    'auto_rotate_unassign'  => $_POST['auto_rotate_unassign']
                ));
            }

            // Auto-Opt-Out Settings
            if ($_POST['auto_optout'] == 'true' && isset($_POST['auto_rotate_hours']) && isset($_POST['auto_optout_time']) && isset($_POST['auto_optout_actions'])) {
                $params = array_merge($params, array(
                    'auto_optout_days'      => $_POST['auto_optout_days'],
                    'auto_optout_hours'     => $_POST['auto_optout_hours'],
                    'auto_optout_time'      => $_POST['auto_optout_time'],
                    'auto_optout_actions'   => $_POST['auto_optout_actions']
                ));
            }

            // Shark Tank
            if (!empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])) {
                $params['shark_tank'] = $_POST['shark_tank'];
            }

            // Lender Auto-Assignment
            if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
                $params['auto_assign_lenders']  = $_POST['auto_assign_lenders'];
            }

            // Mail Settings
            if (!empty(Settings::getInstance()->MODULES['REW_MAIL_MANDRILL']) || !empty(Settings::getInstance()->MODULES['REW_MAIL_SENDGRID'])) {
                $params['mail_settings'] = json_encode($mail_settings);
            }

            // Calendar Settings
            if (isset($_POST['calendar_notifications'])) {
                $params = array_merge($params, array(
                    'calendar_notifications'        => $_POST['calendar_notifications'],
                ));
            }

            // Save Settings
            $update->execute($params);

            // Success
            $success[] = __('Settings have successfully been saved.');

            try {
                // Set Anti-Spam Email Settings
                Settings::set('anti_spam.optin', $_POST['anti_spam_optin']);
                Settings::set('anti_spam.consent_text', $_POST['anti_spam_consent_text']);

                // Set Anti-Spam Text Settings
                if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
                    Settings::set('anti_spam_sms.optin', $_POST['anti_spam_sms_optin']);
                    Settings::set('anti_spam_sms.consent_text', $_POST['anti_spam_sms_consent_text']);
                }
            } catch (PDOException $e) {
                $errors[] = __('Anti-Spam settings could not be updated.');
            }

            try {
                // Set Dropbox API Key
                $dropboxApiKey = $_POST['settings']['moxiemanager.dropbox.app_id'];
                Settings::set('moxiemanager.dropbox.app_id', $dropboxApiKey);
            } catch (PDOException $e) {
                $errors[] = __('Dropbox App Key could not be updated.');
            }

            // Google API Key Settings
            try {
                // Set Google API Key
                $googleMapsApiKey = trim($_POST['settings']['google.maps.api_key']);
                Settings::set('google.maps.api_key', $googleMapsApiKey);
            } catch (PDOException $e) {
                $errors[] = __('Google API Key could not be updated.');
            }

            if (isset($_POST['settings']['search_area_label'])) {
                try {
                    Settings::set('search_area_label', $_POST['settings']['search_area_label']);
                } catch (PDOException $e) {
                    $errors[] = __('Search Area Label could not be updated.');
                }
            }

            // Save Notices & Redirect to Form
            $authuser->setNotices($success, $errors);
            header('Location: ?success');
            exit;

        // Database Error
        } catch (PDOException $e) {
            $errors[] = __('Error Occurred while saving your changes.');
        }
    }
}

// Agents in Auto-Assignment
$auto_assign = array();

// Agents in Auto-Rotation
$auto_rotate = array();

// Agents in Auto-Opt-Out
$auto_optout = array();

// Agents in Rotation
$result = $db->query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name`, `auto_assign_admin`, `auto_assign_agent`, `auto_rotate`, `auto_optout` FROM `agents` WHERE (`auto_assign_admin` = 'true' OR `auto_assign_agent` = 'true' OR `auto_rotate` = 'true');");
while ($row = $result->fetch()) {
    // Auto-Assignment
    if ($row['auto_assign_admin'] == 'true' && $row['auto_assign_agent'] == 'true') {
        $auto_assign[] = $row;
    }
    // Auto-Rotation
    if ($row['auto_rotate'] == 'true' && $row['auto_assign_agent'] == 'true') {
        $auto_rotate[] = $row;
    }
    // Auto-Opt-Out
    if ((($row['auto_assign_admin'] == 'true' && $row['auto_assign_agent'] == 'true') || $row['auto_rotate'] == 'true') && $row['auto_optout'] == 'true') {
        $auto_optout[] = $row;
    }
}

// Rotation Days
$settings['auto_rotate_days'] = is_array($settings['auto_rotate_days']) ? $settings['auto_rotate_days'] : (!empty($settings['auto_rotate_days']) ? explode(',', $settings['auto_rotate_days']) : '');

// Auto-Opt-Out Days
$settings['auto_optout_days'] = is_array($settings['auto_optout_days']) ? $settings['auto_optout_days'] : (!empty($settings['auto_optout_days']) ? explode(',', $settings['auto_optout_days']) : '');

// Unserialize Automated Opt-Out Actions
$settings['auto_optout_actions'] = is_array($settings['auto_optout_actions']) ? $settings['auto_optout_actions'] : unserialize($settings['auto_optout_actions']);

// Lenders in Auto-Assignment
$auto_assign_lenders = array();
if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
    $result = $db->query("SELECT `id`, `first_name`, `last_name`, `auto_assign_admin`, `auto_assign_optin` FROM `lenders` WHERE (`auto_assign_admin` = 'true' OR `auto_assign_optin` = 'true');");
    while ($row = $result->fetch()) {
        $auto_assign_lenders[] = $row;
    }
}

$settings['search_area_label'] = Settings::get('search_area_label');


// Unserialize Scoring Setting
$settings['scoring'] = is_array($settings['scoring']) ? $settings['scoring'] : unserialize($settings['scoring']);

try {
    // Anti-Spam Email Settings
    $settings['anti_spam_optin'] = Settings::get('anti_spam.optin');
    $settings['anti_spam_consent_text'] = Settings::get('anti_spam.consent_text');

    // Anti-Spam Text Message Settings
    $settings['anti_spam_sms_optin'] = Settings::get('anti_spam_sms.optin');
    $settings['anti_spam_sms_consent_text'] = Settings::get('anti_spam_sms.consent_text');
} catch (PDOException $e) {
    $errors[] = __('Anti-Spam settings could not be loaded.');
}

try {
    // Dropbox API Key
    $dropboxApiKey = Settings::get('moxiemanager.dropbox.app_id');
} catch (PDOException $e) {
    $errors[] = __('Dropbox App Key could not be loaded.');
}

// Google API Key Settings
try {
    // Google API Key
    $googleMapsApiKey = Settings::get('google.maps.api_key');
} catch (PDOException $e) {
    $errors[] = __('Google API Key could not be loaded.');
}

// Score System
$scores = array(
    'visits'    => array(
        'title' => __('# of Return Visits'),
        'value' => isset($settings['scoring']['visits']) ? $settings['scoring']['visits'] : 5
    ),
    'listings'  => array(
        'title' => __('# of Viewed Listings'),
        'value' => isset($settings['scoring']['listings']) ? $settings['scoring']['listings'] : 5
    ),
    'favorites' => array(
        'title' => __('# of %s Listings', __('Favorite')),
        'value' => isset($settings['scoring']['favorites']) ? $settings['scoring']['favorites'] : 5
    ),
    'searches'  => array(
        'title' => __('# of Saved Searches'),
        'value' => isset($settings['scoring']['searches']) ? $settings['scoring']['searches'] : 5
    ),
    'inquiries' => array(
        'title' => __('# of Inquiries'),
        'value' => isset($settings['scoring']['inquiries']) ? $settings['scoring']['inquiries'] : 5
    ),
    'calls'     => array(
        'title' => __('Good Phone Number'),
        'value' => isset($settings['scoring']['calls']) ? $settings['scoring']['calls'] : 5
    ),
    'manual'    => array(
        'title' => __('Agent Added Lead'),
        'value' => isset($settings['scoring']['manual']) ? $settings['scoring']['manual'] : 5
    ),
    'price' => array(
        'title' => __('Average Price'),
        'value' => isset($settings['scoring']['price']) ? $settings['scoring']['price'] : 5
    ),
);
