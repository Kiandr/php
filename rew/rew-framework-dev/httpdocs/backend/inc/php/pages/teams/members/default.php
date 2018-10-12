<?php

// Check for Team Management
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
if (isset($_GET['id'])) {
    $team = Backend_Team::load($_GET['id']);
}

// Get Authorization Managers
$settings = Settings::getInstance();
$teamAuth = new REW\Backend\Auth\TeamsAuth($settings);
$teamSubdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Team\SubdomainAuth::class);

/* Throw Missing Team Exception */
if (empty($team) || !$team instanceof Backend_Team) {
    throw new \REW\Backend\Exceptions\MissingId\MissingTeamException();
}

// Authorized to Manage All Teams
if (!$teamAuth->canManageTeams($authuser)) {
    // Authorized to Manage Own Teams
    if (!$teamAuth->canViewTeams($authuser) && $team->getPrimaryAgent() == $authuser->info('id')) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit this team')
        );
    }
}

// Authorized to Assign/Unassign/Edit Team Members
if ($teamAuth->canManageTeams($authuser) ||
    ($teamAuth->canManageOwn($authuser)
    && $team->getPrimaryAgent() == $authuser->info('id'))) {
    // Allow for assigning, unassigning, deletion or updating the team
    $can_assign = true;
    $can_unassign = true;
    $can_edit = true;
    $can_delete = true;
}

// DB connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// Remove a team
if (isset($_GET['delete'])) {
    if (!$can_unassign) {
        $errors[] = __('Error! You do not have the required permissions to remove agents from in this team!');
    }

    // Check that the agent is resolved
    $agent = Backend_Agent::load($_GET['delete']);
    if (!isset($agent)) {
        $errors[] = __('Error! Agent could not be created from provided id!');
    } else if (!isset($team)) {
        $errors[] = __('Error! Team could not be found!');
    }

    // Unassign agent
    if (empty($errors)) {
        try {
            $team->unassignAgent($agent->getId());
            $success[] = __('The selected agent has been removed from this group.');

            // Log Event Users
            $event_users = [new History_User_Agent($authuser->info('id'))];
            if ($authuser->info('id') != $agent->getId()) {
                $event_users[] = new History_User_Agent($agent->getId());
            }

            // Log Event: Added to Team
            $event = new History_Event_Update_TeamRemove(array(
                'team' => ['id' => $team->getId(), 'name' => $team->info('name')],
                'removing_agent' => $authuser->info('id'),
                'secondary_agent' => $agent->getId()
            ), $event_users);

            // Save to DB
            $event->save();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Count Members
$agents_count_query = $db->prepare("SELECT COUNT(DISTINCT(`at`.`agent_id`)) AS 'total'"
    . " FROM `" . TABLE_TEAM_AGENTS . "` `at`"
    . " JOIN `" . LM_TABLE_AGENTS . "` `a` ON `at`.`agent_id` = `a`.`id`"
    . " WHERE `at`.`team_id` = :team_id AND `at`.`agent_id` != :agent_id;");
$agents_count_query->execute(['team_id' => $_GET['id'], 'agent_id' => $team->getPrimaryAgent()]);
$count = $agents_count_query->fetch();

// Manage Members
$agents = [];
if (!empty($count['total'])) {
    // SQL Limit
    $page_limit = 25;
    if ($count > $page_limit) {
        $limitvalue = (($_GET['p'] - 1) * $page_limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $limit_query  = " LIMIT " . $limitvalue . ", " . $page_limit;
    }

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

    // Get Permissions for Each Team Agent
    $agents =  array_map(function ($team_agent) use ($team) {
        $permissions = $team->getAgentPermissions($team_agent);
        return array_merge(['agent_id' => $team_agent], $permissions);
    }, $team->getSecondaryAgents());
}

// Get Primary Agent
$owning_agent = Backend_Agent::load($team->getPrimaryAgent());
