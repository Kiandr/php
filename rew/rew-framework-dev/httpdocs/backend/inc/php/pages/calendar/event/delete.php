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
}

$can_delete_events = $calendarAuth->canDeleteCalendars($authuser);

if (!$can_delete_events) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to delete calendar events.')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Warnings
$warnings = array();

try {
    $db = DB::get();

    // Confirm Delete
    if (isset($_POST['delete'])) {
        $event = $db->fetch(
            "
            SELECT `e`.`id`, `e`.`google_event_id`, `e`.`microsoft_event_id`, COUNT(`d`.`id`) AS `dates`
            FROM `" . TABLE_CALENDAR_EVENTS . "` `e`
            LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `d` ON `e`.`id` = `d`.`event`
            WHERE `e`.`id` = :id " . (!empty($sql_agent) ? "
            AND " . sprintf($sql_agent, 'e`.`agent') : "") . "
            GROUP BY `e`.`id`;",
            [':id' => $_POST['id']]
        );

        if (empty($event)) {
            throw new \REW\Backend\Exceptions\MissingId\Calendar\MissingEventException();
        }

        // Delete Event Dates
        $stmt = $db->prepare("DELETE FROM `" . TABLE_CALENDAR_DATES . "` WHERE `event` = :event;");
        $stmt->execute([':event' => $event['id']]);

        // Delete Event Reminders
        $stmt = $db->prepare("DELETE FROM `" . TABLE_CALENDAR_REMINDERS . "` WHERE `event` = :event;");
        $stmt->execute([':event' => $event['id']]);

        try {
            // Delete from Google Calendar
            if (!empty(Settings::getInstance()->MODULES['REW_GOOGLE_CALENDAR']) &&
                $authuser->info('google_calendar_sync') == 'true' &&
                !empty($google_calendar) &&
                !empty($event['google_event_id'])
            ) {
                $google_calendar->deleteEvent($event['google_event_id']);
            }

            // Delete from Outlook Calendar
            if (!empty(Settings::getInstance()->MODULES['REW_OUTLOOK_CALENDAR']) &&
                $authuser->info('microsoft_calendar_sync') == 'true' &&
                !empty($microsoft_calendar) &&
                !empty($event['microsoft_event_id'])
            ) {
                $microsoft_calendar->deleteEvent($event['microsoft_event_id']);
            }
        } catch (Exception_OAuthCalendarError $e) {
            Log::error($e);
            $warnings[] = $e->getMessage();
        }

        // Success
        $success[] = __('The selected calendar event has successfully been deleted.');
    }
} catch (\REW\Backend\Exceptions\MissingId\Calendar\MissingEventException $e) {
    throw $e;
} catch (PDOException $e) {
    $errors[] = __('An error occurred while attempting to delete the selected event.');
    Log::error('Query Error: ' . $e->getMessage());
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

if (empty($errors)) {
    // Save Notices & Redirect to List
    $authuser->setNotices($success, $errors, $warnings);
    header('Location: /backend/calendar/');
    exit();
}
