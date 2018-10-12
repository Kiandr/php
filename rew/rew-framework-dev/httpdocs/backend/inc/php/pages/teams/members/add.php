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
    if (!$teamAuth->canManageOwn($authuser) && $team->getPrimaryAgent() == $authuser->info('id')) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit this team')
        );
    }
}

// DB connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// Get Team Permissions
$permissionSets = [
    ['title'       => __('Lead Permissions'),
    'permissions' => [
        new Backend_Team_Permission_Granted_AccessLeads(),
        new Backend_Team_Permission_Granting_ShareLeads()
    ]],
    ['title'       => __('Listing Permissions'),
    'permissions' => [
        new Backend_Team_Permission_Granted_FeatureListings(),
        new Backend_Team_Permission_Granting_ShareFeatureListings()
    ]],
    ['title'       => __('Subdomain Permissions'),
    'permissions' => [
        new Backend_Team_Permission_Granted_EditSubdomain(),
        new Backend_Team_Permission_Granted_AssignLeads()
    ]]
];

// Get History Data
$event_permissions = [
    Backend_Team::GRANTED_KEY => [],
    Backend_Team::GRANTING_KEY => []
];

// Process checksum
$_POST['permissions'][Backend_Team::GRANTED_KEY] = 0;
$_POST['permissions'][Backend_Team::GRANTING_KEY] = 0;

// Build Permissions
foreach ($permissionSets as $k => $permissionSet) {
    foreach ($permissionSet['permissions'] as $permission) {
        $column = $permission->getColumn();
        if (isset($_POST[$column])) {
            $updated_value = 0;
            foreach ($permission->getValues() as $value) {
                if ($_POST[$column] == $value['value'] && (!isset($updated_value) || $value['value'] > $updated_value)) {
                    $updated_value = $value['value'];
                    $updated_value_string = $value['title'];
                }
            }
            $_POST['permissions'][$permission->getKey()] += $updated_value;
            $event_permissions[$permission->getKey()][$permission->getTitle()] = $updated_value_string;
        }
    }
}

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'agent_id', 'title' => 'Agent');

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        try {
            // Fetch added team member's name from the database
            $query = $db->prepare("SELECT CONCAT(`first_name`, ' ', `last_name`) FROM `agents` WHERE `id` = ?;");
            $query->execute([$_POST['agent_id']]);
            $agentName = $query->fetchColumn();

            // Get Valid Permissions
            $team->assignAgent(
                $_POST['agent_id'],
                $_POST['permissions'][Backend_Team::GRANTED_KEY],
                $_POST['permissions'][Backend_Team::GRANTING_KEY]
            );

            // Display success notification
            $success[] = __('%s has successfully been added to the team.', $agentName);
            $authuser->setNotices($success, $errors);

            // Log Event Users
            $event_users = [new History_User_Agent($authuser->info('id'))];
            if ($authuser->info('id') != $_POST['agent_id']) {
                $event_users[] = new History_User_Agent($_POST['agent_id']);
            }

            // Log Event: Added to Team
            $event = new History_Event_Update_TeamAdd(array(
                'team' => ['id' => $team->getId(), 'name' => $team->info('name')],
                'adding_agent' => $authuser->info('id'),
                'secondary_agent' => $_POST['agent_id'],
                'granted_permissions' => serialize($event_permissions[Backend_Team::GRANTED_KEY]),
                'granting_permissions' => serialize($event_permissions[Backend_Team::GRANTING_KEY])
            ), $event_users);

            // Save to DB
            $event->save();

            // Redirect back to team's summary page
            header('Location: ' . URL_BACKEND . 'teams/summary/?id=' . $team->getId());
            exit;

        // Query Error
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Build Permissions
$team_permissions = $_POST['permissions'];
foreach ($permissionSets as $k => $permissionSet) {
    $permissionSet[$k]['permissions'] =  array_map(function ($permission) use ($team_permissions) {
        $permission->use_default = true;
        foreach ($permission->getValues() as $value => $title) {
            if ($title['value'] != 0 && ($team_permissions[$permission->getKey()] & $title['value'])) {
                $permission->use_default = false;
                break;
            }
        }
        return $permission;
    }, $permissionSet['permissions']);
}

//Current Team Agents
$included_agents = $team->getAgents();

// Addable Agents
$agents_query = $db->prepare("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `" . LM_TABLE_AGENTS
    . "` WHERE `id` NOT IN (" . implode(', ', array_fill(0, count($included_agents), '?')) . ") ORDER BY `last_name` ASC;");
$agents_query->execute($included_agents);
$new_agents = $agents_query->fetchAll();
