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
    }
}

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
    }

    // Microsoft Calendar Object
    if ($calendarAuth->canSyncWithOutlookCalander($authuser)) {
        $microsoft_calendar = new OAuth_Calendar_Microsoft($page, $authuser);
    }

    // Add Calendar Event
    if (isset($_GET['submit'])) {
        // Required Fields
        $required   = array();
        $required[] = array('value' => 'title',       'title' => 'Event Title');
        $required[] = array('value' => 'start_date',  'title' => 'Start Date');
        $required[] = array('value' => 'end_date',    'title' => 'End Date');
        if (empty($_POST['all_day'])) {
            $required[] = array('value' => 'start_time',  'title' => 'Start Time');
            $required[] = array('value' => 'end_time',    'title' => 'End Time');
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

            // Try To Create Calendar Event
            try {
                $db->beginTransaction();

                // Build INSERT Query
                $query = "INSERT INTO `" . TABLE_CALENDAR_EVENTS . "` SET "
                    . "`agent`             = :agent, "
                    . "`type`              = :type, "
                    . "`title`             = :title, "
                    . "`body`              = :body, "
                    . "`timestamp_created` = NOW();";

                $stmt = $db->prepare($query);
                $stmt->execute(array(
                    ":agent" => $authuser->info('id'),
                    ":type"  => $_POST['type'],
                    ":title" => $_POST['title'],
                    ":body"  => $_POST['body']
                ));

                // Insert ID
                $event_id = $db->lastInsertId();

                // Insert Calendar Event Date
                $query = "INSERT INTO `" . TABLE_CALENDAR_DATES . "` SET "
                    . "`event` = :event, "
                    . "`start` = :start, "
                    . "`end`   = :end, "
                    . "`all_day` = :all_day, "
                    . "`timestamp_created` = NOW();";

                // Execute Query
                $stmt = $db->prepare($query);
                $stmt->execute(array(
                    ":event" => $event_id,
                    ":start"  => $start,
                    ":end" => $end,
                    ":all_day" => !empty($_POST['all_day']) ? 'true' : 'false'
                ));

                $db->commit();
            } catch (PDOException $e) {
                $errors[] = __('An error occurred while saving your event.');

                $db->rollback();

                throw $e;
            }

            // Success
            $success[] = __('Your calendar event has successfully been saved.');

            // Get Type Title
            if (!empty($_POST['type'])) {
                $event_type = $db->fetch("SELECT `title` FROM `" . TABLE_CALENDAR_TYPES . "` WHERE `id` =  :id", array(":id" => $_POST['type']));
            }

            try {
                if (!empty(Settings::getInstance()->MODULES['REW_GOOGLE_CALENDAR']) && $authuser->info('google_calendar_sync') == 'true' && !empty($google_calendar)) {
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

                    // Push to Google Calendar
                    $google_event_id = $google_calendar->push($google_event, 'INSERT');

                    if (!empty($google_event_id)) {
                        // Add Google Event ID
                        try {
                            $query = "UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                                . "`google_event_id` = :google_event_id "
                                . "WHERE `id` = :id;";

                            $stmt = $db->prepare($query);
                            $stmt->execute(array(
                                ":google_event_id" => $google_event_id,
                                ":id"              => $event_id
                            ));
                        } catch (PDOException $e) {
                            $errors[] = __('An error occurred while syncing your event with Google Calendar.');
                            Log::error($e);
                        }
                    }
                }

                if (!empty(Settings::getInstance()->MODULES['REW_OUTLOOK_CALENDAR']) && $authuser->info('microsoft_calendar_sync') == 'true' && !empty($microsoft_calendar)) {
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

                    // Push to Outlook Calendar
                    $microsoft_event_id = $microsoft_calendar->push($microsoft_event, 'INSERT');

                    if (!empty($microsoft_event_id)) {
                        // Add Microsoft Event ID
                        try {
                            $query = "UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                                . "`microsoft_event_id` = :microsoft_event_id "
                                . "WHERE `id` = :id;";

                            $stmt = $db->prepare($query);
                            $stmt->execute(array(
                                ":microsoft_event_id" => $microsoft_event_id,
                                ":id"                 => $event_id
                            ));
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

            // Share with Agents - Insert Attendees
            $agents = $_POST['agents'];
            if (!empty($agents) && is_array($agents)) {
                foreach ($agents as $agent) {
                    if (is_numeric($agent)) {
                        try {
                            $query = "INSERT INTO `" . TABLE_CALENDAR_ATTENDEES . "` SET "
                                . "`type`  = 'Agent', "
                                . "`user`  = :user, "
                                . "`event` = :event;";

                            $stmt = $db->prepare($query);
                            $stmt->execute(array(
                                ":user"  => $agent,
                                ":event" => $event_id
                            ));
                        } catch (PDOException $e) {
                            $errors[] = __('An error occurred while sharing event.');
                            Log::error($e);
                            break;
                        }
                    }
                }
            }

            // Save Notices & Redirect To Calendar Page
            $authuser->setNotices($success, $errors);
            header('Location: ' . URL_BACKEND . 'calendar');
            exit;
        }
    }

    $start_date = '';
    if (!empty($_POST['start_date'])) {
        $start_date = $_POST['start_date'];
    } else if (!empty($_GET['date'])) {
        $start_date = $_GET['date'];
    } else {
        $start_date = date('Y-m-d');
    }

    $start_time = '';
    if (!empty($_POST['start_time'])) {
        $start_time = $_POST['start_time'];
    } else if (!empty($_GET['start_time'])) {
        $start_time = date('H:i', strtotime($_GET['start_time']));
    } else {
        $start_time = date('H:i');
    }

    $end_date = '';
    if (!empty($_POST['end_date'])) {
        $end_date = $_POST['end_date'];
    } else if (!empty($_GET['date'])) {
        $end_date = $_GET['date'];
    } else {
        $end_date = date('Y-m-d');
    }

    $end_time = '';
    if (!empty($_POST['end_time'])) {
        $end_time = $_POST['end_time'];
    } else if (!empty($_GET['start_time'])) {
        $end_time = date('H:i', strtotime($_GET['start_time']));
    } else {
        $end_time = date('H:i');
    }


    $event = [
        'title'      => $_POST['title'],
        'type'       => $_POST['type'],
        'body'       => $_POST['body'],
        'start_date' => $start_date,
        'start_time' => $start_time,
        'end_date'   => $end_date,
        'end_time'   => $end_time,
        'all_day'    => ($_POST['all_day'] == 'true' ? 'checked' : ''),
        'agents'     => $_POST['agents']
    ];

    // Has Access To All Agents
    if ($can_manage_all) {
        try {
            // Agents (with Access to Calendar)
            $options['agents'] = array();
            $query = "SELECT SQL_CACHE `id` AS `value`, CONCAT(`first_name`, ' ', `last_name`) AS `title` FROM `agents` WHERE `id` != :id AND (`permissions_user` & " . Auth::PERM_CALENDAR_AGENT . " || `permissions_admin` & " . Auth::PERM_CALENDAR_MANAGE . ") ORDER BY `first_name` ASC;";
            $results = $db->fetchAll($query, array(":id" => $authuser->info('id')));

            foreach ($results as $result) {
                $options['agents'][] = $result;
            }
        } catch (PDOException $e) {
            $errors[] = __('Error Loading Agents');
            Log::error($e);
        }
    }

    // Calendar Event Types
    try {
        $query = "SELECT `id` AS `value`, `title` FROM `" . TABLE_CALENDAR_TYPES . "`;";
        $types = $db->fetchAll($query);
    } catch (PDOException $e) {
        $errors[] = __('Error Loading Calendar Event Types');
        throw $e;
    }

// Flow Control Catch Block.  Log Error
} catch (Exception $e) {
    Log::error($e);
}
