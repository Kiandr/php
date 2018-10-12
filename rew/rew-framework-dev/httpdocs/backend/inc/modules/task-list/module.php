<?php

/**
 * Task List module for viewing and completing tasks.
 * Can be loaded with a user_id to display tasks for a specific lead/agent/lender(specified by $mode).
 */

// User ID
$user_id = $this->config['user_id'];

// Module can load in 4 modes - Lead, Agent, Lender, or Admin
$mode = (in_array($this->config['mode'], array('Lead', 'Agent', 'Lender', 'Admin')) ? $this->config['mode'] : false);

// Tasks per page
$page_limit = (!empty($this->config['page_limit']) ? $this->config['page_limit'] : 10);

// Notice collection
$errors = array();
$success = array();

// DB connection
$db = DB::get();

// Query Lead
if ($mode === 'Lead') {
    $queryString = "SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;";
    $queryParams = ['id' => $user_id];
    $lead = $db->fetch($queryString, $queryParams);

    // Lead doesn't exist
    if (empty($lead)) {
        $errors[] = __("An error occurred, the lead with the requested ID does not exist!");
    }

} else {
    $lead = NULL;

}

// Create lead instance
$lead = new Backend_Lead($lead);

// Auth User
$authuser = Auth::get();
$settings = Settings::getInstance();
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);
if (!empty($lead)) {
    $leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);
}

// Action Plan Assign/Unassign permissions
$can_assign = false;
if ($leadAuth && $leadAuth->canAssignActionPlans($authuser)) {
    $can_assign = true;
} else if ($leadsAuth->canAssignActionPlans($authuser)) {
    $can_assign = true;
}

// Action Plan Manage permissions
$can_manage = false;
if ($leadsAuth->canManageActionPlans($authuser)) {
    $can_manage = true;
}

// Track/Display './mass-action-ajax.php' Responses
if (isset($_POST['ajax_response'])) {
    if (!empty($_POST['json_success']) && is_array($_POST['json_success'])) {
        foreach ($_POST['json_success'] as $arjs) {
            $success[] = $arjs;
        }
    }
    if (!empty($_POST['json_errors']) && is_array($_POST['json_errors'])) {
        foreach ($_POST['json_errors'] as $arje) {
            $errors[] = $arje;
        }
    }
}

// Handle task shortcut
unset($_SESSION['task_shortcut']['rew_dialer']);
if (isset($_GET['shortcut']) && !empty($_GET['agent']) && !empty($_GET['task'])) {
    unset($_SESSION['task_shortcut']);
    if ($task = Backend_Task::load($_GET['task'])) {
        // Set $_SESSION values for task completion detection
        $_SESSION['task_shortcut']['task']   = $task->getId();
        $_SESSION['task_shortcut']['user']   = $_GET['id'];
        $_SESSION['task_shortcut']['events'] = $task->getEventTypes();

        // Get shortcut task page to redirect to
        $redirect = $task->getShortcutURL($_GET['id'], (isset($_GET['special']) ? true : false));
        header('Location: ' . $redirect . '&post_task=' . $task->getId() . (isset($_GET['popup']) ? '&popup' : ''));
        exit;
    }
}

// Handle task actions (complete/snooze/dismiss)
if (!empty($_POST['task_action']) && !empty($_POST['task'])) {
    // Load Task
    if ($task = Backend_Task::load($_POST['task'])) {
        $performer = array('id' => $authuser->info('id'), 'type' => $authuser->getType());

        // Snooze Task
        if ($_POST['task_action'] == 'snooze') {
            // Calculate amount in days
            $amount = intval($_POST['snooze_amount']) * intval($_POST['snooze_unit']);
            if($amount < 0) {
                $errors[] = __("Cannot snooze tasks with a negative!");
            }

            if ($task->snooze($_POST['user'], $performer, $amount, $_POST['note'])) {
                $success[] = __("Task successfully snoozed! ");
            } else {
                $errors[] = __("An error occurred while snoozing task!");
            }
        // Dismiss Task
        } else if ($_POST['task_action'] == 'dismiss') {
            // Dismiss followup tasks?
            $followup_dismiss = ($_POST['followup_dismiss'] == 'true') ? true : false;

            if ($task->resolve($_POST['user'], $performer, 'Dismissed', $_POST['note'], $followup_dismiss)) {
                $success[] = __("Task successfully dismissed!");
            } else {
                $errors[] = __("An error occurred while dismissing task!");
            }
        // Complete Task
        } else if ($_POST['task_action'] == 'complete') {
            if ($task->resolve($_POST['user'], $performer, 'Completed', $_POST['note'])) {
                $success[] = __("Task successfully completed!");
            } else {
                $errors[] = __("An error occurred while completing the task!");
            }
        } else if ($_POST['task_action'] == 'note') {
            if ($task->addNote($_POST['user'], $_POST['note'])) {
                $success[] = __("Task note added!");
            } else {
                $errors[] = __("An error occurred while adding task note!");
            }
        }
    }
}

