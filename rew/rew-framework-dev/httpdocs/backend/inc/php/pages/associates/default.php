<?php

// Full Page
$body_class = 'full';

// Get Authorization
$associateAuth = new REW\Backend\Auth\AssociateAuth(Settings::getInstance());

// Authorized to Manage Associates
if (!$associateAuth->canViewAssociates($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view associates.')
    );
}

// Can Edit & Delete
$can_edit = $associateAuth->canManageAssociates($authuser);
$can_delete = $associateAuth->canDeleteAssociates($authuser);

// Success
$success = array();

// Errors
$errors = array();

// Associates
$associates = array();

$db = DB::get();

// Count Rows
$count = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `associates`;");

// Require Rows
if (!empty($count['total'])) {
    // SQL Limit
    $page_limit = 25;
    if ($count['total'] > $page_limit) {
        $limitvalue = (($_GET['p'] - 1) * $page_limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
    }

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

    // Query String
    list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
    parse_str($query, $query_string);

    // Sort Order
    $_GET['sort']  = ($_GET['sort'] === 'DESC') ? 'DESC' : 'ASC';
    $_GET['order'] = !empty($_GET['order']) ? $_GET['order'] : 'name';
    switch ($_GET['order']) {
        case 'logon':
            $sql_order = " ORDER BY `logon` " . $_GET['sort'];
            break;
        case 'name':
        default:
            $sql_order = " ORDER BY `last_name` " . $_GET['sort'] . ", `first_name` " . $_GET['sort'];
            break;
    }
    try {
        // Select Rows
        $query = "SELECT `a`.*"
            . ", CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`"
            . ", UNIX_TIMESTAMP(`auth`.`last_logon`) AS `logon`"
            . " FROM `associates` `a`"
            . " LEFT JOIN `" . Auth::$table . "` `auth` ON `a`.`auth` = `auth`.`id`"
            . $sql_order
            . $sql_limit
            . ";";


        // Build Collection
        foreach ($db->fetchAll($query) as $associate) {
            // Get Photo
            $image = $db->fetch("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'associate' AND `row` = :id ORDER BY `order` ASC LIMIT 1;", ["id" => $associate['id']]);
            if (!empty($image['file'])) {
                $associate['image'] = $image['file'];
            }

            // Add to Associates
            $associates[] = $associate;
        }

    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while loading available ISAs.');
    }
}

// Sort Direction
$url_sort = (($_GET['sort'] == 'DESC') ? 'ASC' : 'DESC');
