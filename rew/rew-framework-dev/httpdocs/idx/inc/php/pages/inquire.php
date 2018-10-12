<?php

// Get Requested Listing
$listing = requested_listing();

// Anti-Spam Settings
$anti_spam = array(
    'optin' => Settings::get('anti_spam.optin'),
    'consent_text' => Settings::get('anti_spam.consent_text')
);

// Require Listing
if (!empty($listing)) {
    // Remove query string to build canonical url (using www domain)
    $canonical = Http_Host::getDomainUrl() . (stristr($_SERVER['REQUEST_URI'], '?') ? substr($_SERVER['REQUEST_URI'], 1, strrpos($_SERVER['REQUEST_URI'], '?') - 1) : substr($_SERVER['REQUEST_URI'], 1));

    // Showing Request
    $inquire_type = $_REQUEST['inquire_type'];
    if ($inquire_type == 'Property Showing') {
        $page_title = Lang::write('IDX_DETAILS_SHOWING_PAGE_TITLE', $listing);
        $meta_keyw  = Lang::write('IDX_DETAILS_SHOWING_META_KEYWORDS', $listing);
        $meta_desc  = Lang::write('IDX_DETAILS_SHOWING_META_DESCRIPTION', $listing);
        $page->info('link.canonical', $canonical . '?inquire_type=Property+Showing');

    // Inquire
    } else {
        $page_title = Lang::write('IDX_DETAILS_INQUIRE_PAGE_TITLE', $listing);
        $meta_keyw  = Lang::write('IDX_DETAILS_INQUIRE_META_KEYWORDS', $listing);
        $meta_desc  = Lang::write('IDX_DETAILS_INQUIRE_META_DESCRIPTION', $listing);
        $page->info('link.canonical', $canonical);
    }

    // Listing address
    $address = implode(", ", array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState'], $listing['AddressZipCode'])));

    // Inquiry types
    $inquiry_types = array_filter(array(
        'More Info'         => array(
            'subject' => 'I would like to request more information regarding this property',
            'message' => ($idx->getLink() === 'cms' ? "Please send me more information regarding " . $address . ".\n\nThank you!" : Lang::write('INQUIRE_ASK_ABOUT', $listing))
        ),
        'Property Showing'  => array(
            'subject' => 'I would like to request a property showing',
            'message' => ($idx->getLink() === 'cms' ? "I'd like to request a showing of " . $address . ".\n\nThank you!" : Lang::write('INQUIRE_REQUEST_SHOWING', $listing))
        ),
        'Contact'           => array(
            'subject' => 'I am inquiring about selling a property',
            'message' => ''
        ),
        'Question'          => array(
            'subject' => 'I have a real estate question',
            'message' => ''
        )
    ));

    // User Session
    $user = User_Session::get();

    // Agent ID is set, find agent to assign new leads
    $agent = false;
    $agent_id = $_GET['agent'];
    if (isset($_POST['agent'])) {
        $agent_id = $_POST['agent'];
        unset($_POST['agent']);
    }
    if (!empty($agent_id) && (!$user->isValid() || $user->info('agent') < 1)) {
        $agent = Backend_Agent::load($agent_id);
    }

    // Lender ID is set, find lender to assign new leads
    $lender = false;
    $lender_id = $_GET['lender'];
    if (isset($_POST['lender'])) {
        $lender_id = $_POST['lender'];
        unset($_POST['lender']);
    }
    if (!empty($lender_id) && (!$user->isValid() || $user->info('lender') < 1)) {
        $lender = Backend_Lender::load($lender_id);
        unset($inquiry_types);
    }

    // Show Form
    $show_form = true;

    // Errors Collection
    $errors = array();

    // Process Submit
    if (isset($_GET['submit'])) {
        // Un-Obfiscate Honeypot Variables
        $email      = trim($_POST['mi0moecs']);
        $first_name = trim($_POST['onc5khko']);
        $last_name  = trim($_POST['sk5tyelo']);

        // Test Honeypot Variable
        $fake = !empty($post['registration_type']);

        // Check & Save First Name
        $error = Validate::stringRequired($first_name) ? '' : 'Please supply your first name.';
        if (!empty($error)) {
            $errors['first_name'] = $error;
        }
        $user->saveInfo('first_name', $first_name);

        // Check & Save Last Name
        $error = Validate::stringRequired($last_name) ? '' : 'Please supply your last name.';
        if (!empty($error)) {
            $errors['last_name'] = $error;
        }
        $user->saveInfo('last_name', $last_name);

        // Check & Save Email
        $error = Validate::email($email) ? '' : 'Please supply a valid email address.';
        if (!empty($error)) {
            $errors['email'] = $error;
        }
        $user->saveInfo('email', $email);

        // Check if not empty or is required & Save Phone Number
        if (Validate::stringRequired($_POST['phone']) || (isset(Settings::getInstance()->SETTINGS['registration_phone']) && !empty(Settings::getInstance()->SETTINGS['registration_phone']))) {
            $error = Validate::phone($_POST['phone']) ? '' : 'Please supply a valid phone number.';
            if (!empty($error)) {
                $errors['phone'] = $error;
            }
        }
        $user->saveInfo('phone', $_POST['phone']);

        // Store Comments
        $user->saveInfo('comments', $_POST['comments']);

        // Spam Check
        require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/routine.spam-stop.php';
        $spam = checkForSpam($package);
        $isSpam = ($spam || !$package['is_browser'] || $fake) ? true : false;

        // Check For Potentially Malicious Submission Data
        $check_fields = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'inquire_type' => $_POST['inquire_type']
        );
        list($not_allowed, $bad_fields) = Validate::formFields($check_fields);
        foreach ($bad_fields as $field_name => $formatted_name) {
            $errors[$field_name] = 'We are sorry.  We are unable to process your submission as ' . $formatted_name . ' contains at least one of the following characters: ' . implode(', ', Format::htmlspecialchars($not_allowed));
        }

        // Errors Found, Show em
        if (empty($errors) && !$isSpam) {
            // Require Contact Snippet Functions
            require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';

            // Generate Email Message
            ob_start();
            include $page->locateTemplate('idx', 'emails', 'inquire');
            $message = ob_get_contents();
            ob_end_clean();

            // Collect Contact Data
            collectContactData(array (
                'first_name' => $user->info('first_name'),
                'last_name'  => $user->info('last_name'),
                'email'      => $user->info('email'),
                'phone'      => $user->info('phone'),
                'comments'   => $user->info('comments'),
                'subject'    => (!empty($inquire_type) ? $inquire_type . ' - ' : '') . implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState']))),
                'message'    => $message,
                'forms'      => 'IDX Inquiry',
                'listing'    => ($inquire_type != 'Contact') ? $listing : null,
                'agent'     => (!empty($agent)  ? $agent->getId()   : null),
                'lender'    => (!empty($lender) ? $lender->getId()  : null),
                'opt_marketing' => isset($_POST['opt_marketing']) ? 'in' : null,
            ), 6);

            // Hide Form
            $show_form = false;

            // List Tracking
            if (!empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
                IDX_COMPLIANCE::trackPageLoad($page, $listing);
            }
        }
    }

    // Set Default $_POST
    $_POST['onc5khko'] = isset($_POST['onc5khko']) ? trim($_POST['onc5khko']) : $user->info('first_name');
    $_POST['sk5tyelo'] = isset($_POST['sk5tyelo']) ? trim($_POST['sk5tyelo']) : $user->info('last_name');
    $_POST['mi0moecs'] = isset($_POST['mi0moecs']) ? trim($_POST['mi0moecs']) : $user->info('email');
    $_POST['phone']    = isset($_POST['phone'])    ? trim($_POST['phone'])    : $user->info('phone');

    // Showing Suite Calendar
    $showing_suite_display = false;
    if (!empty(Settings::getInstance()->MODULES['REW_SHOWING_SUITE'])) {
        if ($_REQUEST['inquire_type'] == 'Property Showing') {
            $showing_suite_display = $page->container('snippet')->addModule('showing-suite', array('mls_number' => $listing['ListingMLS']))->display(false);
        }
    }
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
