<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\LogInterface;

$container = Container::getInstance();
$log = $container->get(LogInterface::class);

/**
 * Use this file to modify the $_REQUEST variable on search.php
 */

// Form Fields that accept Multiples
$multiples = array(
    'search_address',
    'search_location',
    'search_city',
    'search_type',
    'search_subtype',
    'search_subdivision',
    'search_zip',
    'search_mls',
    'school_district',
    'school_elementary',
    'school_middle',
    'school_high',
    'search_office',
    'search_agent',
    'search_status',
    'office_id',
    'agent_id'
);
foreach ($multiples as $multiple) {
    if (!empty($_REQUEST[$multiple]) && is_string($_REQUEST[$multiple])) {
        $_REQUEST[$multiple] = explode(',', $_REQUEST[$multiple]);
        $_REQUEST[$multiple] = Format::trim($_REQUEST[$multiple]);
    }
}

// Search Location
if (!empty($_REQUEST['search_location'])) {
    $_REQUEST['search_city'] = array();
}

// Search All Schools
if (!empty($_REQUEST['search_school'])) {
    $_REQUEST['school_district']   = $_REQUEST['search_school'];
    $_REQUEST['school_elementary'] = $_REQUEST['search_school'];
    $_REQUEST['school_middle']     = $_REQUEST['search_school'];
    $_REQUEST['school_high']       = $_REQUEST['search_school'];
}

// Build and Set DriveTime Polygon
if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
    $drive_time = $container->get(DriveTime::class);
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

// Polygon/Radius/Bounds in affect, remove other location criteria
if (!empty($_REQUEST['map']['polygon']) || !empty($_REQUEST['map']['radius']) || !empty($_REQUEST['map']['bounds'])) {
    $locations = array(
        'search_city',
        'search_subdivision',
        'search_location',
        'search_area',
        'search_county',
        'search_zip',
        'school_district',
        'school_elementary',
        'school_middle',
        'school_high'
    );
    foreach ($locations as $loc) {
        unset($_REQUEST[$loc]);
    }
}

// Most feeds use Y/NULL for openhouses... But not all.
$search_oh_input = reset(IDX_Panel::get('HasOpenHouse')->getInputs());
if (!empty($_REQUEST[$search_oh_input])) {
    if ($_REQUEST[$search_oh_input] == 'N') {
        $_REQUEST[$search_oh_input] = array('N', null);
    } else if (is_array($_REQUEST[$search_oh_input]) && in_array('N', $_REQUEST[$search_oh_input])) {
        $_REQUEST[$search_oh_input][] = null;
    }
}

// Run hook and modify $_REQUEST data
$_REQUEST = Hooks::hook(Hooks::HOOK_IDX_SEARCH_REQUEST)->run($_REQUEST);
