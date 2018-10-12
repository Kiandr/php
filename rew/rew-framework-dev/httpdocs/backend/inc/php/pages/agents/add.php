<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization Managers
$settings = Settings::getInstance();
$agentsAuth = new REW\Backend\Auth\AgentsAuth($settings);
$subdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Agent\SubdomainAuth::class);

// Authorized to Create Leads
if (!$agentsAuth->canManageAgents($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to add an agent.')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Warnings
$warnings = array();

// Process Submission
if (isset($_GET['submit'])) {
    // Extra SQL Query
    $query_extras = '';
    $query_extras_vals =[];

    // Trim Whitespaces
    $fields = array('first_name', 'last_name', 'email', 'username', 'password');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $_POST[$field] = rtrim($_POST[$field], ' ');
        }
    }

    // Required Fields
    $required   = array();
    $required[] = array('value' => 'first_name', 'title' => __('First Name'));
    $required[] = array('value' => 'last_name',  'title' => __('Last Name'));
    $required[] = array('value' => 'email',   'title' => __('Email Address'));
    $required[] = array('value' => 'username',   'title' => __('Username'));
    $required[] = array('value' => 'password',   'title' => __('Password'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Require Valid Email Address
    if (!Validate::email($_POST['email'], true)) {
        $errors[] = __('Please supply a valid email address.');
    }

    // Require Valid SMS Email Address
    if (!empty($_POST['sms_email'])) {
        if (!Validate::email($_POST['sms_email'], true)) {
            $errors[] = __('Please supply a valid SMS email address.');
        }
    }

    // Password Validation
    if (!empty($_POST['password'])) {
        try {
            Validate::password($_POST['password']);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    // Validate Username (Check for Duplicates)
    if (!empty($_POST['username'])) {
        Auth::validateUsername($_POST['username'], null, $errors);
    }

    // Upload Agent Photo if exists
    if ($_FILES['agent_photo']['size'] > 0) {
        try {
            // Handle agent image upload
            $uploader = new Backend_Uploader_Form('agent_photo', 'images');
            $uploader->handleUpload(DIR_AGENT_IMAGES, false);

            // If upload is successful, update agent settings
            $query_extras .= "`image`=" . $db->quote($uploader->getName()) . ", ";
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
    }


    // Agent CMS Subdomain
    if ($subdomainAuth->canCreateSubdomains()) {
        try {
            // Get Formatted Subdomain Link
            if (!empty($_POST['cms_link'])) {
                $cmsLink = Format::slugify($_POST['cms_link']);
            }

            // Check for duplicate
            if ($_POST['cms'] == 'true') {
                if (!isset($cmsLink)) {
                    $errors[] = __('Agent Link is a required field if Agent CMS is enabled.');
                } else {
                    // Check Duplicate Agent Subdomains
                    try {
                        $duplicate = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . LM_TABLE_AGENTS . "` WHERE `cms_link` = :cms_link;", ['cms_link' => $cmsLink]);
                    } catch (PDOException $e) {}
                    if (!empty($duplicate['total'])) {
                        throw new InvalidArgumentException(
                            __('An agent subdomain with this link already exists. Please use another.')
                        );
                    }

                    // Check Duplicate Teams Subdomains
                    if (!empty(Settings::getInstance()->MODULES['REW_TEAMS']) &&!empty(Settings::getInstance()->MODULES['REW_TEAM_CMS'])) {
                        try {
                            $duplicate = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_TEAMS . "` WHERE `subdomain_link` = :subdomain_link;", ['subdomain_link' => $cmsLink]);
                        } catch (PDOException $e) {}
                        if (!empty($duplicate['total'])) {
                            throw new InvalidArgumentException(
                                __('A team subdomain with this link already exists. Please use another.')
                            );
                        }
                    }
                }
            }

            // Only Our Staff May Update The Feed Settings
            if (Settings::isREW()) {
                // Agent CMS Subdomain Feeds Settings
                $agent_feeds = is_array($_POST['feeds']) ? implode(",", $_POST['feeds']) : $_POST['feeds'];
                $query_extras .= "`cms_idxs` = :cms_idxs, ";
                $query_extras_vals["cms_idxs"] = $agent_feeds;
            }

            // Enable Subdomain Request
            if (Settings::getInstance()->IDX_FEED !== 'cms' && $_POST['cms'] === 'true') {
                $mls_info = array();

                if (!empty($_POST['requested_feeds'])) {
                    foreach ($_POST['requested_feeds'] as $feed) {
                        $idx = Util_IDX::getIdx($feed);

                        $mls_info[$feed] = array(
                            'long_name' => $idx->getTitle(),
                            'agent_id'  => $_POST['feeds_agent'][$feed]
                        );
                    }
                } else {
                    throw new InvalidArgumentException(
                        __('Unable to set up agent subdomain. At least one feed must be provided.')
                    );
                }

                if (!\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->sendAgentSubdomainRequest($_POST['first_name'], $_POST['last_name'], $mls_info, $cmsLink)) {
                    throw new InvalidArgumentException(
                        __('Unable to send MLS details to IDX department.  Please contact support at support@realestatewebmasters.com')
                    );
                }
            }

            // Update Subdomain Query
            $query_extras .= "`cms`       = :cms, "
                .  "`cms_link`  = :cms_link, "
                . "`cms_addons` = :cms_addons, ";
            $query_extras_vals["cms"] = $_POST['cms'];
            $query_extras_vals["cms_link"] = $_POST['cms_link'];
            $query_extras_vals['cms_addons'] = isset($_POST['cms_addons']) ? implode(',', $_POST['cms_addons']) : '';

        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = __('Unable to save agent website details until the IDX department has been given MLS details.  Please contact support at support@realestatewebmasters.com');
        }
    }

    // Check Errors
    if (empty($errors)) {
        //Get Encrypted Password
        $password = $authuser->encryptPassword($_POST['password']);

        // Insert Auth Account
        try {
            $db->prepare("INSERT INTO `" . Auth::$table . "` SET "
                . "`type`				= :type, "
                . "`username`			= :username, "
                . "`password`			= :password, "
                . "`timestamp_created`	= NOW();")
            ->execute([
                'type' => Auth::TYPE_AGENT,
                'username' => $_POST['username'],
                'password' => $password
            ]);

            // Auth ID
            $auth_id = $db->lastInsertId();

            // Query Error
        } catch (PDOException $e) {
            $errors[] = __('An error occurred while creating new account.');
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Require ENUM('true', 'false') DEFAULT 'false'
        $_POST['auto_assign_agent'] = ($_POST['auto_assign_agent'] == 'true') ? 'true' : 'false';
        $_POST['auto_assign_admin'] = ($_POST['auto_assign_admin'] == 'true') ? 'true' : 'false';
        $_POST['auto_rotate'] = ($_POST['auto_rotate'] == 'true') ? 'true' : 'false';
        $_POST['auto_optout'] = ($_POST['auto_optout'] == 'true') ? 'true' : 'false';

        // Require ENUM('true', 'false') DEFAULT 'false'
        $_POST['auto_search'] = ($_POST['auto_search'] == 'true') ? 'true' : 'false';

        // Default Permissions
        $permissions = array(
            Auth::PERM_LISTINGS_AGENT,
            Auth::PERM_AGENTS_VIEW,
            Auth::PERM_AGENTS_EMAIL,
            Auth::PERM_LEADS_CAMPAIGNS,
            Auth::PERM_CALENDAR_AGENT,
            Auth::PERM_TEAMS_VIEW,
            (!empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED']) ? Auth::PERM_BLOG_AGENT : 0)
        );
        $permissions = array_sum($permissions);

        // Agent Manager Extras
        if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) {
            $query_extras .= "`display`  = :display, ";
            $query_extras_vals["display"] = $_POST['display'];
            $query_extras .= "`website`  = :website, ";
            $query_extras_vals["website"] = $_POST['website'];
            // Format Agent ID
            $agent_ids = Format::trim($_POST['agent_id']);
            if (!empty($agent_ids) && is_array($agent_ids)) {
                $agent_id = array();
                foreach ($agent_ids as $feed => $id) {
                    if (!Util_IDX::getFeed($feed)) {
                        continue;
                    }
                    if (empty($id)) {
                        continue;
                    }
                    $agent_id[$feed] = $id;
                }
                $agent_id = !empty($agent_id) ? json_encode($agent_id) : '';
                $query_extras .= "`agent_id` = :agent_id, ";
                $query_extras_vals["agent_id"] = $agent_id;
            }
        }

        // REW API Extras
        if ($agentsAuth->canManageApp($authuser)) {
            $query_extras .= "`auto_assign_app_id` = :auto_assign_app_id, ";
            $query_extras_vals["auto_assign_app_id"] = (!empty($_POST['api_source_id']) ? $_POST['api_source_id'] : null);
            $query_extras .= "`auto_rotate_app_id` = :auto_rotate_app_id, ";
            $query_extras_vals["auto_rotate_app_id"] = (!empty($_POST['api_source_id']) ? $_POST['api_source_id'] : null);
        }

        // Agent Spotlight Extras
        if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) {
            $query_extras .= "`display_feature` = :display_feature, ";
            $query_extras_vals["display_feature"] = $_POST['display_feature'];
        }

        // Showing Suite Settings
        if (!empty(Settings::getInstance()->MODULES['REW_SHOWING_SUITE'])) {
            $query_extras .= "`showing_suite_email` = :showing_suite_email, ";
            $query_extras_vals["showing_suite_email"] = $_POST['showing_suite_email'];
        }

        // Featured Offices
        $office = !empty($_POST['office']) ? intval($_POST['office']) : null;
        $query_extras .= "`office` = :office, ";
        $query_extras_vals["office"] = $office;

        // Build INSERT Query
        try{
            $db->prepare("INSERT INTO `" . LM_TABLE_AGENTS . "` SET "
                . "`auth`              = :auth, "
                . "`first_name`        = :first_name, "
                . "`last_name`         = :last_name, "
                . "`email`             = :email, "
                . "`sms_email`         = :sms_email, "
                . "`default_filter`    = :default_filter, "
                . "`default_order`     = :default_order, "
                . "`default_sort`      = :default_sort, "
                . "`timezone`		   = :timezone, "
                . "`add_sig`		   = :add_sig, "
                . "`signature`		   = :signature, "
                . "`page_limit`		   = :page_limit, "
                . "`remarks`		   = :remarks, "
                . "`office_phone`	   = :office_phone, "
                . "`home_phone`		   = :home_phone, "
                . "`cell_phone`		   = :cell_phone, "
                . "`fax`			   = :fax, "
                . "`title`			   = :title, "
                . $query_extras
                // Agent Permissions
                . "`permissions_user`  = :permissions_user, "
                . "`permissions_admin` = :permissions_admin, "
                // Auto Assignment & Auto Rotation
                . "`auto_rotate`	   = :auto_rotate, "
                . "`auto_optout`	   = :auto_optout, "
                . "`auto_search`	   = :auto_search, "
                . "`auto_assign_admin` = :auto_assign_admin, "
                . "`auto_assign_agent` = :auto_assign_agent, "
                . "`auto_assign_time`  = NOW(), "
                . "`timestamp`		 = NOW();")
            ->execute(array_merge([
                'auth' => $auth_id,
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'email' => trim($_POST['email']),
                'sms_email' => trim($_POST['sms_email']),
                'default_filter' => $_POST['default_filter'],
                'default_order' => $_POST['default_order'],
                'default_sort' => $_POST['default_sort'],
                'timezone' => $_POST['timezone'],
                'add_sig' => $_POST['add_sig'],
                'signature' => $_POST['signature'],
                'page_limit' => $_POST['page_limit'],
                'remarks' => $_POST['remarks'],
                'office_phone' => $_POST['office_phone'],
                'home_phone' => $_POST['home_phone'],
                'cell_phone' => $_POST['cell_phone'],
                'fax' => $_POST['fax'],
                'title' => $_POST['title'],
                'permissions_user'  => $permissions,
                'permissions_admin'=> 0,
                'auto_rotate' => $_POST['auto_rotate'],
                'auto_optout' => $_POST['auto_optout'],
                'auto_search' => $_POST['auto_search'],
                'auto_assign_admin' => $_POST['auto_assign_admin'],
                'auto_assign_agent' => $_POST['auto_assign_admin']
            ],$query_extras_vals));

            // Insert ID
            $insert_id = $db->lastInsertId();

            // Success
            $success[] = __('Agent has successfully been created.');

            // Select Agent Row
            try {
                $agent = $db->fetch("SELECT * FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :id;", ['id' => $insert_id]);
            } catch (PDOException $e) {}

            if (Skin::hasFeature(Skin::AGENT_EDIT_NETWORKS)) {
                $backendAgent = new Backend_Agent($agent);
                $backendAgent->setSocialNetworks($_POST);
            }

            // Agent CMS Sub-Site
            agent_site($agent, $errors);

            // Opted In
            if ($agent['auto_assign_agent'] == 'true') {
                // Log Event: Agent has been opted in
                $event = new History_Event_Update_OptIn(array(
                    'admin'     => $authuser->info('id')
                ), array(
                    new History_User_Agent($agent['id']),
                    new History_User_Agent($authuser->info('id'))
                ));

                // Save to Database
                $event->save();
            }

            // Update RE/MAX Launchpad Credentials
            if (!empty(Settings::getInstance()->MODULES['REW_REMAX_LAUNCHPAD'])) {
                if (isset($_POST['remax_launchpad_username']) && !empty($_POST['remax_launchpad_username'])) {
                    // Check if Another User on This Site Already Uses the Username
                    try {
                        $remax_creds = $db->fetch("SELECT `remax_launchpad_username` FROM `" . LM_TABLE_AGENTS . "` WHERE `remax_launchpad_username` = :remax_launchpad_username LIMIT 1;", ['remax_launchpad_username' => $_POST['remax_launchpad_username']]);

                        // Send (Create/Delete/Update) Request to Central SSO System
                        $curl = new Util_Curl();
                        $curl->setBaseURL('https://sso.realestatewebmasters.com');
                        $response = $curl->executeRequest('/launchpad/accounts/', array('new_url' => $_SERVER['HTTP_HOST'], 'new_user' => $_POST['remax_launchpad_username']), Util_Curl::REQUEST_TYPE_POST, array(
                            CURLOPT_SSL_VERIFYHOST => 2,
                            CURLOPT_SSL_VERIFYPEER => true
                        ));
                        if (!empty($response)) {
                            $response = (is_string($response)) ? json_decode($response) : $response;

                            // Update DB Entry on SUCCESS Response
                            if ($response->return_status == 'success') {
                                // Update Local RE/MAX Launchpad Username Record
                                try {
                                    $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET "
                                        . "`remax_launchpad_username`   = :remax_launchpad_username, "
                                        . "`remax_launchpad_url`		= :remax_launchpad_url "
                                        . " WHERE "
                                        . "`id` = :id;")
                                    ->execute([
                                        'remax_launchpad_username' => $_POST['remax_launchpad_username'],
                                        'remax_launchpad_url' => $_SERVER['HTTP_HOST'],
                                        'id' =>  $agent['id']
                                    ]);


                                } catch (PDOException $e) {
                                    $errors[] = __('Failed to update RE/MAX Launchpad username.');
                                }

                            } else {
                                $errors[] = __(
                                    'Failed to update RE/MAX Launchpad username records. If the issue persists, please contact {linkStart}our support team{linkEnd}',
                                    [
                                        '{linkStart}' => '<a href="mailto:support@realestatewebmasters.com">',
                                        '{linkEnd}' => '</a>'
                                    ]
                                );
                            }
                        }
                    } catch (PDOException $e) {
                        $errors[] = __('Failed to update RE/MAX Launchpad username records. Username is used by another account on this site.');
                    }
                }
            }

            // Trigger hook after agent account is created
            Hooks::hook(Hooks::HOOK_AGENT_CREATE)->run($agent);

            // Save Notices & Redirect to Edit Form
            $authuser->setNotices($success, $errors, $warnings);
            header('Location: ../agent/edit/?id=' . $insert_id);
            exit;

            // Query Error
        } catch (PDOException $e) {
            $errors[] = __('Error occurred, Agent could not be added. Please try again.');
        }
    }
}

// Default Page Limit
$_POST['page_limit'] = isset($_POST['page_limit']) ? $_POST['page_limit'] : 20;

// Select Defaults from Super Admin
try {
    foreach ($db->fetchAll("SELECT `a`.`timezone` FROM `" . LM_TABLE_AGENTS . "` `a` WHERE `a`.`id` = '1';") as $k => $v) {
        $_POST[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
} catch (PDOException $e) {}

// Timezones
try {
    foreach ($db->fetchAll("SELECT `t`.*, SEC_TO_TIME(`time_diff`) as `gmt_off` FROM `" . LM_TABLE_TIMEZONES . "` `t` ORDER BY `time_diff`, `daylight_savings`;") as $row) {
        // strip off the unneeded :00 from the end
        $row['gmt_off'] = substr($row['gmt_off'], 0, strpos($row['gmt_off'], ':00'));
        $timezone[] = $row;
    }
} catch (PDOException $e) {}

// Select Offices
try {
    $offices = $db->fetchAll("SELECT `id` AS `value`, `title` AS `title` FROM `" . TABLE_FEATURED_OFFICES . "` ORDER BY `sort` ASC;");
} catch (PDOException $e) {}

// API Applications
$api_applications = array();
if (!empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
    try {
        foreach ($db->fetchAll("SELECT * FROM `api_applications` ORDER BY `name`;") as $api_app) {
            $api_applications[] = $api_app;
        }
    } catch (PDOException $e) {}
}

if ($subdomainAuth->canCreateSubdomains($authuser)) {
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

// Load social media networks
$networks = Skin::hasFeature(Skin::AGENT_EDIT_NETWORKS) ? Backend_Agent::getAvailableSocialNetworks() : array();

// Addon Config
$addons = [];
foreach (Util_CMS::SUBDOMAIN_MODULE_CONFIG_KEYS as $config) {
    if (Settings::getInstance()->MODULES[$config['module_key']] !== false) {
        $addons[] = $config;
    }
}