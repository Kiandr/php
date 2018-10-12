<?php

// Get Authorization
$calendarAuth = new REW\Backend\Auth\CalendarAuth(Settings::getInstance());

// Require permission to edit all associates
$can_manage_all = $calendarAuth->canManageCalendars($authuser);
if (!$can_manage_all) {
    // Require permission to edit self
    if (!$calendarAuth->canManageOwnCalendars($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage calendars.')
        );
    } else {
        // Restrict to Only Agent's Data
        $sql_agent = "`%1\$s` = '" . $authuser->info('id') . "'";
    }
} else if (!empty($_GET['personal'])) {
    // Filter Agent Query
    $sql_agent = "`%1\$s` = '" . $authuser->info('id') . "'";
}

// Success
$success = array();

// Errors
$errors = array();

// Warnings
$warnings = array();

// Form Options
$options = array();

try {
    $db = DB::get();

    try {
        // Event Types
        $options['event_types'] = array();
        $results = $db->fetchAll("SELECT `id` AS `value`, `title` FROM `" . TABLE_CALENDAR_TYPES . "`;");

        foreach ($results as $result) {
            $options['event_types'][] = $result;
        }
    } catch (PDOException $e) {
        $errors[] = __('Error Loading Event Types');
        Log::error($e);
    }


    // Permission to view all agent events
    if ($can_manage_all) {
        // Agents (with Access to Calendar)
        try {
            $options['agents'] = array();
            $results = $db->fetchAll(
                "SELECT SQL_CACHE `id` AS `value`, CONCAT(`first_name`, ' ', `last_name`) AS `title`
                FROM `" . LM_TABLE_AGENTS . "`
                WHERE `id` != :id
                AND (`permissions_user` & :perm_calendar_agent || `permissions_admin` & :perm_calendar_manage)
                ORDER BY `first_name` ASC;",
                [
                ':id'                   => $authuser->info('id'),
                ':perm_calendar_agent'  => Auth::PERM_CALENDAR_AGENT,
                ':perm_calendar_manage' => Auth::PERM_CALENDAR_MANAGE
                ]
            );

            foreach ($results as $result) {
                $options['agents'][] = $result;
            }
        } catch (PDOException $e) {
            $errors[] = __('Error Loading Agents');
            Log::error($e);
        }
    }

    // Initialize Calendar
    $calendar = new REW\Backend\Calendar();

    // Calendar View
    $view = in_array($_GET['view'], ['day', 'list']) ? $_GET['view'] : 'default';

    // Calendar URL
    $calendar_url = URL_BACKEND . 'calendar/?date=%s' . ($view !== 'default' ? '&view=' . $_GET['view'] : '');

    // Selected Date
    $_GET['date'] = isset($_GET['date']) ? $_GET['date'] : $_SESSION['date'];

    // Remember Last Date
    $_SESSION['date'] = $_GET['date'];

    // Select Date
    if (!empty($_GET['date'])) {
        $calendar->setDate(($_GET['date']));
    }

    $calendar_app_data = json_encode([
        'year'  => $calendar->getYear(),
        'month' => $calendar->getMonth(),
        'day'   => $calendar->getDay(),
        'days'  => $calendar->getDays(),
        'view'  => $view
    ]);

    // Used For Calendar Rendering
    // # of Days in Month
    $days = $calendar->getDays();
    // Date Information
    $info = $calendar->getdateOfFirst();
    // Current Day of Week
    $weekday = $info['wday'];

    if ($_GET['view'] == 'day') {
        $date = date('l, F j, Y', mktime(0, 0, 0, $calendar->getMonth(), $calendar->getDay(), $calendar->getYear()));
    } else {
        $date = date('F, Y', mktime(0, 0, 0, $calendar->getMonth(), 1, $calendar->getYear()));
    }
} catch (Exception $e) {
    Log::error($e);
}
