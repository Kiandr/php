<?php

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$agentId = isset($_POST['agent']) ? $_POST['agent'] : $_GET['agent'];
if (empty($agentId)) {
    $agentId = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
}

// Get Backend Agent object for summary
$agent = Backend_Agent::load($agentId);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to view agent history
if (!$agentAuth->canSetTasks()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit this agents action plans.')
    );
}

$task_list = new Module('task-list', array(
    'path'    => Settings::getInstance()->DIRS['BACKEND'] . '/inc/modules/task-list/',
    'user_id' => $agent->getId(),
    'mode'    => 'Agent'
));
