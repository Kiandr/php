<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

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
$agentsAuth = new REW\Backend\Auth\AgentsAuth($settings, $authuser);
$associatesAuth = new REW\Backend\Auth\AssociateAuth($settings);
$lendersAuth = new REW\Backend\Auth\LendersAuth($settings);

// Not authorized to view lead
if (!$leadAuth->canViewLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view this leads notes'
    );
}

// Get Not Filters
$_GET['personal'] = isset($_POST['personal']) ? $_POST['personal'] : $_GET['personal'];
if ($authuser->isLender() || (!$leadAuth->canViewAllLeadContent() || isset($_GET['personal']))) {
    $sql_agent_notes = "(`" . ($authuser->isLender() ? 'lender' : 'agent_id') . "` = '" . $authuser->info('id') . "'"
        ." OR `share` = 'true' OR (`agent_id` IS NULL AND `lender` IS NULL AND `associate` IS NULL))";
}

// Everyone can share notes!
$can_share = true;

// Note ID
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];

// Delete Note
if (!empty($_GET['delete'])) {
    $query = "DELETE FROM `" . LM_TABLE_NOTES . "`"
        . " WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "'"
        . " AND `user_id` = '" . $lead['id'] . "'"
        . (!empty($sql_agent_notes) ? ' AND ' . $sql_agent_notes : '') . ";";
    if (mysql_query($query)) {
        $success[] = 'The selected note has been deleted.';
    } else {
        $errors[] = 'An error occurred while trying to delete the selected note.';
    }
}

// Edit Note
if (!empty($_GET['edit'])) {
    // Select Note
    $query = "SELECT * FROM `" . LM_TABLE_NOTES . "`"
        . " WHERE `id` = '" . mysql_real_escape_string($_GET['edit']) . "'"
        . " AND `user_id` = '" . $lead['id'] . "'"
        . (!empty($sql_agent_notes) ? ' AND ' . $sql_agent_notes : '') . ";";
    if ($result = mysql_query($query)) {
        $edit = mysql_fetch_assoc($result);

        // Require Note
        if (!empty($edit)) {
            // Check If Can Note be Shared
            $can_share = !empty($can_share) && !(empty($edit['agent_id']) && empty($edit['lender']) && empty($edit['associate']));

            // Process Submit (Save Changes)
            if (isset($_GET['submit'])) {
                // Required Fields
                $required   = array();
                $required[] = array('value' => 'note', 'title' => 'Note Details');

                // Process Required Fields
                foreach ($required as $require) {
                    if (empty($_POST[$require['value']])) {
                        $errors[] = $require['title'] . ' is a required field.';
                    }
                }

                // Check Errors
                if (empty($errors)) {
                    // Escape HTML
                    $_POST['note'] = htmlspecialchars($_POST['note']);

                    // Share Note
                    if (!empty($can_share)) {
                        $share = !empty($_POST['share']) ? true : false;
                    }

                    // Build INSERT Query
                    $query = "UPDATE `" . LM_TABLE_NOTES . "` SET "
                           . (!empty($can_share) ? "`share` = '" . (!empty($share) ? 'true' : 'false') . "', " : "")
                           . "`note`  = '" . mysql_real_escape_string($_POST['note']) . "'"
                           . " WHERE `id` = '" . $edit['id'] . "';";

                    // Execute Query
                    if (mysql_query($query)) {
                        // Success
                        $success[] = 'The selected note has successfully been saved.';

                        // Unset Data
                        unset($edit, $_POST['note'], $_POST['share']);

                    // Query Error
                    } else {
                        $errors[] = 'An error occurred while attempting to save note.';
                    }
                }
            }
        }

    // Query Error
    } else {
        $errors[] = 'Error Occurred while located selected Lead Note.';
    }
} else {
    // Process Submit (Add New Note)
    if (isset($_GET['submit'])) {
        // Required Fields
        $required   = array();
        $required[] = array('value' => 'note', 'title' => 'Note Details');

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = $require['title'] . ' is a required field.';
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Escape HTML
            $_POST['note'] = htmlspecialchars($_POST['note']);

            // Share Note
            $share = !empty($can_share) && !empty($_POST['share']) ? true : false;

            // Build INSERT Query
            $query = "INSERT INTO `" . LM_TABLE_NOTES . "` SET "
                   . "`user_id`   = '" . $lead['id'] . "', "
                   . ($authuser->isAgent()      ? "`agent_id`  = '" . $authuser->info('id') . "', " : "")   // Added by Agent
                   . ($authuser->isLender()     ? "`lender`    = '" . $authuser->info('id') . "', " : "")   // Added by Lender
                   . ($authuser->isAssociate()  ? "`associate` = '" . $authuser->info('id') . "', " : "")   // Added by ISA
                   . "`note`	  = '" . mysql_real_escape_string($_POST['note']) . "', "
                   . "`share`	  = '" . (!empty($share) ? 'true' : 'false') . "', "
                   . "`timestamp` = NOW();";

            // Execute Query
            if (mysql_query($query)) {
                // Success
                $success[] = 'The new note has successfully been posted.';

                // Log Event: New Lead Note
                $event = new History_Event_Create_LeadNote(array(
                    'details' => $_POST['note'],
                    'share'   => $share
                ), array(
                    new History_User_Lead($lead['id']),
                    $authuser->getHistoryUser()
                ));

                // Save to DB
                $event->save();

            // Query Error
            } else {
                $errors[] = 'An error occurred while attempting to post new note.';
            }
        }
    }
}

// Lead Notes
$history = array();
$query = "SELECT `id`, `type`, `note`, `agent_id`, `associate`, `lender`, `share`, UNIX_TIMESTAMP(`timestamp`) AS `date` FROM `" . LM_TABLE_NOTES
    . "` WHERE `user_id` = '" . $lead['id'] . "'" . (!empty($sql_agent_notes) ? ' AND ' . $sql_agent_notes : '') . " ORDER BY `timestamp` DESC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        // Check Delete Permissions
        $row['can_delete'] = (
            $leadAuth->canViewAllLeadContent()
            || ($authuser->isAssociate() && $row['associate'] == $authuser->info('id'))
            || ($authuser->isAgent() && $row['agent_id'] == $authuser->info('id'))
            || ($authuser->isLender() && $row['lender'] == $authuser->info('id'))
        );

        // Check Edit Permissions
        $row['can_edit'] = (
            $leadAuth->canViewAllLeadContent()
            || ($authuser->isAssociate() && $row['associate'] == $authuser->info('id'))
            || ($authuser->isAgent() && $row['agent_id'] == $authuser->info('id'))
            || ($authuser->isLender() && $row['lender'] == $authuser->info('id'))
        );

        // Added by Agent
        if (!empty($row['agent_id'])) {
            if ($agent = mysql_query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `". LM_TABLE_AGENTS . "` WHERE `id` = '" . $row['agent_id'] . "';")) {
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

        // Added by Lender
        } else if (!empty($row['lender'])) {
            if ($lender = mysql_query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `lenders` WHERE `id` = '" . $row['lender'] . "';")) {
                $lender = mysql_fetch_assoc($lender);
                if (!empty($lender)) {
                    $row['lender'] = $lender;
                }
            }
        }

        // Add to Collection
        $history[date('d-m-Y', $row['date'])][] = $row;
    }
} else {
    // Query Error
    $errors[] = 'Error Occurred while loading Lead Notes.';
}
