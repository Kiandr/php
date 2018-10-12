<?php

use REW\Core\Interfaces\ModuleInterface;
use REW\Backend\Partner\Firstcallagent as Partner_Firstcallagent;

// Include Backend Configuration
include_once dirname(__FILE__) . '/../../../common.inc.php';

// Require Authorization
if (!$authuser->isValid()) {
    die('{}');
}

$db = DB::get();

// Get Authorization Managers
$settings = Settings::getInstance();
$leadsAuth           = new REW\Backend\Auth\LeadsAuth($settings);
$teamsAuth           = new REW\Backend\Auth\TeamsAuth($settings);
$teamsLeadsAuth      = new REW\Backend\Auth\Lead\TeamLeadAuth($settings);
$calendarAuth        = new REW\Backend\Auth\CalendarAuth($settings);

// Authorized to Delete Leads
$can_delete = $leadsAuth->canDeleteLeads($authuser);

// Authorized to Assign Leads
$can_assign = $leadsAuth->canAssignLeads($authuser);

// Authorized to Email Leads
$can_email = $leadsAuth->canEmailLeads($authuser);

// Can Share
$can_share = $leadsAuth->canShareLeads($authuser);

// Success
$success = array();

// Errors
$errors = array();

// Warnings
$warnings = array();

// JSON Response
$json = array();

if (isset($_GET['notifications'])) {
    $notifications = array();
    // FCA lead limit warning
    if ($authuser->isSuperAdmin()) {
        $container = Container::getInstance();

        $fca = $container->make(Partner_Firstcallagent::class);
        if ($fca->isEnabled() && $fca->hasAPIKey()) {
            $fca_data = $fca->leadLimit();

            if (!empty($fca_data['message'])) {
                $notifications['warning'][]= $fca_data['message'];
            }
        }
    }
    if (!empty($notifications)) {
        $json['notifications'] = $notifications;
    }
}

/**
 * Load Module
 */
if (isset($_GET['module']) && !empty($_GET['module'])) {
    // Module Options
    $options = isset($_GET['options']) ? $_GET['options'] : false;

    // Set AJAX
    $options['ajax'] = true;

    // Load Module
    $container = Container::getInstance();
    $module = null;
    if ($container->has($_GET['module'])) {
        // Load the already-built module, if possible. This is a minor efficiency improvement so that 2 modules don't
        // get created for external packages, but, it is also the only way that said external packages can access
        // options other than superglobals or having a separate controller for ajax and html.
        $module = $container->get($_GET['module']);
        if ($module instanceof ModuleInterface && $module->getId() == $_GET['module']) {
            foreach ($options as $key => $val) {
                $module->config($key, $val);
            }
        } else {
            $module = null;
        }
    }
    if (!$module) {
        // Module Path (Use Backend). We don't want to set the path of an already-loaded module because it probably
        // doesn't exist in rew-framework.
        $options['path'] = dirname(__FILE__) . '/../../../inc/modules/' . escapeshellcmd($_GET['module']) . '/';

        $module = $container->make(ModuleInterface::class, ['id' => $_GET['module'], 'config' => $options]);
    }

    // Module CSS, JS & HTML
    $module = array(
        'uid'        => $module->getUID(),
        'stylesheet' => $module->css(),
        'javascript' => $module->javascript(),
        'content'    => $module->display(false)
    );

    // Minify HTML
    if (!empty($module['content'])) {
        if (!empty(Settings::getInstance()->SETTINGS['MINIFY_HTML'])) {
            $module['content'] = Minify_HTML::minify($module['content']);
        }
    } else {
        unset($module['content']);
    }

    // Minify CSS
    if (!empty($module['stylesheet'])) {
        if (!empty(Settings::getInstance()->SETTINGS['MINIFY_CSS'])) {
            $module['stylesheet'] = Minify_CSS::minify($module['stylesheet']);
        }
    } else {
        unset($module['stylesheet']);
    }

    // Minify Javascript
    if (!empty($module['javascript'])) {
        if (!empty(Settings::getInstance()->SETTINGS['MINIFY_JS'])) {
            $module['javascript'] = JSMin::minify($module['javascript']);
        }
    } else {
        unset($module['javascript']);
    }

    // Return Module
    $json['module'] = $module;
}

/**
 * Perform Group Action
 *  - Requires $_POST['leads']
 *  - Returns $json['leads']
 *  - Returns $json['action']
 */