// Plan Management
// Assign Action Plan
if ($can_assign && isset($_GET['assign_plan']) && !empty($user_id)) {
    try {
        if ($plan = Backend_ActionPlan::load($_POST['plan_id'])) {
            if ($plan->assign($user_id, $authuser)) {
                $success[] = __('The task has been successfully assigned to this lead.');
            }
        }
    } catch (Exception $e) {
        $errors[] = __($e->getMessage());
    }
}

// Unassign Action Plan
if ($can_assign && isset($_GET['unassign_plan']) && !empty($user_id)) {
    try {
        if ($plan = Backend_ActionPlan::load($_GET['plan_id'])) {
            $plan->unassign($user_id, $authuser);
        }
    } catch (Exception $e) {
        $errors[] = __('There was an issue unassigning the selected action plan.');
    }
}

// Re-direct on Success/Failure
if (!empty($user_id)) {
    if (!empty($success)) {
        $authuser->setNotices($success, $errors);
        header('Location: ?id='  . $user_id . '&success');
        exit;
    }
}

// Task status tabs
$status_tabs = array(
    __('Pending'),
    __('Completed'),
    __('Dismissed'),
    __('Expired'),
);

// Task type filter options
$type_options = task_type_options();

// Task due filter options
$due_options = array(
    '1'  => __('1 Day'),
    '2'  => __('2 Days'),
    '7'  => __('7 Days'),
    '30' => __('30 Days')
);

// Action plan filter/assign options
$plans = $db->fetchAll("SELECT `name`, `id` FROM `" . TABLE_ACTIONPLANS . "` ORDER BY `name` ASC;");
$plan_options = array();
foreach ($plans as $plan) {
    $plan_options[$plan['id']] = $plan['name'];
}

$where = array();
$params = array();

// Task filter criteria
// Task status - Pending tasks by default
$_GET['status'] = isset($_GET['status'])  ? $_GET['status'] : $_SESSION['task_status'];
$_GET['status'] = !empty($_GET['status']) ? $_GET['status'] : 'Pending';
$status = $_SESSION['task_status'] = $_GET['status'];
$where[] = "`ut`.`status` = :status";
$params['status'] = $status;

// Filter task list based on view (Agent, Lender, Lead). Master Task List (Admin Only) is unfiltered.
switch ($mode) {
    case 'Agent':
        if ($status == 'Pending') {
            $where[] = "`u`.`agent` = :agent AND `t`.`performer` = 'Agent'";
        } else {
            $where[] = "`ut`.`performer` = 'Agent' AND `ut`.`performer_id` = :agent";
        }
        $params['agent'] = $authuser->isSuperAdmin() ? $user_id : $authuser->info('id');
        break;

    case 'Lender':
        if ($status == 'Pending') {
            $where[] = "`u`.`lender` = :lender AND `t`.`performer` = 'Lender'";
        } else {
            $where[] = "`ut`.`performer` = 'Lender' AND `ut`.`performer_id` = :lender";
        }
        $params['lender']  = $authuser->info('id');
        break;

    case 'Lead':
        $where[] = "`u`.`id` = :user_id";
        $params['user_id'] = $user_id;
        break;
}

// Task type
$_GET['type'] = isset($_GET['type'])  ? $_GET['type'] : $_SESSION['task_type'];
$_GET['type'] = !empty($_GET['type']) ? $_GET['type'] : 'All';
$type = $_SESSION['task_type'] = $_GET['type'];
if ($type != 'All') {
    $where[] = "`ut`.`type` = :type";
    $params['type'] = $type;
}

