<?php

// Get CMS DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Warnings
$warnings = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$agent = Backend_Agent::load($_GET['id']);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);
$subdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Agent\SubdomainAuth::class);
$calendarAuth = new REW\Backend\Auth\CalendarAuth($settings);

// Not authorized to view all leads
if (!$agentAuth->canEditAgent()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit this agent')
    );
}

// New Row Successful
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = __('New Agent has successfully been created.');
}

// Authorized to Delete Agent
$can_delete = $agentAuth->canDeleteAgent();

// Authorized to Email Leads
$can_email = $agentAuth->canEmailAgent();

// Authorized to edit admin only fields
$can_edit_admin_only_fields = $agentAuth->canManageAgent();

// REW_LITE disables add functionality, but maintains delete functions
$requiredToCancelSubdomains = ($settings->MODULES['REW_LITE'] && $agent['cms'] === 'true');

// Format Agent ID
if (!empty($agent['agent_id'])) {
    $agent_id = json_decode($agent['agent_id'], true);
    if (!is_array($agent_id)) {
        $agent_id = array(
            Settings::getInstance()->IDX_FEED => $agent['agent_id'],
        );
    }
    $agent['agent_id'] = $agent_id;
}

// Agent ID Default
if (empty($agent['agent_id'])) {
    $agent['agent_id'] = array(Settings::getInstance()->IDX_FEED => '');
}

// Delete Photo
if (isset($_GET['deletePhoto']) && !empty($agent['image'])) {
    try {
        $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET `image` = '' WHERE `id` = :id;")->execute(['id' => $agent['id']]);
        if (file_exists(DIR_AGENT_IMAGES . $agent['image'])) {
            unlink(DIR_AGENT_IMAGES . $agent['image']);
        }
        $success[] = __('Agent Photo has successfully been removed.');
        unset($agent['image']);
    } catch (PDOException $e) {
        $errors[] = __('Agent Photo could not be removed.');
    }
}

