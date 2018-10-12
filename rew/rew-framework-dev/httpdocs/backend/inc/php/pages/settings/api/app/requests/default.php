<?php

// Get Authorization Managers
$settingsAuth = new REW\Backend\Auth\SettingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$settingsAuth->canManageApi($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage the API')
    );
}

// Application ID
$_GET['id'] = !empty($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

// Get Selected Row
$result  = mysql_query("SELECT * FROM `api_applications` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "';");
$application = mysql_fetch_array($result);

// Require application
if (empty($application)) {
    return;
}

// Clear entries
if (isset($_GET['clear'])) {
    $sql = "DELETE FROM `api_requests` WHERE `app_id` = '" . $application['id'] . "';";
    if (mysql_query($sql)) {
        // Reset counts
        mysql_query("UPDATE `api_applications` SET `num_requests_ok` = 0, `num_requests_error` = 0 WHERE `id` = '" . $application['id'] . "';");

        // Success
        $success[] = __('All logged requests for this application have been cleared.');

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect
        header('Location: ?id=' . $application['id']);
        exit;
    }
}

// Sort Order
$_GET['sort'] = !empty($_GET['sort']) ? mysql_real_escape_string($_GET['sort']) : (!empty($_SESSION['api_requests_sort']) ? $_SESSION['api_requests_sort'] : 'DESC');

// Default Order
$_GET['order'] = !empty($_GET['order']) ? mysql_real_escape_string($_GET['order']) : (!empty($_SESSION['api_requests_order']) ? $_SESSION['api_requests_order'] : 'created');

// Remember sorting
$_SESSION['api_requests_sort'] = $_GET['sort'];
$_SESSION['api_requests_order'] = $_GET['order'];

// Sort by Column
switch ($_GET['order']) {
    case 'uri':
        $sql_order = " ORDER BY `method`, `uri` " . $_GET['sort'];
        break;
    case 'status':
        $sql_order = " ORDER BY `status` " . $_GET['sort'];
        break;
    case 'user_agent':
        $sql_order = " ORDER BY `user_agent` " . $_GET['sort'];
        break;
    case 'ip':
        $sql_order = " ORDER BY `ip` " . $_GET['sort'];
        break;
    case 'duration':
        $sql_order = " ORDER BY `duration` " . $_GET['sort'];
        break;
    case 'created':
        $sql_order = " ORDER BY `timestamp` " . $_GET['sort'];
        break;
    default:
        $sql_order = " ORDER BY `timestamp` " . $_GET['sort'];
        break;
}

// Get count
$sql_count = "SELECT COUNT(`id`) AS 'total' FROM `api_requests` WHERE `app_id` = '" . $application['id'] . "';";
$count_requests = mysql_fetch_assoc(mysql_query($sql_count));

// Query String
list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
parse_str($query, $query_string);

// Sort Direction
$url_sort = (($_GET['sort'] == 'DESC') ? 'ASC' : 'DESC');

// Page Limit
$page_limit = 40;

// Search Limit
if ($count_requests['total'] > $page_limit) {
    $limitvalue = (($_GET['p'] - 1) * $page_limit);
    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
    $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
}

// Pagination
$pagination = generate_pagination($count_requests['total'], $_GET['p'], $page_limit, $query_string);

// Application requests
$requests = array();

// Build query
$sql = "SELECT *, "
        . "UNIX_TIMESTAMP(`timestamp`) AS `timestamp_created` "
            . "FROM `api_requests` "
        . "WHERE `app_id` = '" . $application['id'] . "' "
        . $sql_order
        . $sql_limit;

// Fetch results
if ($result_requests = mysql_query($sql)) {
    while ($request_row = mysql_fetch_assoc($result_requests)) {
        // Format values
        if ($request_row['duration'] >= 1) {
            $request_row['duration'] = number_format($request_row['duration'], 4) . ' sec';
        } else {
            $request_row['duration'] = number_format($request_row['duration'] * 1000, 2) . ' ms';
        }

        // Censor internal IP
        if (strpos($request_row['ip'], '192.') === 0 || strpos($request_row['ip'], '10.') === 0) {
            $request_row['ip'] = 'REW Internal';
        }

        // Add to collection
        $requests[] = $request_row;
    }
} else {
    // Query error
    $errors[] = __('Failed to get application requests: %s', mysql_error());
}
