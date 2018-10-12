<?php

// Get Database
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Agent ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Agent
$agent = Backend_Agent::load($_GET['id']);

// Editing self
$edit_self = $_GET['id'] === $authuser->info('id');

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to view agent history
if (!$agentAuth->canManageAgent() && !$edit_self) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit agent notifications.')
    );
}

// Get Leads Authorization
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);

// Is Sharktank Enabled?
$sharktank = Container::getInstance()->get(\REW\Backend\Controller\Leads\SharktankController::class);
$isSharktankEnabled = $sharktank->isSharktankEnabled();

try {
    // Agent Notifications
    $notifications = $agent->getNotifications();

    // Process Submit
    // - Check incoming notifications. If provided, check for valid 'CC Email'.
    // - Check outgoing notifications. If enabled, require a 'Notify Email' to be set.
    if (isset($_GET['submit'])) {
        // Notification Settings
        if (!empty($_POST['settings'])) {
            // Load Notification Settings
            $notifications->loadSettings($_POST['settings'], $errors);
        }

        // Check Errors
        if (empty($errors)) {
            // Serialize using JSON
            $data = json_encode($notifications->getSettings());

            // Save Settings to Agent
            try {
                $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET `notifications` = :notifications WHERE `id` = :id;")
                ->execute([
                    'notifications' => $data,
                    'id' => $agent['id']
                ]);
                $success[] = __('Notification settings have successfully been saved.');

            // Query Error
            } catch (PDOException $e) {
                $errors[] = __('An error occurred while saving notification settings.');
            }
        }
    }

    // Get Notification Settings
    $settings = $notifications->getSettings();

    // Default to Agent's Email Address
    $settings['email'] = isset($settings['email']) ? $settings['email'] : $agent['email'];

// Gotta Catch 'Em All!
} catch (Exception $e) {
    $errors[] = __('An error has occurred while loading agent notification settings.');
    Log::error($e);
}
