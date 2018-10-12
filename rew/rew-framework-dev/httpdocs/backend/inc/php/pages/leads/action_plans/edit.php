<?php

// Action Plan ID
$action_plan_id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Export All Leads
if (!$leadsAuth->canManageActionPlans($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage action plans.')
    );
}

// Success
$success = array();

// Error
$errors = array();

// Label Colours Dependency
$di = Container::getInstance();
$labelColours = $di->get(REW\Backend\Store\LabelColourStore::class);
$actionPlanLabels = $labelColours->getLabelColours();

// Check if the Action Plan exists
if (!isset($_GET['ajax']) && !isset($_GET['submit'])) {
    $check_plan = Backend_ActionPlan::load($_GET['id']);
    if (empty($check_plan->info('id'))) {
        $errors[] = 'Failed to locate action plan with the requested ID';
        $authuser->setNotices($success, $errors);
        header(sprintf(
            'Location: %sleads/action_plans/',
            Settings::getInstance()->URLS['URL_BACKEND']
        ));
        exit;
    }
}

// Handle AJAX calls from Action Plan Builder
if (isset($_GET['ajax'])) {
    $json = array();

    // Task form submissions
    if (isset($_GET['submitTaskForm'])) {
        // Load Action Plan
        $action_plan = Backend_ActionPlan::load($_POST['actionplan_id']);

        // Require Action Plan
        if ($action_plan) {
            // Format task due time
            $_POST['time'] = date('H:i:s', strtotime($_POST['time']));

            // Save new task
            if ($_POST['mode'] == 'add') {
                try {
                    // Use POST to build the task
                    $task = Backend_Task::create($_POST);

                    if ($task_id = $action_plan->addTask($task)) {
                        $json['task_id'] = $task_id;
                        $success[] = __('Task Added!');
                    } else {
                        $errors[] = __('Error! Unable to save task!');
                    }
                } catch (Exception_ValidationError $e) {
                    // Error saving task
                    $errors[] = $e->getMessage();
                }

            // Edit existing task
            } else if ($_POST['mode'] == 'edit') {
                if ($task = Backend_Task::load($_POST['task_id'])) {
                    // Update the row with the form POST and save
                    $task->setRow($_POST);
                    try {
                        if ($task->save()) {
                            $success[] = __('Task updated!');
                        } else {
                            $errors[] = __('Unable to update task!');
                        }
                    } catch (Exception_ValidationError $e) {
                        // Error saving task
                        $errors[] = $e->getMessage();
                    }
                } else {
                    $errors[] = __('Error! Unable to load task!');
                }
            } else {
                $errors[] = __('Error! Mode not specified!');
            }
        } else {
            $errors[] = __('Error! Action Plan not found!');
        }

    // Delete Task
    } else if (isset($_GET['deleteTask']) && !empty($_POST['task_id'])) {
        // Load Action Plan
        $action_plan = Backend_ActionPlan::load($_POST['actionplan_id']);

        // Require Action Plan
        if ($action_plan) {
            $action_plan->removeTask($_POST['task_id']);
            $success[] = __('Task Deleted!');
        } else {
            $errors[] = __('Error! Action Plan not found!');
        }

    // Generate and return action plan builder markup
    } else if (isset($_GET['drawPlanBuilder'])) {
        // Require Action Plan
        if (!empty($_POST['actionplan_id'])) {
            $db = DB::get();

            // Get Tasks
            $tasks = array();
            $query = $db->prepare("SELECT `id`, `name`, `type`, `performer`, `automated`, `offset`, `time`, `parent_id` "
                . " FROM `" . TABLE_TASKS . "` "
                . " WHERE `actionplan_id` = :actionplan_id "
                . " ORDER BY `offset` ASC, `time` ASC;");
            $query->execute(array('actionplan_id' => $_POST['actionplan_id']));
            while ($row = $query->fetch()) {
                $tasks[] = $row;
            }

            if (!empty($tasks)) {
                // Construct task tree for plan_builder to draw
                $task_tree = array();
                foreach ($tasks as $task) {
                    if (is_null($task['parent_id'])) {
                        $task['parent_id'] = 0;
                    }
                    // Add to tree
                    if (!array_key_exists($task['id'], $task_tree)) {
                        $task_tree[$task['id']] = array('self' => $task);
                    } else {
                        $task_tree[$task['id']]['self'] = $task;
                    }
                    // Add parent to tree
                    if (!array_key_exists($task['parent_id'], $task_tree)) {
                        $task_tree[$task['parent_id']] = array();
                    }
                    // Add to parent
                    if ($task['parent_id'] == 0) {
                        $task_tree[$task['parent_id']][$task['id']] = &$task_tree[$task['id']];
                    } else {
                        $task_tree[$task['parent_id']]['children'][$task['id']] = &$task_tree[$task['id']];
                    }
                }

                ob_start();
                plan_builder($task_tree[0]);
                $json['plan_html'] = ob_get_clean();
            } else {
                $json['plan_html'] = '<div><p>' . __('No Tasks') . '</p></div>';
            }
        } else {
            $errors[] = __('Error! Action Plan not found!');
        }

    // AJAX calls for task add/edit forms
    } else if (isset($_GET['loadTaskForm']) && !empty($_GET['task_mode'])) {
        // Build form via the task-form module
        $task_form = new Module('task-form', array(
            'mode'          => $_GET['task_mode'],
            'task_id'       => $_GET['task_id'],
            'actionplan_id' => $_GET['actionplan_id'],
            'type'          => $_GET['task_type'],
            'path'          => Settings::getInstance()->DIRS['BACKEND'] . '/inc/modules/task-form/'
        ));

        // Return the module ouput
        $json['form_html'] = $task_form->display(false);
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
} else {
    // Process Submit
    if (isset($_GET['submit'])) {
        // Require Plan Name
        if (empty($_POST['name'])) {
            $errors[] = __('Please supply a name for the action plan.');
        }

        // Require at least on Scheduled Day
        if (empty($_POST['day_adjust'])) {
            $errors[] = __('At least one scheduled day is required to create an action plan.');
        }

        if (empty($errors)) {
            // Load Plan
            if ($action_plan = Backend_ActionPlan::load($_POST['actionplan_id'])) {
                // Set new values
                $action_plan->info('name', $_POST['name']);
                $action_plan->info('style', $_POST['style']);
                $action_plan->info('description', $_POST['description']);
                $action_plan->info('day_adjust', implode(',', $_POST['day_adjust']));

                // Save the action plan
                if ($action_plan->save()) {
                    // Save success notice for display on next page
                    $success[] = __('Action plan has successfully been updated.');
                    $authuser->setNotices($success, $errors);

                    // Redirect back to edit form
                    header('Location: ../edit/?id=' . $action_plan->getId());
                    exit;
                }
            }
        }
    }

    // Select Action Plan
    $action_plan = Backend_ActionPlan::load($action_plan_id);

    /* Throw Missing Agent Exception */
    if (empty($action_plan)) {
        throw new \REW\Backend\Exceptions\MissingId\MissingActionPlanException();
    }

    // Options for task type picker menu
    $type_options = task_type_options();
}
