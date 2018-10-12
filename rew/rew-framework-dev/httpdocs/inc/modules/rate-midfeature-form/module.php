<?php

// DB Connection
$db = DB::get('cms');

//Map Setup
$container = $this->getContainer();
$page = $container->getPage();
$page->getSkin()->loadMapApi();

// Set default email address
$user = User_Session::get();
$user_info = $user->getRow();
$email_value = $user->info('email');

if (!empty($user_info) && is_array($user_info)) {
    $user_location =  implode(', ', array_filter(array($user_info['address1'], $user_info['city'], $user_info['state'], $user_info['zip'])));
}

// Module configuration
$address_placeholder    = $this->config('addressPlaceholder') ? $this->config('addressPlaceholder') : 'Enter your address';
$email_placeholder      = $this->config('emailPlaceholder') ? $this->config('emailPlaceholder') : 'Enter your email';
$submit_button          = $this->config('submitButton') ? $this->config('submitButton') : 'Submit';
$guaranteed_sold_form   = $this->config('formGuaranteed') ? $this->config('formGuaranteed') : 'guaranteed-sale-form';

/**
 * Create Guaranteed Sold Lead
 */
if (isset($_GET['ajax']) && isset($_GET['apply'])) {
    //Error handeling variables
    $honeypot_email = null;
    $json['success'] = array();
    $json['error'] = array();

    // Email Test
    $email      = trim($_POST['mi0moecs']);
    $email_test = Validate::email($email, true);
    if (!$email_test) {
        $json['error'][] = 'Please supply a valid email.';
    }

    // Address Test
    $address    = trim($_POST['full_address']);
    $address_test = Validate::stringRequired($address, true);
    if (!$address_test) {
        $json['error'][] = 'Please supply an address.';
    }

    // Spam Test
    if (empty($json['error'])) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/backend/inc/php/routine.spam-stop.php';
        $spam = checkForSpam($package);
        if ($spam || !$package['is_browser'] || !empty($honeypot_email)) {
            $json['error'][] = 'Email detected as spam.';
        }
    }

    if (empty($json['error'])) {
        // Collect Contact Data
        require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';
        collectContactData(array(
            'email'          => isset($email) ? $email : null,
            'full_address'   => isset($address) ? $email : null,
            'address1'       => isset($_POST['street_address']) ? $_POST['street_address'] : null,
            'city'           => isset($_POST['city']) ? $_POST['city'] : null,
            'state'          => isset($_POST['state']) ? $_POST['state'] : null,
            'county'         => isset($_POST['county']) ? $_POST['county'] : null,
            'zip'            => isset($_POST['zip']) ? $_POST['zip'] : null,
            'message'        => getFormVars(),
            'forms'          => 'Guaranteed Sold CTA'
        ), 13);

        $json['form'] = rew_snippet('form-guaranteed', false);
        $json['success'] = true;

        // JSON Response
        header('Content-Type: application/json');
        die(json_encode($json));
    }
} else if (isset($_GET['ajax']) && isset($_GET['submit'])) {
    $json['form'] = rew_snippet('form-guaranteed', false);

    // JSON Response
    header('Content-Type: application/json');
    die(json_encode($json));
}