if (!empty($_GET['action']) && !empty($_POST['leads'])) {
    // App Database
    $db = DB::get();

    // Return Leads in JSON
    $json['leads'] = array();

    // Selected Leads
    $leads = array();
    foreach (array_unique($_POST['leads']) as $lead) {
        $lead = $db->fetch("SELECT * FROM `users` WHERE `id` = '" . $lead . "';");

        // Unset Invalid Leads
        if (!$leadsAuth->canManageLeads($authuser)) {
            if (!$leadsAuth->canManageOwn($authuser)) {
                //Agent lacks required permissions to edit this lead
                unset($lead);
            }
        }

        // Agent Mode: Only Show Assigned Leads or Team Leads
        if (!$leadsAuth->canManageLeads($authuser) && $lead['agent'] != $authuser->info('id')) {
            if (!$teamsAuth->canViewTeamLeads() || !$lead['share_lead']) {
                //Agent lacks required permissions to edit this lead
                unset($lead);
            }

            // Not authorized to fully edit team lead
            if (!$teamsLeadsAuth->checkFullyEditableAgent($authuser, $lead['agent'])) {
                //Agent lacks required permissions to edit this lead
                unset($lead);
            }
        }

        // If lead is set
        if (!empty($lead)) {
            $leads[] = new Backend_Lead($lead);
        }
    }

    // Action Type
    switch ($_GET['action']) {

        /**
         * Delete Leads
         *  - Requires $can_delete
         */
        case 'delete':
            // Return Action Type
            $json['action'] = 'delete';

            try {
                // Permission Required
                if (empty($can_delete)) {
                    throw new Exception_PermissionRequired('You do not have permission to perform this action.');
                }

                // Process Leads..
                foreach ($leads as $lead) {
                    // Delete Lead
                    $lead->delete($authuser);

                    // Return Lead ID
                    $json['leads'][] = $lead->getId();

                    // Success
                    $success[] = '<strong>' . $lead->getNameOrEmail() . '</strong> has successfully been deleted.';
                }
            } catch (PDOException $e) {
                $errors[] = $lead->getNameOrEmail() . ' could not be deleted.';
                Log::error($e);
            } catch (Exception_PermissionRequired $e) {
                $errors[] = $e->getMessage();
            }

            break;

        /**
         * Assign Leads to Agent
         *  - Requires $can_assign
         *  - Requires $_POST['agent_id']
         *  - Returns $json['agent']
         */
        case 'assign':
            // Return Action Type
            $json['action'] = 'assign';

            try {
                // Permission Required
                if (empty($can_assign)) {
                    throw new Exception_PermissionRequired('You do not have permission to perform this action.');
                }

                // Assign to Agent
                if (!empty($_POST['agent_id'])) {
                    // Locate Agent
                    $agent = Backend_Agent::load($_POST['agent_id']);
                    if (empty($agent)) {
                        throw new Exception_RowNotFound('Agent not found.');
                    }

                    // Return Assigned Agent
                    $json['agent'] = array('id' => $agent->getId(), 'name' => $agent->getName());

                    // Assign Leads to Agent
                    $assigned = $agent->assign($leads, $authuser, $errors);

                    // Assigned Leads
                    if (!empty($assigned)) {
                        $json['leads'] = array_map(function ($lead) use (&$success, $agent) {
                            $success[] = $lead->getNameOrEmail() . ' has been assigned to Agent ' . $agent->getName() . '.';
                            return $lead->getId();
                        }, $assigned);
                    }

                    // Assign to Lender
                } else if (!empty($_POST['lender_id'])) {
                    // Locate Lender Row
                    $lender = Backend_Lender::load($_POST['lender_id']);
                    if (empty($lender)) {
                        throw new Exception_RowNotFound('Lender not found.');
                    }

                    // Return Assigned Lender
                    $json['lender'] = array('id' => $lender->getId(), 'name' => $lender->getName());

                    // Assign Leads to Lender
                    $assigned = $lender->assign($leads, $authuser, $errors);

                    // Assigned Leads
                    if (!empty($assigned)) {
                        $json['leads'] = array_map(function ($lead) use (&$success, $lender) {
                            $success[] = $lead->getNameOrEmail() . ' has been assigned to Lender ' . $lender->getName() . '.';
                            return $lead->getId();
                        }, $assigned);
                    }
                }
            } catch (Exception_PermissionRequired $e) {
                $errors[] = $e->getMessage();
            } catch (Exception_RowNotFound $e) {
                $errors[] = $e->getMessage();
            }

            break;

        /**
         * Assign Leads to Group
         *  - Checks $can_assign
         *  - Requires $_POST['group_id']
         *  - Returns $json['group']
         */
        case 'group':
            // Return Action Type
            $json['action'] = 'group';

            try {
                // Locate Group Row
                $group = $db->getCollection('groups')->getRow($_POST['group_id']);
                if (empty($group)) {
                    throw new Exception_RowNotFound('Group not found.');
                }

                // Return Group Row (Used in Success Callback)
                $json['group'] = $group;

                // Process Leads..
                foreach ($leads as $lead) {
                    // Check Group Status
                    $assigned = $db->fetch("SELECT * FROM `users_groups` WHERE `group_id` = '" . $group['id'] . "' AND user_id = '" . $lead->getId() . "';");


                    // Current lead Owner
                    $agent = Backend_Agent::load($lead->info('agent'));

                    // Current lead groups
                    $groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $lead->getID());

                    // Un-Assign from Group
                    if (!empty($_POST['unassign'])) {
                        // Lead is not in this group
                        if (empty($assigned)) {
                            continue;
                        }

                        // Remove Lead from Group
                        $lead->removeGroup($group, $authuser);

                        // Success
                        $success[] = $lead->getNameOrEmail() . ' has been removed from "' . $group['name'] . '".';

                        // Un-Assigned
                        $json['unassign'] = true;

                        // Sync all partners
                        Hooks::hook(Hooks::HOOK_LEAD_SYNC_PARTNER_REMOVING_GROUP)->run($lead, $agent, $group, $groups);

                        // Assign to Group
                    } else {
                        // Lead is already in this group
                        if (!empty($assigned)) {
                            continue;
                        }

                        // Assign Lead to Group
                        $lead->assignGroup($group, $authuser);

                        // Success
                        $success[] = $lead->getNameOrEmail() . ' has been added to "' . $group['name'] . '".';

                        // Sync all partners
                        Hooks::hook(Hooks::HOOK_LEAD_SYNC_PARTNER_ADDING_GROUP)->run($lead, $agent, $group, $groups);

                    }

                    // Return Lead ID
                    $json['leads'][] = $lead->getId();
                }
            } catch (PDOException $e) {
                $errors[] = $lead->getNameOrEmail() . ' could not be added to "' . $group['name'] . '".';
                Log::error($e);
            } catch (Exception_RowNotFound $e) {
                $errors[] = $e->getMessage();
            }

            break;


        /**
         * Message Lead
         */
        case 'message':
            // Return Action Type
            $json['action'] = 'message';

            try {
                // Require Message
                if (!Validate::stringRequired($_POST['message'])) {
                    $errors[] = 'You must provide a message.';

                    // Require Message
                } else if (!Validate::stringRequired($_POST['subject'])) {
                    $errors[] = 'You must provide a subject.';
                } elseif (isset($_POST['reply']) && !isset($_POST['replyTo'])) {
                    $errors[] = 'You must provide the message this is a replying to.';
                } else {
                    // Return Subject
                    $json['subject'] = $_POST['subject'];

                    // Return Message
                    $json['message'] = $_POST['message'];

                    // Process Leads..
                    foreach ($leads as $lead) {
                        $insertMessage = $db->prepare("INSERT INTO `" . LM_TABLE_MESSAGES . "` SET "
                            . "`user_id`    = ?, "
                            . "`agent_id`   = ?, "
                            . "`subject`    = ?, "
                            . "`message`    = ?, "
                            . "`reply`      = " . (isset($_POST['reply']) ? "'Y'" : "'N'") . ", "
                            . "`sent_from`  = 'agent', "
                            . "`agent_read` = 'Y', "
                            . "`timestamp`  = NOW();");

                        if ($insertMessage->execute([
                            $lead->getId(),
                            $authuser->info('id'),
                            $json['subject'],
                            $json['message']
                        ])) {
                            $insert_id = $db->lastInsertId();
                            $json['id'] = $insert_id;

                            //Create new category
                            if ($_POST['reply']) {
                                $category = $db->fetch("SELECT category FROM `" . LM_TABLE_MESSAGES . "` WHERE id = :id;", ["id" => $_POST['replyTo']]);
                                $insertMessage = $db->prepare("UPDATE `" . LM_TABLE_MESSAGES . "` SET `category` = :category WHERE `id` = :insert_id;");
                                $insertMessage->execute([
                                    'category' => isset($_POST['reply']) ? $category['category'] : 0,
                                    'insert_id' => $insert_id
                                ]);
                            } else {
                                $insertMessage = $db->prepare("UPDATE `" . LM_TABLE_MESSAGES . "` SET `category` = :insert_id WHERE `id` = :insert_id;");
                                $insertMessage->execute(['insert_id' => $insert_id]);
                            }

                            // Build Email Message
                            $message = '<p><b>' . $authuser->info('first_name') . ' ' . $authuser->info('last_name') . '</b> '
                                . (isset($_POST['reply']) ? 'has replied to one of your messages:' : 'has sent you a new message:') . '</p>';
                            $message .= '<p>' . $_POST['message'] . '<br />';
                            $message .= '<p>' . str_repeat('-', 50) . '</p>';
                            $message .= '<p>To reply to this message and to view any other messages you might have, log-in to your Private Control Panel at <a href="' . $settings->SETTINGS['URL_IDX'] . '?dashboard">' . $settings->SETTINGS['URL_IDX'] . '</a></p>';
                            $message .= '<p><b>As a Reminder... </b><br />';
                            $message .= 'Username: ' . $lead['email'] . '<br />';
                            $message .= '<p>Thank you for working with us!</p>';

                            // Build Mailer
                            $mailer = new REW\Backend\Email\Email(
                                $authuser,
                                ['email_subject' => htmlspecialchars_decode((isset($_POST['reply']) ? 'RE: ' : '') . $_POST['subject']),
                                    'email_message' => $message]
                            );

                            /* Send Email */
                            $recipient = [
                                'id'        => $lead['id'],
                                'first_name'=> $lead['first_name'],
                                'last_name' => $lead['last_name'],
                                'email'     => $lead['email']
                            ];
                            $mailer_errors = [];
                            $mailer->send([$recipient], REW\Backend\Email\Email::TYPE_LEADS, $errors);

                            // Check for mailer errors
                            if (empty($errors)) {
                                /* Unset Data */
                                unset($_POST['subject'], $_POST['message']);

                                // Return Lead ID
                                $json['leads'][] = $lead->getId();

                                // Success
                                $success[] = 'A message has been sent to <strong>' . $lead->getNameOrEmail() . '</strong>.';
                            }
                        }
                    }
                }

                // DB Error
            } catch (PDOException $e) {
                $errors[] = 'A message could not be sent to ' . $lead->getNameOrEmail();
                Log::error($e);
            }

            break;

        /**
         * Update Lead Status
         */
        case 'status':
            // Return Action Type
            $json['action'] = 'status';

            try {
                // Require Status
                if (!Validate::stringRequired($_POST['status'])) {
                    $errors[] = 'You must select a status.';

                    // Require Reason for Rejection
                } elseif ($_POST['status'] == 'rejected' && !Validate::stringRequired($_POST['rejectwhy'])) {
                    $errors[] = 'You must provide a reason for rejection.';
                } else {
                    // Return Status
                    $json['status'] = $_POST['status'];

                    // Process Leads..
                    foreach ($leads as $lead) {
                        // Save Reason for Rejection
                        if ($_POST['status'] == 'rejected') {
                            // Update Lead Row
                            mysql_query("UPDATE `users` SET `rejectwhy` = '" . mysql_real_escape_string($_POST['rejectwhy']) . "' WHERE `id` = '" . mysql_real_escape_string($lead->getId()) . "';");

                            // Update Lead Instance
                            $lead->info('rejectwhy', $_POST['rejectwhy']);

                            // Fire Lead Rejected Hook
                            Hooks::hook(Hooks::HOOK_AGENT_LEAD_REJECT)->run($lead->getRow());
                        }

                        // Update Lead Status
                        $lead->status($_POST['status'], $authuser);

                        // Return Lead ID
                        $json['leads'][] = $lead->getId();

                        // Success
                        $success[] = '<strong>' . $lead->getNameOrEmail() . '</strong> has successfully been updated.';
                    }
                }

                // DB Error
            } catch (PDOException $e) {
                $errors[] = $lead->getNameOrEmail() . ' could not be updated.';
                Log::error($e);
            }

            break;

        /**
         * Assign Action Plan
         *  - Requires $_POST['action_plan']
         */
        case 'action_plan':
            if ($leadsAuth->canAssignActionPlans($authuser)) {
                // Return Action Type
                $json['action'] = 'action_plan';

                // Load Action Plan
                if ($action_plan = Backend_ActionPlan::load($_POST['action_plan'])) {
                    // Add new plan to JSON response so the lead row can be updated
                    $json['action_plan'] = array(
                        'id'    => $action_plan['id'],
                        'name'  => (strlen($action_plan['name']) > 12 ? substr($action_plan['name'], 0, 12) . '...' : $action_plan['name']),
                        'style' => $action_plan['style']
                    );

                    // Process Leads
                    foreach ($leads as $lead) {
                        // Un-Assign Action Plan
                        if (!empty($_POST['unassign'])) {
                            if ($action_plan->unassign($lead->getId(), $authuser)) {
                                $success[] = 'Action Plan "' . $action_plan->info('name') . '" unassigned from ' . $lead->getName() . '!';
                                $json['unassign'] = true;
                            }
                            // Assign Action Plan
                        } else {
                            // Attempt to assign
                            try {
                                if ($action_plan->assign($lead->getId(), $authuser)) {
                                    $success[] = 'Action Plan "' . $action_plan->info('name') . '" assigned to ' . $lead->getName() . '!';
                                }
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        }

                        // Return Lead ID
                        $json['leads'][] = $lead->getId();
                    }
                } else {
                    $errors[] = 'Error! Unable to load specified action plan!';
                }
            } else {
                $error[] = 'Error! Insufficient permissions to assign or unassign an action plan.';
            }

            break;
    }
}

