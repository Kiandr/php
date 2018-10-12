<?php

// App DB
$db = DB::get();

// Get Authorization
$lendersAuth = new REW\Backend\Auth\LendersAuth(Settings::getInstance());

// Can Edit & Delete
$can_edit = $lendersAuth->canManageLenders($authuser);
$can_delete = $lendersAuth->canDeleteLenders($authuser);

// Require permission to edit all lenders
if (!$lendersAuth->canViewLenders($authuser)) {
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

// Fetch Row
try {
$lender = $db->fetch("SELECT `l`.*, `tz`.`name` AS `timezone` FROM `lenders` `l` LEFT JOIN `" . LM_TABLE_TIMEZONES . "` `tz` ON `l`.`timezone` = `tz`.`id` WHERE `l`.`id` = :id;", ["id" => $_GET['id']]);
} catch (PDOException $e) {}

/* Throw Missing ID Exception */
if (empty($lender)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLenderException();
}

// Lender Photo
try {
    $image = $db->fetch("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'lender' AND `row` = :row ORDER BY `order` ASC LIMIT 1;", ["row" => $lender['id']]);
    if (!empty($image['file'])) {
        $lender['image'] = $image['file'];
    }
} catch (PDOException $e) {}
