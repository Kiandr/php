<?php

// Get Authorization Manager
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

// Authorized to Export All Leads
if (!$reportsAuth->canViewActionPlanReports($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view action plan reports.')
    );
}

// DB connection
$db = DB::get();

// Default Date Filter (Last 30 Days)
if (!isset($_GET['ajax']) && empty($_GET['start']) && empty($_GET['end'])) {
    $start = strtotime(date('Y-m-d', strtotime('-30 days')));
    $end = strtotime(date('Y-m-d'));
}

// Generate stats via AJAX
if (isset($_GET['ajax'])) {
    $where  = array();
    $params = array();

    // Filter by Date
    $start = false;
    $end = false;
    if (!empty($_GET['start']) && !empty($_GET['end'])) {
        $start = strtotime($_GET['start']);
        $end = strtotime($_GET['end']);
        if (!empty($start) && !empty($end)) {
            $where[] = "`ut`.`timestamp_scheduled` BETWEEN '" . date('Y-m-d 00:00:00', $start) . "' AND '" . date('Y-m-d 23:59:59', $end) . "'";
        }
    }

    // Statistics for specified plan
    if (!empty($_GET['actionplan_id'])) {
        $where[] = "`ut`.`actionplan_id` = :actionplan_id";
        $params['actionplan_id'] = $_GET['actionplan_id'];
    }

    // Statistics for a specific task type
    if (!empty($_GET['type'])) {
        $where[] = "`ut`.`type` = :type";
        $params['type'] = $_GET['type'];
    }

    $where = !empty($where) ? ' AND ' . implode(' AND ', $where) : '';

    // Running count of totals for each task status
    $total_pending   = 0;
    $total_completed = 0;
    $total_dismissed   = 0;
    $total_expired   = 0;

    $agent_where = array();
    $agent_params = array();

    // Filter List of Agents
    if (!empty($_GET['agent_id'])) {
        $agent_where[] = "`id` = :agent_id";
        $agent_params[':agent_id'] = array('data' => $_GET['agent_id'], 'type' => PDO::PARAM_INT);
    }

    // Get All Agents/Lenders/Associates
    $agents = array();
    $result = $db->prepare("SELECT `id`, 'Agent' AS `type`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` "
        // [Morgan Temp Request] Remove Lenders/Associates as Selectable Performers
// 			. "UNION ALL SELECT `id`, 'Lender' AS `type`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `lenders` "
// 			. "UNION ALL SELECT `id`, 'Associate' AS `type`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates` "
        . (!empty($agent_where) ? ' WHERE ' . implode(' AND ', $agent_where) : '')
        . ";");
    foreach ($agent_params as $field => $param) {
        $result->bindParam($field, $param['data'], $param['type']);
    }
    $result->execute();

    // Go through each agent and build stats
    while ($row = $result->fetch()) {
        $row['pending']   = 0;
        $row['dismissed']   = 0;
        $row['expired']   = 0;
        $row['completed'] = 0;

        if ($row['type'] == 'Agent') {
            $performer_pending = " AND `u`.`agent` = '" . $row['id'] . "'";
            $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/';
        } else if ($row['type'] == 'Lender') {
            $performer_pending = " AND `u`.`lender` = '" . $row['id'] . "'";
            $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'lenders/lender/';
        } else if ($row['type'] == 'Associate') {
            $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'associates/associate/';
        }

        // Links to agent/lender/associate summary page and task list
        $row['url'] = $performer_url . 'summary/?id=' . $row['id'];
        $row['tasks_url'] = $performer_url . 'tasks/?id=' . $row['id'];

        // Get pending Tasks
        $pending = $db->fetch("SELECT COUNT(`ut`.`task_id`) AS `total` "
            . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
            . " JOIN `users` `u` ON `ut`.`user_id` = `u`.`id` "
            . " WHERE `ut`.`status` = 'Pending' "
            . " AND `ut`.`performer` = '" . $row['type'] . "'"
            . $performer_pending
            . $where
            . ";", $params);
        $row['pending'] = $pending['total'];
        $total_pending += $pending['total'];

        // Get Completed Tasks
        $completed = $db->fetch("SELECT COUNT(`ut`.`task_id`) AS `total` "
            . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
            . " WHERE `status` = 'Completed' "
            . " AND `performer` = '" . $row['type'] . "' "
            . " AND `performer_id` = '" . $row['id'] . "'"
            . $where
            . ";", $params);
        $row['completed'] = $completed['total'];
        $total_completed += $completed['total'];

        // Get Dismissed Tasks
        $dismissed = $db->fetch("SELECT COUNT(`ut`.`task_id`) AS `total` "
            . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
            . " WHERE `status` = 'Dismissed' "
            . " AND `performer` = '" . $row['type'] . "' "
            . " AND `performer_id` = '" . $row['id'] . "'"
            . $where
            . ";", $params);
        $row['dismissed'] = $dismissed['total'];
        $total_dismissed += $dismissed['total'];

        // Get Expired Tasks
        $expired = $db->fetch("SELECT COUNT(`ut`.`task_id`) AS `total` "
            . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
            . " WHERE `status` = 'Expired' "
            . " AND `performer` = '" . $row['type'] . "' "
            . " AND `performer_id` = '" . $row['id'] . "'"
            . $where
            . ";", $params);
        $row['expired'] = $expired['total'];
        $total_expired += $expired['total'];

        $agents[] = $row;
    }

    // Get tasks autocompleted by System
    if (empty($_GET['agent_id'])) {
        $system = $db->fetch("SELECT COUNT(`task_id`) AS `total` "
            . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
            . " WHERE `status` = 'Completed' "
            . " AND `performer` = 'System'"
            . $where
            . ";", $params);
    }

    // Pie Chart Data
    $pie = array(
        'type' => 'pie',
        'name' => 'Count',
        'data' => array(
            array(__('Completed'), (int) $total_completed),
            array(__('Dismissed'),   (int) $total_dismissed),
            array(__('Expired'),   (int) $total_expired),
            array(__('Pending'),   (int) $total_pending)
        )
    );

    // Build agent/system/totals table entries
    ob_start();

    foreach ($agents as $agent) {
        echo '<tr>';
        echo '<td><h4 class="item_content_title"><a href="' . $agent['url'] . '">' . $agent['name'] . '</a></h4></td>';
        echo '<td style="border-left: 1px solid #ccc;">' . $agent['type'] . '</td>';
        echo '<td style="border-left: 1px solid #ccc;"><a href="' . $agent['tasks_url'] . '&status=Pending">' . $agent['pending'] . ($agent['type'] == __('Associate') ? ' <sup style="vertical-align: super;">&#8224;</sup>' : '') . '<a></td>';
        echo '<td style="border-left: 1px solid #ccc;"><a href="' . $agent['tasks_url'] . '&status=Completed">' . $agent['completed'] . '<a></td>';
        echo '<td style="border-left: 1px solid #ccc;"><a href="' . $agent['tasks_url'] . '&status=Dismissed">' . $agent['dismissed'] . '<a></td>';
        echo '<td style="border-left: 1px solid #ccc;"><a href="' . $agent['tasks_url'] . '&status=Expired">' . $agent['expired'] . ($agent['type'] == __('Associate') ? ' <sup style="vertical-align: super;">&#8224;</sup>' : '') . '<a></td>';
        echo '</tr>';
    }
    // Tasks completed by system
    if (!empty($system)) {
        echo '<tr>';
        echo '<td><h4 class="item_content_title">' . __('Automated') . '</h4></td>';
        echo '<td style="border-left: 1px solid #ccc;">' . __('System') . '</td>';
        echo '<td style="border-left: 1px solid #ccc;">-</td>';
        echo '<td style="border-left: 1px solid #ccc;">' . $system['total'] . '</td>';
        echo '<td style="border-left: 1px solid #ccc;">-</td>';
        echo '<td style="border-left: 1px solid #ccc;">-</td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td>' . __('Total') . '</td>';
    echo '<td style="border-left: 1px solid #ccc;"></td>';
    echo '<td style="border-left: 1px solid #ccc;"><b>' . $total_pending . '</b></td>';
    echo '<td style="border-left: 1px solid #ccc;"><b>' . $total_completed . '</b></td>';
    echo '<td style="border-left: 1px solid #ccc;"><b>' . $total_dismissed . '</b></td>';
    echo '<td style="border-left: 1px solid #ccc;"><b>' . $total_expired . '</b></td>';
    echo '</tr>';

    // Send as JSON
    header('Content-type: application/json');

    // Return JSON
    die(json_encode(array(
        'html' => ob_get_clean(),
        'pie'  => $pie
    )));
}

// Action plan filter options
$action_plans = array();
try {
    $result = $db->query("SELECT `id`, `name` FROM `" . TABLE_ACTIONPLANS . "`;");
    while ($row = $result->fetch()) {
        $action_plans[] = $row;
    }
} catch (Exception $e) {
}

// Load Agents for Search Field
$all_agents = array();
try {
    $result = $db->query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents`");
    while ($row = $result->fetch()) {
        $all_agents[] = $row;
    }
} catch (Exception $e) {
}

// Task type filter options
$type_options = task_type_options();
