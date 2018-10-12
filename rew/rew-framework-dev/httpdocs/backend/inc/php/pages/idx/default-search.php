<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\LogInterface;

// Get Authorization Managers
$idxAuth = new REW\Backend\Auth\IDXAuth(Settings::getInstance());

// Authorized to manage directories
if (!$idxAuth->canManageSearch($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage idx searches')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Select IDX
if (!empty($_REQUEST['feed'])) {
    Util_IDX::switchFeed($_REQUEST['feed']);
}

// IDX objects
$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Select IDX Defaults
$result   = mysql_query("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '" . Settings::getInstance()->IDX_FEED . "' LIMIT 1;");
$defaults = mysql_fetch_assoc($result);
if (empty($defaults)) {
    $result   = mysql_query("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '' LIMIT 1;");
    $defaults = mysql_fetch_assoc($result);
}

/* Throw Missing ID Exception */
if (empty($defaults)) {
    throw new \REW\Backend\Exceptions\MissingSettings\MissingIdxSearchException();
}

// Process Submit
if (isset($_GET['submit'])) {
    // Page Limit Between 1 and 48
    $page_limit = $_POST['page_limit'];
    if ($page_limit < 1 || $page_limit > 48) {
        $errors[] = __('Page Limit must be a number between 1 and 48.');
    }

    // Check Errors
    if (empty($errors)) {
        // Serialize Criteria
        $criteria = serialize($_POST);

        // Build INSERT Query
        $query = "INSERT INTO `" . TABLE_IDX_DEFAULTS . "` SET "
               . "`idx`        = '" . mysql_real_escape_string($_POST['feed']) . "',"
               . "`view`       = '" . mysql_real_escape_string($_POST['view']) . "',"
               . "`sort_by`    = '" . mysql_real_escape_string($_POST['sort_by']) . "',"
               . "`page_limit` = '" . mysql_real_escape_string($_POST['page_limit']) . "',"
               . "`split`      = '" . mysql_real_escape_string($_POST['split']) . "',"
               . "`panels`     = '" . mysql_real_escape_string(serialize($_POST['panels'])) . "',"
               . "`criteria`   = '" . mysql_real_escape_string($criteria) . "',"
               . "`timestamp_created` = NOW()"
               . " ON DUPLICATE KEY UPDATE "
               . "`view`       = '" . mysql_real_escape_string($_POST['view']) . "',"
               . "`sort_by`    = '" . mysql_real_escape_string($_POST['sort_by']) . "',"
               . "`page_limit` = '" . mysql_real_escape_string($_POST['page_limit']) . "',"
               . "`split`      = '" . mysql_real_escape_string($_POST['split']) . "',"
               . "`panels`     = '" . mysql_real_escape_string(serialize($_POST['panels'])) . "',"
               . "`criteria`   = '" . mysql_real_escape_string($criteria) . "',"
               . "`timestamp_updated` = NOW();";

        // Execute Query
        if (mysql_query($query)) {
            // Success
            $success[] = __('Search Defaults have successfully been updated.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?success' . (!empty($_REQUEST['feed']) ? '&feed=' . urlencode($_REQUEST['feed']) : ''));
            exit;

        // Query Error
        } else {
            $errors[] = __('Search Defaults could not be saved, please try again.');
        }
    }
}

// Default Criteria
if (!empty($defaults['criteria'])) {
    $criteria = unserialize($defaults['criteria']);
}

// Default Page Limit
$defaults['page_limit'] = !empty($defaults['page_limit']) && ($defaults['page_limit'] > 0) ? $defaults['page_limit'] : 12;

// Default View
$defaults['view'] = isset($defaults['view']) ? $defaults['view'] : 'grid';

// Default Split (LEC-2013 Only)
$defaults['split'] = Skin::getDirectory() === 'lec-2013' ? (!empty($defaults['split']) ? $defaults['split'] : -1) : null;

// Set $_REQUEST Criteria
$criteria = is_array($criteria) ? $criteria : array();
$_REQUEST = search_criteria($idx, $criteria);

// Build and Set DriveTime Polygon
if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
    $container = \Container::getInstance();
    $log = $container->make(LogInterface::class);
    $drive_time = $container->make(DriveTime::class);
    try {
        $drive_time->modifyServerMapRequests(
            $_REQUEST['dt_address'],
            $_REQUEST['dt_direction'],
            $_REQUEST['dt_travel_duration'],
            $_REQUEST['dt_arrival_time'],
            $_REQUEST['place_zoom'],
            $_REQUEST['place_lat'],
            $_REQUEST['place_lng']
        );
    } catch (Exception $e) {
        $log->error($e->getMessage());
    }
}

// IDX Builder
$builder = new IDX_Builder(array(
    'map'       => true,
    'panels'    => unserialize($defaults['panels']),
    'split'     => $defaults['split'],
    'toggle'    => true
));

$mapLabels = [
    'open' => 'Open',
    'closed' => 'Closed'
];
$container = Container::getInstance();
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);
$mapLabels = $hooks->hook(Hooks::HOOK_BACKEND_IDX_MAP_LABEL)->run($mapLabels);
