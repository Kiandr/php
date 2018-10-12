<?php

// Page Instance
$page = $this->getContainer()->getPage();

// Require Google Map API
$page->getSkin()->loadMapApi();

// Show 'Sell my Home' tab
$sell_tab = !empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION']) && Settings::getInstance()->SETTINGS['agent'] == 1 && !empty(Settings::get('google.maps.api_key'));

// Form settings
$form_name = 'Sell My Home';
$form_auto_responder = 14;

// Placeholder text
$search_placeholder = 'City, ' . Locale::spell('Neighborhood') . ', Address, ' . Locale::spell('Zip') . ' or ' . Lang::write('MLS') . ' #';
$address_placeholder = 'Your Street Address&hellip;';
$email_placeholder = 'Your Email Address';

// Default values
$user = User_Session::get();
$email_value = $user->info('email');
$address_value = array($user->info('address1'), $user->info('city'), $user->info('state'), $user->info('zip'));
$address_value = trim(implode(', ', array_filter($address_value)), ', ');

// Handle POST request and return JSON response
if ($this->config('ajax') && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = array();
    try {
        // Lead data
        $data = Format::trim(array(
            'honeypot'      => $_POST['email'],
            'email'         => $_POST['mi0moecs'],
            'full_address'  => $_POST['full_address'],
            'address'       => $_POST['address'],
            'city'          => $_POST['city'],
            'state'         => $_POST['state'],
            'county'        => $_POST['county'],
            'zip'           => $_POST['zip']
        ));

        // Empty honey pot field was filled out (wth - must be spam?)
        if (!empty($data['honeypot'])) {
            throw new UnexpectedValueException('Your submission was detected as SPAM.');
        }

        // Require valid email address
        if (!Validate::email($data['email'], true)) {
            throw new UnexpectedValueException('Please provide a valid email address.');
        }

        // Require valid street address
        if (!Validate::stringRequired($data['full_address'])) {
            throw new UnexpectedValueException('Please provide your street address.');
        }

        // Collect contact data
        require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';
        $userTrackID = collectContactData(array(
            'email'          => $data['email'],
            'full_address'   => $data['full_address'],
            'address1'       => $data['address'],
            'city'           => $data['city'],
            'state'          => $data['state'],
            'county'         => $data['county'],
            'zip'            => $data['zip'],
            'message'        => getFormVars(),
            'forms'         => $form_name
        ), $form_auto_responder);

        // All is well!
        $json['success'] = true;

    // Validation error
    } catch (UnexpectedValueException $e) {
        $json['error'] = $e->getMessage();

    // Unexpected error
    } catch (Exception $e) {
        $json['error'] = 'An unexpected error has occurred.';
        //$json['error'] = $e->getMessage();
    }

    // Return JSON response data
    header('Content-Type: application/json');
    echo json_encode($json);
    exit;
}
