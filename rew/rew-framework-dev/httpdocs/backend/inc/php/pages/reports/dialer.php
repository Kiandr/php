<?php

// Get Authorization Managers
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

// Authorized to view all dialer reports
if (!$reportsAuth->canViewDialerReport($authuser)) {
    // Authorized to view own dialer reports
    if (!$reportsAuth->canViewOwnDialerReport($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to view dialer reports')
        );
    } else {
        // Restrict to Single Agent
        $sql_agent = " WHERE `id` = '" . $authuser->info('id') . "'";
    }
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
    array('title' => __('All Time'),        'value' => 'all', 'selected' => empty($sql_date)),
    array('title' => __('Last 7 Days'),     'value' => date('Y-m-d', strtotime('-7 days'))  . '|' . date('Y-m-d')),
    array('title' => __('Last 14 Days'),    'value' => date('Y-m-d', strtotime('-14 days')) . '|' . date('Y-m-d')),
    array('title' => __('Last 30 Days'),    'value' => date('Y-m-d', strtotime('-30 days')) . '|' . date('Y-m-d')),
    array('title' => __('Last 60 Days'),    'value' => date('Y-m-d', strtotime('-60 days')) . '|' . date('Y-m-d')),
    array('title' => __('Custom Range'),    'value' => 'custom', 'selected' => !empty($sql_date))
);

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

            // Display amount and types of calls placed by each agent
                $stats = $db->fetch("SELECT COUNT(`u`.`type`) AS `total`, COUNT(IF(`subtype` = 'Contact', 1, NULL)) AS `contacted`, COUNT(IF(`subtype` = 'Attempt', 1, NULL)) AS `attempted`, COUNT(IF(`subtype` = 'Voicemail', 1, NULL)) AS `voicemail`, COUNT(IF(`subtype` = 'Invalid', 1, NULL)) AS `invalid` "
                        . "FROM `history_users` `u` LEFT JOIN `history_events` `e` ON `u`.`event` = `e`.`id` "
                        . "WHERE `u`.`user` = " . $db->quote($agent['id']) . " AND `e`.`type` = 'Phone' AND `u`.`type` = 'Agent'"
                        . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'e`.`timestamp') : ''));

            // Response Data
            $agent = array_merge($agent, $stats);

            // Update Record
            $agents[$k] = $agent;
        }
    }

    //Add ISAs to call report if ISA module is enabled
    if (Settings::getInstance()->MODULES['REW_ISA_MODULE']) {
        //Select ISAs
        $associates = $db->fetchAll("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates`" . (!empty($sql_agent) ? $sql_agent : "") . ";");

        if (!empty($associates)) {
            // Generate Report
            foreach ($associates as $k => $associate) {
                // URL to ISA Summary
                $associate['url'] = Settings::getInstance()->URLS['URL_BACKEND'] . 'associates/associate/summary/?id=' . $associate['id'];

                // Display amount and types of calls placed by each ISA
                $stats = $db->fetch("SELECT COUNT(`u`.`type`) AS `total`, COUNT(IF(`subtype` = 'Contact', 1, NULL)) AS `contacted`, COUNT(IF(`subtype` = 'Attempt', 1, NULL)) AS `attempted`, COUNT(IF(`subtype` = 'Voicemail', 1, NULL)) AS `voicemail`, COUNT(IF(`subtype` = 'Invalid', 1, NULL)) AS `invalid` "
                        . "FROM `history_users` `u` LEFT JOIN `history_events` `e` ON `u`.`event` = `e`.`id` "
                        . "WHERE `u`.`user` = " . $db->quote($associate['id']) . " AND `e`.`type` = 'Phone' AND `u`.`type` = 'Associate'"
                        . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'e`.`timestamp') : ''));

                // Response Data
                $associate = array_merge($associate, $stats);

                // Update Record
                $associates[$k] = $associate;
            }

            //Merge into agents array for display on report page
            $agents = array_merge($agents, $associates);
        }
    }

    //Sort agents by number of calls
    if (!empty($agents)) {
        $_GET['sort'] = ($_GET['sort'] == 'ASC') ? 'ASC' : 'DESC';
        $_GET['order'] = !empty($_GET['order']) ? $_GET['order'] : 'total';
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