/**
 * Perform Lead Action
 *  - $_POST['lead']
 */
if (!empty($_GET['action']) && !empty($_POST['lead'])) {
    // Select Lead
    $query = "SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = '" . mysql_real_escape_string($_POST['lead']) . "';";
    if ($result = mysql_query($query)) {
        $lead = mysql_fetch_assoc($result);

        // Require Lead
        if (!empty($lead)) {
            $lead['name'] = Format::trim($lead['first_name'].' '.$lead['last_name']);
            $lead['name'] = $lead['name'] ?: $lead['email'];

            // Action Type
            switch ($_GET['action']) {

                /**
                 * Update Lead Quick Notes
                 */
                case 'notes':
                    // Update Lead Row
                    $query = "UPDATE `" . LM_TABLE_LEADS . "` SET `notes` = '" . mysql_real_escape_string($_POST['notes']) . "' WHERE `id` = '" . mysql_real_escape_string($lead['id']) . "';";
                    if (mysql_query($query)) {
                        // Log Event: Track Lead Change
                        $event = new History_Event_Update_Lead(array(
                            'field' => 'notes',
                            'old' => $lead['notes'],
                            'new' => $_POST['notes']
                        ), array(
                            new History_User_Lead($lead['id']),
                            $authuser->getHistoryUser()
                        ));

                        // Save to DB
                        $event->save();

                        // Success
                        $success[] = 'Your changes  have successfully been saved.';

                        // Query Error
                    } else {
                        $errors[] = 'An error occurred while attempting to save your notes.';
                    }

                    break;

                /**
                 *
                 * Add Lead Note (General)
                 *  - Require $_POST['note']
                 *
                 */
                case 'note':
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
                        // Insert Lead Note
                        $query = "INSERT INTO `" . LM_TABLE_NOTES . "` SET "
                            . ($authuser->isAgent()      ? "`agent_id`  = '" . $authuser->info('id') . "', " : "")   // Added by Agent
                            . ($authuser->isLender()     ? "`lender`    = '" . $authuser->info('id') . "', " : "")   // Added by Lender
                            . ($authuser->isAssociate()  ? "`associate` = '" . $authuser->info('id') . "', " : "")   // Added by ISA
                            . "`user_id`   = '" . mysql_real_escape_string($lead['id'])    . "', "
                            . "`note`      = '" . mysql_real_escape_string($_POST['note']) . "', "
                            . "`share`	 = '" . (!empty($_POST['share']) ? 'true' : 'false') . "', "
                            . "`timestamp` = NOW();";

                        // Execute Query
                        if (mysql_query($query)) {
                            // Log Event: New Lead Note
                            $event = new History_Event_Create_LeadNote(array(
                                'share'   => !empty($_POST['share']),
                                'details' => $_POST['note']
                            ), array(
                                new History_User_Lead($lead['id']),
                                $authuser->getHistoryUser()
                            ));

                            // Save to DB
                            $event->save();

                            // Success
                            $success[] = 'Your note has successfully been posted.';

                            // Query Error
                        } else {
                            $errors[] = 'An error occurred while attempting to post your note.';
                        }
                    }

                    break;

                /**
                 *
                 * Track Phone Call
                 *  - Requires $_POST['type'], $_POST['details']
                 *  - Checks $_POST['type'] for: call, attempt, voicemail, invalid
                 *
                 */
                case 'call':
                    // Required Fields
                    $required   = array();
                    $required[] = array('value' => 'type', 'title' => 'Call Outcome');
                    $required[] = array('value' => 'details', 'title' => 'Call Details');

                    // Process Required Fields
                    foreach ($required as $require) {
                        if (empty($_POST[$require['value']])) {
                            $errors[] = $require['title'] . ' is a required field.';
                        }
                    }

                    // Call Type
                    if (!empty($_POST['type'])) {
                        switch ($_POST['type']) {
                            // Talked to Lead
                            case 'call':
                                $phone = 'History_Event_Phone_Contact';
                                break;
                            // Call Attempt
                            case 'attempt':
                                $phone = 'History_Event_Phone_Attempt';
                                break;
                            // Received Voicemail / Left Message
                            case 'voicemail':
                                $phone = 'History_Event_Phone_Voicemail';
                                break;
                            // Bad Phone Number
                            case 'invalid':
                                $phone = 'History_Event_Phone_Invalid';
                                break;
                            // Error
                            default:
                                $errors[] = 'Invalid Call Type';
                                break;
                        }
                    }

                    // Check Errors
                    if (empty($errors)) {
                        try {
                            // Log Event: Track Phone Call
                            $event = new $phone (array(
                                'details' => $_POST['details']
                            ), array(
                                new History_User_Lead($lead['id']),
                                $authuser->getHistoryUser()
                            ));

                            // Save to DB
                            $event->save();

                            // Success
                            $success[] = 'Your phone call has successfully been logged.';

                            // Run hook
                            Hooks::hook(Hooks::HOOK_AGENT_CALL_OUTGOING)->run($authuser->getInfo(), $lead, $_POST['type'], $_POST['details']);

                            // Error Occurred
                        } catch (Exception $e) {
                            $errors[] = 'An error occurred while logging your phone call.';
                            if (Settings::isREW()) {
                                $errors[] = $e->getMessage();
                            }
                        }
                    }

                    break;

                /**
                 *
                 * Add Lead Reminder
                 *  - Requires $_POST['timestamp'], $_POST['type'], $_POST['details']
                 *
                 */
                case 'reminder':
                    // Required Fields
                    $required   = array();
                    $required[] = array('value' => 'timestamp', 'title' => 'Reminder Date');
                    $required[] = array('value' => 'type',      'title' => 'Reminder Type');
                    $required[] = array('value' => 'details',   'title' => 'Reminder Details');

                    // Process Required Fields
                    foreach ($required as $require) {
                        if (empty($_POST[$require['value']])) {
                            $errors[] = $require['title'] . ' is a required field.';
                        }
                    }

                    // parse DateTime from timestamp
                    if (!empty($_POST['timestamp'])) {
                        try {
                            $timestamp = strtotime($_POST['timestamp']);
                            if (empty($timestamp)) {
                                throw new Exception('Invalid Reminder Date');
                            }
                        } catch (Exception $e) {
                            $errors[] = 'Invalid Reminder Date';
                        }
                    }

                    // Check Errors
                    if (empty($errors)) {
                        // Reminder Date & Time
                        $timestamp = date('Y-m-d H:i:s', $timestamp);

                        // Share
                        $share = (!empty($can_share) && !empty($_POST['share'])) ? true : false;

                        // Create New Reminder
                        $query = "INSERT INTO `" . LM_TABLE_REMINDERS . "` SET "
                            . ($authuser->isAgent()      ? "`agent`     = '" . $authuser->info('id') . "', " : "")
                            . ($authuser->isAssociate()  ? "`associate` = '" . $authuser->info('id') . "', " : "")
                            . "`user_id`   = '" . $lead['id'] . "', "
                            . "`type`      = '" . mysql_real_escape_string($_POST['type']) . "', "
                            . "`timestamp` = '" . mysql_real_escape_string($timestamp) . "', "
                            . "`details`   = '" . mysql_real_escape_string($_POST['details']) . "',"
                            . "`share` = '" . (!empty($share) ? "true" : "false") . "', "
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
                                if ($calendarAuth->canSyncWithGoogleCalander($authuser) || $calendarAuth->canSyncWithOutlookCalander($authuser)) {
                                    // Create Page to pass to calendar constructor
                                    $page = new Page(array('skin' => 'Skin_Backend'));

                                    if ($calendarAuth->canSyncWithGoogleCalander($authuser)) {
                                        // Google Calendar Object
                                        $google_calendar = new OAuth_Calendar_Google($page, $authuser);

                                        if (!empty($google_calendar)) {
                                            // Google Event Object
                                            $google_event = new OAuth_Calendar_GoogleEvent();

                                            // Populate Event Object
                                            $google_event->start = $timestamp;
                                            $google_event->end = $timestamp;
                                            $google_event->title = $lead['name'];
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
                                    }

                                    if ($calendarAuth->canSyncWithOutlookCalander($authuser)) {
                                        // Microsoft Calendar Object
                                        $microsoft_calendar = new OAuth_Calendar_Microsoft($page, $authuser);

                                        if (!empty($microsoft_calendar)) {
                                            // Microsoft Event Object
                                            $microsoft_event = new OAuth_Calendar_MicrosoftEvent();

                                            // Populate Event Object
                                            $microsoft_event->start = $timestamp;
                                            $microsoft_event->end = $timestamp;
                                            $microsoft_event->title = $lead['name'];
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
                                    }
                                }
                            } catch (Exception_OAuthCalendarError $e) {
                                Log::error($e);
                                $warnings[] = $e->getMessage();
                            }

                            // Log Event: New Lead Reminder
                            $event = new History_Event_Create_LeadReminder(array(
                                'timestamp' => strtotime($timestamp),
                                'type'      => $_POST['type'],
                                'details'   => $_POST['details']
                            ), array(
                                new History_User_Lead($lead['id']),
                                $authuser->getHistoryUser()
                            ));

                            // Save to DB
                            $event->save();

                            // Success
                            $success[] = 'Your lead reminder has successfully been set.';

                            // Query Error
                        } else {
                            $errors[] = 'An error occurred while attempting to save your reminder.';
                        }
                    }

                    break;

                /**
                 *
                 * Add Recommended Listing
                 *  - Requires $_POST['mls_number']
                 *  - Checks $_POST['notify'] to send Email, Optional $_POST['message'] Included
                 *
                 */
                case 'listing':
                    // Escape Data
                    $_POST['mls_number'] = htmlspecialchars(trim($_POST['mls_number']));
                    $_POST['notify']     = htmlspecialchars($_POST['notify']);
                    $_POST['message']    = htmlspecialchars($_POST['message']);

                    // Replace Tags with lead and agent info
                    try {
                        $lead = $db->fetch(
                            "SELECT * FROM " . LM_TABLE_LEADS . " WHERE `id` = :id;",
                            ['id' => $_POST['lead']]
                        );
                    } catch(PDOException $exception) {
                        $errors[] = "An error occured while loading the lead.";
                    }

                    if (!empty($lead)) {
                        $verify = sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], Format::toGuid($lead['guid']));

                        $tags = [
                            '{first_name}'=> $lead['first_name'],
                            '{last_name}'=> $lead['last_name'],
                            '{email}'=> $lead['email'],
                            '{verify}'=> $verify,
                            '{signature}'=> $authuser->info('signature')
                        ];

                        foreach($tags as $tag => $val ) {
                            $_POST['message'] = str_replace($tag, $val, $_POST['message']);
                        }

                    }

                    // Required Fields
                    $required   = array();
                    $required[] = array('value' => 'mls_number', 'title' => Lang::write('MLS') . ' Number');

                    // Process Required Fields
                    foreach ($required as $require) {
                        if (empty($_POST[$require['value']])) {
                            $errors[] = $require['title'] . ' is a required field.';
                        }
                    }

                    // Check Duplicate
                    if (!empty($_POST['mls_number'])) {
                        $query = "SELECT `id`, `agent_id`, `associate` FROM `" . LM_TABLE_SAVED_LISTINGS . "` WHERE `user_id` = '" . $lead['id'] . "' AND `mls_number` = '" . mysql_real_escape_string($_POST['mls_number']) . "';";
                        if ($result = mysql_query($query)) {
                            $duplicate = mysql_fetch_assoc($result);
                            // Already Exists
                            if (!empty($duplicate)) {
                                // Recommended Listing
                                if (!empty($duplicate['agent_id']) || !empty($duplicate['associate'])) {
                                    $errors[] = 'This listing has already been recommended to this lead.';
                                    // Saved Favorite
                                } else {
                                    $errors[] = 'This listing is already a ' . Locale::spell('favorite') . ' listing to this lead.';
                                }
                            }
                        }
                    }

                    // Check Errors
                    if (empty($errors)) {
                        $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($_POST['mls_number']) . "'";

                        // Any global criteria
                        $idx->executeSearchWhereCallback($search_where);

                        // Locate Listing
                        $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $search_where . " LIMIT 1;");

                        // Unknown Listing
                        if (empty($listing)) {
                            $errors[] = 'Listing Not Found: ' . Lang::write('MLS_NUMBER') . $_POST['mls_number'];
                        } else {
                            // Parse Listing
                            $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                            // Save Recommended Listing to Lead Favorites
                            $query = "INSERT INTO `" . LM_TABLE_SAVED_LISTINGS . "` SET "
                                . "`user_id`     = '" . $lead['id'] . "', "
                                . ($authuser->isAgent()      ? "`agent_id`  = '" . $authuser->info('id') . "', "     : "`agent_id` = NULL, ") // Added by Agent
                                . ($authuser->isAssociate()  ? "`associate`  = '" . $authuser->info('id') . "', "    : "`associate` = NULL,") // Added by ISA
                                . "`mls_number`  = '" . mysql_real_escape_string($listing['ListingMLS']) . "', "
                                . "`table`       = '" . mysql_real_escape_string($idx->getTable()) . "', "
                                . "`idx`         = '" . mysql_real_escape_string($idx->getName()) . "', "
                                . "`type`        = '" . mysql_real_escape_string($listing['ListingType']) . "', "
                                . "`city`        = '" . mysql_real_escape_string($listing['AddressCity']) . "', "
                                . "`subdivision` = '" . mysql_real_escape_string($listing['AddressSubdivision']) . "', "
                                . "`bedrooms`    = '" . mysql_real_escape_string($listing['NumberOfBedrooms']) . "', "
                                . "`bathrooms`   = '" . mysql_real_escape_string($listing['NumberOfBathrooms']) . "', "
                                . "`sqft`        = '" . mysql_real_escape_string($listing['NumberOfSqFt']) . "', "
                                . "`price`       = '" . mysql_real_escape_string($listing['ListingPrice']) . "', "
                                . "`timestamp`   = NOW()"
                                . ";";

                            // Execute Query
                            if (mysql_query($query)) {
                                // Log Event: Agent Recommended Listing
                                $event = new History_Event_Action_SavedListing(array(
                                    'listing' => $listing
                                ), array(
                                    new History_User_Lead($lead['id']),
                                    $authuser->getHistoryUser()
                                ));

                                // Save to DB
                                $event->save();

                                // Send Notification to Lead
                                if (!empty($_POST['notify'])) {
                                    // Setup Mailer
                                    $mailer = new Backend_Mailer_ListingRecommendation(array(
                                        'listing'   => $listing,
                                        'message'   => $_POST['message'],                   // Email Message
                                        'signature' => $authuser->info('signature'),        // Signature
                                        'append'    => ($authuser->info('add_sig') == 'Y')  // Append Signature
                                    ));

                                    // Load Agent
                                    $agent = new Backend_Agent($authuser->getInfo());

                                    // Check Outgoing Notification Settings for Listing Recommendations
                                    $mailer = $agent->checkOutgoingNotifications($mailer, Backend_Agent_Notifications::OUTGOING_LISTING_RECOMMEND);

                                    // Set Sender
                                    $mailer->setSender($authuser->info('email'), $authuser->getName());

                                    // Set Recipient
                                    $mailer->setRecipient($lead['email'], Format::trim($lead['first_name'] . ' ' . $lead['last_name']));

                                    // Send Email
                                    if ($mailer->Send()) {
                                        // Success
                                        $success[] = 'Listing Recommendation has successfully been sent.';

                                        // Mailer Error
                                    } else {
                                        $errors[] = 'An error occurred while trying to send your recommendation.';
                                    }
                                } else {
                                    // Success
                                    $success[] = 'Listing Recommendation has successfully been saved.';
                                }

                                // Query Error
                            } else {
                                $errors[] = 'An error occurred while trying to save your recommendation.';
                            }
                        }
                    }

                    break;

                /**
                 *
                 * Add Recommended Listing
                 *  - Requires (array) $_POST['groups']
                 *
                 */
                case 'groups':
                    if ($_POST['auto_assign_groups'] == 'Y') {
                        $_POST['groups'] = (!empty($_POST['groups']) && is_array($_POST['groups'])) ? $_POST['groups'] : null;

                        // Return Action Type
                        $json['action'] = 'groups';

                        // Track and Return Groups
                        $json['groups'] = array();

                        if (!empty($_POST['groups'])) {
                            $_lead = new Backend_Lead($lead);

                            try {
                                foreach ($_POST['groups'] as $group_id) {
                                    // Locate Group Row
                                    $group = $db->getCollection('groups')->getRow($group_id);
                                    if (empty($group)) {
                                        throw new Exception_RowNotFound('Group not found.');
                                    }

                                    // Return Group Row (Used in Success Callback)
                                    $json['groups'][] = $group;

                                    // Check Group Status
                                    $assigned = $db->fetch("SELECT * FROM `users_groups` WHERE `group_id` = '" . $group['id'] . "' AND user_id = '" . $_lead->getId() . "';");

                                    // Lead is already in this group
                                    if (!empty($assigned)) {
                                        $success[] = $_lead->getNameOrEmail() . ' is already assigned to "' . $group['name'] . '".';
                                        continue;
                                    }

                                    // Assign Lead to Group
                                    $_lead->assignGroup($group, $authuser);

                                    // Success
                                    $success[] = $_lead->getNameOrEmail() . ' has been added to "' . $group['name'] . '".';

                                    // Current lead Owner
                                    $agent = Backend_Agent::load($_lead->info('agent'));

                                    // Current lead groups
                                    $groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $_lead->getID());

                                    // Sync Partner Groups
                                    Hooks::hook(Hooks::HOOK_LEAD_SYNC_PARTNER_ADDING_GROUP)->run($_lead, $agent, $group, $groups);

                                }
                            } catch (PDOException $e) {
                                $errors[] = $_lead->getNameOrEmail() . ' could not be added to "' . $group['name'] . '".';
                                Log::error($e);
                            } catch (Exception_RowNotFound $e) {
                                $errors[] = $e->getMessage();
                            }
                        } else {
                            $errors[] = 'No groups were provided.';
                        }
                    } else {
                        $success[] = 'Performer opted out of auto-assigning groups.';
                    }

                    break;
            }

            // Unknown Lead
        } else {
            $errors[] = 'The selected lead could not be found.';
        }

        // Query Error
    } else {
        $errors[] = 'An error occurred while loading the selected lead.';
    }
}

