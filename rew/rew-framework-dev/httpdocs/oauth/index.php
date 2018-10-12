<?php

// Facebook (OAuth 2.0)
// 1. Register App: https://developers.facebook.com/apps
// 2. Get App Key & App Secret

// Google (OAuth 2.0)
// 1. Register App: https://code.google.com/apis/console/#access
// 2. Get App Key & App Secret

// Microsoft (OAuth 2.0)
// 1. Register App: https://manage.dev.live.com/
// 2. Get App Key & App Secret

// Yahoo (OAuth 1.0)
// 1. Register App: http://developer.yahoo.com/oauth
// 2. Get App Key & App Secret
//
// !!IMPORTANT: Must Select 'Social Directory (Profiles) Read/Write Public and Private' under Permissions

// Twitter (OAuth 1.0)
// 1. Register App: https://dev.twitter.com/apps
//  - Requires Proper 'Callback URL' to Work
// 2. Get App Key & App Secret
//
// !!IMPORTANT: Must Specificy 'Callback URL'

// LinkedIn (OAuth 1.0)
// 1. Register App: https://www.linkedin.com/secure/developer
//  - Requires Proper 'Integration URL' to Work
// 2. Get App Key & App Secret

// OAuth 1.0 (Signed Requests)
// 1. GET Request Token ('oauth_token' and 'oauth_secret')
// 2. Redirect to Login URL
// 3. Receive $_GET['oauth_verifier']
// 4. GET Access Token

// OAuth 2.0
// 1. Redirect to Login URL, Set 'state'
// 2. Receive $_GET['code']
// 3. GET Access Token


// Require Composer Vendor Auto loader
require_once dirname(__FILE__) . '/../../boot/app.php';

// Start Session
session_start();

// Success
$success = array();

// Errors
$errors = array();

// User session
$user = User_Session::get();

// IDX Social Connect
$social_connect = $user->info('connect') ?: false;

// User Cancelled Request, Close Window
if ($_GET['error'] == 'access_denied') {
    echo '<script> window.close(); </script>';
    exit;
}

