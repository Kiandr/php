<?php

// Require Composer Vendor Auto loader
require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

// Include BDX Settings
require_once $_SERVER['DOCUMENT_ROOT'] . '/builders/classes/Settings.php';

// Get Authorization Managers
$bdxAuth = new REW\Backend\Auth\BDXAuth(Settings::getInstance(), BDX\Settings::getInstance());

// Require permission to edit bdx settings
if (!$bdxAuth->canManageSettings($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to edit bdx settigns.'
    );
}

// Agent Collection
$agents = array();

// Lender Collection
$lenders = array();
    
// State/Subdivision Collection
$states = array();
    
// Success Collection
$success = array();
    
// Error Collection
$errors = array();

// CMS Database
$db = DB::get('cms');
    
// Select BDX Defaults
$sth = $db->query("SELECT `settings` FROM `bdx_settings` LIMIT 1;");
$settings = $sth->fetch();
$settings = unserialize($settings['settings']);
    
// Process Submit
if (isset($_GET['submit'])) {
        // Ensure selected cities are included in the settings for each state
    if (!empty($settings['states']) && is_array($settings['states'])) {
        foreach ($settings['states'] as $key => $val) {
            if (!empty($val['cities'])) {
                $_POST['states'][$key]['cities'] = $val['cities'];
            }
        }
    }
        
        // Serialize Settings
        $new_settings = serialize($_POST);
        
        // Build INSERT Query
        $sql = "INSERT INTO `bdx_settings` SET "
            . "`id` 				= :id,"
            . "`settings`   		= :settings,"
            . "`timestamp_created` 	= NOW()"
            . " ON DUPLICATE KEY UPDATE "
            . "`settings`   		= :settings,"
            . "`timestamp_updated` 	= NOW();";
        $sth = $db->prepare($sql);
        
        // Execute Query
    if ($sth->execute(array(':id' => 1, ':settings' => $new_settings))) {
        // Success
        $success[] = 'BDX Settings have successfully been updated.';
        
        // Save Notices
        $authuser->setNotices($success, $errors);
        
        // Redirect Back to Form
        header('Location: ?success');
        exit;
        
        // Query Error
    } else {
        $errors[] = 'Search Defaults could not be saved, please try again.';
    }
}
    
// Decode Settings
if (!empty($settings['settings'])) {
        $settings['settings'] = unserialize($settings['settings']);
}
    
try {
        // BDX Database
        $db_settings = \BDX\Settings::getInstance()->DATABASES['bdx'];
        $db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
    
    if (\BDX\Settings::getInstance()->STATES) {
        // User Friendly State List with corresponding BDX values
        $statesUSA = \BDX\State::getStates();
                        
        // Initialize States array with states provided in settings file
        if (is_array(\BDX\Settings::getInstance()->STATES)) {
            foreach (\BDX\Settings::getInstance()->STATES as $state) {
                $states[$statesUSA[$state]] = array(
                    'title' => $statesUSA[$state],
                    'value' => $state
                );
            }
                
            // Default to all states
        } else {
            foreach ($statesUSA as $key => $val) {
                $states[$val] = array(
                    'title' => $val,
                    'value' => $key
                );
            }
        }
    }
        
        // Get Agent List
        $sth = $db->query("SELECT `id`, `first_name`, `last_name` FROM `" . TABLE_AGENTS . "` ORDER BY `last_name`, `first_name`");
    while ($agent = $sth->fetch()) {
        $agents[] = $agent;
    }
        
        // Get Lender List
    if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
        $sth = $db->query("SELECT `id`, `first_name`, `last_name` FROM `lenders` ORDER BY `last_name`, `first_name`");
        while ($lender = $sth->fetch()) {
            $lenders[] = $lender;
        }
    }
                
// Error Occurred
} catch (Exception $e) {
        Log::error($e);
}
