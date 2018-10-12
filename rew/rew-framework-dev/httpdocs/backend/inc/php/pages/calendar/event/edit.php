<?php

// Get Authorization
$calendarAuth = new REW\Backend\Auth\CalendarAuth(Settings::getInstance());

// Require Authorization
$can_manage_all = $calendarAuth->canManageCalendars($authuser);
if (!$can_manage_all) {
    // Require permission to edit self
    if (!$calendarAuth->canManageOwnCalendars($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage calendars.')
        );
    } else {
        $sql_agent = "`%1\$s` = '" . $authuser->info('id') . "'";
    }
}

$can_delete_events = $calendarAuth->canDeleteCalendars($authuser);

try {
    $db = DB::get();

    // Success
    $success = array();

    // Errors
    $errors = array();

    // Warnings
    $warnings = array();

    // Google Calendar Object
    if ($calendarAuth->canSyncWithGoogleCalander($authuser)) {
        $google_calendar = new OAuth_Calendar_Google($page, $authuser);
    } else {
        unset($google_calendar);
    }

    // Microsoft Calendar Object
    if ($calendarAuth->canSyncWithOutlookCalander($authuser)) {
        $microsoft_calendar = new OAuth_Calendar_Microsoft($page, $authuser);
    } else {
        unset($microsoft_calendar);
    }

    if (isset($_GET['submit'])) {
        // Go To Delete Page
        if (isset($_POST['delete'])) {
            require_once('delete.php');
        }

        // Select Row
        $event = $db->fetch("SELECT *
            FROM `" . TABLE_CALENDAR_EVENTS . "`
            WHERE `id` = :id " .
            (!empty($sql_agent) ? ' AND ' . sprintf($sql_agent, 'agent') : ''), [':id' => $_POST['id']]);

        // Event Not Found
        if (empty($event)) {
            throw new \REW\Backend\Exceptions\MissingId\Calendar\MissingEventException();
        }

        // Required Fields
        $required   = array();
        $required[] = array('value' => 'title',       'title' => __('Event Title'));
        $required[] = array('value' => 'start_date',  'title' => __('Start Date'));
        $required[] = array('value' => 'end_date',    'title' => __('End Date'));
        if (empty($_POST['all_day'])) {
            $required[] = array('value' => 'start_time',  'title' => __('Start Time'));
            $required[] = array('value' => 'end_time',    'title' => __('End Time'));
        }

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Ensure we have valid start/end dates
        if (strtotime($_POST['start_date']) > strtotime($_POST['end_date'])) {
            $errors[] = __("Start date must be before end date.");
        } else if (empty($_POST['all_day']) &&
            strtotime($_POST['start_date']) === strtotime($_POST['end_date']) &&
            strtotime($_POST['start_time']) > strtotime($_POST['end_time'])
        ) {
            $errors[] = __("Start time must be before end time.");
        }

        // Check Errors
        if (empty($errors)) {
            // All Day Event
            if (!empty($_POST['all_day'])) {
                $start = date('Y-m-d', strtotime($_POST['start_date']));
                $end = date('Y-m-d', strtotime($_POST['end_date']));
            } else {
                $start = date('Y-m-d H:i:s', strtotime($_POST['start_date'] . ' ' . $_POST['start_time']));
                $end = date('Y-m-d H:i:s', strtotime($_POST['end_date'] . ' ' . $_POST['end_time']));
            }

            // Require Numeric or NULL
            $_POST['type'] = is_numeric($_POST['type']) ? $_POST['type'] : null;

            try {
                $db->beginTransaction();

                // Build UPDATE Query
                $stmt = $db->prepare("UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                   . "`type`              = :type, "
                   . "`title`             = :title, "
                   . "`body`              = :body, "
                   . "`timestamp_updated` = NOW()"
                   . " WHERE id = :id;");
                // Execute Query
                $stmt->execute([
                    ':type'  => $_POST['type'],
                    ':title' => $_POST['title'],
                    ':body'  => $_POST['body'],
                    ':id'    => $event['id']
                ]);

                // Build UPDATE Query
                $stmt = $db->prepare("UPDATE `" . TABLE_CALENDAR_DATES . "` SET "
                    . "`start` = :start, "
                    . "`end`   = :end, "
                    . "`all_day`   = :all_day, "
                    . "`timestamp_updated` = NOW()"
                    . " WHERE `id` = :id AND `event` = :event;");

                // Execute Query
                $stmt->execute([
                    ':start' => $start,
                    ':end'   => $end,
                    ':all_day' => ($_POST['all_day'] == 'true' ?: 'false'),
                    ':id'    => $_POST['date_id'],
                    ':event' => $event['id']
                ]);

                $db->commit();
            } catch (PDOException $e) {
                Log::error($e);

                $db->rollback();

                throw new Exception(__('An error occurred while saving your event.'));
            }

            // Success
            $success[] = __('Calendar Event has successfully been updated.');

            // Get Type Title
            if (!empty($_POST['type'])) {
                $event_type = $db->fetch("SELECT `title` FROM `" . TABLE_CALENDAR_TYPES . "` WHERE `id` =  :id", [':id' => $_POST['type']]);
            }

            try {
                if (!empty($google_calendar) && !empty($event['google_event_id'])) {
                    // Google Event Object
                    $google_event = new OAuth_Calendar_GoogleEvent();

                    // Populate Event Object
                    $google_event->start = $start;
                    $google_event->end = $end;
                    $google_event->title = $_POST['title'];
                    $google_event->description = $_POST['body'];
                    $google_event->colorId = $_POST['type'];
                    $google_event->type = $event_type['title'];
                    if (isset($_POST['all_day'])) {
                        $google_event->all_day_event = true;
                    }
                    $google_event->event_id = $event['google_event_id'];

                    // Push to Google Calendar
                    $google_event_id = $google_calendar->push($google_event, 'UPDATE');

                    if (!empty($google_event_id)) {
                        try {
                            // Add Google Event ID
                            $stmt = $db->prepare("UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                                . "`google_event_id` = :google_event_id "
                                . "WHERE `id` = :id;");

                            // Execute Query
                            $stmt->execute([
                                ':google_event_id' => $google_event_id,
                                'id'               => $event_id
                            ]);
                        } catch (PDOException $e) {
                            $errors[] = __('An error occurred while syncing your event with Google Calendar.');
                            Log::error($e);
                        }
                    }
                }

                if (!empty($microsoft_calendar) && !empty($event['microsoft_event_id'])) {
                    // Microsoft Event Object
                    $microsoft_event = new OAuth_Calendar_MicrosoftEvent();

                    // Populate Event Object
                    $microsoft_event->start = $start;
                    $microsoft_event->end = $end;
                    $microsoft_event->title = $_POST['title'];
                    $microsoft_event->description = $_POST['body'];
                    $microsoft_event->type = $event_type['title'];
                    if (isset($_POST['all_day'])) {
                        $microsoft_event->all_day_event = true;
                    }
                    $microsoft_event->event_id = $event['microsoft_event_id'];

                    // Push to Outlook Calendar
                    $microsoft_event_id = $microsoft_calendar->push($microsoft_event, 'UPDATE');

                    if (!empty($microsoft_event_id)) {
                        try {
                            // Add Microsoft Event ID
                            $stmt = $db->prepare("UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                                . "`microsoft_event_id` = :microsoft_event_id "
                                . "WHERE `id` = :id;");

                            // Execute Query
                            $stmt->execute([
                                ':microsoft_event_id' => $microsoft_event_id,
                                ':id'                 => $event_id
                            ]);
                        } catch (PDOException $e) {
                            $errors[] = __('An error occurred while syncing your event with Outlook Calendar.');
                            Log::error($e);
                        }
                    }
                }
            } catch (Exception_OAuthCalendarError $e) {
                Log::error($e);
                $warnings[] = $e->getMessage();
            }

            // Admin Mode
            if ($authuser->info('mode') === 'admin') {
                // Remove Old Attendees
                try {
                    $stmt = $db->prepare("DELETE FROM `" . TABLE_CALENDAR_ATTENDEES . "` WHERE `event` = :event AND `type` = 'Agent'");

                    $stmt->execute([
                        ':event' => $event['id']
                    ]);
                } catch (PDOException $e) {
                    Log::error($e);

                    throw new Exception(__('Unable to remove old event attendees'));
                }

                // Share with Agents - Insert Attendees
                $agents = array();
                if (!empty($_POST['agents']) && is_array($_POST['agents'])) {
                    $params = [];
                    $placeholders = [];
                    foreach ($_POST['agents'] as $agent) {
                        if (is_numeric($agent)) {
                            try {
                                $stmt = $db->prepare(
                                    "INSERT INTO `" . TABLE_CALENDAR_ATTENDEES . "` SET "
                                    . "`type`  = 'Agent', "
                                    . "`user`  = :user, "
                                    . "`event` = :event;"
                                );

                                $stmt->execute([
                                    ':user'  => $agent,
                                    ':event' => $event['id']
                                ]);

                                $agents[] = $agent;
                            } catch (PDOException $e) {
                                Log::error($e);
                                throw new Exception(__('An error occurred while sharing event.'));
                            }
                        }
                    }
                }
            }

            // All Updating Was A Success; Save Notices & Redirect To Calendar Page
            $authuser->setNotices($success, $errors);
            header('Location: ' . URL_BACKEND . 'calendar');
            exit;
        }
    }

    // Has Access To All Agents
    if ($can_manage_all) {
        try {
            // Agents (with Access to Calendar)
            $options['agents'] = array();
            $query = "SELECT SQL_CACHE `id` AS `value`, CONCAT(`first_name`, ' ', `last_name`) AS `title` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` != :id AND (`permissions_user` & " . Auth::PERM_CALENDAR_AGENT . " || `permissions_admin` & " . Auth::PERM_CALENDAR_MANAGE . ") ORDER BY `first_name` ASC;";
            $results = $db->fetchAll($query, array(":id" => $authuser->info('id')));

            foreach ($results as $result) {
                $options['agents'][] = $result;
            }
        } catch (PDOException $e) {
            Log::error($e);
            throw new Exception(__('Error Loading Agents'));
        }
    }

    // Load Event
    try {
        $event_id = $_GET['id'] ?: $_POST['id'];

        if (empty($event_id)) {
            throw new \REW\Backend\Exceptions\MissingId\Calendar\MissingEventException();
        }

        $query =
            "SELECT
                `t1`.`id`,
                `t1`.`title` AS `title`,
                `body`,
                `google_event_id`,
                `microsoft_event_id`,
                `t1`.`type`,
                `t3`.`id` as `date_id`,
                UNIX_TIMESTAMP(`t3`.`start`) AS `start`,
                UNIX_TIMESTAMP(`t3`.`end`) AS `end`,
                `t3`.`all_day`,
                GROUP_CONCAT(`t4`.`user`) AS `agents` "
            . "FROM `" . TABLE_CALENDAR_EVENTS . "` `t1` "
            . "LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `t3` ON `t1`.`id` = `t3`.`event` "
            . "LEFT JOIN `" . TABLE_CALENDAR_ATTENDEES . "` `t4` ON `t1`.`id` = `t4`.`event` AND `t4`.`type` = 'Agent' "
            . "WHERE `t1`.`id` = :id" . (!empty($sql_agent) ? ' AND `t1`.`agent` = :agent' : '');

        $params = [];
        $params[':id'] = $event_id;
        if (!empty($sql_agent)) {
            $params[':agent'] = $authuser->info('id');
        }

        $event = $db->fetch($query, $params);

        // Event Not Found
        if (empty($event['id'])) {
            throw new \REW\Backend\Exceptions\MissingId\Calendar\MissingEventException();
        }

        // Explode Agents In To Array
        if (!empty($event['agents'])) {
            $event['agents'] = explode(',', $event['agents']);
        }
    } catch (PDOException $e) {
        Log::error($e);
        throw new Exception(__("Unable To Load The Requested Event"));
    }

    // If Failed Edit, Replace Event With Last Entered Info
    if (!empty($_POST)) {
        foreach ($event as $k => $v) {
            if (!empty($_POST[$k])) {
                $event[$k] = $_POST[$k];
            }
        }
    } else {
        $event['start_date'] = date('Y-m-d', $event['start']);
        $event['start_time'] = date('H:i', ($event['all_day'] == 'true' ? time() : $event['start']));

        $event['end_date'] = date('Y-m-d', $event['end']);
        $event['end_time'] = date('H:i', ($event['all_day'] == 'true' ? time() : $event['end']));
    }

    // Calendar Event Types
    try {
        $types = $db->fetchAll("SELECT `id` AS `value`, `title` FROM `" . TABLE_CALENDAR_TYPES . "`;");
    } catch (PDOException $e) {
        Log::error($e);
        throw new Exception(__('Error Loading Calendar Event Types'));
    }

// Bubble Up As This Exception Is Handled At The Application Level
} catch (\REW\Backend\Exceptions\MissingIdException $e) {
    throw $e;
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