/**
 * Add New Option
 */
if (isset($_GET['addOption'])) {
    // Require Option
    $option = htmlspecialchars($_POST['option']);
    if (!empty($option)) {
        // Extra Params
        $params = array();
        if (!empty($_POST['params'])) {
            parse_str($_POST['params'], $params);
        }

        // Option Type
        switch ($_POST['type']) {
            // Add New Event Type
            case 'eventType':
                // Generate INSERT Query
                $query = "INSERT INTO `" . TABLE_CALENDAR_TYPES . "` SET "
                    . "`title` = '" . mysql_real_escape_string($option) . "', "
                    . "`agent` = '" . $authuser->info('id') . "';";

                // Execute Query
                if (mysql_query($query)) {
                    // Return New Option
                    $result = mysql_query("SELECT `id` AS `value`, `title` FROM `" . TABLE_CALENDAR_TYPES . "` WHERE `id` = '" . mysql_insert_id() . "';");
                    $json['option'] = mysql_fetch_assoc($result);

                    // Query Error
                } else {
                    $errors[] = 'Error Occurred';
                }

                break;

            // Add New Listing Type
            case 'listingType':
                // Generate INSERT Query
                $query = "INSERT INTO `" . TABLE_LISTING_FIELDS . "` SET "
                    . "`value` = '" . mysql_real_escape_string($option) . "', "
                    . "`user` = 'true', "
                    . "`field` = 'type';";

                // Execute Query
                if (mysql_query($query)) {
                    // Return New Option
                    $result = mysql_query("SELECT `value`, `value` AS `title` FROM `" . TABLE_LISTING_FIELDS . "` WHERE `id` = '" . mysql_insert_id() . "';");
                    $json['option'] = mysql_fetch_assoc($result);

                    // Query Error
                } else {
                    $errors[] = 'Error Occurred';
                }

                break;

            // Add New Listing Status
            case 'listingStatus':
                // Generate INSERT Query
                $query = "INSERT INTO `" . TABLE_LISTING_FIELDS . "` SET "
                    . "`value` = '" . mysql_real_escape_string($option) . "', "
                    . "`user` = 'true', "
                    . "`field` = 'status';";

                // Execute Query
                if (mysql_query($query)) {
                    // Return New Option
                    $result = mysql_query("SELECT `value`, `value` AS `title` FROM `" . TABLE_LISTING_FIELDS . "` WHERE `id` = '" . mysql_insert_id() . "';");
                    $json['option'] = mysql_fetch_assoc($result);

                    // Query Error
                } else {
                    $errors[] = 'Error Occurred';
                }

                break;

            // Add New Listing Feature
            case 'listingFeature':
                // Generate INSERT Query
                $query = "INSERT INTO `" . TABLE_LISTING_FIELDS . "` SET "
                    . "`value` = '" . mysql_real_escape_string($option) . "', "
                    . "`user` = 'true', "
                    . "`field` = 'feature';";

                // Execute Query
                if (mysql_query($query)) {
                    // Return New Option
                    $result = mysql_query("SELECT `value`, `value` AS `title` FROM `" . TABLE_LISTING_FIELDS . "` WHERE `id` = '" . mysql_insert_id() . "';");
                    $json['option'] = mysql_fetch_assoc($result);

                    // Query Error
                } else {
                    $errors[] = 'Error Occurred';
                }

                break;

            // Add New Location
            case 'listingLocation':
                // Require State
                $params['state'] = htmlentities(trim($params['state']));
                if (empty($params['state'])) {
                    $errors[] = 'You must first select a State/Province.';

                    // Insert Row
                } else {
                    // Generate INSERT Query
                    $query = "INSERT INTO `" . TABLE_LISTING_LOCATIONS . "` SET "
                        . "`state` = '" . mysql_real_escape_string($params['state']) . "', "
                        . "`local` = '" . mysql_real_escape_string($option) . "', "
                        . "`user` = 'Y';";

                    // Execute Query
                    if (mysql_query($query)) {
                        // Return New Option
                        $result = mysql_query("SELECT `local` AS `value`, `local` AS `title` FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `id` = '" . mysql_insert_id() . "';");
                        $json['option'] = mysql_fetch_assoc($result);

                        // Query Error
                    } else {
                        $errors[] = 'Error Occurred';
                    }
                }

                break;

            // Unknown
            default:
                $errors[] = 'Unknown Option Type';
                break;
        }

        // Unknown Option
    } else {
        $errors[] = 'Unknown Option';
    }
}

