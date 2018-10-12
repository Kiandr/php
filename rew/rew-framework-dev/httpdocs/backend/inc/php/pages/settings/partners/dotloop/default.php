<?php

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageDotloop($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage Dotloop integrations')
    );
}

// DB Instance
$db = DB::get();

// Partner instance
$api = new Partner_DotLoop(Backend_Agent::load($authuser->info('id')), $db);

// Success
$success = [];

// Errors
$errors = [];

// Setup mode
if (isset($_GET['setup']) && !empty($_GET['code'])) {
    // Request + Test API Access Token
    $access_tokens = $api->requestAccessTokens($_GET['code']);
    if (!empty($access_tokens)) {
        // Get Active Account Info
        $api->setAccessToken($access_tokens['access_token']);
        $account_info = $api->getAccountInfo();
        if (!empty($account_info['id'])) {
            // Merge Changes Into Current Partners
            $partners = array_merge($authuser->info('partners'), [
                'dotloop' => [
                    'account_id' => $account_info['id'],
                    'access_token' => $access_tokens['access_token'],
                    'refresh_token' => $access_tokens['refresh_token'],
                    'token_updated' => date('Y-m-d h:i:s', time())
                ],
            ]);

            // Build query
            try {
                $query = $db->prepare("UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;");
                if ($query->execute([
                    'partners' => json_encode($partners),
                    'id' => $authuser->info('id'),
                ])) {
                    $success[] = __('Your DotLoop integration has been successfully activated.');
                }
            } catch (Exception $e) {
                $errors[] = __('Failed to update DotLoop integration settings.');
                Log::error($e->getMessage());
            }
            $authuser->setNotices($success, $errors);

            // Redirect
            if (!empty($success)) {
                if (!empty($_GET['setup'])) {
                    header(sprintf('Location: %s', Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $_GET['setup']));
                } else {
                    header('Location: ?');
                }
                exit;
            }
        } else {
            $errors[] = __('Failed to retrieve DotLoop Account Info');
        }
    } else {
        $errors[] = __('Failed to retrieve an API Access Token.');
    }
}

// API integration status
$logins_valid = false;

// Test API Access Token Validity
if ($api->validateAPIAccess()) {
    // Logins are Valid
    $logins_valid = true;

    // Overview mode
    if (!isset($_GET['setup'])) {
        // Form submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check for Errors
            if (empty($errors)) {
                // Update Specific Partner Values
                $partners = $authuser->info('partners');
                try {
                    $query = $db->prepare("UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;");
                    if ($query->execute([
                        'partners' => json_encode($partners),
                        'id' => $authuser->info('id'),
                    ])) {
                        $success[] = __('Your changes have successfully been saved.');
                    }
                } catch (Exception $e) {
                    $errors[] = __('Failed to update DotLoop settings.');
                    Log::error($e->getMessage());
                }
                $authuser->setNotices($success, $errors);

                // Redirect
                if (!empty($success)) {
                    header('Location: ?');
                    exit;
                }
            }
        }
    }
}
