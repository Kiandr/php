<?php

// Get Requested Listing
$listing = requested_listing();

// Require Listing
if (!empty($listing)) {
    // REW Twilio Module is not installed
    if (empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) {
        header('Location: ' . $listing['url_details']);
        exit;
    }

    // Must be logged in
    if (!$user->isValid()) {
        $user->setRedirectUrl($listing['url_details']);
        $user->saveInfo('details_popup', $_SERVER['REQUEST_URI']); // Set popup to open
        $url_register = $listing['url_register'] . '?contact_method=text' . (isset($_GET['popup']) ? '&popup' : '');
        header('Location: ' . $url_register);
        exit;
    }

    // Remove query string to build canonical url (using www domain)
    $canonical = Http_Host::getDomainUrl() . (stristr($_SERVER['REQUEST_URI'], '?') ? substr($_SERVER['REQUEST_URI'], 1, strrpos($_SERVER['REQUEST_URI'], '?') - 1) : substr($_SERVER['REQUEST_URI'], 1));

    // Maximum character length
    $maxlength = 80;

    // Page meta information
    $page_title = Lang::write('IDX_DETAILS_PHONE_PAGE_TITLE', $listing);
    $meta_keyw = Lang::write('IDX_DETAILS_PHONE_META_KEYWORDS', $listing);
    $meta_desc = Lang::write('IDX_DETAILS_PHONE_META_DESCRIPTION', $listing);
    $page->info('link.canonical', $canonical);

    // Default message (and placeholder text)
    $placeholder = $listing['Address'] . ' (' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . ')';
    $_POST['message'] = $_POST['message'] ?: $placeholder;

    // User Session
    $user = User_Session::get();

    // Error data
    $errors = array();
    $error = false;

    // Success
    $success = $user->info('success');
    $user->saveInfo('success', false);

    // Process Submit
    if (isset($_GET['submit'])) {
        // Message cannot be longer $maxlength
        $message = trim($_POST['message']);
        if (strlen($message) > $maxlength) {
            $errors['message'] = 'Your message cannot be longer than 80 characters.';
        }

        try {
            // DB connection
            $db = DB::get();

            // Require php-libphonenumber for formatting and validating phone numbers
            require_once Settings::getInstance()->DIRS['LIB'] . 'libphonenumber/autoload.php';
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

            try {
                // Validate & save cell phone number
                $backend_lead = Backend_Lead::load($user->user_id(), $db);
                $phone_check = $backend_lead->validateCellNumber($_POST['to']);

                // Phone number details
                $friendly_name = $phone_check['friendly_name'];
                $phone_number = $phone_check['phone_number'];

            // Validation error occurred
            } catch (UnexpectedValueException $e) {
                throw $e;
            }

            // Send listing details URL (Append ?uid query to auto-login)
            $url = $listing['url_details'];
            if ($guid = $user->info('guid')) {
                $url .= '?uid=' . Format::toGuid($guid);
            }

            // Send listing details in SMS message
            $body = $url . (!empty($message) ? PHP_EOL . $message : '');

            // Twilio Partner
            $twilio = Partner_Twilio::getInstance();

            // Choose first available phone number
            $numbers = $twilio->getTwilioNumbers();
            $from = $numbers ? array_pop($numbers)['phone_number'] : false;

            // Send to this number
            $to = $phone_number;

            // Send property information
            $twilio->sendSmsMessage($to, $from, $body);

            // Track outgoing text message
            (new History_Event_Text_Listing(array(
                'to'        => $to,
                'from'      => $from,
                'body'      => $message,
                'listing'   => $listing
            ), array(
                new History_User_Lead($user->user_id())
            )))->save($db);

            // Success message
            $success = 'Thank you! This listing has been sent to you at <strong>' . $friendly_name . '</strong>.';
            $user->saveInfo('success', $success);

            // Redirect back to page
            $url_redirect = $listing['url_phone'] . (isset($_GET['popup']) ? '?popup' : '');
            header('Location: ' . $url_redirect);
            exit;

        // Validation error
        } catch (UnexpectedValueException $e) {
            $errors['to'] = $e->getMessage();

        // Parse error
        } catch (\libphonenumber\NumberParseException $e) {
            $errors['to'] = 'The provided phone number does not seem to be valid.';

        // Twilio exception thrown
        } catch (Partner_Twilio_Exception $e) {
            // Internal error (invalid "From" number was used)
            if ($e->getMessage() === Partner_Twilio::ERROR_FROM_INVALID) {
                $error= Partner_Twilio::ERROR_UNEXPECTED;
            } else {
                // Twilio API message
                $error = $e->getMessage();
            }
        }
    } else {
        // Cell phone #
        $to = $user->info('phone_cell');
    }

    // Require page specific javascript
    $page->addJavascript(Settings::getInstance()->URLS['URL'] . 'inc/js/idx/phone.js', 'external');
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
