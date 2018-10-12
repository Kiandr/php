<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Warnings
$warnings = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Prepopulate Add Reminder Data (Used For Dashboard Event Scheduling, Specifically Showing Requests)
$_POST['type'] = !isset($_POST['type']) && isset($_GET['type']) ? $_GET['type'] : $_POST['type'];
$_POST['details'] = !isset($_POST['details']) && isset($_GET['details']) ? $_GET['details'] : $_POST['details'];

// Query Lead
$lead = $db->fetch("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;", ['id' => $_GET['id']]);

/* Throw Missing $lead Exception */
if (empty($lead)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
}

// Create lead instance
$lead = new Backend_Lead($lead);

// Get Lead Authorization
$leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);
$calendarAuth = new REW\Backend\Auth\CalendarAuth($settings);

// Not authorized to view lead reminders
if (!$leadAuth->canViewReminders()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view this leads reminders'
    );
}

// Get Lead Reminder Filters
if (!$leadAuth->canViewAllLeadContent()) {
    // Agent Filter
    $sql_agent = "`agent` = '" . $authuser->info('id') . "'";
} else {
    // Get Agent Filter
    $_GET['personal'] = isset($_POST['personal']) ? $_POST['personal'] : $_GET['personal'];
    $personal = isset($_POST['personal']) ? $_POST['personal'] : $_GET['personal'];
}

// Can Share
$can_share = $authuser->isSuperAdmin() || $authuser->isAssociate();

// Get Agent Auth
$agentAuth = new REW\Backend\Auth\AgentsAuth($settings);
$assocAuth = new REW\Backend\Auth\AssociateAuth($settings);

// Can Sync Google Calendars
$can_sync_with_google = $calendarAuth->canSyncWithGoogleCalander($authuser);

// Can Sync Outlook Calendars
$can_sync_with_outlook = $calendarAuth->canSyncWithOutlookCalander($authuser);

// Google Calendar Object
if ($can_sync_with_google) {
    $google_calendar = new OAuth_Calendar_Google($page, $authuser);
}

// Microsoft Calendar Object
if ($can_sync_with_outlook) {
    $microsoft_calendar = new OAuth_Calendar_Microsoft($page, $authuser);
}

// Reminder ID
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];

// Delete Reminder
if (!empty($_GET['delete'])) {
    // Require Reminder
    $query = "SELECT `id`, `google_event_id`, `microsoft_event_id` FROM `" . LM_TABLE_REMINDERS
        . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "' AND `user_id` = '" . $lead['id'] . "'"
        . (!empty($sql_agent) && !$calendarAuth->canDeleteCalendars($authuser) ? ' AND ' . $sql_agent : '') . ";";
    if ($result = mysql_query($query)) {
        $reminder =  mysql_fetch_assoc($result);

        try {
            // Delete from Google Calendar
            if ($can_sync_with_google && !empty($google_calendar) && !empty($reminder['google_event_id'])) {
                $google_calendar->deleteEvent($reminder['google_event_id']);
            }

            // Delete from Outlook Calendar
            if ($can_sync_with_outlook && !empty($microsoft_calendar) && !empty($reminder['microsoft_event_id'])) {
                $microsoft_calendar->deleteEvent($reminder['microsoft_event_id']);
            }
        } catch (Exception_OAuthCalendarError $e) {
            Log::error($e);
            $warnings[] = $e->getMessage();
        }

        $query = "DELETE FROM `" . LM_TABLE_REMINDERS . "` WHERE `id` = '" . $reminder['id'] . "' AND `user_id` = '" . $lead['id'] . "'" . (!empty($sql_agent) && !$calendarAuth->canDeleteCalendars($authuser) ? ' AND ' . $sql_agent : '') . ";";
        if (mysql_query($query)) {
            $success[] = 'The selected reminder has been deleted.';
        } else {
            $errors[] = 'An error occurred while trying to delete the selected reminder.';
        }
    } else {
        $errors[] = 'An error occurred while trying to locate the selected reminder.';
    }
}

