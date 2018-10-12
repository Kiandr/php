<?php

// Require either edit or add mode
$mode = ($this->config['mode'] == 'edit') ? 'edit' : 'add';

// Task type
$type = !empty($this->config['type']) ? $this->config['type'] : 'Custom';

// DB connection
$db = DB::get();

// Set up for task editing
if ($mode == 'edit') {
    // Task ID required for edit mode
    if (!empty($this->config['task_id'])) {
        // Load the task to edit
        $task_id = intval($this->config['task_id']);
        $task = Backend_Task::load($task_id);

        $task = $task->getRow();

        // Edit Form Title
        $form_title = __('Editing %s Task - "%s"', $task['type'], $task['name']);

        // Format time to be compatable with form input
        $task['time'] = date('h:i A', strtotime($task['time']));
    }

// Set up for task adding
} else if ($mode == 'add') {
    // Parent ID, Plan ID, and Type from plan builder
    $task['parent_id'] = !empty($this->config['task_id']) ? $this->config['task_id'] : false;
    $task['actionplan_id'] = !empty($this->config['actionplan_id']) ? $this->config['actionplan_id'] : false;
    $task['type'] = $type;

    // Get info of parent task this task is being added to
    if (!empty($task['parent_id'])) {
        $parent = $db->fetch("SELECT `name` FROM `" . TABLE_TASKS . "` WHERE `id` = :parent_id;", array('parent_id' => $task['parent_id']));
    }

    // Add Form Title
    $form_title = !empty($parent) ? __('Adding New %s Task - Follow-Up of "%s"', $task['type'], $parent['name'] ) : __('Adding New %s Task ', $task['type']);
}

// Task types that can be automated
$automated_tasks = array('Group', 'Email');
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) {
    $automated_tasks[] = 'Text'; // Auto texts if Twilio available
}
$can_automate = in_array($task['type'], $automated_tasks) ? true : false;

// Text Task Message Length Limit
$text_msg_limit = 160;

// Get pre-built document options for email tasks
if ($task['type'] == 'Email') {
    // Documents
    $documents = array();
    $query = "SELECT `c`.`id` AS `cat_id`, `c`.`name` AS `cat_name`, `d`.`id` AS `doc_id`, `d`.`name` AS `doc_name`"
        . " FROM `" . LM_TABLE_DOC_CATEGORIES . "` `c` LEFT JOIN `" . LM_TABLE_DOCS . "` `d` ON `c`.`id` = `d`.`cat_id`"
        . " ORDER BY `cat_name` ASC, `doc_name` ASC;";
    if ($docs = $db->fetchAll($query)) {
        foreach ($docs as $doc) {
            $documents[$doc['cat_id']]['name'] = $doc['cat_name'];
            $documents[$doc['cat_id']]['docs'][$doc['doc_id']] = $doc['doc_name'];
        }
    } else {
        $errors[] = __('Error occurred while loading Document options.');
    }

// Get group options
} else if ($task['type'] == 'Group') {
    $groups = $db->fetchAll("SELECT `id`, `name`, `style` FROM `groups` WHERE `agent_id` IS NULL AND `associate` IS NULL;");

// Limit Text Message Length
} else if ($task['type'] == 'Text') {
    if (strlen($_POST['message']) > $text_msg_limit) {
        $errors[] = __('Text message contents exceed character limit of %s characters.', $text_msg_limit);
    }
}

// General task input options
$type_options = task_type_options();
$performer_options = task_performer_options();
