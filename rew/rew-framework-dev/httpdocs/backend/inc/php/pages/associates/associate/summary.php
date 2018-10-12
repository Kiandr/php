<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Associate ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Load Associate
$associate = Backend_Associate::load($_GET['id']);

// Throw Missing Associates Exception
if (empty($associate)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAssociateException();
}

// Require Associate
$timezoneQuery = $db->prepare("SELECT `name` FROM `" . LM_TABLE_TIMEZONES . "` WHERE `id` = :id;");
$timezoneQuery->execute(['id' => $associate['timezone']]);
$timezone = $timezoneQuery->fetchColumn();

// Get Authorization
$associateAuth = new REW\Backend\Auth\Associates\AssociateAuth($settings, $authuser, $associate);

// Not authorized to view associate
if (!$associateAuth->canViewAssociate() && !$associateAuth->isSelf()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view this associate.')
    );
}

// Can Edit
$can_edit = $associateAuth->canEditAssociate();

// Can Delete
$can_delete = $associateAuth->canEditAssociate();

// Cannot Email Self
$can_email = $associateAuth->canEmailAssociate();
