<?php


// Get Authorization Managers
$settings = Settings::getInstance();
$teamAuth = new REW\Backend\Auth\TeamsAuth($settings);
$teamSubdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Team\SubdomainAuth::class);

// Authorized to Create Teams
if (!$teamAuth->canManageTeams($authuser)) {
    // Authorized to Create Own Teams
    if (!$teamAuth->canManageOwn($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to create teams.')
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
        }
    }
}

// Process Submit
if (isset($_GET['submit'])) {
    // Get Agent
    if (!$can_assign) {
        $_POST['agent_id'] = $authuser->info('id');
    }
    $agent = Backend_Agent::load($_POST['agent_id']);

    // Ensure Valid Agent
    if (!isset($agent)) {
        $errors[] = __('An invalid agent id was provided.');
    }

    // Required Fields
    $required   = array();
    $required[] = array('value' => 'name', 'title' => 'Team Name');
    $required[] = array('value' => 'agent_id', 'title' => 'Agent Id');

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Agent Subdomain
    if ($teamSubdomainAuth->canCreateSubdomains($authuser)) {
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
                        . " WHERE `subdomain_link` = :subdomain_link");
                    $query->execute(['subdomain_link' => $_POST['subdomain_link']]);
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
    if ($teamSubdomainAuth->canCreateSubdomains($authuser) && empty($errors)) {
            // Only Our Staff May Update The Feed Settings
        if (Settings::isREW()) {
            // Agent CMS Subdomain Feeds Settings
            $team_feeds = is_array($_POST['feeds']) ? implode(",", $_POST['feeds']) : $_POST['feeds'];
            $query_extras .= "`subdomain_idxs` = :subdomain_idxs, ";
            $query_extra_params['subdomain_idxs'] = $team_feeds;
        }

            // Check if a team was added
        if (Settings::getInstance()->IDX_FEED !== 'cms' && $_POST['subdomain'] === 'true') {
            $mls_info = array();

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
            }

            if (!\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->sendTeamSubdomainRequest($_POST['name'], $mls_info)) {
                $errors[] = __('Unable to send MLS details to IDX department. Please contact support at support@realestatewebmasters.com');
            }
        }

            // Update Query Extra
        if (empty($errors)) {
            $create_team = true;
            $query_extras .= "`subdomain`    = :subdomain, "
            . "`subdomain_link`  = :subdomain_link, "
            . "`subdomain_addons` = :subdomain_addons, ";
            $query_extra_params = array_merge($query_extra_params, [
                'subdomain' => $_POST['subdomain'],
                'subdomain_link' => $_POST['subdomain_link'],
                'subdomain_addons' => (isset($_POST['subdomain_addons']) ? implode(',', $_POST['subdomain_addons']) : '')
            ]);
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
            }
        }
    }

        // Check Errors
    if (empty($errors)) {
        // Build INSERT Query
        $insert_query = $db->prepare("INSERT INTO `" . TABLE_TEAMS . "` SET "
            . "`agent_id` = :agent_id, "
            . "`name`        = :name, "
            . "`description` = :description, "
            . "`style`       = :style, "
            . $query_extras
            . "`timestamp`       = NOW();");

        $insert_params = array_merge([
            'agent_id' => $agent->getId(),
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'style' => $_POST['style']
        ], $query_extra_params);

        // Execute Query
        if ($insert_query->execute($insert_params)) {
            // Insert ID
            $insert_id = $db->lastInsertId();

            // Create Team Primary Agent
            $insert_agent_query = $db->prepare(
                "INSERT INTO `" . TABLE_TEAM_AGENTS. "` SET "
                . "`team_id` = :team_id, "
                . "`agent_id` = :agent_id, "
                . "`granted_permissions`  = :granted_permissions, "
                . "`granting_permissions` = :granting_permissions;"
            );

            $insert_agent_params= [
                'team_id'              => $insert_id,
                'agent_id'             => $agent->getId(),
                'granted_permissions'  => $_POST['permissions'][Backend_Team::GRANTED_KEY],
                'granting_permissions' => $_POST['permissions'][Backend_Team::GRANTING_KEY]
            ];
            $insert_agent_query->execute($insert_agent_params);

            // Select Teams Row
            $query = $db->prepare("SELECT * FROM `" . TABLE_TEAMS . "` WHERE `id` = :id");
            $query->execute(['id' => $insert_id]);
            $team = $query->fetch();

            // Team CMS Subdomain
            if ($create_team) {
                team_site($team, $errors);
            }

            // Log Event Users
            $event_users = [new History_User_Agent($authuser->info('id'))];
            if ($authuser->info('id') != $agent->getId()) {
                $event_users[] = new History_User_Agent($agent->getId());
            }

            // Log Event: New Team Created
            $event = new History_Event_Create_Team(array(
                'id' => $insert_id,
                'name' => $team['name'],
                'creating_agent' => $authuser->info('id'),
                'primary_agent' => $agent->getId()
            ), $event_users);

            // Save to DB
            $event->save();

            // Display success notification
            $success[] = sprintf(__('%s has successfully been created.', $_POST['name']));
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../members/?id=' . $insert_id);
            exit;

        // Query Error
        } else {
            $errors[] = __('Error occurred, Team could not be added. Please try again.');
        }
    }
}

//Get Agent List
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

if ($teamSubdomainAuth->canCreateSubdomains($authuser)) {
        // List Of IDXs Team Site Has Access To
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

// Addon Config
$addons = [];
foreach (Util_CMS::SUBDOMAIN_MODULE_CONFIG_KEYS as $config) {
    if (Settings::getInstance()->MODULES[$config['module_key']] !== false) {
        $addons[] = $config;
    }
}
