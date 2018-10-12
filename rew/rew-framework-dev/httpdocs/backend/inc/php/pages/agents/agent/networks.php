<?php

// Get Database
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Get CMS db
$db = DB::get();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$agent = Backend_Agent::load($_GET['id']);

// Get auth
$authuser = Auth::get();

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Get Reports Authorization
$reportsAuth = new REW\Backend\Auth\ReportsAuth($settings);

// Not authorized to view analytics
if (!$reportsAuth->canViewAnalyticsReport($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('There are no agent networks to edit.')
    );
}

// Not authorized to view agent history
if (!$agentAuth->canManageAgent()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit agent networks.')
    );
}

// URL Back to this Page
$url = $settings->SETTINGS['URL_RAW'] . substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) . '?id=' . $agent['id'];

// Connected Networks
$networks = array();

// Google
$networks['google'] = array(
    'disabled' => false,
    'title' => 'Google Analytics',
    'data'   => !empty($agent['network_google_service_account']) ? json_decode($agent['network_google_service_account'], true) : false,
    'account'  => null,
    'callback' => $url . '&verify=google',
    'disconnect' => $url . '&disconnect=google'
);

// Connect Networks
foreach ($networks as $key => $network) {
    // Action Status
    $connect = isset($_POST['connect']) && ($_POST['connect'] == $key);
    $disconnect = isset($_GET['disconnect']) && ($_GET['disconnect'] == $key);

    // Each Network is Different..
    switch ($key) {

        /**
         * Google Analytics
         */
        case 'google':
            // Connect Account
            if (!empty($connect)) {
                if (!empty($_FILES['service_account_key']['tmp_name'])) {
                    $query = sprintf(
                        "UPDATE `%s` SET `network_google_service_account` = :network WHERE `id` = %s;",
                        LM_TABLE_AGENTS,
                        (int)$agent['id']
                    );
                    $serviceAccountKey = file_get_contents($_FILES['service_account_key']['tmp_name']);
                    if (!is_array($decodedApiKey = json_decode($serviceAccountKey, true))) {
                        $errors[] = 'Failed to read service account key.';
                    }

                    // Connect to Google (to test key)
                    if (!$errors) {
                        try {
                            /** @var Google_Service_Analytics $analyticsService */
                            $analyticsService = \Container::getInstance()->get(Google_Service_Analytics::class);
                            $client = $analyticsService->getClient();
                            $client->setAuthConfig($decodedApiKey);
                            $client->setScopes([
                                Google_Service_Analytics::ANALYTICS_READONLY
                            ]);
                            $profiles = $analyticsService->management_profiles->listManagementProfiles('~all', '~all');
                            if ($profiles) {
                                $network['account'] = array(
                                    'name' => $profiles[0]->name
                                );
                            }
                            $success[] = 'Successfully authenticated using the provided service account key.';
                        } catch (\Exception $exception) {
                            $errors[] = 'Failed to authenticate using the provided service account key.';
                        }
                    }

                    // Save to DB
                    if (!$errors) {
                        $stmt = $db->prepare($query);
                        if (!$stmt->execute(['network' => $serviceAccountKey])) {
                            // Query Error
                            $errors[] = 'Error occurred while connecting to ' . $network['title'] . '.';
                        }
                    }
                } else {
                    $errors[] = 'Could not load the service account key';
                }

            // Disconnect Account
            } else if (!empty($network['data']) && !empty($disconnect)) {
                // Remove from DB
                try {
                    try {
                        // Clear Access Token
                        $query = sprintf("UPDATE `%s` SET `network_google_service_account` = '' WHERE `id` = :id;", LM_TABLE_AGENTS);
                        $stmt = $db->prepare($query);
                        $stmt->execute(['id' => $agent['id']]);// Redirect URL
                        header('Location: ' . $url);
                        exit;
                    } catch (PDOException $e) {
                        // Query Error
                        $errors[] = __('Error occurred while disconnected from %s.', $network['title']);
                    }

                    // Success Notification
                    $success[] = 'Disconnected from ' . $network['title'] . '.';
                    $authuser->setNotices($success, $errors);

                    // Redirect URL
                    header('Location: ' . $url);
                    exit;

                // Exception Caught
                } catch (Exception $e) {
                    $errors[] = 'Error occurred while disconnecting from ' . $network['title'] . '.';
                }

            // Connected
            } else if (!empty($network['data'])) {
                /** @var Google_Service_Analytics $analyticsService */
                $analyticsService = \Container::getInstance()->get(Google_Service_Analytics::class);
                $client = $analyticsService->getClient();
                $client->setAuthConfig($network['data']);
                $client->setScopes([
                    Google_Service_Analytics::ANALYTICS_READONLY
                ]);
                try {
                    $profiles = $analyticsService->management_profiles->listManagementProfiles('~all', '~all');
                    if ($profiles) {
                        $network['account'] = array(
                            'name' => $profiles[0]->name
                        );
                    }

                // Exception Caught
                } catch (Exception $e) {
                    $errors[] = 'Error occurred while connecting to ' . $network['title'] . '.';
                }
            }

            break;
    }

    // Update Row
    $networks[$key] = $network;
}