// Process Submission
if (isset($_GET['submit'])) {
    // Extra Query
    $query_extras = '';
    $query_extras_vals = [];

    // Trim Whitespaces
    $fields = array('first_name', 'last_name', 'email', 'username');
    if ($_POST['update_password']) {
        array_merge($fields, array('new_password','confirm_password'));
    }
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $_POST[$field] = rtrim($_POST[$field], ' ');
        }
    }

    // Require First Name
    if (empty($_POST['first_name'])) {
        $errors[] = __('Please supply a first name.');
    }

    // Require Last Name
    if (empty($_POST['last_name'])) {
        $errors[] = __('Please supply a last name.');
    }

    // Require Vaid Email
    if (!Validate::email($_POST['email'], true)) {
        $errors[] = __('Please supply a valid email address.');
    }

    // Require Valid SMS Email Address
    if (!empty($_POST['sms_email'])) {
        if (!Validate::email($_POST['sms_email'], true)) {
            $errors[] = __('Please supply a valid SMS email address.');
        }
    }

    // Not Editing Super Admin
    if ($agent['id'] != 1) {
        // Validate Username
        Auth::validateUsername($_POST['username'], $agent['auth'], $errors);

        // Require Password
        if ($_POST['update_password']) {
            if (empty($_POST['new_password']) && $agent['id'] != 1) {
                $errors[] = __('Please supply a password');
            }
            if (empty($_POST['confirm_password']) && $agent['id'] != 1) {
                $errors[] = __('Please confirm your new password');
            }
            if ($_POST['new_password'] != $_POST['confirm_password'] && $agent['id'] != 1) {
                $errors[] = __('Password confirmation does not match');
            }
        }

        // Password Validation
        if (!empty($_POST['new_password'])) {
            try {
                Validate::password($_POST['new_password']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
    }

    // Agent CMS Subdomain
    if ($requiredToCancelSubdomains || ($subdomainAuth->canCreateSubdomains() && $agent['id'] != '1')) {
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
                        $duplicate = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . LM_TABLE_AGENTS . "` WHERE `cms_link` = :cms_link AND `id` != :id;", [
                            'cms_link' => $cmsLink,
                            'id' => $agent['id']
                        ]);
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
                $query_extras_vals['cms_idxs'] = $agent_feeds;
            }

            // Enable Subdomain Request
            if (!$requiredToCancelSubdomains && Settings::getInstance()->IDX_FEED !== 'cms' && $agent['cms'] !== 'true' && $_POST['cms'] === 'true') {
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
                        __('Unable to send MLS details to IDX department.')
                    );
                }

            // Disable Subdomain Notification
            } else if (Settings::getInstance()->IDX_FEED !== 'cms' && $agent['cms'] === 'true' && $_POST['cms'] !== 'true') {
                if (!\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->sendAgentSubdomainCancellationNotice($agent)) {
                    throw new InvalidArgumentException(
                        __('Unable to send cancellation notice to IDX department.  Please contact the IDX department at idx@realestatewebmasters.com')
                    );
                }
            }

            // Make sure to unset addons if agent subdomain is disabled
            if ($_POST['cms'] !== 'true') {
                unset($_POST['cms_addons']);
            }

            // Update Subdomain Query
            $query_extras .= "`cms`       = :cms, "
                .  "`cms_link`  = :cms_link, "
                . "`cms_addons` = :cms_addons, ";
            $query_extras_vals['cms'] = $_POST['cms'];
            $query_extras_vals['cms_link'] = $_POST['cms_link'];
            $query_extras_vals['cms_addons'] = isset($_POST['cms_addons']) ? implode(',', $_POST['cms_addons']) : '';

        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = __('Unable to save agent website details until the IDX department has been given MLS details.  Please contact support at support@realestatewebmasters.com');
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Require ENUM('true', 'false') DEFAULT 'false'
        $_POST['auto_assign_agent'] = ($_POST['auto_assign_agent'] == 'true') ? 'true' : 'false';
        if ($can_edit_admin_only_fields) {
            $_POST['auto_assign_admin'] = ($_POST['auto_assign_admin'] == 'true') ? 'true' : 'false';
            $_POST['auto_rotate'] = ($_POST['auto_rotate'] == 'true') ? 'true' : 'false';
            $_POST['auto_optout'] = ($_POST['auto_optout'] == 'true') ? 'true' : 'false';
        }

        // Require ENUM('true', 'false') DEFAULT 'false'
        $_POST['auto_search'] = ($_POST['auto_search'] == 'true') ? 'true' : 'false';

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

        // Agent Manager Extras
        if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) || !empty($_COMPLIANCE['backend']['always_show_idx_agent'])) {
            if ($can_edit_admin_only_fields) {
                $query_extras .= "`display` = :display, ";
                $query_extras_vals['display'] = $_POST['display'];
            }
            $query_extras .= "`website`  = :website, ";
            $query_extras_vals['website'] = $_POST['website'];
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
                $query_extras_vals['agent_id'] = $agent_id;
            }
        }

        // REW API Extras
        if ($agentAuth->canManageApp()) {
            $query_extras .= "`auto_assign_app_id` = :auto_assign_app_id, ";
            $query_extras_vals['auto_assign_app_id'] = (!empty($_POST['api_source_id']) ? $_POST['api_source_id'] : null);
            $query_extras .= "`auto_rotate_app_id` = :auto_rotate_app_id, ";
            $query_extras_vals['auto_rotate_app_id'] = (!empty($_POST['api_source_id']) ? $_POST['api_source_id'] : null);
        }

        // Agent Spotlight Extras
        if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) {
            if ($can_edit_admin_only_fields) {
                $query_extras .= "`display_feature` = :display_feature, ";
                $query_extras_vals['display_feature'] = $_POST['display_feature'];
            }
        }

        // Showing Suite Settings
        if (!empty(Settings::getInstance()->MODULES['REW_SHOWING_SUITE'])) {
            $query_extras .= "`showing_suite_email` = :showing_suite_email, ";
            $query_extras_vals['showing_suite_email'] = $_POST['showing_suite_email'];
        }

        // Featured Offices Extras
        $office = !empty($_POST['office']) ? intval($_POST['office']) : null;
        $query_extras .= "`office` = :office, ";
        $query_extras_vals['office'] = $office;

        // Include in Auto Assignment
        $query_extras .= "`auto_assign_agent` = :auto_assign_agent, ";
        $query_extras_vals['auto_assign_agent'] = $_POST['auto_assign_agent'];
        if ($can_edit_admin_only_fields) {
            $query_extras .= "`auto_assign_admin` = :auto_assign_admin, ";
            $query_extras_vals['auto_assign_admin'] = $_POST['auto_assign_admin'];
            if ($_POST['auto_assign_admin'] == 'true' && $_POST['auto_assign_admin'] != $agent['auto_assign_admin']) {
                $query_extras .= "`auto_assign_time` = NOW(), ";
            }
        } else {
            if ($_POST['auto_assign_agent'] == 'true' && $_POST['auto_assign_agent'] != $agent['auto_assign_agent']) {
                $query_extras .= "`auto_assign_time` = NOW(), ";
            }
        }

        // Admin Mode
        if ($can_edit_admin_only_fields) {
            // Include in Auto Rotation
            $query_extras .= "`auto_rotate` = :auto_rotate, ";
            $query_extras_vals['auto_rotate'] = $_POST['auto_rotate'];

            // Include in Automated Opt-Out
            $query_extras .= "`auto_optout` = :auto_optout, ";
            $query_extras_vals['auto_optout'] = $_POST['auto_optout'];
            if ($_POST['auto_assign_agent'] == 'true' && $_POST['auto_assign_agent'] != $agent['auto_assign_agent']) {
                $query_extras .= "`auto_optout_time` = NOW(), ";
            }
        }

        // Google Calendar Extras
        if ($calendarAuth->canPushToGoogleCalander($authuser)) {
            $query_extras .= "`google_calendar_sync` = :google_calendar_sync, ";
            $query_extras_vals['google_calendar_sync'] = $_POST['google_calendar_sync'];
        }

        // Outlook Calendar Extras
        if ($calendarAuth->canPushToOutlookCalander($authuser)) {
            $query_extras .= "`microsoft_calendar_sync` = :microsoft_calendar_sync, ";
            $query_extras_vals['microsoft_calendar_sync'] = $_POST['microsoft_calendar_sync'];
        }
        try {
            // Build UPDATE Query
            $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET "
                   . "`first_name`        = :first_name, "
                   . "`last_name`         = :last_name, "
                   . "`email`             = :email, "
                   . "`sms_email`         = :sms_email, "
                   . (!empty($can_email) ? "`signature` = :signature, " : "")
                   . (!empty($can_email) ? "`add_sig` = :add_sig, " : "")
                   . "`remarks`           = :remarks, "
                   . "`office_phone`      = :office_phone, "
                   . "`home_phone`        = :home_phone, "
                   . "`cell_phone`        = :cell_phone, "
                   . "`fax`               = :fax, "
                   . "`title`             = :title, "
                   . "`auto_search`       = :auto_search, "
                   . $query_extras
                   . "`default_filter`    = :default_filter, "
                   . "`default_order`     = :default_order, "
                   . "`default_sort`      = :default_sort, "
                   . "`timezone`          = :timezone, "
                   . "`page_limit`        = :page_limit"
                   . " WHERE "
                   . "`id` = :id;")
                ->execute(array_merge([
                    'first_name' =>  trim($_POST['first_name']),
                    'last_name' =>  trim($_POST['last_name']),
                    'email' =>  trim($_POST['email']),
                    'sms_email' =>  trim($_POST['sms_email']),
                    'signature' => (!empty($can_email) ? $_POST['signature'] : ''),
                    'add_sig' => (!empty($can_email) ? $_POST['add_sig'] : 'N'),
                    'remarks' =>  $_POST['remarks'],
                    'office_phone' =>  $_POST['office_phone'],
                    'home_phone' =>  $_POST['home_phone'],
                    'cell_phone' =>  $_POST['cell_phone'],
                    'fax' =>  $_POST['fax'],
                    'title' =>  $_POST['title'],
                    'auto_search' =>  $_POST['auto_search'],
                    'default_filter' =>  $_POST['default_filter'],
                    'default_order' =>  $_POST['default_order'],
                    'default_sort' =>  $_POST['default_sort'],
                    'timezone' =>  $_POST['timezone'],
                    'page_limit' =>  $_POST['page_limit'],
                    'id'=> $agent['id']
                ], $query_extras_vals));

            if (Skin::hasFeature(Skin::AGENT_EDIT_NETWORKS)) {
                $agent->setSocialNetworks($_POST);
            }

            // Update RE/MAX Launchpad Credentials
            if (!empty(Settings::getInstance()->MODULES['REW_REMAX_LAUNCHPAD'])) {
                if (isset($_POST['remax_launchpad_username'])
                && ($_POST['remax_launchpad_username'] != $agent['remax_launchpad_username'] || $agent['remax_launchpad_url'] != $_SERVER['HTTP_HOST'])) {
                    // Check if Another User on This Site Already Uses the Username
                    try {
                        $remax_creds = $db->fetch("SELECT `remax_launchpad_username` FROM `" . LM_TABLE_AGENTS . "` WHERE `remax_launchpad_username` = :remax_launchpad_username AND `id` != :id LIMIT 1;", [
                            'remax_launchpad_username' => $_POST['remax_launchpad_username'],
                            'id' => $_GET['id']
                        ]);
                    } catch (PDOException $e) {}
                    if (empty($remax_creds) || empty($_POST['remax_launchpad_username'])) {
                        // Send (Create/Delete/Update) Request to Central SSO System
                        $curl = new Util_Curl();
                        $curl->setBaseURL('https://sso.realestatewebmasters.com');
                        $response = $curl->executeRequest('/launchpad/accounts/', array('old_url' => $agent['remax_launchpad_url'], 'new_url' => $_SERVER['HTTP_HOST'], 'old_user' => $agent['remax_launchpad_username'], 'new_user' => $_POST['remax_launchpad_username']), Util_Curl::REQUEST_TYPE_POST, array(
                            CURLOPT_SSL_VERIFYHOST => 2,
                            CURLOPT_SSL_VERIFYPEER => true
                        ));
                        if (!empty($response)) {
                            $response = (is_string($response)) ? json_decode($response) : $response;

                            // Update DB Entry on SUCCESS Response
                            if ($response->return_status == 'success' || empty($_POST['remax_launchpad_username'])) {
                                // Build UPDATE Query
                                try {
                                    $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET "
                                        . "`remax_launchpad_username`   = :remax_launchpad_username, "
                                        . "`remax_launchpad_url`        = :remax_launchpad_url "
                                        . " WHERE "
                                        . "`id` = :id;")
                                    ->execute([
                                        'remax_launchpad_username' => $_POST['remax_launchpad_username'],
                                        'remax_launchpad_url' => $_SERVER['HTTP_HOST'],
                                        'id' => $agent['id']
                                    ]);

                                    // Update Local RE/MAX Launchpad Username Record
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
                    } else {
                        $errors[] = __('Failed to update RE/MAX Launchpad username records. Username is used by another account on this site.');
                    }
                }
            }

            // Success
            if(empty($errors)) {
                $success[] = __('Your changes have successfully been saved.');
            }

            // Opt Status Changed
            if ($_POST['auto_assign_agent'] != $agent['auto_assign_agent']) {
                // Opted In
                if ($_POST['auto_assign_agent'] == 'true') {
                    // Log Event: Agent has been opted in
                    $event = new History_Event_Update_OptIn(array(
                        'admin' => ($can_edit_admin_only_fields ? $authuser->info('id') : null)
                    ), array(
                        new History_User_Agent($agent['id']),
                        ($can_edit_admin_only_fields ? new History_User_Agent($authuser->info('id')) : null)
                    ));

                    // Save to Database
                    $event->save();

                // Opted Out
                } elseif ($_POST['auto_assign_agent'] == 'false') {
                    // Log Event: Agent has been opted out
                    $event = new History_Event_Update_OptOut(array(
                       'admin' => ($can_edit_admin_only_fields ? $authuser->info('id') : null)
                    ), array(
                        new History_User_Agent($agent['id']),
                        ($can_edit_admin_only_fields ? new History_User_Agent($authuser->info('id')) : null)
                    ));

                    // Save to Database
                    $event->save();
                }
            }

            // Load Updated Agent
            $agent = Backend_Agent::load($agent['id']);

            // Not Super Admin
            if ($agent['id'] != 1) {
                //Encrypt provided password.  If this fails abort password/username update
                if ($_POST['update_password']) {
                    $password = $authuser->encryptPassword($_POST['new_password']);
                }

                if ($_POST['update_password'] && (empty($password))) {
                    $errors[] = 'Username and password could not be updated.';
                } else {
                    // Update Username / Password
                    try {
                        $db->prepare("UPDATE `" . Auth::$table . "` SET "
                            . "`username`    = :username"
                            . (($_POST['update_password']) ? ", `password`    = :password":"")
                            . " WHERE `id`    = :id;")
                        ->execute(array_merge([
                            'username' => $_POST['username'],
                            'id' => $agent['auth']
                        ], ($_POST['update_password']) ? ['password' => $password] : []));
                        // If Editting Self, Update Username & Password
                        if ($agentAuth->isSelf()) {
                            $authuser->update($_POST['username'], (($_POST['update_password']) ? $password : null));
                        }

                    // Query Error
                    } catch (PDOException $e) {
                        $errors[] = ($_POST['update_password']) ?
                            __('An error occurred while updating username and password.') :
                            __('An error occurred while updating username.');
                    }

                    // Agent CMS Sub-Site
                    agent_site($agent, $errors);
                }
            }

            // Trigger hook after agent account is updated
            Hooks::hook(Hooks::HOOK_AGENT_UPDATE)->run($agent->getRow());

            // Save Notices & Redirect to Edit Form
            $authuser->setNotices($success, $errors, $warnings);
            header('Location: ?id=' . $agent['id']);
            exit;

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('Error occurred, Your changes could not be saved.');
        }
    }

    // Use $_POST Data
    foreach ($agent as $k => $v) {
        $agent[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Subdomain addons
$agent['cms_addons'] = explode(',', $agent['cms_addons']);

// Timezones
$timezone = array();
try {
    // strip off the unneeded :00 from the end
    foreach($db->fetchAll("SELECT `t`.*, SEC_TO_TIME(`time_diff`) as `gmt_off` FROM `" . LM_TABLE_TIMEZONES . "` `t` ORDER BY `time_diff`, `daylight_savings`;") as $row) {
        // strip off the unneeded :00 from the end
        $row['gmt_off'] = substr($row['gmt_off'], 0, strpos($row['gmt_off'], ':00'));
        $timezone[] = $row;
    }
} catch (PDOException $e) {}

// Select Offices
try {
    $offices = $db->fetchAll("SELECT `id` AS `value`, `title` AS `title` FROM `" . TABLE_FEATURED_OFFICES . "` ORDER BY `sort` ASC;");
} catch (PDOException $e) {}

// Agent Opt-Out Feature
$optout = new Backend_Agent_OptOut();

// API Applications
$api_applications = array();
if (!empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
    try {
        $api_applications = $db->fetchAll("SELECT * FROM `api_applications` ORDER BY `name`;");
    } catch (PDOException $e) {}
}

// Load RE/MAX Launchpad SSO Settings
$agent['remax_launchpad_username'] = false;
if (!empty(Settings::getInstance()->MODULES['REW_REMAX_LAUNCHPAD'])) {
    try {
        $remax_creds = $db->fetch("SELECT `remax_launchpad_username`, `remax_launchpad_url` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :id LIMIT 1;", ['id' => $_GET['id']]);
        if ($remax_creds['remax_launchpad_url'] == $_SERVER['HTTP_HOST']) {
            $agent['remax_launchpad_username'] = $remax_creds['remax_launchpad_username'];
        }
    } catch (PDOException $e) {}
}

// Authorized to Toggle Google Calendar Push
if ($calendarAuth->canPushToGoogleCalander($authuser)) {
    $authorized_google = true;
}

// Authorized to Toggle Outlook Calendar Push
if ($calendarAuth->canPushToOutlookCalander($authuser)) {
    $authorized_microsoft = true;
}

// Authorized to Create Agent Subdomain
if ($requiredToCancelSubdomains || ($subdomainAuth->canCreateSubdomains() && $agent['id'] != '1')) {
    // List Of IDXs Agent Site Has Access To
    $agent_idxs = explode(",", $agent['cms_idxs']);
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
$networks = Skin::hasFeature(Skin::AGENT_EDIT_NETWORKS) ? $agent->getSocialNetworks(true) : array();

// Addon Config
$addons = [];
foreach (Util_CMS::SUBDOMAIN_MODULE_CONFIG_KEYS as $config) {
    if (Settings::getInstance()->MODULES[$config['module_key']] !== false) {
        $addons[] = $config;
    }
}