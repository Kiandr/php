<?php

// App DB
$db = DB::get();

// Get Authorization
$lendersAuth = new REW\Backend\Auth\Lender\LenderActionPlansAuth(Settings::getInstance());

// Can Edit & Delete
$can_edit = $lendersAuth->canManageLenders($authuser);
$can_delete = $lendersAuth->canDeleteLenders($authuser);

// Require permission to edit all lenders
if (!$lendersAuth->canManageActionPlans($authuser)) {
    // Require permission to edit self
    if (!$lendersAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit lenders.')
        );
    } else {
        // Row ID
        $_GET['id'] = $authuser->info('id');
    }
} else {
    // Row ID
    $_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
}

// Success
$success = array();

// Errors
$errors = array();

// Require Agent
try {
    $lender = $db->fetch("SELECT * FROM `lenders` WHERE `id` = :id;", ["id" => $_GET['id']]);
} catch (PDOException $e) {};

/* Throw Missing ID Exception */
if (empty($lender)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLenderException();
}

$task_list = new Module('task-list', array(
    'path'    => Settings::getInstance()->DIRS['BACKEND'] . '/inc/modules/task-list/',
    'user_id' => $lender['id'],
    'mode'    => 'Lender'
));
