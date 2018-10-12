<?php

/**
 * @global \REW\Core\Interfaces\User\SessionInterface $user
 */

// Redirect if logged in
if ($user->isValid()) {
    header('Location: /');
    exit;
}

// Page Meta Information
$page_title = Lang::write('IDX_REGISTER_PAGE_TITLE');
$meta_keyw  = Lang::write('IDX_REGISTER_META_KEYWORDS');
$meta_desc  = Lang::write('IDX_REGISTER_META_DESCRIPTION');
$settings   = Settings::getInstance();

// Get Skin
$skin = Container::getInstance()->get(REW\Core\Interfaces\SkinInterface::class);

// IDX Listing Requested
$listing = requested_listing();
if (!empty($listing)) {
    $user->setRedirectUrl($listing['url_details']);
}


// Show Form
$show_form = true;

// Anti-Spam Email
$anti_spam = array(
    'optin' => $settings->LANG == 'en-CA' ? false : Settings::get('anti_spam.optin'),
    'consent_text' => Settings::get('anti_spam.consent_text') ?: $skin->getDefaultConsentMessage()
);

// Anti-Spam Text
$anti_spam_sms = array(
    'optin' => $settings->LANG == 'en-CA' ? false : Settings::get('anti_spam_sms.optin'),
    'consent_text' => Settings::get('anti_spam_sms.consent_text')
);

// Success
$success = array();

// User Errors
$errors = $user->info('errors');
$errors = is_array($errors) ? $errors : array();
$user->saveInfo('errors', false);

// Social Networks
$networks = OAuth_Login::getProviders();

// Require phone number
$require_phone = in_array($_POST['contact_method'], array('phone', 'text')) || $settings->SETTINGS['registration_phone'];

