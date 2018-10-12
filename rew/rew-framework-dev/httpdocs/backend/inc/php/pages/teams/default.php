<?php

// Get Authorization Managers
$teamAuth = new REW\Backend\Auth\TeamsAuth(Settings::getInstance());

// Authorized to View All Teams
if (!$teamAuth->canViewTeams($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view teams.')
    );
}

// Check if teams can be added
$can_create = $teamAuth->canManageTeams($authuser) || $teamAuth->canManageOwn($authuser);

// Check if teams can be added
$can_view_unsubscribed = $teamAuth->canManageTeams($authuser);

// DB connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// Delete a team
if (isset($_GET['delete'])) {
    // Check that the team exists
    $team = Backend_Team::load($_GET['delete']);
    if (empty($team)) {
        throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
    }

    // Authorized to Delete any team
    if (!$teamAuth->canManageTeams($authuser)) {
        // Authorized to Delete this team
        if (!$teamAuth->canManageOwn($authuser)) {
            $errors[] = __('Error! You do not have the required permissions to delete this team!');
        }
    }

    // Delete Team
    if (empty($errors)) {
        $team_query = $db->prepare("DELETE FROM `" . TABLE_TEAMS . "` WHERE `id` = :id;");
        if ($team_query->execute(['id' => $team->getId()])) {
            $success[] = __('The selected team has successfully been deleted.');

            // Log Event Users
            $event_users = [new History_User_Agent($authuser->info('id'))];
            if ($authuser->info('id') != $team->getPrimaryAgent()) {
                $event_users[] = new History_User_Agent($team->getPrimaryAgent());
            }

            // Log Event: Team has been deleted
            $event = new History_Event_Delete_Team(array(
                'team' => ['id' => $team->getId(), 'name' => $team->info('name'), 'members' => count($team->getAgents())],
                'deleting_agent' => $authuser->info('id'),
                'primary_agent' => $team->getPrimaryAgent()
            ), $event_users);

            // Save to DB
            $event->save();
        } else {
            $errors[] = __('An error occurred while trying to delete the selected team.');
        }

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect to Page
        header('Location: ?');
        exit;
    }
}

// Quit a team
if (isset($_GET['leave'])) {
    // Check that the agent is resolved
    $team = Backend_Team::load($_GET['leave']);
    if (!isset($team)) {
        $errors[] = __('Error! Team could not be found!');
    }

    // Unassign agent
    if (empty($errors)) {
        try {
            $team->unassignAgent($authuser->info('id'));
            $success[] = __('You have left the group.');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

//Prepare SQL
$join_query = [];
$where_query = [];
$limit_query = [];
$sql_params = [];

// Get Where Param
$sql_params['agent_id'] = $authuser->info('id');
if ($teamAuth->canManageTeams($authuser)) {
    if ($_GET['type'] == 'owned') {
        $where_query[] = "`t`.`agent_id` = :agent_id";
    } else if ($_GET['type'] == 'subscribed') {
        $where_query[] = "`t`.`agent_id` != :agent_id AND `at`.`agent_id` = :agent_id";
    } else {
        unset($sql_params['agent_id']);
    }
} else {
    $where_query[] = "`at`.`agent_id` = :agent_id";
}

$sql_query = "SELECT `t`.`id`"
    . " FROM `" . TABLE_TEAMS . "` `t`"
    . " LEFT JOIN `" . TABLE_TEAM_AGENTS . "` `at` ON `t`.`id` = `at`.`team_id`"
    . (!empty($where_query) ? " WHERE " . implode(' AND ', $where_query) : '')
    . " GROUP BY `t`.`id`";

//Count Teams
$count_query = $db->prepare($sql_query);
$count_query->execute($sql_params);
$count = $count_query->rowCount();

// Check Count
if (!empty($count)) {
    // SQL Limit
    $page_limit = 25;
    if ($count > $page_limit) {
        $limitvalue = (($_GET['p'] - 1) * $page_limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $limit_query  = " LIMIT " . $limitvalue . ", " . $page_limit;
    }

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

    // Select Agents
    $team_query = $db->prepare($sql_query . $sql_limit);
    $team_query->execute($sql_params);
    $teams = $team_query->fetchAll(PDO::FETCH_COLUMN);

    //Allow Agents to leave groups the do not own
    if (!empty($teams)) {
        $unsubscribed = $_GET['type'] == 'unsubscribed';
        $teams = array_filter(array_map(function ($team_id) use ($authuser, $teamAuth, $db, $unsubscribed) {

            $team = Backend_Team::load($team_id);

            // Check if any team can be edited or deleted
            if ($teamAuth->canManageTeams($authuser)
                || ($teamAuth->canManageOwn($authuser)
                && $team['agent_id'] == $authuser->info('id'))
            ) {
                $team['can_edit'] = true;
                $team['can_delete'] = true;
            }

            // Authorized to Manage Subdomain
            if ($team->info("subdomain") == "true"
                && ($team->checkAgent($authuser->info('id'), [Backend_Team::PERM_EDIT_SUBDOMAIN])
                || $teamAuth->canManageTeams($authuser))
            ) {
                $team['can_view_subdomain'] = true;
            }

            //Check if Auth is a Secondary Agent in Team
            if ($authuser->info('id') != $team->getPrimaryAgent()) {
                $team['can_leave'] = true;
            }

            // Filter Unsubscribed Teams
            if ($unsubscribed && in_array($authuser->info('id'), $team->getAgents())) {
                return null;
            }

            // Get Agents Count
            $team['member_count'] = count($team->getAgents());

            return $team;
        }, $teams));
    }
}
