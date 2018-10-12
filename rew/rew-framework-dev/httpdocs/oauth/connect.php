<?php

// Don't allow indexing of this page
header('X-Robots-Tag: noindex, nofollow, noarchive');

// OAuth error occurred
if (!empty($_GET['error'])) {
    // User Cancelled Request, Close Window
    if ($_GET['error'] == 'access_denied') {
        echo '<script> window.close(); </script>';
        exit;
    }
}

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Start session
session_start();

// Get user session
$user = User_Session::get();

try {
    // OAuth errors
    $errors = array();

    // Provider required
    $provider = trim(strtolower((string) $_GET['provider']));
    if (empty($provider)) {
        throw new Exception('Invalid request');
    }

    // DB connection
    $db = DB::get();

    // Get API settings
    $settings = $db->getCollection('default_info')->search(array(
        '$eq' => array('agent' => 1)
    ))->fetch();

    // Error occurred
    if (empty($settings)) {
        throw new Exception('Invalid settings');
    }

    // Unknown provider
    if (!isset($settings[$provider . '_apikey']) && isset($settings[$provider . '_secret'])) {
        throw new Exception('Unknown provider');
    }

    // Provider OAuth settings
    $apikey = $settings[$provider . '_apikey'];
    $secret = $settings[$provider . '_secret'];
    if (empty($apikey) || empty($secret)) {
        throw new Exception('Invalid provider settings');
    }

    try {
        // Load provider
        $oauth = null;
        switch ($provider) {
            case 'facebook':
                $oauth = new OAuth_Login_Facebook($apikey, $secret);
                $options = array('scope' => 'email', 'display' => 'popup');
                break;
            case 'google':
                $oauth = new OAuth_Login_Google($apikey, $secret);
                $options = array('scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email');
                break;
            case 'microsoft':
                $oauth = new OAuth_Login_Microsoft($apikey, $secret);
                $options = array('scope' => 'user.read', 'display' => 'popup');
                break;
            case 'twitter':
                $oauth = new OAuth_Login_Twitter($apikey, $secret);
                $options = array();
                break;
            case 'yahoo':
                $oauth = new OAuth_Login_Yahoo($apikey, $secret);
                $options = array();
                break;
            case 'linkedin':
                $oauth = new OAuth_Login_LinkedIn($apikey, $secret);
                $options = array('scope' => 'r_emailaddress');
                break;
        }

        // Get login URL
        $url = $oauth->getLoginUrl($options);

        // IDX Social Connect
        $user->saveInfo('connect', true);

        // Redirect to OAuth Login
        header('Location: ' . $url, true, 301);
        exit;

    // OAuth error occurred
    } catch (Exception_OAuthLoginError $e) {
        $errors[] = $e->getMessage();
        Log::error($e);
    }

// Error occurred
} catch (Exception $e) {
    $errors[] = $e->getMessage();
    Log::error($e);
}

// Errors occurred
if (!empty($errors)) {
    // Set user errors
    $user->saveInfo('errors', $errors);

    // OAuth error occurred
    echo '<script>
		if (window.opener) {
			window.opener.$(window.opener).trigger(\'oauth-error\', [window]);
		} else {
			window.location.href = \'/idx/register.html\';
		}
	</script>';
    exit;
}
exit;