try {
    // Get Leads DB
    $db = DB::get('users');

    // Get API Settings
    $settings = $db->getCollection('default_info')->search(array(
        '$eq' => array('agent' => 1)
    ))->fetch();

    // Auth User
    $authuser = Auth::get();

    // OAuth State
    $state = $_GET['state'];

    // Connect State
    $oauth = null;
    $oauth_token = null;
    $oauth_profile = null;
    switch ($state) {
        // Connect with Facebook
        case 'OAuth_Login_Facebook':
            $oauth          = new OAuth_Login_Facebook($settings['facebook_apikey'], $settings['facebook_secret']);
            $oauth_token    = 'oauth_facebook';
            $oauth_profile  = 'network_facebook';
            break;

        // Connect with Google
        case 'OAuth_Login_Google':
            $oauth          = new OAuth_Login_Google($settings['google_apikey'], $settings['google_secret']);
            $oauth_token    = 'oauth_google';
            $oauth_profile  = 'network_google';
            break;

        // Connect with Microsoft
        case 'OAuth_Login_Microsoft':
            $oauth          = new OAuth_Login_Microsoft($settings['microsoft_apikey'], $settings['microsoft_secret']);
            $oauth_token    = 'oauth_microsoft';
            $oauth_profile  = 'network_microsoft';
            break;

        // Connect with Yahoo
        case 'OAuth_Login_Yahoo':
            $oauth          = new OAuth_Login_Yahoo($settings['yahoo_apikey'], $settings['yahoo_secret']);
            $oauth_token    = 'oauth_yahoo';
            $oauth_profile  = 'network_yahoo';
            break;

        // Connect with Twitter
        case 'OAuth_Login_Twitter':
            $oauth          = new OAuth_Login_Twitter($settings['twitter_apikey'], $settings['twitter_secret']);
            $oauth_token    = 'oauth_twitter';
            $oauth_profile  = 'network_twitter';
            break;

        // Connect with LinkedIn
        case 'OAuth_Login_LinkedIn':
            $oauth          = new OAuth_Login_LinkedIn($settings['linkedin_apikey'], $settings['linkedin_secret']);
            $oauth_token    = 'oauth_linkedin';
            $oauth_profile  = 'network_linkedin';
            break;

        // Unknown OAuth Type, throw Exception
        default:
            throw new Exception('Unknown OAuth Type');
            break;
    }

    // OAuth Token
    $token = false;

    // OAuth 2.0+
    if ($oauth->getVersion() >= 2.0) {
        // OAuth Code Received
        if (isset($_GET['code'])) {
            // Verify Token
            $response = $oauth->verifyToken($_GET['code'], true);
            $token = $response['access_token'];
        }

    // OAuth 1.0
    } else {
        // Verify Access Token
        if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
            // Verify Token
            $token = $oauth->verifyToken($_GET['oauth_token'], $_GET['oauth_verifier']);
        }
    }

    // Request made from the backend
    if (empty($social_connect) && !empty($authuser) && $authuser->isValid() && !empty($response)) {
        // Require Token, throw Exception
        if (empty($response['access_token'])) {
            throw new Exception('Missing Access Token');
        }

        // Calculate expiry time
        if (!empty($response['expires_in'])) {
            $response['expire_time'] = time() + $response['expires_in'];
        }

        // Encode response
        $response = json_encode($response);

        // Set user token
        $authuser->info($oauth_profile, $response);

        try {
            // Prepare UPDATE query
            $update = $db->prepare("UPDATE `agents` SET `"  . $oauth_profile . "` = :oauth WHERE `id` = :id;");

            // Update agents table
            $update->execute(array(
                'id'    => $authuser->info('id'),
                'oauth' => $response
            ));

            // Success
            $success[] = "The OAuth data was successfully inserted";

        // Database error
        } catch (PDOException $e) {
            $errors[] = "There was an error inserting the OAuth data";
        }

        // OAuth error
        if (!empty($success)) {
            echo '<script> window.opener.location.reload(); window.close(); </script>';
            exit;
        }
    } else {
        // Require Token, throw Exception
        if (empty($token)) {
            throw new Exception('Missing Access Token');
        }

        // Get Profile Data
        $profile = $oauth->getProfile($token);

        // Find Lead by OAuth Token
        $lead = $db->fetch("SELECT * FROM `users` WHERE `" . $oauth_token . "` = " . $db->quote($token) . " LIMIT 1;");

        // Not Found, Fallback and Try by Email Address
        if (empty($lead) && !empty($profile['email'])) {
            $lead = $db->fetch("SELECT * FROM `users` WHERE `email` = " . $db->quote($profile['email']) . " LIMIT 1;");
        }

        // Existing Lead Found
        if (!empty($lead)) {
            // Set User Id (Log In)
            $user->setUserId($lead['id']);

            // Clear Data
            $user->saveInfo('connected', false);

            // Setup Lead
            $lead = new Backend_Lead($lead);

            // Store Network Details
            $lead[$oauth_profile] = json_encode($profile);

            // Store OAuth Token
            $lead[$oauth_token] = $token;

            // Save Lead
            $lead->save($db);

            // Success
            $success[] = 'You have successfully been logged in.';

            // Log Event: Lead Logged In via Third-Party
            $event = new History_Event_Action_Connected(array(
                'ip' => $_SERVER['REMOTE_ADDR'],
                'name' => $oauth->getName(),
                'data' => $profile,
                'type' => 'login'
            ), array(
                new History_User_Lead($lead['id'])
            ));

            // Save to DB
            $event->save($db);

        // No Lead Found
        } else {
            // Store Data in $_SESSION, Accessed in idx/inc/php/pages/connect.php
            $user->saveInfo('connected', array(
                'provider' => $oauth->getName(),    // Readable Provider Name
                'store_profile' => $oauth_profile,  // DB Field to Store Profile Data
                'store_token' => $oauth_token,      // DB Field to Store OAuth Token
                'profile' => $profile,              // Profile Data
                'token' => $token                   // OAuth Token
            ));

            // Success
            $success[] = 'You have successfully connected via <strong>' . $oauth->getName() . '</strong>.';
        }
    }

    // Success
    if (!empty($success)) {
        // Success Message
        $user->saveInfo('success', $success);

        // Logged in success
        if (!empty($lead)) {
            echo '<script>
				if (window.opener) {
					window.opener.$(window.opener).trigger(\'oauth-login\', [window]);
				} else {
					window.location.href = \'/\';
				}
			</script>';
            exit;
        }

        // OAuth successful
        echo '<script>
			if (window.opener) {
				window.opener.$(window.opener).trigger(\'oauth-success\', [window]);
			} else {
				window.location.href = \'/idx/connect.html\';
			}
		</script>';
        exit;
    }

// Validation Error
} catch (Exception_ValidationError $e) {
    $errors[] = $e->getMessage();
    Log::error($e);

// Exception Caught
} catch (Exception $e) {
    $errors[] = 'An error has occurred. Please try again.';
    Log::error($e);
}

// Error Occurred
if (!empty($errors)) {
    // User Errors
    if (!empty($social_connect)) {
        $user->saveInfo('errors', $errors);
    }

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