// Task due period
$_GET['due'] = isset($_GET['due']) ? $_GET['due'] : $_SESSION['task_due'];
$due = $_SESSION['task_due'] = $_GET['due'];
if (!empty($due)) {
    if ($status == 'Pending') {
        $where[] = "`ut`.`timestamp_due` <= DATE_ADD(NOW(), INTERVAL :due DAY)";
    } else {
        $where[] = "`ut`.`timestamp_resolved` >= DATE_SUB(NOW(), INTERVAL :due DAY)";
    }
    $params['due'] = $due;
}

// Task action plan
$_GET['plan'] = isset($_GET['plan'])  ? $_GET['plan'] : $_SESSION['task_plan'];
$_GET['plan'] = !empty($_GET['plan']) ? $_GET['plan'] : '';
$plan = $_SESSION['task_plan'] = $_GET['plan'];
if (!empty($plan)) {
    $where[] = "`t`.`actionplan_id` = :actionplan_id";
    $params['actionplan_id'] = $plan;
}

// Order By due time for pending tasks, or resolved time for resolved tasks
if ($status == 'Pending') {
    $order = " ORDER BY `ut`.`timestamp_due` ASC";
} else {
    $order = " ORDER BY `ut`.`timestamp_resolved` DESC";
}

// Count Tasks
$count = array('total' => 0);
try {
    $sql = "SELECT "
        . "`ut`.`id`, `ut`.`type`, `ut`.`performer`, `u`.`agent`, `u`.`lender` "
        . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
        . " JOIN `" . TABLE_TASKS . "` `t` ON `ut`.`task_id` = `t`.`id` "
        . " JOIN `users` `u` ON `ut`.`user_id` = `u`.`id` "
        . " WHERE " . implode(' AND ', $where)
        . " GROUP BY `ut`.`id`"
        . ";";
    $count_query = $db->prepare($sql);
    $count_query->execute($params);
    if ($count_tasks = $count_query->fetchAll()) {
        foreach ($count_tasks as $count_task) {
            // Limit Mass-Task Compeltion IDs to Specific Authuser's Tasks
            $queue_mass_process = true;
            if ((!$authuser->isSuperAdmin() || $mode != 'Admin')
                && (
                    ($authuser->isAgent() && ($count_task['performer'] != 'Agent' || $count_task['agent'] != $authuser->info('id')))
                    || ($authuser->isLender() && ($count_task['performer'] != 'Lender' || $count_task['lender'] != $authuser->info('id')))
                )
            ) {
                $queue_mass_process = false;
            }
            // These IDs Determine Which Tasks to Queue Into Mass-Process Handlers
            if ($queue_mass_process) {
                $count['tasks'][$count_task['type']][] = $count_task['id'];
            }
            $count['total']++;
        }
    }
} catch (Exception $e) {
    $errors[] = __('Error loading task counter');
}

// Query String
list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
parse_str($query, $query_string);

// Task List Pagination
if ($count['total'] > $page_limit) {
    $limitvalue = (($_GET['p'] - 1) * $page_limit);
    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
    $limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
}

// Pagination
$pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

// Collection of tasks organized into days
$tasks_timeline = array();

