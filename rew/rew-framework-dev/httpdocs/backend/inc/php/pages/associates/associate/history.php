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

// Get Authorization
$associateAuth = new REW\Backend\Auth\Associates\AssociateAuth($settings, $authuser, $associate);

// Not authorized to view associate history
if (!$associateAuth->canViewHistory()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view this associates history.')
    );
}

// Check edit/delete associate permissions
if ($associateAuth->canEditAssociate()) {
    $can_edit = true;
    $can_delete = true;
}

// Associate History
$history = array();

try {
    // Count History
    $count = $db->fetch("SELECT COUNT(`he`.`id`) AS `total` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` `he` LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `hu` ON `he`.`id` = `hu`.`event` WHERE `hu`.`user` = :user AND `hu`.`type` = 'Associate';", ["user" => $associate['id']]);

    // Search Limit
    $limit = 250;
    if ($count['total'] > $limit) {
        $limitvalue = (($_GET['p'] - 1) * $limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit = " LIMIT " . $limitvalue . ", " . $limit;
    }

    // Query String
    list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
    parse_str($query, $query_string);

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $limit, $query_string);

    // Order Data
    $sql_sort = ($_GET['sort'] == 'ASC') ? 'ASC' : 'DESC';
    $sql_order = " ORDER BY `he`.`timestamp` " . $sql_sort . ", `he`.`id` " . $sql_sort;

    try {
        // Select Associate History from Database
        foreach ($db->fetchAll("SELECT `he`.`id` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` `he` LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `hu` ON `he`.`id` = `hu`.`event` WHERE `hu`.`user` = :user AND `hu`.`type` = 'Associate'" . $sql_order . $sql_limit . ";", ["user" => $associate['id']]) as $event) {
            // Load History Event
            $event = History_Event::load($event['id']);

            // Add to Collection, Use Date as Key
            $history[date('d-m-Y', $event->getTimestamp())][] = $event;
        }
    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while attempting to load ISA\'s history.');
    }

    // Query Error
} catch (PDOException $e) {
    $errors[] = __('An error occurred while attempting to load ISA\'s history.');
}