/**
 * Remove Option
 */
if (isset($_GET['removeOption'])) {
    // Require Option
    $option = $_POST['option'];
    if (!empty($option)) {
        // Extra Params
        $params = array();
        if (empty($_POST['params'])) {
            parse_str($_POST['params'], $params);
        }

        // Option Type
        switch ($_POST['type']) {
            case 'eventType':
                $query = "DELETE FROM `" . TABLE_CALENDAR_TYPES . "` WHERE `id` = '" . mysql_real_escape_string($option) . "';";
                break;
            case 'listingType':
                $query = "DELETE FROM `" . TABLE_LISTING_FIELDS . "` WHERE `value` = '" . mysql_real_escape_string($option) . "' AND `user` = 'true' AND `field` = 'type';";
                break;
            case 'listingStatus':
                $query = "DELETE FROM `" . TABLE_LISTING_FIELDS . "` WHERE `value` = '" . mysql_real_escape_string($option) . "' AND `user` = 'true' AND `field` = 'status';";
                break;
            case 'listingFeature':
                $query = "DELETE FROM `" . TABLE_LISTING_FIELDS . "` WHERE `value` = '" . mysql_real_escape_string($option) . "' AND `user` = 'true' AND `field` = 'feature';";
                break;
            case 'listingLocation':
                $query = "DELETE FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `local` = '" . mysql_real_escape_string($option) . "' AND `user` = 'Y';";
                break;

            default:
                $errors[] = 'Unknown Option Type';
                break;
        }

        // Require Query
        if (!empty($query)) {
            if (mysql_query($query)) {
                // Success
                $success[] = 'Option Successfully Removed';

                // Query Error
            } else {
                $errors[] = 'Error Occurred';
            }
        }

        // Unknown Option
    } else {
        $errors[] = 'Unknown Option';
    }
}

