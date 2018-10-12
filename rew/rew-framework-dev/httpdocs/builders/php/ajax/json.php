<?php

namespace BDX;

// Require Composer Vendor Auto loader
require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

// BDX dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/builders/classes/Settings.php';

// Close session
@session_write_close();

try {
    // BDX Database
    $db_settings = Settings::getInstance()->DATABASES['bdx'];
    $db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);

    if (Settings::getInstance()->FRAMEWORK) {
        // CMS Database
        $db_settings = \Settings::getInstance()->DATABASES['default'];
        $db = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
    }
    
    // JSON Data
    $json = array();
    
    // Process ID
    $_POST['pid'] = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
    if (!empty($_POST['pid'])) {
        $json['pid'] = $_POST['pid'];
    }
    
    // Auto Complete
    if (!empty($_REQUEST['q']) && !empty($_REQUEST['state']) && isset($_REQUEST['search'])) {
        // Result Limit
        $limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : false;
        
        // Available Options
        $json['options'] = array();
                
        // Set Field
        switch ($_REQUEST['search']) {
            case 'search[Community]':
                $field = 'SubdivisionName';
                break;
            case 'search[ZipCode]':
                $field = 'Zip';
                break;
            case 'search[Builder]':
                $field = 'BrandName';
                break;
        }
        
        // Prepare Query
        if (!empty($field)) {
            $query = $db_bdx->prepare("SELECT DISTINCT `" . $field . "` AS `value`, `" . $field . "` AS `title` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "`"
                    . " WHERE `" . $field . "` IS NOT NULL AND `" . $field . "` != '' AND `" . $field . "` LIKE :q AND `State` = :state"
                    . " GROUP BY `" . $field . "`"
                    . " ORDER BY `" . $field . "`"
                    . " LIMIT 50");
            
            // Execute Query
            $query->execute(array(':q' => $_REQUEST['q'] . '%', ':state' => $_REQUEST['state']));
            
            // Get Options
            while ($option = $query->fetch()) {
                // Format Title
                $option['title'] = ucwords(strtolower($option['title']));
                
                // Add to Collection
                $json['options'][] = $option;
            
                // Stop after $limit Reached
                if (!empty($limit) && count($json['options']) >= $limit) {
                    break;
                }
            }
        }
    }
    
    // Get City List
    if (isset($_REQUEST['cities']) && !empty($_REQUEST['state'])) {
        // Set state
        $state = $_REQUEST['state'];
        
        // Get BDX Settings
        $bdx_settings = Settings::getBDXSettings($db);
        
        // Escape cities
        $quoted_cities = array();
        if (!empty($bdx_settings['states'][$state]['cities']) && is_array($bdx_settings['states'][$state]['cities'])) {
            foreach ($bdx_settings['states'][$state]['cities'] as $city) {
                $quoted_cities[] = $db->quote($city);
            }
        }
        
        // Available Options
        $json['options'] = array();
        
        // Prepare Query
        $query = $db_bdx->prepare("SELECT DISTINCT `City` AS `value`, `City` AS `title` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "`"
                . " WHERE `State` = :state"
                . (!empty($quoted_cities) ? " AND `City` IN (" . implode(',', $quoted_cities) . ")" : "")
                . " ORDER BY `City`");
        
        // Execute Query
        $query->execute(array(':state' => $state));
        
        // Get Options
        while ($option = $query->fetch()) {
            // Format Title
            $option['title'] = ucwords(strtolower($option['title']));
            
            // Add to Collection
            $json['options'][] = $option;
        }
    }
    
// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
    // Send as JSON
    header('Content-Type: application/json');
    
    // Return JSON Response
    die(json_encode($json));