// Toggle Reminder
if (!empty($_GET['toggle'])) {
    $query = "SELECT `id`, `completed` FROM `" . LM_TABLE_REMINDERS
        . "` WHERE `id` = '" . mysql_real_escape_string($_GET['toggle']) . "' AND `user_id` = '" . $lead['id'] . "'"
        . (!empty($sql_agent) ? " AND (`share` = 'true' OR " . $sql_agent . ")" : '') . ";";
    if ($result = mysql_query($query)) {
        $toggle = mysql_fetch_assoc($result);
        if (!empty($toggle)) {
            $completed = ($toggle['completed'] == 'false') ? 'true' : 'false';
            $query = "UPDATE `" . LM_TABLE_REMINDERS . "` SET `completed` = '" . $completed . "' WHERE `id` = '" . $toggle['id'] . "';";
            if (mysql_query($query)) {
                header('Location: ?id=' . $lead['id']);
                exit;
            } else {
                $errors[] = 'An error occurred while trying to update the selected reminder.';
            }
        } else {
            $errors[] = 'The selected reminder could not be found.';
        }
    } else {
        $errors[] = 'An error occurred while trying to locate the selected reminder.';
    }
}

// Edit Reminder
if (!empty($_GET['edit'])) {
    // Select Row
    $query = "SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `" . LM_TABLE_REMINDERS
        . "` WHERE `id` = '" . mysql_real_escape_string($_GET['edit']) . "' AND `user_id` = '" . $lead['id'] . "'"
        . (!empty($sql_agent) && !$calendarAuth->canManageCalendars($authuser) ? ' AND ' . $sql_agent : '') . ";";
    if ($result = mysql_query($query)) {
        $edit = mysql_fetch_assoc($result);

        // Require Row
        if (!empty($edit)) {
            // Can Share (If Belongs to Super Admin or ISA)
            $can_share = !empty($can_share) && ($edit['agent'] == 1) || (!empty($edit['associate']) && ($authuser->isSuperAdmin() || ($authuser->isAssociate() && $authuser->info('id') == $edit['associate'])));

            $edit['date'] = date('F d, Y', $edit['timestamp']);
            $edit['time'] = date('H:i', $edit['timestamp']);

            // Process Submit
            if (isset($_GET['submit'])) {
                // Required Fields
                $required   = array();
                $required[] = array('value' => 'type', 'title' => 'Reminder Type');
                $required[] = array('value' => 'date', 'title' => 'Reminder Date');
                $required[] = array('value' => 'time', 'title' => 'Reminder Time');
                $required[] = array('value' => 'details', 'title' => 'Reminder Details');

                // Process Required Fields
                foreach ($required as $require) {
                    if (empty($_POST[$require['value']])) {
                        $errors[] = $require['title'] . ' is a required field.';
                    }
                }

                // Parse DateTime from timestamp
                $timestamp = strtotime($_POST['date'] . ' ' . $_POST['time']);
                if (empty($timestamp)) {
                    $errors[] = 'Invalid Reminder Date';
                } else {
                    $datetime = date('Y-m-d H:i:s', $timestamp);
                }

                // Check Errors
                if (empty($errors)) {
                    // Escape HTML
                    $_POST['type'] = htmlspecialchars($_POST['type']);
                    $_POST['date'] = htmlspecialchars($_POST['date']);
                    $_POST['details'] = htmlspecialchars($_POST['details']);

                    // Share
                    $share = (!empty($can_share) && !empty($_POST['share'])) ? true : false;

                    // Completed
                    $completed = !empty($_POST['completed']) ? true : false;

                    // Build UPDATE Query
                    $query = "UPDATE `" . LM_TABLE_REMINDERS . "` SET "
                           . "`type`      = '" . mysql_real_escape_string($_POST['type']) . "', "
                           . "`details`   = '" . mysql_real_escape_string($_POST['details']) . "', "
                           . "`completed` = '" . (!empty($completed) ? "true" : "false") . "', "
                           . "`share` = '" . (!empty($share) ? "true" : "false") . "', "
                           . "`timestamp` = '" . $datetime . "', "
                           . "`timestamp_updated` = NOW()"
                           . " WHERE `id` = '" . $edit['id'] . "';";

                    // Execute Query
                    if (mysql_query($query)) {
                        // Get Type Title
                        if (!empty($_POST['type'])) {
                            $event_type_query = mysql_query("SELECT `title` FROM `" . TABLE_CALENDAR_TYPES . "` WHERE `id` =  '" . $_POST['type'] . "'");
                            if ($event_type_query) {
                                $event_type = mysql_fetch_assoc($event_type_query);
                            }
                        }

                        try {
                            if ($can_sync_with_google && !empty($google_calendar) && !empty($edit['google_event_id'])) {
                                // Google Event Object
                                $google_event = new OAuth_Calendar_GoogleEvent();

                                // Populate Event Object
                                $google_event->start = $datetime;
                                $google_event->end = $datetime;
                                $google_event->title = $lead->getNameOrEmail();
                                $google_event->description = $_POST['details'];
                                $google_event->type = $event_type['title'];
                                $google_event->event_id = $edit['google_event_id'];

                                // Push to Google Calendar
                                $google_event_id = $google_calendar->push($google_event, 'UPDATE');

                                if (!empty($google_event_id)) {
                                    // Add Google Event ID
                                    $query = "UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                                            . "`google_event_id` = '" . mysql_real_escape_string($google_event_id) . "' "
                                            . "WHERE `id` = '" . mysql_real_escape_string($event_id) . "';";

                                    // Execute Query
                                    if (!mysql_query($query)) {
                                        $errors[] = 'An error occurred while saving your event.';
                                        Log::error('Query Error: ' . mysql_error());
                                    }
                                }
                            }

                            if ($can_sync_with_outlook && !empty($microsoft_calendar) && !empty($edit['microsoft_event_id'])) {
                                // Microsoft Event Object
                                $microsoft_event = new OAuth_Calendar_MicrosoftEvent();

                                // Populate Event Object
                                $microsoft_event->start = $datetime;
                                $microsoft_event->end = $datetime;
                                $microsoft_event->title = $lead->getNameOrEmail();
                                $microsoft_event->description = $_POST['details'];
                                $microsoft_event->type = $event_type['title'];
                                $microsoft_event->event_id = $edit['microsoft_event_id'];

                                // Push to Outlook Calendar
                                $microsoft_event_id = $microsoft_calendar->push($microsoft_event, 'UPDATE');

                                if (!empty($microsoft_event_id)) {
                                    // Add Microsoft Event ID
                                    $query = "UPDATE `" . TABLE_CALENDAR_EVENTS . "` SET "
                                            . "`microsoft_event_id` = '" . mysql_real_escape_string($microsoft_event_id) . "' "
                                            . "WHERE `id` = '" . mysql_real_escape_string($event_id) . "';";

                                    // Execute Query
                                    if (!mysql_query($query)) {
                                        $errors[] = 'An error occurred while saving your event.';
                                        Log::error('Query Error: ' . mysql_error());
                                    }
                                }
                            }
                        } catch (Exception_OAuthCalendarError $e) {
                            Log::error($e);
                            $warnings[] = $e->getMessage();
                        }

                        // Success
                        $success[] = 'The selected reminder has successfully been saved.';

                        // Save notices & redirect on success
                        $authuser->setNotices($success, $errors);
                        header('Location: ?id='  . $lead['id'] . '&success');
                        exit;

                    // Query Error
                    } else {
                        $errors[] = 'An error occurred while attempting to save reminder.';
                    }
                }
            }
        }

    // Query Error
    } else {
        $errors[] = 'Error Occurred while located selected reminder.';
    }
} else {
    // Add Row
    if (isset($_GET['submit'])) {
        // Required Fields
        $required   = array();
        $required[] = array('value' => 'type', 'title' => 'Reminder Type');
        $required[] = array('value' => 'date', 'title' => 'Reminder Date');
        $required[] = array('value' => 'time', 'title' => 'Reminder Time');
        $required[] = array('value' => 'details', 'title' => 'Reminder Details');

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = $require['title'] . ' is a required field.';
            }
        }

        // Parse DateTime from timestamp
        $timestamp = strtotime($_POST['date'] . ' ' . $_POST['time']);
        if (empty($timestamp)) {
            $errors[] = 'Invalid Reminder Date';
        } else {
            $datetime = date('Y-m-d H:i:s', $timestamp);
        }

        // Check Errors
        if (empty($errors)) {
            // Escape HTML
            $_POST['type'] = htmlspecialchars($_POST['type']);
            $_POST['details'] = htmlspecialchars($_POST['details']);

            // Share
            $share = (!empty($can_share) && !empty($_POST['share'])) ? true : false;

            // Build INSERT Query
            $query = "INSERT INTO `" . LM_TABLE_REMINDERS . "` SET "
                   . "`user_id`   = '" . $lead['id'] . "', "
                   . ($authuser->isAgent()      ? "`agent` = '"     . $authuser->info('id') . "', " : "`agent` = NULL, ")
                   . ($authuser->isAssociate()  ? "`associate` = '" . $authuser->info('id') . "', " : "`associate` = NULL, ")
                   . "`type`      = '" . mysql_real_escape_string($_POST['type']) . "', "
                   . "`details`   = '" . mysql_real_escape_string($_POST['details']) . "', "
                   . (!empty($share) ? "`share` = 'true', " : "`share` = 'false', ")
                   . "`timestamp` = '" . $datetime . "', "
                   . "`completed` = 'false', "
                   . "`timestamp_created` = NOW();";

            // Execute Query
            if (mysql_query($query)) {
                // Insert ID
                $reminder_id = mysql_insert_id();

                // Get Type Title
                if (!empty($_POST['type'])) {
                    $event_type_query = mysql_query("SELECT `title` FROM `" . TABLE_CALENDAR_TYPES . "` WHERE `id` =  '" . $_POST['type'] . "'");
                    if ($event_type_query) {
                        $event_type = mysql_fetch_assoc($event_type_query);
                    }
                }

                try {
                    if ($can_sync_with_google && !empty($google_calendar)) {
                        // Google Event Object
                        $google_event = new OAuth_Calendar_GoogleEvent();

                        // Populate Event Object
                        $google_event->start = $datetime;
                        $google_event->end = $datetime;
                        $google_event->title = $lead->getNameOrEmail();
                        $google_event->description = $_POST['details'];
                        $google_event->type = $event_type['title'];

                        // Push to Google Calendar
                        $google_event_id = $google_calendar->push($google_event, 'INSERT');

                        if (!empty($google_event_id)) {
                            // Add Google Event ID
                            $query = "UPDATE `" . LM_TABLE_REMINDERS . "` SET "
                                    . "`google_event_id` = '" . mysql_real_escape_string($google_event_id) . "' "
                                    . "WHERE `id` = '" . mysql_real_escape_string($reminder_id) . "';";

                            // Execute Query
                            if (!mysql_query($query)) {
                                $errors[] = 'An error occurred while saving your event.';
                                Log::error('Query Error: ' . mysql_error());
                            }
                        }
                    }

                    if ($can_sync_with_outlook && !empty($microsoft_calendar)) {
                        // Microsoft Event Object
                        $microsoft_event = new OAuth_Calendar_MicrosoftEvent();

                        // Populate Event Object
                        $microsoft_event->start = $datetime;
                        $microsoft_event->end = $datetime;
                        $microsoft_event->title = $lead->getNameOrEmail();
                        $microsoft_event->description = $_POST['details'];
                        $microsoft_event->type = $event_type['title'];

                        // Push to Outlook Calendar
                        $microsoft_event_id = $microsoft_calendar->push($microsoft_event, 'INSERT');

                        if (!empty($microsoft_event_id)) {
                            // Add Microsoft Event ID
                            $query = "UPDATE `" . LM_TABLE_REMINDERS . "` SET "
                                    . "`microsoft_event_id` = '" . mysql_real_escape_string($microsoft_event_id) . "' "
                                    . "WHERE `id` = '" . mysql_real_escape_string($reminder_id) . "';";

                            // Execute Query
                            if (!mysql_query($query)) {
                                $errors[] = 'An error occurred while saving your event.';
                                Log::error('Query Error: ' . mysql_error());
                            }
                        }
                    }
                } catch (Exception_OAuthCalendarError $e) {
                    Log::error($e);
                    $warnings[] = $e->getMessage();
                }

                // Success
                $success[] = 'Your reminder has successfully been added.';

                // Log Event: New Lead Reminder
                $event = new History_Event_Create_LeadReminder(array(
                    'timestamp' => strtotime($datetime),
                    'type'      => $_POST['type'],
                    'details'   => $_POST['details']
                ), array(
                    new History_User_Lead($lead['id']),
                    $authuser->getHistoryUser()
                ));

                // Save to DB
                $event->save();

                $authuser->setNotices($success, $errors);
                header('Location: ?id='  . $lead['id'] . '&success');
                exit;

            // Query Error
            } else {
                $errors[] = 'An error occurred while attempting to add new reminder.';
            }
        }
    }
}

