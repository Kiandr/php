<?php

// Backwards-Compatable: Change Format::number to 'number_format', Change .on to .bind
// Require `history_data` Patch for Optimal Performance: r3843.sql, r3973.sql

// Get Authorization Managers
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$reportsAuth->canViewResponseReport($authuser)) {
    if (!$reportsAuth->canViewOwnResponseReports($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to view response reports')
        );
    }
    // Restrict to Single Agent
    $sql_agent = " WHERE `id` = '" . $authuser->info('id') . "'";
}

// User PDO
$db = DB::get('users');

// Full Size
$body_class = 'full';

// Query String
list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
parse_str($query, $query_string);
unset($query_string['ajax']);

// Default Filter (Last 30 Days)
if (!isset($_GET['ajax']) && empty($_GET['start']) && empty($_GET['end'])) {
    $_GET['start'] = date('Y-m-d', strtotime('-30 days'));
    $_GET['end'] = date('Y-m-d');
}

// Filter by Date
$sql_date = false;
$start = false;
$end = false;
if (!empty($_GET['start']) && !empty($_GET['end'])) {
    $start = strtotime($_GET['start']);
    $end = strtotime($_GET['end']);
    if (!empty($start) && !empty($end)) {
        $sql_date = "`%s` BETWEEN '" . date('Y-m-d 00:00:00', $start) . "' AND '" . date('Y-m-d 23:59:59', $end) . "'";
    }
}

// Date Filters
$ranges = array(
    array('title' => 'All Time',        'value' => 'all', 'selected' => empty($sql_date)),
    array('title' => 'Last 7 Days',     'value' => date('Y-m-d', strtotime('-7 days'))  . '|' . date('Y-m-d')),
    array('title' => 'Last 14 Days',    'value' => date('Y-m-d', strtotime('-14 days')) . '|' . date('Y-m-d')),
    array('title' => 'Last 30 Days',    'value' => date('Y-m-d', strtotime('-30 days')) . '|' . date('Y-m-d')),
    array('title' => 'Last 60 Days',    'value' => date('Y-m-d', strtotime('-60 days')) . '|' . date('Y-m-d')),
    array('title' => 'Custom Range',    'value' => 'custom', 'selected' => !empty($sql_date))
);

// Lead Statuses
$statuses = array('accepted', 'pending', 'closed', 'rejected');

// Show Form
$show_form = true;

// Hide Report
$report = false;

// Agent Stats
if (isset($_GET['ajax'])) {
    // Show Report
    $report = true;

    // Hide Form
    $show_form = false;

    // Select Agents
    $agents = $db->fetchAll("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents`" . (!empty($sql_agent) ? $sql_agent : "") . ";");
    if (!empty($agents)) {
        // Generate Report
        foreach ($agents as $k => $agent) {
            // URL to Agent Summary
            $agent['url'] = Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=' . $agent['id'];

            // Leads by Status
            foreach ($statuses as $status) {
                $query = "SELECT COUNT(`id`) AS `total` FROM `users` WHERE `agent` = " . $db->quote($agent['id']) . " AND `status` = " . $db->quote($status) . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'timestamp_assigned') : '') . ";";
                if ($status == 'rejected') {
                    $query = "SELECT COUNT(`user_id`) AS `total` FROM `users_rejected` WHERE `agent_id` = " . $db->quote($agent['id']) . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'timestamp') : '') . ";";
                }
                $count = $db->fetch($query);
                if (!empty($count['total'])) {
                    $agent[$status] = $count['total'];
                    if ($status != 'rejected') {
                        $agent['leads'] += $count['total'];
                    }
                }
            }

            // Require Assigned Leads to Generate Report
            if (!empty($agent['leads'])) {
                // Agent Response Times (from Calls/Emails sent since Assigned to Agent)
                $stats = $db->fetch("SELECT MIN(`time`) AS `min`, MAX(`time`) AS `max`, AVG(`time`) AS `avg`, COUNT(`type`) AS `total`, COUNT(IF(`type` = 'Email', 1, NULL)) AS `emails`, COUNT(IF(`type` = 'Phone', 1, NULL)) AS `calls` FROM ("
                    . "SELECT TIMESTAMPDIFF(SECOND, `u`.`timestamp_assigned`, `e`.`timestamp`) AS `time`, `e`.`type`"
                    . " FROM `users` `u`"
                    . " LEFT JOIN `history_events` `e` ON `e`.`timestamp` > `u`.`timestamp_assigned` AND (`e`.`type` = 'Phone' OR (`e`.`type` = 'Email' AND `e`.`subtype` NOT IN ('AutoResponder', 'Campaign', 'Listings', 'Reminder')))"
                    . " JOIN `history_users` `l` ON `e`.`id` = `l`.`event` AND `l`.`user` = `u`.`id`"
                    . " JOIN `history_users` `a` ON `e`.`id` = `a`.`event` AND `a`.`user` = `u`.`agent`"
                    . " WHERE `u`.`agent` = " . $db->quote($agent['id']) . " AND `u`.`status` IN ('accepted', 'pending', 'closed')"
                    . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'u`.`timestamp_assigned') : '')
                    . " GROUP BY `u`.`id`"
                    . " ORDER BY `e`.`timestamp` ASC"
                . ") `t`;");

                // Response Data
                $agent = array_merge($agent, $stats);

                // Response Rate
                if (!empty($agent['total'])) {
                    $agent['rate'] = ($agent['total'] / $agent['leads']) * 100;
                    $agent['rate_emails'] = ($agent['emails'] / $agent['total']) * 100;
                    $agent['rate_calls']  = ($agent['calls'] / $agent['total']) * 100;
                }
            }

            // Update Record
            $agents[$k] = $agent;
        }

        // Sort Agents by Rate
        $_GET['sort'] = ($_GET['sort'] == 'ASC') ? 'ASC' : 'DESC';
        $_GET['order'] = !empty($_GET['order']) ? $_GET['order'] : 'leads';
        usort($agents, function ($a, $b) {
            $k = $_GET['order'];
            // Empty to Bottom
            if (empty($a[$k])) {
                return 2;
            } elseif (empty($b[$k])) {
                return -2;
            }
            // Compare Values
            if ($a[$k] == $b[$k]) {
                return 0;
            } else if ($_GET['sort'] == 'ASC') {
                return ($a[$k] < $b[$k]) ? -1 : 1;
            } else {
                return ($a[$k] > $b[$k]) ? -1 : 1;
            }
        });
    }
}
