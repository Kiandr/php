<?php

// Include Backend Configuration
include_once dirname(__FILE__) . '/../../../common.inc.php';

// Require Authorization
if (!$authuser->isValid()) {
    die('{}');
}

// Success
$success = array();

// Errors
$errors = array();

// JSON Data
$json = array();

/**
 * Process Multiple Tasks
 */
if ($_GET['action'] == 'process_tasks') {
    // Track Error/Success Task IDs
    $error_tasks = array();
    $success_tasks = array();

    if (!empty($_POST['user_task_ids'])) {
        // App Database
        $db = (is_null($db)) ? DB::get() : $db;

        // Prepare User Task Query
        try {
            $sql = "SELECT `u`.`guid`, `u`.`email`, `u`.`first_name`, `u`.`last_name`, CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `full_name`, "
                . " `ut`.`user_id`, `ut`.`task_id`, `ut`.`type` "
                . " FROM `users_tasks` `ut` "
                . " LEFT JOIN `users` `u` ON `u`.`id` = `ut`.`user_id` "
                . " WHERE `ut`.`status` = 'Pending' "
                . " AND `ut`.`timestamp_due` <= NOW() "
                . " AND `ut`.`id` = :id "
                . ";";
            $ut_query = $db->prepare($sql);
        } catch (Exception $e) {
            $errors[] = __('Failed to load task data: %s', $e->getMessage());
        }

        if (empty($errors)) {
            foreach ($_POST['user_task_ids'] as $id) {
                // Fetch User Task
                if ($ut_query->execute(array('id' => $id))) {
                    $user_task = $ut_query->fetch();
                }

                if (!empty($user_task)) {
                    if ($task = Backend_Task::load($user_task['task_id'])) {
                        // AP Task ID
                        $task_id = $task->getId();

                        // Handle Task by Type
                        switch ($user_task['type']) {
                            case 'Group':
                            case 'Email':
                            case 'Text':
                                // Process Email Task
                                if ($task->processAndResolve($user_task['user_id'])) {
                                    // Track Task as Successfully Processed
                                    $success_tasks[] = $id;
                                }
                                break;
                        }
                    }
                }

                // Track Task as Failed to Process
                if (!in_array($id, $success_tasks)) {
                    $error_tasks[] = $id;
                }
            }
        }
    } else {
        $errors[] = __('Failed to process tasks - no user task IDs were provided.');
    }

    // Compile Error/Success IDs Into Return Message
    if (!empty($error_tasks)) {
        $errors[] = __('Failed to process %s tasks.', count($error_tasks));
    }
    if (!empty($success_tasks)) {
        $success[] = __('Successfully processed %s tasks.', count($success_tasks));
    }
}

/**
 * Queue Call Tasks into REW Dialer
 */
if ($_GET['action'] == 'queue_dialer') {
    unset($_SESSION['task_shortcut']['rew_dialer']);

    if (isset(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'])
        && Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] > 0
        && ($authuser->isAgent())
    ) {
        if (!empty($_POST['user_task_ids'])) {
            $user_task_ids = (is_array($_POST['user_task_ids'])) ? $_POST['user_task_ids'] : array($_POST['user_task_ids']);

            // Prepare User Task Query
            try {
                $sql = "SELECT `u`.`id` AS `user_id`, `ut`.`id` AS `task_id` "
                    . " FROM `users_tasks` `ut` "
                    . " LEFT JOIN `users` `u` ON `u`.`id` = `ut`.`user_id` "
                    . " WHERE `ut`.`id` = :_id "
                    . ";";
                $ut_query = $db->prepare($sql);
            } catch (Exception $e) {
                $errors[] = 'Failed to load user IDs: ' . $e->getMessage();
            }

            if (empty($errors)) {
                $json['dq'] = array();
                foreach ($user_task_ids as $_id) {
                    if ($ut_query->execute(array('_id' => $_id))) {
                        $_user = $ut_query->fetch();
                        if (!empty($_user)) {
                            // Keep Track of IDs for Task Auto-Resolution
                            $_SESSION['task_shortcut']['rew_dialer'] = true;
                            // Build Response
                            if (!in_array($_user['user_id'], $json['dq'])) {
                                $json['dq'][] = $_user['user_id'];
                            }
                        }
                    }
                }
            }
        } else {
            $errors[] = __('Failed to process tasks - no user task IDs were provided.');
        }
    } else {
        $errors[] = __('Failed to queue calls - REWDialer is not enabled.');
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