// Lead Reminders
$reminders = array();
$query = "SELECT `r`.*, `t`.`title` AS `type`, IF(`r`.`timestamp` < NOW() AND `r`.`completed` = 'false', 1, 0) AS `weight`, UNIX_TIMESTAMP(`r`.`timestamp`) AS `timestamp`"
       . " FROM `" . LM_TABLE_REMINDERS . "` `r` LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `r`.`type` = `t`.`id`"
       . " WHERE `r`.`user_id` = '" . $lead['id'] . "'"
       // Only Show Agent's Own Reminders (and Shared Notes)
       . ((!$leadAuth->canViewAllLeadContent() || $personal) ? " AND (`r`.`agent` = '" . $authuser->info('id') . "' OR `r`.`share` = 'true')" : "")
       . " ORDER BY `weight` DESC, `r`.`timestamp` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        // Check Delete Permissions
        $row['can_delete'] = $leadAuth->canViewAllLeadContent() || $calendarAuth->canDeleteCalendars($authuser) || $row['agent'] == $authuser->info('id');

        // Check Edit Permissions
        $row['can_edit'] = $leadAuth->canViewAllLeadContent() || $calendarAuth->canManageCalendars($authuser) || $row['agent'] == $authuser->info('id');

        // Added by Agent
        if (!empty($row['agent'])) {
            if ($agent = mysql_query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name`, `permissions_admin` FROM `". LM_TABLE_AGENTS . "` WHERE `id` = '" . $row['agent'] . "';")) {
                $agent = mysql_fetch_assoc($agent);
                if (!empty($agent)) {
                    $row['agent'] = $agent;
                }
            }
        // Added by ISA
        } else if (!empty($row['associate'])) {
            if ($associate = mysql_query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates` WHERE `id` = '" . $row['associate'] . "';")) {
                $associate = mysql_fetch_assoc($associate);
                if (!empty($associate)) {
                    $row['associate'] = $associate;
                }
            }
        }

        // Add to Collection
        $reminders[] = $row;
    }

// Query Error
} else {
    $errors[] = 'An error occurred while loading reminders.';
}

// Reminder Types
$types = array();
$query = "SELECT `id`, `title` FROM `" . TABLE_CALENDAR_TYPES . "`;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $types[] = array('value' => $row['id'], 'title' => $row['title']);
    }
} else {
    $errors[] = 'Error Loading Reminder Types';
}
