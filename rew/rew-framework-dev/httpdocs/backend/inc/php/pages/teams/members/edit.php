<?php

// Check for Team Management
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$team = Backend_Team::load($_GET['id']);

// Agent ID
$_GET['agent'] = isset($_POST['agent']) ? $_POST['agent'] : $_GET['agent'];
$agent = Backend_Agent::load($_GET['agent']);

// Get Authorization Managers
$settings = Settings::getInstance();
$teamAuth = new REW\Backend\Auth\TeamsAuth($settings);
$teamSubdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Team\SubdomainAuth::class);

/* Throw Missing Team Exception */
if (empty($agent) || !$agent instanceof Backend_Agent) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

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

// Process Submit
if (isset($_GET['submit'])) {
    // Check Errors
    if (empty($errors)) {
        try {
            // Process granted checksum
            $event_granted_permissions = [];
            $_POST['permissions'][Backend_Team::GRANTED_KEY] = 0;

            // Process granting checksum
            $event_granting_permissions = [];
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

            // Get Valid Permissions
            $team->updateAgent($agent->getId(), $_POST['permissions'][Backend_Team::GRANTED_KEY], $_POST['permissions'][Backend_Team::GRANTING_KEY]);

            // Log Event Users
            $event_users = [new History_User_Agent($authuser->info('id'))];
            if ($authuser->info('id') != $agent->getId()) {
                $event_users[] = new History_User_Agent($agent->getId());
            }

            // Log Event: Added to Team
            $event = new History_Event_Update_TeamUpdate(array(
                'team' => ['id' => $team->getId(), 'name' => $team->info('name')],
                'updating_agent' => $authuser->info('id'),
                'secondary_agent' => $agent->getId(),
                'granted_permissions' => serialize($event_permissions[Backend_Team::GRANTED_KEY]),
                'granting_permissions' => serialize($event_permissions[Backend_Team::GRANTING_KEY])
            ), $event_users);

            // Save to DB
            $event->save();

            // Notify user of success
            $success[] = __('This team member\'s permissions have successfully been updated.');

            // Save Notices & Redirect to Edit Form
            $authuser->setNotices($success, $errors);
            header('Location: ?id=' . $team->getId() . '&agent=' . $agent->getId());
            exit;

        // Query Error
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Build Permissions
$team_permissions = $team->getAgentPermissions($agent->getId());
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
