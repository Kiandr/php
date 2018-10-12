<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization
$lendersAuth = new REW\Backend\Auth\LendersAuth(Settings::getInstance());

// Authorized to Manage Lenders
if (!$lendersAuth->canViewLenders($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view associates.')
    );
}

// Can Edit & Delete
$can_edit = $lendersAuth->canManageLenders($authuser);
$can_delete = $lendersAuth->canDeleteLenders($authuser);

// Can Email
$can_email  = true;

// Success
$success = array();

// Errors
$errors = array();

// Lenders
$lenders = array();

// Count Rows
try {
    $count = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `lenders`;");
} catch (PDOException $e) {}

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
        case 'leads':
            $sql_order = " ORDER BY `leads` " . $_GET['sort'];
            break;
        case 'logon':
            $sql_order = " ORDER BY `logon` " . $_GET['sort'];
            break;
        case 'name':
        default:
            $sql_order = " ORDER BY `l`.`last_name` " . $_GET['sort'] . ", `l`.`first_name` " . $_GET['sort'];
            break;
    }

    try {
        // Select Rows
        foreach($db->fetchAll("SELECT `l`.*"
            . ", CONCAT(`l`.`first_name`, ' ', `l`.`last_name`) AS `name`"
            . ", COUNT(`u`.`id`) AS `leads`"
            . ", UNIX_TIMESTAMP(`auth`.`last_logon`) AS `logon`"
            . " FROM `lenders` `l` "
            . " LEFT JOIN `users` `u` ON `u`.`lender` = `l`.`id`"
            . " LEFT JOIN `" . Auth::$table . "` `auth` ON `l`.`auth` = `auth`.`id`"
            . " GROUP BY `l`.`id`"
            . $sql_order
            . $sql_limit
        . ";") as $lender) {
            // Lender Photo
            try {
                $image = $db->fetch("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'lender' AND `row` = :row ORDER BY `order` ASC LIMIT 1;", ["row" => $lender['id']]);
                if (!empty($image['file'])) {
                    $lender['image'] = $image['file'];
                }
            } catch (PDOException $e) {}

            // Add to Lenders
            $lenders[] = $lender;
        }

    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while loading available lenders.');
    }
}

// Sort Direction
$url_sort = (($_GET['sort'] == 'DESC') ? 'ASC' : 'DESC');