/**
 * Load Event Types
 */
if (isset($_GET['loadEventTypes'])) {
    // Load Options
    $options = array();
    $query = "SELECT `id`, `title` FROM `" . TABLE_CALENDAR_TYPES . "`;";
    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($result)) {
            $options[] = array('value' => $row['id'], 'title' => $row['title']);
        }

        // Query Error
    } else {
        $errors[] = 'Error Loading Event Types';
    }

    // JSON Options
    $json['options'] = $options;
}

/**
 * Load Listing Fields
 */
$field = false;
if (isset($_GET['loadListingTypes'])) {
    $field = 'type';
}
if (isset($_GET['loadListingStatuses'])) {
    $field = 'status';
}
if (isset($_GET['loadListingFeatures'])) {
    $field = 'feature';
}
if (!empty($field)) {
    // Load Options
    $options = array();
    $query = "SELECT `value`, IF(`user` = 'false', 1, 0) AS `required` FROM `" . TABLE_LISTING_FIELDS . "` WHERE `field` = '" . $field . "' ORDER BY `required` DESC, `value` ASC;";
    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($result)) {
            $options[] = array('value' => $row['value'], 'title' => $row['value'], 'required' => (boolean) $row['required']);
        }

        // Query Error
    } else {
        $errors[] = 'Error Loading Options';
    }

    // JSON Options
    $json['options'] = $options;
}