if ($count['total'] > 0) {
    $query = $db->prepare("SELECT "
        . "`u`.`id` AS `user_id`, "
        . "`u`.`first_name`, "
        . "`u`.`last_name`, "
        . "CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `user_name`, "
        . "`ut`.`id` AS `user_task_id`, "
        . "`ut`.`task_id` AS `id`, "
        . "`ut`.`name`, "
        . "`ut`.`type`, "
        . "`ut`.`status`, "
        . "`ut`.`timestamp_due`, "
        . "IF(`ut`.`timestamp_due` < DATE_ADD(DATE(NOW()), INTERVAL 1 DAY), 1, 0) AS `due_now`, "
        . "`ut`.`timestamp_expire`, "
        . "`ut`.`timestamp_resolved`, "
        . "`ut`.`performer`, "
        . "`ut`.`performer_id`, "
        . "`t`.`parent_id`, "
        . "`t`.`info`, "
        . "`t`.`automated`, "
        . "`a`.`name` AS `plan_name` "
        . "FROM `" . TABLE_USERS_TASKS . "` `ut` "
        . "JOIN `" . TABLE_TASKS . "` `t` ON `t`.`id` = `ut`.`task_id` "
        . "JOIN `" . TABLE_ACTIONPLANS . "` `a` ON `a`.`id` = `ut`.`actionplan_id` "
        . "LEFT JOIN `" . LM_TABLE_LEADS . "` `u` ON `ut`.`user_id` = `u`.`id` "
        . "WHERE " . implode(' AND ', $where) . $order . $limit . ";");

    if ($query->execute($params)) {
        while ($row = $query->fetch()) {
            // Fetch Task Notes
            $notes = $db->fetchAll(
                "SELECT `note`, `timestamp` "
                . " FROM `" . TABLE_USERS_TASKS_NOTES . "` "
                . " WHERE `user_task_id` = :user_task_id "
                . ";",
                array(
                    'user_task_id' => $row['user_task_id']
                )
            );
            foreach ($notes as $note) {
                $row['notes'][] = '<strong>[' . date('M jS, Y - g:i a', strtotime($note['timestamp'])) . ']</strong> ' . $note['note'];
            }

            // Can the user edit (complete/dismiss/snooze/note) this task? User type must match performer (Super Admin can edit any task)
            $row['can_edit'] = ($authuser->getType() == $row['performer'] || $authuser->isSuperAdmin()) ? true : false;

            // Task type icon class
            $row['icon_class'] = Backend_Task::getTypeIcon($row['type']);

            // Process Pending Tasks
            if ($row['status'] == 'Pending') {
                // Get parent task info to give some context/background for this task
                if ($parent = $db->fetch("SELECT `name`, `type`, `performer` FROM `" . TABLE_TASKS . "` WHERE `id` = '" . $row['parent_id'] . "';")) {
                    $row['info'] .= '<p>This task is a follow-up of <strong>' . $parent['name'] . '</strong> (' . $parent['performer'] . ' ' . $parent['type'] . ' Task)</p>';
                }

                // Build task processing URL for some task types
                $process_attributes = 'href="#"'
                    . ' data-aid="' . htmlspecialchars($authuser->info('id')) . '"'
                    . ' data-gid="' . htmlspecialchars($_GET['id']) . '"'
                    . ' data-uid="' . htmlspecialchars($row['user_id']) . '"'
                    . ' data-tid="' . htmlspecialchars($row['id']) . '"'
                    . ' %s';

                // Toggle Display of Standard "Complete Task" Button
                $row['shortcut_only'] = false;

                // Process Tasks
                switch ($row['type']) {
                    case 'Call':
                        $row['shortcut'] = '<a class="btn process-task btn--positive" ' . sprintf($process_attributes, 'data-type="Call" data-action="process"') . '>' . __('Log Call') . '</a>';
                        break;
                    case 'Email':
                        $row['shortcut'] = '<a class="btn process-task btn--positive" ' . sprintf($process_attributes, 'data-type="Email" data-action="process"') . '>' . __('Send Email') .' </a>';
                        break;
                    case 'Search':
                        $row['shortcut'] = '<a class="btn process-task btn--positive" ' . sprintf($process_attributes, 'data-type="Search" data-action="process"') . '>' . __('Save Search') . '</a>';
                        break;
                    case 'Listing':
                        $row['shortcut'] = '<a class="btn process-task btn--positive" ' . sprintf($process_attributes, 'data-type="Listing" data-action="process"') . '>' . __('Send Listing') . '</a>';
                        break;
                    case 'Group':
                        try {
                            $task_group_ids = array();
                            $sql = "SELECT `id` FROM `" . LM_TABLE_GROUPS . "` `g` "
                                . " JOIN `" . TABLE_TASKS_GROUPS . "` `tg` ON `tg`.`group_id` = `g`.`id` "
                                . " WHERE `tg`.`task_id` = :row_id "
                                . " AND `g`.`agent_id` IS NULL "
                                . ";";
                            $task_groups = $db->prepare($sql);
                            if ($task_groups->execute(array('row_id' => $row['id']))) {
                                while ($task_group = $task_groups->fetch()) {
                                    $task_group_ids[] = $task_group['id'];
                                }
                            }
                        } catch (Exception $e) {
                        }
                        // Grab all groups for group selectize
                        // Available Groups (for Assigned Agent)
                        $groups = Backend_Group::getGroups($errors, Backend_Group::AGENT, $authuser->info('id'))
                            + Backend_Group::getGroups($errors);
                        $groups = array_reduce($groups, function($result, $group) use($task_group_ids) {
                            array_push($result, ['id' => $group['id'], 'name' => $group['name'], 'selected' => in_array($group['id'], $task_group_ids)]);
                            return $result;
                        }, []);

                        if (!empty($task_group_ids)) {
                            $row['shortcut'] = '<a class="btn process-task btn--positive" ' . sprintf($process_attributes, 'data-action="process" data-type="Group" data-extra-ids="' . implode(',', $task_group_ids) . '" data-groups=\'' . json_encode($groups, true) . '\'') . '>' . __('Mark as Complete') . '</a>';
                            $row['shortcut_only'] = true;
                        }
                        break;
                    case 'Text':
                        // Provide shortcut to REWText if available
                        if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) {
                            $row['shortcut'] = '<a class="btn process-task btn--positive" ' . sprintf($process_attributes, 'data-type="Text" data-action="process"') . '>' . __('Send Text') . '</a>';
                        }
                        break;
                    case 'Custom':
                    default:
                        $row['shortcut'] = false;
                }

                // Due time display
                $due_time      = strtotime($row['timestamp_due']);
                $row['time']   = $due_time;

                // If the task is currently due - also display the time remaining before the task expires
                if ($row['due_now']) {
                    $row['expire_time'] = strtotime($row['timestamp_expire']);
                }

                // If viewing in Admin or Lead mode, add performer info to the task display
                if (($mode == 'Admin' || $mode == 'Lead') && $row['performer'] != 'System') {
                    $performer_query = false;
                    if ($row['performer'] == 'Agent') {
                        $performer_query = "SELECT `a`.`id`, `a`.`first_name`, `a`.`last_name`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name` "
                            . "FROM `agents` `a` JOIN `users` `u` ON `u`.`agent` = `a`.`id` WHERE `u`.`id` = '" . $row['user_id'] . "';";
                        $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=';
                    } else if ($row['performer'] == 'Lender') {
                        $performer_query = "SELECT `l`.`id`, `first_name`, `last_name`, CONCAT(`l`.`first_name`, ' ', `l`.`last_name`) AS `name` "
                            . "FROM `lenders` `l` JOIN `users` `u` ON `u`.`lender` = `l`.`id` WHERE `u`.`id` = '" . $row['user_id'] . "';";
                        $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'lenders/lender/summary/?id=';
                    }
                    if ($performer_query) {
                        if ($performer = $db->fetch($performer_query)) {
                            $row['performer_initials'] = $performer['first_name'][0] . $performer['last_name'][0];
                            $row['agent_id'] = $performer['id'];
                            $row['performer_name'] = $performer['name'];
                        }
                    }
                }

                // Group the tasks into days, and overdue/due now (displayed at the top)
                if ($row['due_now']) {
                    // "Due Now" timeline group for pending tasks that are due
                    $timeline = 'due_now';
                } else {
                    // Timeline grouping
                    $timeline = date('d-m-Y', $due_time);
                }

            // Process Resolved (Completed/Dismissed/Expired) Tasks
            } else {
                // If viewing in Admin mode, add performer info to the task display
                if ($row['performer'] != 'System') {
                    if ($row['performer'] == 'Agent') {
                        $performer_query = "SELECT `id`, `first_name`, `last_name`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = '" . $row['performer_id'] . "';";
                        $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=';
                    } else if ($row['performer'] == 'Lender') {
                        $performer_query = "SELECT `id`, `first_name`, `last_name`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `lenders` WHERE `id` = '" . $row['performer_id'] . "';";
                        $performer_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'lenders/lender/summary/?id=';
                    }

                    if ($performer = $db->fetch($performer_query)) {
                        $row['performer_initials'] = $performer['first_name'][0] . $performer['last_name'][0];
                        $task['agent_id'] = $performer['id'];
                        $row['performer_name'] = $performer['name'];
                        $row['performer_info'] = $row['status'] . ($row['status'] == 'Expired' ? ' for ' : ' by ') . '<a href="' . $performer_url . $performer['id'] . '">' . $performer['name'] .  '</a>';
                    }
                } else {
                    $row['performer_info'] = $row['status'] . ' by System';
                }

                // Resolved time display
                $resolved_time = strtotime($row['timestamp_resolved']);
                $row['time'] = $resolved_time;

                // Timeline group
                $timeline = date('d-m-Y', $resolved_time);
            }

            // User initials
            $row['user_initials'] = $row['first_name'][0] . $row['last_name'][0];

            // Check for provided task extras (email, message, groups) and add to task info
            $extras = false;
            switch ($row['type']) {
                case 'Email':
                    $email = $db->fetch("SELECT `te`.`subject`, `d`.`document`, `te`.`body` FROM `" . TABLE_TASKS_EMAILS . "` `te` LEFT JOIN `docs` `d` ON `d`.`id` = `te`.`doc_id` WHERE `te`.`task_id` = '" . $row['id'] . "';");
                    if (!empty($email)) {
                        $extras['title'] = 'Email Message:';
                        if (!empty($email['body'])) {
                            $extras['content'] = $email['body'];
                        } else if (!empty($email['document'])) {
                            $extras['content'] = $email['document'];
                        }
                    }
                    break;
                case 'Group':
                    $group_labels = array();
                    $groups = $db->query("SELECT `g`.`name`, `g`.`style` FROM `" . LM_TABLE_GROUPS . "` `g` JOIN `" . TABLE_TASKS_GROUPS . "` `tg` ON `tg`.`group_id` = `g`.`id` WHERE `tg`.`task_id` = '" . $row['id'] . "' AND `g`.`agent_id` IS NULL;");
                    while ($group = $groups->fetch()) {
                        $group_labels[] = '<label class="group group_' . $group['style'] . '">' . $group['name'] . '</label>';
                    }
                    $extras['title'] = 'Groups:';
                    if (!empty($group_labels)) {
                        $extras['content'] = '<div class="groups">' . implode(' ', $group_labels) . '</div>';
                    } else {
                        $extras['content'] = '<span>' . __('There are currently no groups attached to this task. It is possible the group has been deleted since the task was assigned.') . '</span>';
                        unset($row['shortcut']);
                    }
                    break;
                case 'Text':
                    $text = $db->fetch("SELECT `message` FROM `" . TABLE_TASKS_TEXTS . "` WHERE `task_id` = '" . $row['id'] . "';");
                    if (!empty($text)) {
                        $extras['title'] = __('Text Message:');
                        $extras['content'] = $text['message'];
                    }
                    break;
            }
            if (!empty($extras)) {
                $row['extras'] = $extras;
            }

            // Add to timeline
            $tasks_timeline[$timeline][] = $row;
        }
    }
}

