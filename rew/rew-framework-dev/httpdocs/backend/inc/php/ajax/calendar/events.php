<?php

// Include Backend Configuration
include_once dirname(__FILE__) . '/../../../../common.inc.php';

// JSON Response
$json = array();

try {
    // Get Authorization
    $calendarAuth = new REW\Backend\Auth\CalendarAuth(Settings::getInstance());

    // Require permission to edit all associates
    $can_manage_all = $calendarAuth->canManageCalendars($authuser);
    if (!$can_manage_all) {
        // Require permission to edit self
        if (!$calendarAuth->canManageOwnCalendars($authuser)) {
            throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                'You do not have permission to manage calendars.'
            );
        } else {
            // Restrict to Only Agent's Data
            $sql_agent = "`%1\$s` = '" . $authuser->info('id') . "'";
        }
    } else if (!empty($_GET['personal'])) {
        // Filter Agent Query
        $sql_agent = "`%1\$s` = '" . $authuser->info('id') . "'";
    }

    $db = DB::get();

    // Get Event Types
    try {
        $results = $db->fetchAll('SELECT `id`, `title` FROM `' . TABLE_CALENDAR_TYPES . '`');

        foreach ($results as $result) {
            $event_types[$result['id']] = strtolower(str_replace(' ', '-', $result['title']));
        }
    } catch (PDOException $e) {
        $errors[] = 'Unable to load calendar event types';
        throw $e;
    }

    // Base Params Used For All Queries
    $params = [];

    // Calendar Events
    $json['events'] = array();

    // Date Range
    $_POST['end']     = isset($_POST['end'])     ? $_POST['end']     : $_SESSION['end'];
    $_POST['start']   = isset($_POST['start'])   ? $_POST['start']   : $_SESSION['start'];

    // Session Storage
    $_SESSION['start']   = $_POST['start'];
    $_SESSION['end']     = $_POST['end'];

    // Event Filters
    $_POST['filters'] = is_array($_POST['filters']) ? $_POST['filters'] : array();

    // Filer by Agent
    if ($can_manage_all) {
        $_POST['agent'] = isset($_POST['agent']) ? $_POST['agent'] : $_SESSION['agent'];
        if (!empty($_POST['agent'])) {
            $sql_agent = "`%1\$s` = :agent";

            $params[':agent'] = $_POST['agent'];
        }
        $_SESSION['agent'] = $_POST['agent'];
    }

    // Filter by Date
    if (!empty($_POST['start']) && !empty($_POST['end'])) {
        // Date Query
        $sql_date = "`%1\$s` BETWEEN :start AND :end";

        $params[':start'] = $_POST['start'] . ' 00:00:00';
        $params[':end']   = $_POST['end']   . ' 23:59:59';
    }

    // Filters
    if (!empty($_POST['filters']) && is_array($_POST['filters'])) {
        try {
            $filters = [];
            $where = '';

            foreach ($_POST['filters'] as $index => $filter) {
                $_POST['filters'][$index] = strtolower($_POST['filters'][$index]);

                $where .= '`title` = ? OR ';
            }
            $where = trim($where, 'OR ');

            // Filters
            $stmt = $db->prepare("SELECT `id` FROM `" . TABLE_CALENDAR_TYPES . "` WHERE " . $where);
            foreach ($_POST['filters'] as $k => $filter) {
                $stmt->bindValue(($k + 1), $filter, PDO::PARAM_STR);
            }
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $result) {
                $filters[] = $result['id'];
            }


            $filters = array_merge($filters, $_POST['filters']);
        } catch (PDOException $e) {
            $errors[] = 'Error Loading Event Types';
            Log::error($e);

            $filters = [];
        }
    }

    // Calendar Events
    try {
        // Build SELECT Query
        $query = "SELECT `t1`.`id`, `t1`.`agent`, `t1`.`title`, `t1`.`body`, `t2`.`id` AS `date_id`, UNIX_TIMESTAMP(`t2`.`start`) AS `start`, UNIX_TIMESTAMP(`t2`.`end`) AS `end`, `t2`.`all_day`, `t3`.`id` AS `type_id`, t3.`title` AS `type`, GROUP_CONCAT(`t4`.`user`) AS `agents`"
               . " FROM `" . TABLE_CALENDAR_EVENTS . "` `t1`"
               . " LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `t2` ON `t1`.`id` = `t2`.`event` "
               . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t3` ON `t1`.`type` = `t3`.`id`"
               . " LEFT JOIN `" . TABLE_CALENDAR_ATTENDEES . "` `t4` ON `t1`.`id` = `t4`.`event` AND `t4`.`type` = 'Agent'"
               . " WHERE 1"
               . (!empty($filters)
                    ? (in_array('uncategorized', $filters)
                        ? " AND (`t3`.`id` IS NULL OR `t3`.`id` IN ('" . implode("', '", $filters) . "'))"
                        : " AND `t3`.`id` IN ('" . implode("', '", $filters) . "')"
                    ) : '')
               . (!empty($sql_date)  ? ' AND (' . sprintf($sql_date, 't2`.`start') . ' OR ' . sprintf($sql_date, 't2`.`end') . ')' : '')
               . (!empty($sql_agent) ? ' AND (' . sprintf($sql_agent, 't1`.`agent') . ' OR ' . sprintf($sql_agent, 't4`.`user') . ')'  : '')
               . " GROUP BY `t1`.`id`"
               . " ORDER BY `t2`.`start` ASC;";
        $stmt = $db->prepare($query);

        // Execute Query
        $stmt->execute($params);

        // Agent Rows
        $agents = array();

        // Build Collection
        while ($event = $stmt->fetch()) {
            // Select Agent
            if (empty($agents[$event['agent']])) {
                $agents[$event['agent']] = $db->fetch("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :agent;", [':agent' => $event['agent']]);
            }
            $event['agent'] = $agents[$event['agent']];

            // Limit Times To Requested Range.  This Allows Multi-Day/Month Events To Display Correctly
            if ($_POST['view'] !== 'list') {
                if (!empty($_POST['start']) && strtotime($params[':start']) > $event['start']) {
                    $event['start'] = strtotime($params[':start']);
                }
                if (!empty($_POST['end']) && strtotime($params[':end']) < $event['end']) {
                    $event['end'] = strtotime($params[':end']);
                }
            }


            // Add to Collection
            $json['events'][] = array(
                'id'              => $event['id'],
                'date_id'         => $event['date_id'],
                'type'            => $event['type_id'],
                'agent'           => $event['agent'],
                'title'           => $event['title'],
                'body'            => $event['body'],
                'agents'          => !empty($event['agents']) ? explode(",", $event['agents']) : null,
                'start'           => $event['start'],
                'end'             => $event['end'],
                'formatted_start' => date('Y-m-d\TH:i:s', $event['start']),
                'formatted_end'   => date('Y-m-d\TH:i:s', $event['end']),
                'allDay'          => $event['all_day'] === 'true' ? true : false,
                'shared'          => !empty($event['agents']),
                'editable'        => ($can_manage_all || $authuser->info('id') == $event['agent']['id']),
                'className'       => (!empty($event['type_id']) ? $event_types[$event['type_id']] : 'uncategorized')
            );
        }

    // Query Error
    } catch (PDOException $e) {
        $errors[] = 'Error Loading Calendar Events';
        Log::error($e);
    }

    // New Leads
    if (in_array('new leads', $_POST['filters'])) {
        try {
            $query = "SELECT COUNT(DISTINCT `u`.`id`) AS `total`, UNIX_TIMESTAMP(`u`.`timestamp`) AS `date` FROM `" . LM_TABLE_LEADS . "` `u`"
                   . " WHERE 1"
                   . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'u`.`timestamp') : '')
                   . (!empty($sql_agent) ? ' AND ' . sprintf($sql_agent, 'u`.`agent') : '')
                   . " GROUP BY DATE(`u`.`timestamp`)"
                   . " ORDER BY `date` ASC;";

            $stmt = $db->prepare($query);

            $stmt->execute($params);

            while ($row = $stmt->fetch()) {
                // Add Event
                $json['events'][] = array(
                    'title'           => ($row['total'] == 1) ? $row['total'] . ' New Lead' : $row['total'] . ' New Leads',
                    'start'           => $row['date'],
                    'end'             => $row['date'],
                    'formatted_start' => date('Y-m-d\TH:i:s', $row['date']),
                    'formatted_end'   => date('Y-m-d\TH:i:s', $row['date']),
                    'allDay'          => true,
                    'editable'        => false,
                    'className'       => 'new-leads'
                );
            }
        } catch (PDOException $e) {
            $errors[] = 'Error Loading New Leads';
            Log::error($e);
        }
    }

    // Returning Leads
    if (in_array('returning leads', $_POST['filters'])) {
        try {
            $query = "SELECT COUNT(DISTINCT `u`.`id`) AS `total`, UNIX_TIMESTAMP(`v`.`timestamp`) AS `date` FROM `" . LM_TABLE_LEADS . "` `u`"
                   . " LEFT JOIN `" . LM_TABLE_VISITS . "` `v` ON `u`.`id` = `v`.`user_id`"
                   . " WHERE `u`.`num_visits` > 1"
                   . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'v`.`timestamp') : '')
                   . (!empty($sql_agent) ? ' AND ' . sprintf($sql_agent, 'u`.`agent') : '')
                   . " GROUP BY DATE(`v`.`timestamp`)"
                   . " ORDER BY `date` ASC;";
            $stmt = $db->prepare($query);

            $stmt->execute($params);

            while ($row = $stmt->fetch()) {
                // Add Event
                $json['events'][] = array(
                    'title'           => ($row['total'] == 1) ? $row['total'] . ' Returning Lead' : $row['total'] . ' Returning Leads',
                    'start'           => $row['date'],
                    'end'             => $row['date'],
                    'formatted_start' => date('Y-m-d\TH:i:s', $row['date']),
                    'formatted_end'   => date('Y-m-d\TH:i:s', $row['date']),
                    'allDay'          => true,
                    'editable'        => false,
                    'className'       => 'returning-leads'
                );
            }
        } catch (PDOException $e) {
            $errors[] = 'Error Loading Returning Leads';
            Log::error($e);
        }
    }

    // Form Activity
    if (in_array('form submissions', $_POST['filters'])) {
        try {
            $query = "SELECT COUNT(DISTINCT `f`.`id`) AS `total`, UNIX_TIMESTAMP(`f`.`timestamp`) AS `date` FROM `" . LM_TABLE_LEADS . "` `u`"
                   . " LEFT JOIN `" . LM_TABLE_FORMS . "` `f` ON `u`.`id` = `f`.`user_id`"
                   . " WHERE `num_forms` > 0"
                   . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'f`.`timestamp') : '')
                   . (!empty($sql_agent) ? ' AND ' . sprintf($sql_agent, 'u`.`agent') : '')
                   . " GROUP BY DATE(`f`.`timestamp`)"
                   . " ORDER BY `date` ASC;";

            $stmt = $db->prepare($query);

            $stmt->execute($params);

            while ($row = $stmt->fetch()) {
                // Add Event
                $json['events'][] = array(
                    'title'           => ($row['total'] == 1) ? $row['total'] . ' Form Submission' : $row['total'] . ' Form Submissions',
                    'start'           => $row['date'],
                    'end'             => $row['date'],
                    'formatted_start' => date('Y-m-d\TH:i:s', $row['date']),
                    'formatted_end'   => date('Y-m-d\TH:i:s', $row['date']),
                    'allDay'          => true,
                    'editable'        => false,
                    'className'       => 'form-submissions'
                );
            }
        } catch (PDOException $e) {
            $errors[] = 'Error Loading Form Activity';
            Log::error($e);
        }
    }

    // Lead Reminders
    try {
        $query = "SELECT `r`.`id` AS `reminder_id`, `u`.`id` AS `lead_id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `t`.`id` AS `type_id`, `t`.`title` AS `type`, `r`.`share`, `r`.`agent`, `r`.`details`, UNIX_TIMESTAMP(`r`.`timestamp`) AS `timestamp`, `r`.`completed`"
            . " FROM `" . LM_TABLE_LEADS . "` `u`"
            . " LEFT JOIN `" . LM_TABLE_REMINDERS . "` `r` ON `u`.`id` = `r`.`user_id`"
            . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `r`.`type` = `t`.`id`"
            . " WHERE 1"
            . (!empty($_POST['filters'])
                ? (in_array('uncategorized', $_POST['filters'])
                    ? " AND (`t`.`id` IS NULL OR `t`.`id` IN ('" . implode("', '", $filters) . "'))"
                    : " AND `t`.`id` IN ('" . implode("', '", $filters) . "')"
            ) : '')
            . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'r`.`timestamp') : '')
            . ($can_manage_all
                ? (!empty($sql_agent) ? " AND (" . sprintf($sql_agent, 'r`.`agent') . " OR (" . sprintf($sql_agent, 'u`.`agent') . " AND `r`.`share` = 'true'))" : '')
                : " AND `u`.`agent` = '" . $authuser->info('id') . "' AND (`r`.`agent` = '" . $authuser->info('id') . "' OR `r`.`share` = 'true')")
            . " GROUP BY `r`.`id`"
            . " ORDER BY `r`.`timestamp` ASC"
        . ";";

        $stmt = $db->prepare($query);

        $stmt->execute($params);

        while ($row = $stmt->fetch()) {
            //Get Lead Title
            $row['lead_name'] = Format::trim($row['first_name'] . ' ' . $row['last_name']);
            $row['lead_name'] = $row['lead_name'] ?: $row['email'];
            if (strlen($row['lead_name']) > 20) {
                $row['lead_name'] = substr($row['lead_name'], 0, 20) . '...';
            }

            // Add Event
            $json['events'][] = array(
                'id'              => $row['reminder_id'],
                'agent'           => $row['agent_id'],
                'lead'            => $row['lead_id'],
                'title'           => $row['lead_name'] . ': ' . $row['details'],
                'start'           => $row['timestamp'],
                'end'             => $row['timestamp'],
                'formatted_start' => date('Y-m-d\TH:i:s', $row['timestamp']),
                'formatted_end'   => date('Y-m-d\TH:i:s', $row['timestamp']),
                'editable'        => true,
                'overdue'         => ($row['completed'] != 'true' && strtotime($row['timestamp']) <= time()) ? true : false,
                'className'       => (!empty($row['type_id']) ? $event_types[$row['type_id']] : 'uncategorized'),
                'reminder'        => true,
                'completed'       => ($row['completed'] == 'true'),
                'shared'          => ($row['share'] == 'true'),
            );
        }
    // Query Error
    } catch (PDOException $e) {
        $errors[] = 'Error Loading Lead Reminders';
        Log::error($e);
    }

    // Calendar Filters
    $filters = array();

    // Count Lead Reminders by Type
    try {
        $query = "SELECT COUNT(DISTINCT `r`.`id`) AS `total`, `t`.`id` AS `type`"
            . " FROM `" . LM_TABLE_LEADS . "` `u`"
            . " LEFT JOIN `" . LM_TABLE_REMINDERS . "` `r` ON `u`.`id` = `r`.`user_id`"
            . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `r`.`type` = `t`.`id`"
            . " WHERE 1"
            . ($can_manage_all
                ? (!empty($sql_agent) ? " AND (" . sprintf($sql_agent, 'r`.`agent') . " OR (" . sprintf($sql_agent, 'u`.`agent') . " AND `r`.`share` = 'true'))" : '')
                : " AND `u`.`agent` = '" . $authuser->info('id') . "' AND (`r`.`agent` = '" . $authuser->info('id') . "' OR `r`.`share` = 'true')")
            . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'r`.`timestamp') : '')
            . " GROUP BY `t`.`id`;"
        . ";";

        $stmt = $db->prepare($query);

        $stmt->execute($params);

        $reminders = array();

        while ($type = $stmt->fetch()) {
            if (!empty($type['total'])) {
                $reminders[$type['type']] = $type['total'];
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Unable to count lead reminders by type";
        Log::error($e);
    }

    // Event Types
    try {
        $stmt = $db->query("SELECT `t`.`id` FROM `" . TABLE_CALENDAR_TYPES . "` `t` GROUP BY `t`.`id`;");

        while ($filter = $stmt->fetch()) {
            // Count Events
            try {
                $query = "SELECT COUNT(`e`.`id`) AS `total`"
                       . " FROM `" . TABLE_CALENDAR_EVENTS . "` `e`"
                       . " LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `d` ON `e`.`id` = `d`.`event`"
                       . " LEFT JOIN `" . TABLE_CALENDAR_ATTENDEES . "` `a` ON `e`.`id` = `a`.`event` AND `a`.`type` = 'Agent'"
                       . " WHERE `e`.`type` = " . $filter['id']
                       . (!empty($sql_date)  ? ' AND (' . sprintf($sql_date, 'd`.`start') . ' OR ' . sprintf($sql_date, 'd`.`end') . ')' : '')
                       . (!empty($sql_agent) ? ' AND (' . sprintf($sql_agent, 'e`.`agent') . ' OR ' . sprintf($sql_agent, 'a`.`user') . ')'  : '')
                       . ";";

                // Select Count
                $count = $db->fetch($query, $params);
            } catch (PDOException $e) {
                $errors[] = "Unable to count events";
                Log::error($e);
            }

            // Add Reminders to Count
            if (!empty($reminders[$filter['id']])) {
                $count['total'] += $reminders[$filter['id']];
            }

            // Add Filter
            $filters[] = array('value' => $filter['id'], 'count' => $count['total']);
        }
    } catch (PDOException $e) {
        $errors[] = "Unable to count event types";
        Log::error($e);
    }

    // Un-Categorized Events
    try {
        $query = "SELECT COUNT(`e`.`id`) AS `total`"
               . " FROM `" . TABLE_CALENDAR_EVENTS . "` `e`"
               . " LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `d` ON `e`.`id` = `d`.`event`"
               . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `e`.`type` = `t`.`id`"
               . " LEFT JOIN `" . TABLE_CALENDAR_ATTENDEES . "` `a` ON `e`.`id` = `a`.`event` AND `a`.`type` = 'Agent'"
               . " WHERE `t`.`id` IS NULL"
               . (!empty($sql_date)  ? ' AND (' . sprintf($sql_date, 'd`.`start') . ' OR ' . sprintf($sql_date, 'd`.`end') . ')' : '')
               . (!empty($sql_agent) ? ' AND (' . sprintf($sql_agent, 'e`.`agent') . ' OR ' . sprintf($sql_agent, 'a`.`user') . ')'  : '')
               . ";";

        $count = $db->fetch($query, $params);

        $filters[] = array('value' => 'uncategorized', 'count' => $count['total']);
    } catch (PDOException $e) {
        $errors[] = "Unable to count un-categorized events";
        Log::error($e);
    }

    // JSON Filters
    $json['filters'] = $filters;

    // Event Data
    $dates = array();

    // Agenda View
    if ($_POST['view'] == 'list') {
        // Re-Organize Event Data (by Date)
        foreach ($json['events'] as $event) {
            if (!empty($event['end'])) {
                $date = false;
                $continued = false;
                for ($ts = $event['start']; $ts <= $event['end']; $ts += (60 * 60 * 24)) {
                    $date = date('Y-m-d', $ts);
                    $dates[$date][] = $event;
                    if (!empty($date) && empty($continued)) {
                        $event['title'] .= ' (Cont\'d)';
                        unset($event['body']);
                        $continued = true;
                    }
                }
            } else {
                $date = date('Y-m-d', $event['start']);
                $dates[$date][] = $event;
            }
        }
        ksort($dates);

        // Generate HTML View
        $agenda = array();
        if (!empty($dates)) {
            foreach ($dates as $date => $events) {
                $agenda[] = '<fieldset class="grid' . (date('Y-m-d') == $date ? ' today' : '') . '">';
                $agenda[] = '<h2>' . date('l, F jS, Y', strtotime($date)) . '</h2>';
                $agenda[] = '<fieldset>';
                $agenda[] = '<ul class="agenda-list">';
                foreach ($events as $event) {
                    if (!empty($event['allDay'])) {
                        $date = 'All Day';
                    } else {
                        if ($event['start'] == $event['end'] || empty($event['end'])) {
                            $date = date('g:ia', $event['start']);
                        } else {
                            $date = date('g:ia', $event['start']) . ' - ' . date('g:ia', $event['end']);
                        }
                    }
                    $event['title'] .= (!empty($event['shared']) ? ' (Shared)' : '');

                    // CSS Classes
                    $classes = array($event['className']);
                    if (!empty($event['reminder'])) {
                        $classes[] = 'reminder';
                    }
                    if (!empty($event['completed'])) {
                        $classes[] = 'completed';
                    }
                    $classes = implode(' ', $classes);

                    // Reminder Icon
                    $reminder = '';
                    if (!empty($event['reminder'])) {
                        $reminder = '<span class="ico reminder' . (!empty($event['overdue']) ? ' late' : '') . '"></span>';
                    }

                    $agenda[] =
                        '<li class="' . $classes . '" data-id="' . $event['id'] . '">'
                            . '<div class="date">' . $date . '</div>'
                            . '<div class="title">'
                                . $reminder
                                . (!empty($event['editable'])
                                    ? '<a href="' . URL_BACKEND . 'calendar/event/edit/?id=' . $event['id'] . '" class="edit">' . $event['title'] . '</a>'
                                    : (!empty($event['reminder'])
                                        ? '<a href="' . URL_BACKEND . 'leads/lead/reminders/?id=' . $event['lead'] . '">' . $event['title'] . '</a>'
                                        : (!empty($event['shared'])
                                            ? '<a href="#" class="view">' . $event['title'] . '</a>'
                                            : '<span class="view">' . $event['title'] . '</span>'
                                        )
                                    )
                                )
                            . '</div>'
                            . '<div class="body">' . $event['body'] . '</div>'
                        . '</li>';
                }
                $agenda[] = '</ul>';
                $agenda[] = '</fieldset>';
                $agenda[] = '</fieldset>';
            }
        } else {
            $agenda[] = '<p class="block">There are no events for this date range.</p>';
        }
        $agenda = join("\n", $agenda);

        // Agenda View
        $json = array('agenda' => $agenda);
    }
} catch (\REW\Backend\Exceptions\UnauthorizedPageException $e) {
    $errors[] = 'Error! ' . $e->getMessage();
} catch (Exception $e) {
    Log::error($e);
}

/**
 * Build JSON Response
 */

// Send as JSON
header('Content-type: application/json');

// JSON Success
if (!empty($success)) {
    $json['success'] = $success;
}

// JSON Errors
if (!empty($errors)) {
    $json['errors'] = $errors;
}

// Return JSON Data
die(json_encode($json));
