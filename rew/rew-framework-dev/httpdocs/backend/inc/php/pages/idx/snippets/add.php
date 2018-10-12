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
        'You do not have permission to add IDX snippets.'
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Select IDX
if (!empty($_REQUEST['feed'])) {
    Util_IDX::switchFeed($_REQUEST['feed']);
} else {
    if ($defaultFeed = $subdomain->getDefaultFeed()) {
        Util_IDX::switchFeed($defaultFeed);
    }
}

// IDX objects
$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Select IDX Defaults For Feed
$result   = mysql_query("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '" . Settings::getInstance()->IDX_FEED . "' LIMIT 1;");
$defaults = mysql_fetch_assoc($result);
if (empty($defaults)) {
    $result   = mysql_query("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '' LIMIT 1;");
    $defaults = mysql_fetch_assoc($result);
}

// Show Form
$show_form = true;

// Require Row
if (!empty($defaults)) {
    // Process Submit
    if (isset($_GET['submit'])) {
        // Remember Search Panels (Use next time snippet is added)
        $_SESSION['snippet-panels'] = !empty($_POST['panels']) && is_array($_POST['panels']) ? array_keys($_POST['panels']) : array();

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

        // Check Duplicate
        $query = "SELECT `name` FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql(true)
            . " AND `name` = '" . mysql_real_escape_string($_POST['snippet_id']) . "';";
        if ($result = mysql_query($query)) {
            $duplicate = mysql_fetch_array($result);
            if (!empty($duplicate)) {
                $errors[] = 'A snippet with this name already exists.';
            }
        }

        // Generate Snippet
        $code = serialize($_POST);

        // Check Errors
        if (empty($errors)) {
            // Build INSERT Query
            $query = "INSERT INTO `" . TABLE_SNIPPETS . "` SET "
               . $subdomain->getAssignSql()
               . "`name`  = '" . mysql_real_escape_string($_POST['snippet_id']) . "', "
               . "`code`  = '" . mysql_real_escape_string($code) . "', "
               . "`type`  = 'idx';";

            // Execute Query
            if (mysql_query($query)) {
                // Success
                $success[] = 'IDX Snippet has successfully been created.';

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                header('Location: ../edit/?id=' . $_POST['snippet_id'] . $subdomain->getPostLink(true));
                exit;
            } else {
                // Query Error
                $errors[] = 'IDX Snippet could not be created, please try again.';
            }
        }
    }

    // Search Options
    $options = array();

    // View Options
    $options['views'] = array_filter(array(
        array('value' => 'grid', 'title' => 'Thumbnails'),
        array('value' => 'detailed', 'title' => 'List'),
        (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) ? array('value' => 'map', 'title' => 'Map') : null)
    ));

    // Sort Options
    $options['sort'] = array(
        array('value' => 'DESC-ListingPrice', 'title' => 'Price, Highest First'),
        array('value' => 'ASC-ListingPrice',  'title' => 'Price, Lowest First')
    );

    // Page Limit
    $_POST['page_limit'] = $_POST['page_limit'] ?: $defaults['page_limit'] ?: false;
    if (empty($_POST['page_limit']) || $_POST['page_limit'] < 1) {
        $_POST['page_limit'] = 6;
    }

    // Price Range Links
    $_POST['price_ranges'] = ($_POST['price_ranges']) ? $_POST['price_ranges'] : 'false';

    if (Skin::hasFeature(Skin::HIDE_SEARCH_TAGS)) {
        // Hide Search Tags
        $_POST['hide_tags'] = isset($_POST['hide_tags']) ? $_POST['hide_tags'] : 'false';
    }

    // Default View
    $_POST['view'] = $_POST['view'] ?: $defaults['view'] ?: 'grid';

    // Order / Sort
    $_POST['sort_by'] = $_POST['sort_by'] ?: $defaults['sort_by'] ?: null;
    list($_POST['sort'], $_POST['order']) = explode('-', $_POST['sort_by']);

    // Default Criteria
    if (!empty($defaults['criteria'])) {
        $criteria = unserialize($defaults['criteria']);
    }

    // Posted Criteria
    if (!empty($code)) {
        $criteria = unserialize($code);
    }

    // Set $_REQUEST Criteria
    $criteria = is_array($criteria) ? $criteria : array();
    $_REQUEST = search_criteria($idx, $criteria);

    // Snippet Panels
    if (isset($_SESSION['snippet-panels']) && is_array($_SESSION['snippet-panels'])) {
        $builder_panels = [];
        foreach ($_SESSION['snippet-panels'] as $panel) {
            $builder_panels[$panel] = array('display' => 1);
        }
    } else {
        $builder_panels = unserialize($defaults['panels']);
        if (empty($builder_panels) || !is_array($builder_panels)) {
            $builder_panels = array(
                'city'  => array('display' => 1),
                'type'  => array('display' => 1),
                'price' => array('display' => 1),
                'rooms' => array('display' => 1)
            );
        }
    }

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
}

$mapLabels = [
    'open' => 'Open',
    'closed' => 'Closed'
];
$container = Container::getInstance();
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);
$mapLabels = $hooks->hook(Hooks::HOOK_BACKEND_IDX_MAP_LABEL)->run($mapLabels);
