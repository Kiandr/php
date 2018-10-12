<?php

// Full Page
$body_class = 'full';

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage Lead Keywords
if (!$leadsAuth->canManageSocialNetworks($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to manage keywords.'
    );
}

// Success
$success = array();

// Errors
$errors = array();

// DB connection
$db = DB::get();

// Load settings from database
$settings = $db->fetch("SELECT * FROM `" . TABLE_SETTINGS . "` WHERE `agent` = 1;");

/* Throw Missing Default Settings Exception */
if (!empty($defaults)) {
    throw new \REW\Backend\Exceptions\MissingSettings\MissingSocialMediaException();
}

// Process Submit
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // POST data
        $settings = array(
            'facebook_apikey'   => Format::trim($_POST['facebook_apikey']),
            'facebook_secret'   => Format::trim($_POST['facebook_secret']),
            'google_apikey'     => Format::trim($_POST['google_apikey']),
            'google_secret'     => Format::trim($_POST['google_secret']),
            'microsoft_apikey'  => Format::trim($_POST['microsoft_apikey']),
            'microsoft_secret'  => Format::trim($_POST['microsoft_secret']),
            'twitter_apikey'    => Format::trim($_POST['twitter_apikey']),
            'twitter_secret'    => Format::trim($_POST['twitter_secret']),
            'linkedin_apikey'   => Format::trim($_POST['linkedin_apikey']),
            'linkedin_secret'   => Format::trim($_POST['linkedin_secret']),
            'yahoo_apikey'      => Format::trim($_POST['yahoo_apikey']),
            'yahoo_secret'      => Format::trim($_POST['yahoo_secret'])
        );

        // Generate UPDATE Query
        $db->prepare("UPDATE `" . TABLE_SETTINGS . "` SET "
            . " `facebook_apikey`	= :facebook_apikey, "
            . " `facebook_secret`	= :facebook_secret, "
            . " `google_apikey`		= :google_apikey, "
            . " `google_secret`		= :google_secret, "
            . " `microsoft_apikey`	= :microsoft_apikey, "
            . " `microsoft_secret`	= :microsoft_secret, "
            . " `twitter_apikey`	= :twitter_apikey, "
            . " `twitter_secret`	= :twitter_secret, "
            . " `linkedin_apikey`	= :linkedin_apikey, "
            . " `linkedin_secret`	= :linkedin_secret, "
            . " `yahoo_apikey`		= :yahoo_apikey, "
            . " `yahoo_secret`		= :yahoo_secret, "
            . " `timestamp_updated`	= NOW()"
            . " WHERE `agent` = 1"
        . ";")->execute($settings);

        // Success!
        $success[] = 'Your settings have successfully been saved.';

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred while saving your changes.';
        //$errors[] = $e->getMessage();
    }

    // Save notices & redirect to form
    $authuser->setNotices($success, $errors);
    header('Location: ?submit');
    exit;
}