// Process Form
if (isset($_GET['register'])) {
    // Un-Obfiscate Honeypot Variables
    $email      = trim($_POST['mi0moecs']);
    $first_name = trim($_POST['onc5khko']);
    $last_name  = trim($_POST['sk5tyelo']);

    // Test Honeypot Variables
    $fake = !empty($post['registration_type']);

    // Preferred contact method (Email, Phone or Text)
    $_POST['contact_method'] = in_array($_POST['contact_method'], array('email', 'phone', 'text')) ? $_POST['contact_method'] : 'email';
    $user->saveInfo('contact_method', $_POST['contact_method']);

    // Check & Save First Name
    $error = Validate::stringRequired($first_name) ? '' : 'Please supply your first name.';
    if (!empty($error)) {
        $errors[] = $error;
    }
    $user->saveInfo('first_name', $first_name);

    // Check & Save Last Name
    $error = Validate::stringRequired($last_name) ? '' : 'Please supply your last name.';
    if (!empty($error)) {
        $errors[] = $error;
    }
    $user->saveInfo('last_name', $last_name);

    // Check if not empty or is required & Save Phone Number
    if (Validate::stringRequired($_POST['phone']) || $require_phone) {
        $error = Validate::phone($_POST['phone']) ? '' : 'Please supply a valid phone number.';
        if (!empty($error)) {
            $errors[] = $error;
        }
    }
    $user->saveInfo('phone', $_POST['phone']);

    // Check & Save Email
    $error = Validate::email($email) ? '' : 'Please supply a valid email address.';
    if (!empty($error)) {
        $errors[] = $error;
    }
    $user->saveInfo('email', $email);

    // Password Ruleset in Affect
    if (!empty($settings->SETTINGS['registration_password'])) {
        // Check Password
        $error = Validate::stringRequired($_POST['password']) ? '' : 'Please enter your desired password.';
        if (!empty($error)) {
            $errors[] = $error;
        }

        // Password Check
        if ($_POST['password'] != $_POST['confirm_password']) {
            $errors[] = 'The two passwords you supplied did not match one another.';
        }

        // Validate password
        if (!empty($_POST['password'])) {
            try {
                Validate::password($_POST['password']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
    }

    // Require Compliance Agreement
    global $_COMPLIANCE;
    if (!empty($_COMPLIANCE['register']['agree'])) {
        $agree = $_COMPLIANCE['register']['agree'];
        if (is_array($agree) && empty($_POST['agree'])) {
            $errors[] = 'You must agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.';
        }
    }

    // Check Duplicate Email
    $checkEmail = $db_users->fetchQuery("SELECT COUNT(*) AS `total` FROM `users` WHERE `email` = '" . $db_users->cleanInput($email) . "'" . (!empty($settings->SETTINGS['registration_password']) ? " AND `password` != ''" : '') . ";");
    if ($checkEmail['total'] != 0) {
        $errors[] = 'Your email address has already been registered. <a href="' . $settings->SETTINGS['URL_IDX_LOGIN'] . '" data-modal="login">Login here</a>';
    }

    // Spam Check
    require_once $settings->DIRS['BACKEND'] . 'inc/php/routine.spam-stop.php';
    $spam = checkForSpam($package);
    if ($spam || !$package['is_browser'] || $fake) {
        $errors[] = 'We\'re sorry but you were detected as spam.';
    }

    // Check For Potentially Malicious Submission Data
    $check_fields = array(
        'onc5khko' => $_POST['onc5khko'],
        'sk5tyelo' => $_POST['sk5tyelo']
    );
    list($not_allowed, $bad_fields) = Validate::formFields($check_fields);
    foreach ($bad_fields as $bad_field) {
        $errors[] = 'We are sorry.  We are unable to process your submission as your ' . $bad_field . ' contains at least one of the following characters: ' . implode(', ', Format::htmlspecialchars($not_allowed));
    }

    // Opt-In vs Opt-Out
    $opt_marketing = $_POST['opt_marketing'];
    $_POST['opt_marketing'] = isset($_POST['opt_marketing']) ? 'in' : 'out';
    $user->saveInfo('opt_marketing', $_POST['opt_marketing']);

    // Opt-In vs Opt-Out (Saved Searches)
    $_POST['opt_searches'] = isset($opt_marketing) ? 'in' : 'out';
    $user->saveInfo('opt_searches', $_POST['opt_searches']);

    // Opt-In vs Opt-Out (Text Messages)
    $_POST['opt_texts'] = isset($_POST['opt_texts']) ? 'in' : 'out';
    $user->saveInfo('opt_texts', $_POST['opt_texts']);

    // Check Errors
    if (empty($errors)) {
        // Lead Password
        $_POST['password'] = isset($_POST['password']) ? $user->encryptPassword($_POST['password']) : '';

        // Include Required Files
        require_once $settings->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';

        // Email Message to Send to Agent
        $message  = '<p>A new user has registered at <a href="' . $settings->SETTINGS['URL_IDX_REGISTER'] . '">' . $settings->SETTINGS['URL_IDX_REGISTER'] . '</a>.</p>';
        $message .= '<p>The following information was collected about the user:</p>';
        $message .= $user->formatUserInfo();

        // Capture Lead
        $lead = collectContactData(array (
            'first_name'   => $user->info('first_name'),
            'last_name'    => $user->info('last_name'),
            'password'     => $_POST['password'],
            'email'        => $user->info('email'),
            'phone'        => $user->info('phone'),
            'message'      => $message,
            'forms'        => 'IDX Registration',
            'opt_marketing' => $_POST['opt_marketing'],
            'opt_searches'  => $_POST['opt_searches'],
            'opt_texts'     => $_POST['opt_texts'],
            'contact_method'=> $_POST['contact_method'],
            'listing'      => $listing,
        ), 5);

        // Set User Session ID
        $user->setUserId($lead);

        // Validate User Session
        $user->validate();

        // Get User Row
        $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE `id` = '" . $db_users->cleanInput($lead) . "';");

        // Success
        $success[] = 'You have successfully been registered!';

        // Hide Form
        $show_form = false;

        // Conversion Tracking
        $ppc = Util_CMS::getPPCSettings();
    }
}

// Default contact method
$default_contact_method = in_array($_GET['contact_method'], array('email', 'phone', 'text'))  ? $_GET['contact_method'] : false;
$default_contact_method = $default_contact_method ?: $settings->SETTINGS['default_contact_method'];

// Auto-Fill User Data
$first_name   = $user->info('first_name');
$last_name    = $user->info('last_name');
$email        = isset($_POST['mi0moecs']) ? $_POST['mi0moecs'] : $user->info('email');
$phone        = $user->info('phone');
$_POST['opt_marketing'] = $_POST['opt_marketing'] ?: $_GET['opt_marketing'];
$opt_marketing = isset($_POST['opt_marketing']) ? $_POST['opt_marketing'] : (!empty($anti_spam['optin']) ? $anti_spam['optin'] : $user->info('opt_marketing'));
$opt_texts = $anti_spam_sms['optin'] ?: $user->info('opt_texts');
$contact_method = $user->info('contact_method') ?: $default_contact_method;

//Requires verification
$requires_verification  = (!empty(Settings::getInstance()->SETTINGS['registration_verify']) && !Validate::verifyWhitelisted($user->info('email'))) || Validate::verifyRequired($user->info('email'));
