<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Export All Leads
if (!$leadsAuth->canManageActionPlans($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage action plans.')
    );
}

$task_list = new Module('task-list', array(
    'path' => Settings::getInstance()->DIRS['BACKEND'] . '/inc/modules/task-list/',
    'mode' => 'Admin'
));