// Descriptive task filter message
// Filtering by task types
$type_msg = $type . ' Tasks ';

// Filtering by time period (status dependent)
if (!empty($due)) {
    $period_msg = ($status == 'Pending' ? __('due in next %s days', $due ) : __('%s in last %s days', strtolower($status), $due));
} else {
    $period_msg = ($status == 'Pending' ? __('due any time') : __('%s any time', strtolower($status)));
}

// Filtering by action plan
$plan_msg = (!empty($plan) ? ' from "' . $plan_options[$plan]  . '" Action Plan, ' : '');

// Build message
$task_filter_message = Format::htmlspecialchars($type_msg . $plan_msg . $period_msg . '. (' . $count['total'] . ' Tasks)');

// If in Lead mode, get currently assigned Action Plans
if ($mode == 'Lead' && !empty($user_id)) {
    $assigned_plans = array();
    $query = $db->prepare("SELECT `a`.`id`, `a`.`name`, `a`.`style`, `ua`.`timestamp_assigned`, `ua`.`timestamp_completed` "
        . " FROM `" . TABLE_USERS_ACTIONPLANS . "` `ua` "
        . " JOIN `" . TABLE_ACTIONPLANS . "` `a` ON `ua`.`actionplan_id` = `a`.`id` "
        . " WHERE `user_id` = :user_id"
        . " ORDER BY `name` ASC;");
    if ($query->execute(array('user_id' => $user_id))) {
        while ($row = $query->fetch()) {
            $assigned_plans[$row['id']] = $row;
        }
    }
    if (!empty($errors) || !empty($success)) {
        $authuser->setNotices($success, $errors);
        header('Location: ?id='  . $user_id );
        exit;
    }
}
