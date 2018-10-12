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
$requiredToCancelSubdomains = ($settings->MODULES['REW_LITE'] && $team['subdomain'] === 'true');

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

// DB Connection
$db = DB::get();

// Colours for colour picker
$labelColours = Container::getInstance()->get(REW\Backend\Store\LabelColourStore::class);
$teamLabels = $labelColours->getLabelColours();

// Success
$success = array();

// Errors
$errors = array();

// Can Edit Team
$can_edit = true;

// Can Delete Team
$can_delete = true;

// Can Assign Primary Agent
$can_assign = $teamAuth->canManageTeams($authuser);

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
        ]]
];

// Get Team Subdomain Permissions
if ($teamSubdomainAuth->canManageOwnSubdomain($authuser) || $teamSubdomainAuth->canManageSubdomains($authuser)) {
    $permissionSets[] = [
        'title'       => __('Subdomain Permissions'),
        'permissions' => [
            new Backend_Team_Permission_Granted_EditSubdomain(),
            new Backend_Team_Permission_Granted_AssignLeads()
        ]
    ];
}

// Process checksum
$_POST['permissions'][Backend_Team::GRANTED_KEY] = 0;
$_POST['permissions'][Backend_Team::GRANTING_KEY] = 0;

// Delete Photo
if (isset($_GET['deletePhoto']) && !empty($team['image'])) {
    $updatePhotoQuery = $db->prepare("UPDATE `" . TABLE_TEAMS . "` SET `image` = '' WHERE `id` = :id;");
    if ($updatePhotoQuery->execute(['id' => $team['id']])) {
        if (file_exists(DIR_TEAM_IMAGES . $team['image'])) {
            unlink(DIR_TEAM_IMAGES . $team['image']);
        }
        $success[] = __('Team Photo has successfully been removed.');
        unset($team['image']);
    } else {
        $errors[] = __('Team Photo could not be removed.');
    }
}

