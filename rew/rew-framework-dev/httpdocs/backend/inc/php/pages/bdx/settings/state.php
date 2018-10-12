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

// Success Collection
$success = array();
    
// Error Collection
$errors = array();
    
// Cities collection
$cities = array();
    
// Selected state
$state = $_GET['id'];
    
try {
        // CMS Database
        $db = DB::get('cms');
        
        // Select BDX Defaults
        $sth = $db->query("SELECT `settings` FROM `bdx_settings` LIMIT 1;");
        $settings = $sth->fetch();
        $settings = unserialize($settings['settings']);
            
        // Process Submit
    if (isset($_GET['submit'])) {
        if (!empty($_POST['state'])) {
            // Set Cities
            $settings['states'][$_POST['state']]['cities'] = $_POST['cities'];
                
            // Set enabled flag
            $settings['states'][$_POST['state']]['enabled'] = 'true';
                
            // Serialize new settings
            $new_settings = serialize($settings);
                        
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
                    header('Location: ?success&id=' . $_POST['state']);
                    exit;
                // Query Error
            } else {
                $errors[] = 'Search Defaults could not be saved, please try again.';
            }
        }
    }
        
        // BDX Database
        $db_settings = \BDX\Settings::getInstance()->DATABASES['bdx'];
        $db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
                
        // Get cities within each state
        $query = $db_bdx->prepare("SELECT DISTINCT `City`"
                . " FROM  `" . \BDX\Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "`"
                . " WHERE `State` = :state"
                . " ORDER BY `City`");
        
        // Execute
        $query->execute(array(':state' => $state));
        
        // Fetch All cities
    while ($city = $query->fetch()) {
        $cities[] = $city['City'];
    }
// Error Occurred
} catch (Exception $e) {
        Log::error($e);
}
