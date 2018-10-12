<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Export All Leads
if (!$leadsAuth->canManageActionPlans($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage action plans.')
    );
}

// DB connection
$db = DB::get();

// Delete Action Plan
if (!empty($_GET['delete'])) {
    $action_plan = Backend_ActionPlan::load($_GET['delete']);
    if ($action_plan->delete()) {
        $success[] = __('The selected action plan has successfully been deleted.');
    } else {
        $errors[] = __('An error occurred while trying to delete the selected action plan.');
    }
}

// Action Plans collection
$action_plans = array();

// Get all Action Plans
$plans = $db->fetchAll("SELECT * FROM `" . TABLE_ACTIONPLANS . "`;");
foreach ($plans as $plan) {
    // Count the number of tasks on this plan
    $tasks = $db->fetch("SELECT COUNT(`id`) as `count` FROM `" . TABLE_TASKS . "` WHERE `actionplan_id` = '" . $plan['id'] . "';");
    $plan['task_count'] = $tasks['count'];

    // Count the number of leads that have this plan in progress
    $leads_in_progress = $db->fetch("SELECT COUNT(`user_id`) as `count` FROM `" . TABLE_USERS_ACTIONPLANS . "` WHERE `timestamp_completed` IS NULL AND `actionplan_id` = '" . $plan['id'] . "';");
    $plan['leads_in_progress'] = $leads_in_progress['count'];

    // Count the number of leads that have completed this plan
    $leads_completed = $db->fetch("SELECT COUNT(`user_id`) as `count` FROM `" . TABLE_USERS_ACTIONPLANS . "` WHERE `timestamp_completed` IS NOT NULL AND `actionplan_id` = '" . $plan['id'] . "';");
    $plan['leads_completed'] = $leads_completed['count'];

    // Add to collection
    $action_plans[] = $plan;
}
