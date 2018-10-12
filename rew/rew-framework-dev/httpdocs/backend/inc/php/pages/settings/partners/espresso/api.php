<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageEspresso($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage espresso integrations')
    );
}

$errors = array();

// This handles requests from Espresso
if (!empty($_POST['apiAction'])) {
    // Build API Object
    $api = new Partner_Espresso();

    // Init DB Object
    $db = (!empty($db)) ? $db : DB::get();

    // Espresso is Requesting Lead Information
    if ($_POST['apiAction'] == 'contacts') {
        // Return lead data
        $api->setContacts($_POST['contactids'], $_POST['tpid']);

        // Check for Errors
        if ($api->getLastError() != null) {
            $errors[] = $api->getLastError();
        }

        // Output for Espresso to Parse
        if (empty($errors)) {
            //Grab Contacts
            $contacts = $api->getContacts();

            // Check the Contacts Array
            if (!empty($contacts)) {
                $task_query = null;

                // Check if Dialer is Running in Action Plan Mode
                if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
                    if (!empty($_POST['tpsessvalue']) && $_POST['tpsesskey'] == 'user_info') {
                        // User Info Return Value
                        $tp_sess_val = explode('-', $_POST['tpsessvalue']);

                        // Retrieve Action Plan Tasks Matching Response Criteria
                        if ($tp_sess_val[2] === 'true') {
                            $task_query = $db->prepare(
                                "SELECT `ut`.`task_id`, `ut`.`user_id` FROM `users_tasks` `ut` "
                                . " LEFT JOIN `tasks` `t` ON `t`.`id` = `ut`.`task_id` "
                                . " WHERE `ut`.`user_id` = :lead_id "
                                . (($tp_sess_val[3] !== 'true')
                                    ? " AND `t`.`automated` = 'N' "
                                    : "")
                                . (($tp_sess_val[4] !== 'false')
                                    ? " AND `ut`.`actionplan_id` = :ap_id "
                                    : "")
                                . " AND `ut`.`type` = 'Call' "
                                . " AND `ut`.`status` = 'Pending' "
                                . " AND `ut`.`timestamp_due` < NOW() "
                                . ";"
                            );
                        }
                    }
                }

                // Build the Response Output
                $output = "";
                foreach ($contacts as $contact) {
                    // Reset Contact Note
                    $_note = null;

                    // Complete Appropriate Action Plan Tasks
                    if (!empty($task_query)) {
                        try {
                            $bound_params = array('lead_id' => $contact['id']);
                            if ($tp_sess_val[4] !== 'false' && intval($tp_sess_val[4]) > 0) {
                                $bound_params['ap_id'] = $tp_sess_val[4];
                            }
                            $task_query->execute($bound_params);
                            $tasks = $task_query->fetchAll();

                            // Process Tasks
                            if (!empty($tasks)) {
                                $_note = '';
                                foreach ($tasks as $task) {
                                    if ($_task = Backend_Task::load($task['task_id'])) {
                                        // Get note stuff
                                        $_note .= '[' . strip_tags($_task->info('name')) . '] ' . "\n"
                                            . (!empty($_task->info('info')) ? strip_tags($_task->info('info')) : '< this task has no special instructions >')
                                            . "\n\n";
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            $errors[] = __('An error occurred while processing action plan tasks for user ID: %s', $lead['id']) ;
                        }
                    }

                    $output .= "contact|"
                        . "contactid="  . urlencode($contact['id'])
                        . "&firstname=" . urlencode($contact['first_name'])
                        . "&lastname="  . urlencode($contact['last_name'])
                        . "&phone="     . urlencode($contact['phone'])
                        . "&email="     . urlencode($contact['email'])
                        . (!empty($_note)
                            ? "&notes=" . urlencode('(' . date('M jS @g:ia') . ') Task Notes: ' . "\n" . $_note)
                            : '')
                        . "\n";
                }

                // Set Response
                $response = $output;
            } else {
                $errors[] = 'No Valid Contacts Selected.';
            }
        }

    // Espresso is Returning Call Session Results
    } else if ($_POST['apiAction'] == 'calldone') {
        // Grab Lead Based on contactid Response
        $api->setContacts($_POST['contactid'], $_POST['tpid'], $auth_type);

        // Check for Errors
        if ($api->getLastError() != null) {
            $errors[] = $api->getLastError();
        }
        if (empty($errors)) {
            // Retrieve Contacts
            $contacts = $api->getContacts();
            if (!empty($contacts)) {
                $lead = new Backend_Lead($contacts[0]);
                if (!empty($lead)) {
                    $phone = false;

                    // Call Type
                    if (!empty($_POST['status'])) {
                        switch ($_POST['status']) {
                            // Talked to Lead
                            case 'Interested':
                            case 'Not Interested':
                            case 'Do Not Call':
                            case 'No Result':
                                $phone = 'History_Event_Phone_Contact';
                                break;
                                // Call Attempt
                            case 'Busy Phone':
                            case 'No Answer':
                            case 'Wrong Person':
                                $phone = 'History_Event_Phone_Attempt';
                                break;
                                // Received Voicemail / Left Message
                            case 'Voicemail':
                                $phone = 'History_Event_Phone_Voicemail';
                                break;
                                // Bad Phone Number
                            case 'Bad Phone':
                            case 'Fax Machine':
                                $phone = 'History_Event_Phone_Invalid';
                                break;
                                // Custom status
                            default:
                                $phone = 'History_Event_Phone_Contact';
                                break;
                        }

                        // Log Phone Call
                        if (!empty($phone) && !empty($_POST['user_info'])) {
                            // Split User Info Response Pieces
                            $user_info = explode('-', $_POST['user_info']);
                            // [0] Auth User ID   [int]
                            // [1] Auth User Type [str]   (agent|associate)
                            // [2] Task Mode      [str]   (true|false)  - Flag to Complete Action Plan Tasks
                            // [3] Automated      [str]   (true|false)  - Flag to Include Automated Tasks
                            // [4] Plan ID        [mixed] ([int]|false) - Flag to Limit Task Completion to a Specific Action Plan

                            try {
                                // Set Response
                                $response = "OK";

                                // Log Event: Track Phone Call
                                $event = new $phone (array(
                                    'details' => 'REW Dialer Call' . "\n"
                                        . 'Lead Response: ' . $_POST['status'] . "\n"
                                        . 'Notes: ' . ((!empty($_POST['notes'])) ? $_POST['notes'] : 'none')
                                ), array(
                                    new History_User_Lead($lead['id']),
                                    $api->userHistoryInfo(array($user_info[0], $user_info[1]))
                                ));

                                // Save to DB
                                $event->save();

                                // Error Occurred
                            } catch (Exception $e) {
                                $errors[] = __('An error occurred while logging your phone call.');
                            }

                            // Complete Appropriate Action Plan Tasks
                            if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
                                if ($user_info[2] == 'true') {
                                    try {
                                        // Retrieve Action Plan Tasks Matching Response Criteria
                                        $query = $db->prepare(
                                            "SELECT `ut`.`task_id`, `ut`.`user_id` FROM `users_tasks` `ut` "
                                            . " LEFT JOIN `tasks` `t` ON `t`.`id` = `ut`.`task_id` "
                                            . " WHERE `ut`.`user_id` = :lead_id "
                                            . (($user_info[3] !== 'true')
                                                ? " AND `t`.`automated` = 'N' "
                                                : "")
                                            . (($user_info[4] !== 'false')
                                                ? " AND `ut`.`actionplan_id` = :ap_id "
                                                : "")
                                            . " AND `ut`.`type` = 'Call' "
                                            . " AND `ut`.`status` = 'Pending' "
                                            . " AND `ut`.`timestamp_due` < NOW() "
                                            . ";"
                                        );
                                        $bound_params = array('lead_id' => $lead['id']);
                                        if ($user_info[4] !== 'false') {
                                            $bound_params['ap_id'] = $user_info[4];
                                        }
                                        $query->execute($bound_params);
                                        $tasks = $query->fetchAll();

                                        // Process Tasks
                                        if (!empty($tasks)) {
                                            foreach ($tasks as $task) {
                                                if ($_task = Backend_Task::load($task['task_id'])) {
                                                    $performer = array(
                                                        'id' => $user_info[0],
                                                        'type' => $user_info[1]
                                                    );
                                                    if (!$_task->resolve($task['user_id'], $performer, 'Completed', 'REWDialer Response: ' . Format::htmlspecialchars($_POST['status']))) {
                                                        // Failed to resolve - add note to task with error details
                                                        $_task->addNote($task['user_id'], 'The call was processed through the REWDialer, but the system failed to automatically mark this action plan task as completed.');
                                                    }
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        $errors[] = __('An error occurred while processing action plan tasks for user ID: %s', $lead['id']);
                                    }
                                }
                            }
                        }
                    } else {
                        $errors[] = __('Call Status Not Received');
                    }
                } else {
                    $errors[] = __('Invalid Contact.');
                }
            } else {
                $errors[] = __('No Contact Selected.');
            }
        }
    } else {
        $errors[] = __('Invalid API Action Value');
    }
} else {
    $errors[] = __('Missing API Action Value');
}

// Check for Errors
$response = (!empty($errors)) ? 'ERRORS|' . "\n" . implode("\n", $errors) : $response;

// No Need to Continue Past This, Just Need to Output the Response to Espresso
die('1|[[[' . $response . '[[[');
