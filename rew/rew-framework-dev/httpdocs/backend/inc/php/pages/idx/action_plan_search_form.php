<?php

// Make Sure an IDX is Active
if (empty(Settings::getInstance()->IDX_FEED)) {
    echo __('Failed to load IDX Settings. Please try again.');
    exit;
}

// Load DB Object
$db = DB::get();

// Make Sure Task Exists as Requested
$task = $db->fetch("SELECT * FROM `users_tasks` WHERE `user_id` = :user AND `task_id` = :task AND `type` = 'Search' AND `status` = 'Pending' LIMIT 1;", array(
    'user' => $_GET['lead_id'],
    'task' => $_GET['post_task']
));
if (empty($task)) {
    echo __('Failed to load task details. Please try again.');
    exit;
}

// Set Feed
$_GET['feed'] = (!empty($_REQUEST['feed'])) ? $_REQUEST['feed'] : Settings::getInstance()->IDX_FEED;

// Select IDX
if (!empty($_GET['feed'])) {
    Util_IDX::switchFeed($_GET['feed']);
}

// IDX objects
$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Select IDX Defaults for Feed
$defaults = $db->fetch("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = :idx LIMIT 1;", array('idx' => Settings::getInstance()->IDX_FEED));
if (empty($defaults)) {
    $defaults = $db->fetch("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '' LIMIT 1;");
}

// Search Panels
$_POST['panels'] = unserialize($defaults['panels']);

// Default Split (LEC-2013 Only)
$_POST['split'] = Skin::getDirectory() === 'lec-2013' ? (!empty($defaults['split']) ? $defaults['split'] : -1) : null;

// Default Sort Order
$_POST['sort_by'] = isset($defaults['sort_by']) ? $defaults['sort_by'] : null;

// Set $_REQUEST Criteria
$_REQUEST = search_criteria($idx, $_REQUEST);

// Remove Map Panels
$panels = is_array($_POST['panels']) && !empty($_POST['panels']) ? $_POST['panels'] : IDX_Panel::defaults();
unset($panels['polygon'], $panels['radius'], $panels['bounds']);

// Show Map Panels if Maps are Enabled
if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    $panels = array_merge_recursive(array(
        'polygon' => array('display' => true),
        'radius'  => array('display' => true),
        'bounds'  => array('display' => true)
    ), $panels);
}

// IDX Builder
$builder = new IDX_Builder(array(
    'map' => true,
    'panels' => $panels,
    'split' => $_POST['split'],
    'mode' => 'snippet'
));
