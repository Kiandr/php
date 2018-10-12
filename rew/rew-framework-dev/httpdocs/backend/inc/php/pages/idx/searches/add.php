<?php

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

// Show Form
$show_form = true;

// Select IDX
if (!empty($_REQUEST['feed'])) {
    Util_IDX::switchFeed($_REQUEST['feed']);
}

// IDX objects
$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'title', 'title' => __('Search Title'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Page Limit Between 1 and 48
    $page_limit = $_POST['page_limit'];
    if ($page_limit < 1 || $page_limit > 48) {
        $errors[] = __('Page Limit must be a number between 1 and 48.');
    }

    // Check Errors
    if (empty($errors)) {
        // Generate Criteria
        $criteria = serialize($_POST);

        // Build INSERT Query
        $query = "INSERT INTO `" . TABLE_IDX_SEARCHES . "` SET "
               . "`title`             = '" . mysql_real_escape_string($_POST['title']) . "',"
               . "`view`              = '" . mysql_real_escape_string($_POST['view']) . "',"
               . "`sort_by`           = '" . mysql_real_escape_string($_POST['sort_by']) . "',"
               . "`page_limit`        = '" . mysql_real_escape_string($_POST['page_limit']) . "',"
               . "`split`             = '" . mysql_real_escape_string($_POST['split']) . "',"
               . "`panels`            = '" . mysql_real_escape_string(serialize($_POST['panels'])) . "',"
               . "`criteria`          = '" . mysql_real_escape_string($criteria) . "',"
               . "`idx`               = '" . mysql_real_escape_string($_POST['feed']) . "',"
               . "`timestamp_created` = NOW();";

        // Execute Query
        if (mysql_query($query)) {
            // Insert ID
            $insert_id = mysql_insert_id();

            // Success
            $success[] = __('Custom IDX Search has successfully been created.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../edit/?search_id=' . $insert_id);
            exit;
        } else {
            // Query Error
            $errors[] = __('IDX Search could not be created, please try again.');
        }
    }
} else {
    // Select IDX Defaults for Feed
    $result   = mysql_query("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '" . Settings::getInstance()->IDX_FEED . "' LIMIT 1;");
    $defaults = mysql_fetch_assoc($result);
    if (empty($defaults)) {
        $result   = mysql_query("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '' LIMIT 1;");
        $defaults = mysql_fetch_assoc($result);
    }

    // Search Panels
    $_POST['panels'] = unserialize($defaults['panels']);

    // Default Page Limit
    $_POST['page_limit'] = !empty($defaults['page_limit']) && ($defaults['page_limit'] > 0) ? $defaults['page_limit'] : 12;

    // Default Split (LEC-2013 Only)
    $_POST['split'] = Skin::getDirectory() === 'lec-2013' ? (!empty($defaults['split']) ? $defaults['split'] : -1) : null;

    // Default Sort Order
    $_POST['sort_by'] = isset($defaults['sort_by']) ? $defaults['sort_by'] : 12;

    // Default View
    $_POST['view'] = isset($defaults['view']) ? $defaults['view'] : 'grid';
}

// Set $_REQUEST Criteria
$_REQUEST = search_criteria($idx, $_REQUEST);

// IDX Builder
$builder = new IDX_Builder(array(
    'map' => true,
    'panels' => is_array($_POST['panels']) && !empty($_POST['panels']) ? $_POST['panels'] : IDX_Panel::defaults(),
    'split' => $_POST['split']
));
