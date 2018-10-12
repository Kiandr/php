<?php

// App DB
$db = DB::get();

// Get Authorization
$lendersAuth = new REW\Backend\Auth\LendersAuth(Settings::getInstance());

// Can Edit & Delete
$can_edit = $lendersAuth->canManageLenders($authuser);
$can_delete = $lendersAuth->canDeleteLenders($authuser);

// Require permission to edit all lenders
if (!$lendersAuth->canManageLenders($authuser)) {
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

// Select Row
try {
    $lender = $db->fetch("SELECT * FROM `lenders` WHERE `id` = :id;", ["id" => $_GET['id']]);
} catch (PDOException $e) {}

/* Throw Missing ID Exception */
if (empty($lender)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLenderException();
}

// Lender History
$history = array();

// Count History
try {
    $count = $db->fetch("SELECT COUNT(`he`.`id`) AS `total` FROM `"
        . Settings::getInstance()->TABLES['HISTORY_EVENTS']
        . "` `he` LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS']
        . "` `hu` ON `he`.`id` = `hu`.`event` WHERE `hu`.`user` = :user AND `hu`.`type` = 'Lender';",
        [
            "user" => $lender['id']
        ]
    );

    // Search Limit
    $limit = 250;
    if ($count['total'] > $limit) {
        $limitvalue = (($_GET['p'] - 1) * $limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . $limit;
    }

    // Query String
    list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
    parse_str($query, $query_string);

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $limit, $query_string);

    // Order Data
    $sql_sort  = ($_GET['sort'] == 'ASC') ? 'ASC' : 'DESC';
    $sql_order = " ORDER BY `he`.`timestamp` " . $sql_sort . ", `he`.`id` " . $sql_sort;

    // Select Lender History from Database
    try {
        foreach($db->fetchAll("SELECT `he`.`id` FROM `"
            . Settings::getInstance()->TABLES['HISTORY_EVENTS']
            . "` `he` LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS']
            . "` `hu` ON `he`.`id` = `hu`.`event` WHERE `hu`.`user` = :user AND `hu`.`type` = 'Lender'"
            . $sql_order . $sql_limit . ";", [
                "user" =>$lender['id']
        ]) as  $event) {
            // Load History Event
            $event = History_Event::load($event['id']);

            // Add to Collection, Use Date as Key
            $history[date('d-m-Y', $event->getTimestamp())][] = $event;
        }

    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while attempting to load lender\'s history.');
    }

// Query Error
} catch (PDOException $e) {
    $errors[] = __('An error occurred while attempting to load lender\'s history.');
}