/**
 * Load MLS Listing
 */
if (isset($_GET['searchListings']) && !empty($_GET['mls_number'])) {
    global $_COMPLIANCE;

    // Listings Found
    $json['listings'] = array();

    // Search by MLS Numbers (Comma Separated)
    $mls_numbers = array();
    foreach (explode(',', $_GET['mls_number']) as $mls_number) {
        $mls_number = trim($mls_number);
        if (!empty($mls_number)) {
            $mls_numbers[] = $mls_number;
        }
    }

    // Process MLS Numbers
    foreach ($mls_numbers as $mls_number) {
        $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($mls_number) . "'";

        // Any global criteria
        $idx->executeSearchWhereCallback($search_where);

        // Search Listings
        $listing = $db_idx->fetchQuery("SELECT * FROM `" . $idx->getTable() . "` WHERE " . $search_where . ";");

        // Unknown Listing
        if (empty($listing)) {
            $errors[] = 'Listing Not Found: ' . Lang::write('MLS') . $mls_number;
        } else {
            // Parse Listing
            $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

            // Start OB
            ob_start();

            ?>
            <!-- MLS Listing -->
            <table width="625" cellpadding="5" cellspacing="0" style="border: 1px solid #ccc; background-color: #fff;">
                <tr valign="top">
                    <td width="200">
                        <a href="<?=$listing['url_details']; ?>"><img src="<?=IDX_Feed::thumbUrl($listing['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="" width="200" height="150" style="width: 200px; height: 150px" border="0" /></a>
                    </td>
                    <td width="425" style="vertical-align: top;">
                        <div style="padding: 0 10px; font-size: 14px;">
                            <?php if (!empty($_COMPLIANCE['results']['show_icon'])) : ?>
                                <span style="float: right;"><?=$_COMPLIANCE['results']['show_icon']; ?></span>
                            <?php endif; ?>
                            <i style="color: #777; font-size: 13px;"><?=Lang::write('MLS_NUMBER'); ?><?=$listing['ListingMLS']; ?></i><br />
                            <b>$<?=Format::number($listing['ListingPrice']); ?></b><br />
                            <?=$listing['NumberOfBedrooms']; ?> Bedrooms, <?=$listing['NumberOfBathrooms']; ?> Bathrooms<br />
                            <?=ucwords(strtolower($listing['AddressCity'])); ?>, <?=ucwords(strtolower($listing['AddressState'])); ?>
                            <?=!empty($_COMPLIANCE['results']['show_status']) ? '<br />Status: ' . $listing['ListingStatus'] : ''; ?>
                            <p style="font-size: 12px; margin: 0px;"><?=substr(ucwords(strtolower($listing['ListingRemarks'])), 0, 125); ?>...</p>
                            <a href="<?=$listing['url_details']; ?>" style="color: #333; font-size: 12px; font-family: georgia; font-style: italic; display: block; background: #ddd; padding: 2px; margin-top: 5px;">Read More &raquo;</a>
                            <?php if (!empty($_COMPLIANCE['details']['lang']['provider_bold'])) : ?>
                                <?php if (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office'])) : ?>
                                    <p style="font-size: 12px; font-weight: bold;">Provided by: <?=!empty($_COMPLIANCE['details']['show_agent']) && !empty($listing['ListingAgent']) ? $listing['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['details']['show_office']) && !empty($listing['ListingOffice']) ? (!empty($_COMPLIANCE['details']['show_agent']) ? ', ' : '') . $listing['ListingOffice'] : ''; ?></p>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office'])) : ?>
                                    <p style="font-size: 10px;">Listing courtesy of <?=!empty($_COMPLIANCE['details']['show_agent']) && !empty($listing['ListingAgent']) ? $listing['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['details']['show_office']) && !empty($listing['ListingOffice']) ? (!empty($_COMPLIANCE['details']['show_agent']) && !empty($listing['ListingAgent']) ? ', ' : '') . $listing['ListingOffice'] : ''; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
            <p id="listingsEnd">&nbsp;</p>
            <?php


            // Listing Preview
            $preview = ob_get_clean();

            // Minify HTML
            if (!empty(Settings::getInstance()->SETTINGS['MINIFY_HTML'])) {
                $preview = Minify_HTML::minify($preview);
            }

            // Start OB
            ob_start();

            echo '<p id="disclaimer--' . $idx->getName() . '">&nbsp;</p>';
            \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
            echo '<p id="disclaimersEnd">&nbsp;</p>';

            // Listing Preview
            $disclaimer = ob_get_clean();

            // Minify HTML
            if (!empty(Settings::getInstance()->SETTINGS['MINIFY_HTML'])) {
                $disclaimer = Minify_HTML::minify($disclaimer);
            }

            // Return Listing w/ Preview
            $json['listings'][] = [
                'mls_number' => $listing['ListingMLS'],
                'preview' => $preview,
                'disclaimer' => $disclaimer,
                'feed' => $idx->getName()
            ];
        }
    }
}

