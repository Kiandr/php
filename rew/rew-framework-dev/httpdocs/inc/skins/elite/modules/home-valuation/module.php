<?php

// Require the property valuation module to be installed
$showForm = !empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION']);
if (empty($showForm)) {
    return;
}

// URL to cma page
$formAction = '/cma.php';

// Button text
$buttonText = 'What\'s It Worth?';

// Placeholder text
$emailPlaceholder = 'Your Email Address';
$addressPlaceholder = 'Your Home Address&hellip;';

// Current IDX feed (hidden form value)
$idxFeed = Settings::getInstance()->IDX_FEED;

// Default values
$user = User_Session::get();
$emailValue = $user->info('email');
$addressValue = [$user->info('address1'), $user->info('city'), $user->info('state'), $user->info('zip')];
$addressValue = trim(implode(', ', array_filter($addressValue)), ', ');

// Save lead data on POST submission
if ($this->config('ajax') && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = [];
    try {
        // Why was the honeypot provided?
        if (!empty($_POST['honeypot'])) {
            throw new UnexpectedValueException('Your submission was detected as SPAM.');
        }

        // Require valid email address
        if (!Validate::email($_POST['email'], true)) {
            throw new UnexpectedValueException('Please provide a valid email address.');
        }

        // Require valid street address
        if (!Validate::stringRequired($_POST['full_address'])) {
            throw new UnexpectedValueException('Please provide your street address.');
        }

        // Form settings
        $formName = 'Home valuation';
        $formAutoResponderId = null;

        // Require 'collectContactData' function
        require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';

        // Collect contact data
        $userTrackID = collectContactData([
            'email'          => $_POST['email'],
            'full_address'   => $_POST['full_address'],
            'address1'       => $_POST['address'],
            'city'           => $_POST['city'],
            'state'          => $_POST['state'],
            'county'         => $_POST['county'],
            'zip'            => $_POST['zip'],
            'message'        => getFormVars(),
            'forms'         => $formName
        ], $formAutoResponderId);

        // All is well - return ID in response
        $json['success'] = (int) $userTrackID;

    // Validation error
    } catch (UnexpectedValueException $e) {
        $json['error'] = $e->getMessage();

    // Unexpected error
    } catch (Exception $e) {
        $json['error'] = 'An unexpected error has occurred.';
        Log::error($e);
    }

    // Return JSON response
    header('Content-Type: application/json');
    die(json_encode($json));
}
