<?php
$settings = Settings::getInstance();

// Get Authorization Managers
$idxAuth = new REW\Backend\Auth\IDXAuth($settings);

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

// Delete Row
if (!empty($_GET['delete'])) {
    // Select Row
    $result = mysql_query("SELECT * FROM `" . TABLE_IDX_SEARCHES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "';");
    $search = mysql_fetch_assoc($result);

    // Require Row
    if (!empty($search)) {
        // Build DELETE Query
        $query = "DELETE FROM `" . TABLE_IDX_SEARCHES . "` WHERE `id` = '" . $search['id'] . "';";

        // Execute Qurey
        if (mysql_query($query)) {
            // Success
            $success[] = __('The selected IDX Search has successfully been deleted.');
        } else {
            // Query Error
            $errors[] = __('The selected IDX Search could not be deleted.');
        }
    } else {
        // Error
        $errors[] = __('The selected IDX Search could not be found.');
    }
}

// IDX Searches
$searches = array();

// Select Rows
$query = "SELECT * FROM `" . TABLE_IDX_SEARCHES . "` WHERE `idx` = '" . mysql_real_escape_string($settings->IDX_FEED) . "' ORDER BY `title`;";
$result = mysql_query($query);

// Build Collection
while ($search = mysql_fetch_assoc($result)) {
    // Add to Collection
    $searches[] = $search;
}