/**
 * Load History_Event Details
 */
if (isset($_GET['eventDetails'])) {
    // Close session
    session_write_close();

    // Require Event ID
    if (empty($_POST['event'])) {
        $errors[] = 'Invalid Request: Event ID Required';
    } else {
        // Load History Event
        $event = History_Event::load($_POST['event']);
        if (!empty($event) && method_exists($event, 'getDetails')) {
            // Check Permissions
            $can_edit = $event->canEdit($authuser);

            // Display Editable Field
            if (!empty($can_edit)) {
                // Save Edits
                if (!empty($_POST['save'])) {
                    parse_str($_POST['data'], $data);
                    if ($event->edit($data, $errors, $db)) {
                        unset($_POST['edit']);
                    }
                }

                // Show Edit Form
                if (!empty($_POST['edit'])) {
                    $json['html'] = $event->getEditForm()
                        . '<div class="actions">'
                        . '<a href="#save" class="save">Save</a> | '
                        . '<a href="#cancel" class="cancel">Cancel</a>'
                        . '</div>';

                    // Show Details
                } else {
                    $json['html'] = $event->getDetails() . '<div class="actions"><a href="#edit" class="edit">Edit</a></div>';
                }

                // Show Details
            } else {
                $json['html'] = $event->getDetails();
            }

            // Return Event Type
            $json['type'] = $event->getType();

            // Unknown Event
        } else {
            $errors[] = 'Invalid Request: Unknown Event';
        }
    }
}

/**
 * Toggle Lead Reminder
 */
if (isset($_GET['updateReminder'])) {
    if (!empty($_POST['reminder'])) {
        // Locate Reminder
        $query = "SELECT * FROM `" . LM_TABLE_REMINDERS . "` WHERE `id` = '" . mysql_real_escape_string($_POST['reminder']) . "';";
        if ($result = mysql_query($query)) {
            // Fetch Row
            $reminder = mysql_fetch_assoc($result);

            // Require Row
            if (!empty($reminder)) {
                // Reminder Toggle
                $completed = $_POST['completed'] == 'true' ? 'true' : 'false';

                // Update Row
                $query = "UPDATE `" . LM_TABLE_REMINDERS . "` SET `completed` = '" . $completed . "' WHERE `id` = '" . $reminder['id'] . "';";
                if (mysql_query($query)) {
                    // Success
                    $success[] = 'Reminder Marked as ' . ($completed == 'true' ? 'Completed' : 'Un-Complete');

                    // Query Error
                } else {
                    $errors[] = 'Error Updating Reminder';
                }

                // Event Not Found
            } else {
                $errors[] = 'Invalid Request: Unknown Reminder';
            }

            // Query Error
        } else {
            $errors[] = 'Error Loading Reminder';
        }

        // Reminder ID Required
    } else {
        $errors[] = 'Invalid Request: Reminder ID Required';
    }
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
