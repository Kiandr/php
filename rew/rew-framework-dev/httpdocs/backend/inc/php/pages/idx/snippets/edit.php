<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\LogInterface;

// Get Database
$db = DB::get();

// Create Auth Classes
$settings = Settings::getInstance();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageIDXSnippets');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to edit IDX snippets.'
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Row ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Select Row
$result = mysql_query("SELECT * FROM `" . TABLE_SNIPPETS . "` WHERE "
        . $subdomain->getOwnerSql()
        . " AND `name` = '" . mysql_real_escape_string($_GET['id']) . "';");
$snippet = mysql_fetch_assoc($result);

// Throw Missing Page Exception
if (empty($snippet)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingSnippetException();
}

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'snippet_id',    'title' => 'Snippet Name');
    $required[] = array('value' => 'snippet_title', 'title' => 'Snippet Title');

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    // Page Limit Between 1 and 48
    $page_limit = $_POST['page_limit'];
    if ($page_limit < 1 || $page_limit > 48) {
        $errors[] = 'Page Limit must be a number between 1 and 48.';
    }

    // Check Duplicate Rows
    $query = "SELECT `name` FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql(true)
        . " AND `name` = '" . mysql_real_escape_string($_POST['snippet_id']) . "' AND `name` != '" . $snippet['name'] . "'";
    if ($result = mysql_query($query)) {
        $duplicate = mysql_fetch_array($result);
        if (!empty($duplicate)) {
            $errors[] = 'A snippet with this name already exists.';
        }
    }

    // Generate Snippet
    $code = serialize($_POST);

    // No Errors, Update Row
    if (empty($errors)) {
        // Build UPDATE Query
        $query = "UPDATE `" . TABLE_SNIPPETS . "` SET "
            . "`name`  = '" . mysql_real_escape_string($_POST['snippet_id']) . "', "
            . "`code`  = '" . mysql_real_escape_string($code) . "'"
            . " WHERE " . $subdomain->getOwnerSql(true) . " AND `name` = '" . $snippet['name'] . "';";

        // Execute Query
        if (mysql_query($query)) {
            // Success
            $success[] = 'IDX Snippet has successfully been updated.';

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?id=' . $_POST['snippet_id'] . $subdomain->getPostLink(true) . '&success');
            exit;
        } else {
            // Query Error
            $errors[] = 'IDX Snippet could not be saved, please try again.';
        }
    }
}

// Snippet Criteria
if (!empty($snippet['code'])) {
    $criteria = unserialize($snippet['code']);
}

// Set $_REQUEST Criteria
$idx = Util_IDX::getIdx();
$criteria = is_array($criteria) ? $criteria : array();
$_REQUEST = search_criteria($idx, $criteria);

// Page Limit
$criteria['page_limit'] = !empty($criteria['page_limit']) && ($criteria['page_limit'] > 0) ? $criteria['page_limit'] : 5;

// Price Range Links
$criteria['price_ranges'] = isset($criteria['price_ranges']) ? $criteria['price_ranges'] : 'false';

if (Skin::hasFeature(Skin::HIDE_SEARCH_TAGS)) {
    // Hide Search Tags
    $criteria['hide_tags'] = isset($criteria['hide_tags']) ? $criteria['hide_tags'] : 'false';
}

// Default View
$criteria['view'] = isset($criteria['view']) ? $criteria['view'] : 'grid';

// Select IDX
if (!empty($criteria['feed'])) {
    Util_IDX::switchFeed($criteria['feed']);
}

// IDX objects
$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Order / Sort
list($criteria['sort'], $criteria['order']) = explode('-', $criteria['sort_by']);
$criteria['order'] = isset($_REQUEST['order']) ? $_REQUEST['order'] : $criteria['order'];
$criteria['sort']  = isset($_REQUEST['sort'])  ? $_REQUEST['sort']  : $criteria['sort'];

// Snippet Panels (Legacy Hack)
$builder_panels = $criteria['panels'] ?: [];

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
    'map' => true,
    'mode' => 'snippet',
    'panels' => $builder_panels
));

// Pages using this Snippet
$snippet['pages'] = array();

// Check Homepage
$query = "SELECT `agent` FROM `" . TABLE_SETTINGS . "` WHERE " . $subdomain->getOwnerSql() . " AND `category_html` LIKE '%#" . mysql_real_escape_string($snippet['name']) . "#%';";
if ($result = mysql_query($query)) {
    $row = mysql_fetch_assoc($result);
    if (!empty($row)) {
        $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/' . $subdomain->getPostLink(), 'text' => 'Homepage');
    }
}

// Locate Pages
$query = "SELECT `page_id`, `link_name` FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql() . " AND `category_html` LIKE '%#" . mysql_real_escape_string($snippet['name']) . "#%' ORDER BY `link_name` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/pages/edit/?id=' . $row['page_id'] . $subdomain->getPostLink(true), 'text' => $row['link_name']);
    }
}

$mapLabels = [
    'open' => 'Open',
    'closed' => 'Closed'
];
$container = Container::getInstance();
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);
$mapLabels = $hooks->hook(Hooks::HOOK_BACKEND_IDX_MAP_LABEL)->run($mapLabels);