// Process Submit
if (isset($_GET['submit'])) {
    // Record Changes
    $changes = array();

    // Get Agent
    if ($can_assign && isset($_POST['agent_id'])) {
        $agent = Backend_Agent::load($_POST['agent_id']);
    } else {
        $agent = Backend_Agent::load($team->getPrimaryAgent());
    }

    // Required Fields
    $required   = array();
    $required[] = array('value' => 'name', 'title' => 'Team Name');

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Process permission checksum
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
            }
        }
    }

    // Agent Subdomain
    if ($requiredToCancelSubdomains || $teamSubdomainAuth->canCreateSubdomains($authuser)) {
        if (!empty($_POST['subdomain_link'])) {
            $_POST['subdomain_link'] = Format::slugify($_POST['subdomain_link']);
        }
        if ($_POST['subdomain'] == 'true') {
            if (empty($_POST['subdomain_link'])) {
                $errors[] = __('Team Link is a required field if Team CMS is enabled.');
            } else {
                try {
                    $query = $db->prepare("SELECT COUNT(`id`) AS `total`"
                        . " FROM `" . LM_TABLE_AGENTS . "`"
                        . " WHERE `cms_link` = :subdomain_link;");
                    $query->execute(['subdomain_link' => $_POST['subdomain_link']]);
                    $duplicate = $query->fetch();
                    if (!empty($duplicate['total'])) {
                        $errors[] = __('An agent with this Team Link already exists. Please use another.');
                    }
                } catch (\Exception $e) {
                    $errors[] = __('Error occurred when checking agents for a duplicate Team Link.');
                }

                try {
                    $query = $db->prepare("SELECT COUNT(`id`) AS `total`"
                        . " FROM `" . TABLE_TEAMS . "`"
                        . " WHERE `subdomain_link` = :subdomain_link"
                        . " AND `id` != :id;");
                    $query->execute(
                        [
                            'subdomain_link' => $_POST['subdomain_link'],
                            'id' => $team->getId(),
                        ]
                    );
                    $duplicate = $query->fetch();
                    if (!empty($duplicate['total'])) {
                        $errors[] = __('A team with this Team Link already exists. Please use another.');
                    }
                } catch (\Exception $e) {
                    $errors[] = __('Error occurred when checking teams for a duplicate Team Link.');
                }
            }
        }
    }

    // Team Subdomains
    $query_extras = '';
    $query_extra_params = [];
    if (($requiredToCancelSubdomains || $teamSubdomainAuth->canCreateSubdomains($authuser)) && empty($errors)) {
        // Only Our Staff May Update The Feed Settings
        if (Settings::isREW()) {
            // Agent CMS Subdomain Feeds Settings
            $team_feeds = is_array($_POST['feeds']) ? implode(",", $_POST['feeds']) : $_POST['feeds'];
            $query_extras .= "`subdomain_idxs` = :subdomain_idxs, ";
            $query_extra_params['subdomain_idxs'] = $team_feeds;
        }

        if (Settings::getInstance()->IDX_FEED !== 'cms' && $team->info('subdomain')!== 'true' && $_POST['subdomain'] === 'true') {
            $mls_info = array();

            // Send Requested Feeds
            if (!empty($_POST['requested_feeds'])) {
                foreach ($_POST['requested_feeds'] as $feed) {
                    try {
                        $idx = Util_IDX::getIdx($feed);

                        $mls_info[$feed] = array(
                            'long_name' => $idx->getTitle(),
                            'agent_id'  => $_POST['feeds_agent'][$feed]
                        );
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }

                if (!\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->sendTeamSubdomainRequest($team['name'], $mls_info)) {
                    $errors[] = __('Unable to send MLS details to IDX department. Please contact support at support@realestatewebmasters.com');
                }
            }
        } else if (Settings::getInstance()->IDX_FEED !== 'cms' && $team->info('subdomain')=== 'true' && $_POST['subdomain'] !== 'true') {
            if (!\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->sendTeamSubdomainCancellationNotice($team)) {
                $errors[] = __('Unable to send cancellation notice to IDX department. Please contact the IDX department at idx@realestatewebmasters.com');
            }
        }


        if (empty($errors)) {

            // Make sure to unset addons if team subdomain is disabled
            if ($_POST['subdomain'] !== 'true') {
                unset($_POST['subdomain_addons']);
            }

            $create_team = true;
            $query_extras .= "`subdomain`    = :subdomain, "
            . "`subdomain_link`  = :subdomain_link, "
            . "`subdomain_addons` = :subdomain_addons, ";
            $query_extra_params = array_merge($query_extra_params, [
                'subdomain' => $_POST['subdomain'],
                'subdomain_link' => $_POST['subdomain_link'],
                'subdomain_addons' => (isset($_POST['subdomain_addons']) ? implode(',', $_POST['subdomain_addons']) : '')
            ]);
        } else {
            $errors[] = __('Unable to save team website details until the IDX department has been given MLS details. Please contact support at support@realestatewebmasters.com');
        }
    }

    // Upload Team Photo (Resize to 150 by 150)
    if ($_FILES['team_photo']['size'] > 0) {
        $extention = end(explode('.', $_FILES['team_photo']['name']));
        if (!Validate::image($_FILES['team_photo']['tmp_name'], $extention)) {
            $errors[] = __('The photo must be a JPG, GIF, or PNG file.');
        } else {
            $imageName = mt_rand() . '.' . $extention;
            if (move_uploaded_file($_FILES['team_photo']['tmp_name'], DIR_TEAM_IMAGES . $imageName)) {
                $query_extras .= "`image`= :image, ";
                $query_extra_params['image'] = $imageName;
                if (!empty($_POST['image'])) {
                    if (file_exists(DIR_TEAM_IMAGES . $_POST['image'])) {
                        unlink(DIR_TEAM_IMAGES . $_POST['image']);
                    }
                }
            }
        }
    }

    // Reassign agent
    if (isset($agent) && $can_assign && ($agent->getId() != $team->getPrimaryAgent())) {
        // Reassign agent
        try {
            $team->reassignAgent($agent->getId(), $_POST['permissions'][Backend_Team::GRANTED_KEY], $_POST['permissions'][Backend_Team::GRANTING_KEY]);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        // Updated Priamry Agent Change History
        $changes['agent_id'] = [
            'changing_agent' => $authuser->info('id'),
            'team' => ['id' => $team->getId(), 'name' => $team->info('name')],
            'field' => 'agent_id',
            'old' => $team->getPrimaryAgent(),
            'new' => $agent->getId()
        ];

    // Update agent
    } else {
        // Update agent permissions
        try {
            $team->updateAgent($agent->getId(), $_POST['permissions'][Backend_Team::GRANTED_KEY], $_POST['permissions'][Backend_Team::GRANTING_KEY]);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    // Check Errors
    if (empty($errors)) {
        foreach ($team->getRow() as $k => $v) {
            //Skip Agent Changes
            if ($k == 'agent_id') {
                continue;
            }

            // Record Base Changes
            if (isset($_POST[$k]) && ($_POST[$k] != $v)) {
                $changes[$k] = [
                    'team' => ['id' => $team->getId(), 'name' => $team->info('name')],
                    'field' => $k,
                    'old' => $v,
                    'new' => $_POST[$k]
                ];
            }
        }

        try {
            // Build UPDATE Query
            $edit_query = $db->prepare("UPDATE `" . TABLE_TEAMS . "` SET "
               . "`name`        = :name, "
               . "`description` = :description, "
               . "`style`       = :style, "
               . $query_extras
               . "`timestamp`       = NOW()"
               . " WHERE `id` = :id;");

            $edit_params = array_merge([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'style' => $_POST['style'],
                'id' => $team->getId()
            ], $query_extra_params);

            // Execute Query
            $edit_query->execute($edit_params);

            // Success
            $success[] = __('Your changes have successfully been saved.');

            // Load Updated Team
            $team = Backend_Team::load($team->getId());

            // Log Event: Track Team Change
            foreach ($changes as $k => $v) {
                // Log Event Users
                $event_users = [new History_User_Agent($authuser->info('id'))];
                $v['updating_agent'] = $authuser->info('id');
                if (isset($agent) && ($agent->getId() != $authuser->info('id'))) {
                    $event_users[] = new History_User_Agent(strval($agent->getId()));
                    $v['primary_agent'] = $agent->getId();
                }

                // Log Primary Team Agent Change
                if ($can_assign && $k == 'agent_id') {
                    if ($authuser->info('id') != $v['old']) {
                        $event_users[] = new History_User_Agent($v['old']);
                    }
                    if (!in_array($v['new'], [$authuser->info('id'), $v['old']])) {
                        $event_users[] = new History_User_Agent($v['new']);
                    }
                }

                // Save Event
                $event = new History_Event_Update_Team($v, $event_users);
                $event->save();
            }

            // Team CMS Sub-Site
            if ($create_team) {
                team_site($team, $errors);
            }

            // Save Notices & Redirect to Edit Form
            $authuser->setNotices($success, $errors);
            header('Location: ?id=' . $team->getId());
            exit;

        // Query Error
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
            $errors[] = __('Error occurred, Team could not be added. Please try again.');
        }
    }
}

// Get Agent List
if ($can_assign) {
    // Available Agents
    $agents = array();
    try {
        $query = $db->prepare("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name`"
            . " FROM `" . LM_TABLE_AGENTS . "`"
            . " ORDER BY `last_name` ASC;");
        $query->execute();
        $agents = $query->fetchAll();
    } catch (\Exception $e) {
        $errors[] = __('An error occurred while loading Available Agents.');
    }
}

// Get Team CMS Settings
if (!empty(Settings::getInstance()->MODULES['REW_TEAM_CMS'])) {
    // List Of IDXs Agent Site Has Access To
    $team_idxs = explode(",", $team['subdomain_idxs']);

    // List Of IDXs Main Site Has Access To
    $idx_feeds = !empty(Settings::getInstance()->IDX_FEEDS) ? Settings::getInstance()->IDX_FEEDS : array(Settings::getInstance()->IDX_FEED => array('title' => strtoupper(Settings::getInstance()->IDX_FEED)));

    try {
        $all_feeds = array();

        foreach ($idx_feeds as $feed => $info) {
            $idx = Util_IDX::getIdx($feed);

            if ($idx->isCommingled()) {
                $feeds = $idx->getFeeds();
                foreach ($feeds as $feed) {
                    if (!isset($idx_feeds[$feed])) {
                        $idx = Util_IDX::getIdx($feed);
                        $all_feeds[$feed] = $idx->getTitle();
                    }
                }
            } else {
                $all_feeds[$feed] = $idx->getTitle();
            }
        }

        $idx_feeds = $all_feeds;
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Build Permissions
$team_permissions = $team->getAgentPermissions($team->getPrimaryAgent());
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

// Subdomain addons
$team['subdomain_addons'] = explode(',', $team['subdomain_addons']);

// Addon Config
$addons = [];
foreach (Util_CMS::SUBDOMAIN_MODULE_CONFIG_KEYS as $config) {
    if (Settings::getInstance()->MODULES[$config['module_key']] !== false) {
        $addons[] = $config;
    }
}