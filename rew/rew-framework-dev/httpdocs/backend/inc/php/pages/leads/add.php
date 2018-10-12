<?php

// Get Authorization Managers
$settings = Settings::getInstance();
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);

// Authorized to Create Leads
if (!$leadsAuth->canManageLeads($authuser)) {
    // Authorized to Create Own Leads
    if (!$leadsAuth->canManageOwn($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to add a lead.'
        );
    }
}

// Allowed to Assign Leads
$can_assign = $leadsAuth->canAssignLeads($authuser);

// Allowed to Assign Action Plans
$can_assign_action_plans = $leadsAuth->canAssignActionPlans($authuser);

// Check permissions
$can_assign_lender = (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE']) && !empty($can_assign));

// Success
$success = array();

// Errors
$errors = array();

// Show Form
$show_form = true;

// App DB
$db = DB::get();

// Available Action Plans
if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
    try {
        $action_plans = $db->fetchAll("SELECT * FROM `action_plans` ORDER BY `name` ASC;");
    } catch (PDOException $e) {
        $errors[] = 'Failed to load action plans.';
        Log::error($e);
    }
}

// Load Custom Fields
try {
    $customFieldFactory= Container::getInstance()->get(REW\Backend\Leads\CustomFieldFactory::class);
    $customFields = $customFieldFactory->loadCustomFields(true);
} catch (\Exception $e) {
    $errors[] = 'Failed to load custom fields.';
}

// Process Submit
if (isset($_GET['submit'])) {
    // Trim Whitespaces
    $fields = array('first_name', 'last_name', 'email');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $_POST[$field] = rtrim($_POST[$field], ' ');
        }
    }

    // Group List
    $groupList = is_array($_POST['group']) ? implode(',', $_POST['group']) : '';

    // Required Fields
    $required   = array();
    $required[] = array('value' => 'first_name', 'title' => 'First Name');
    $required[] = array('value' => 'last_name',  'title' => 'Last Name');
    $required[] = array('value' => 'email',      'title' => 'Email Address');
    $required[] = array('value' => 'status',     'title' => 'Status');

    // Require Reason for Rejection
    if ($_POST['status'] === 'rejected') {
        $required[] = array('value' => 'rejectwhy', 'title' => 'Reason for Rejection');
    }

    // Disallow Un-Assigned Status if Agent Isn't Super Admin
    if ($_POST['status'] == 'unassigned' && $_POST['agent'] !== '1') {
        $errors[] = 'Can not set status to Un-Assigned for an assigned lead.';
    }

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    // Require Valid Email Address
    if (!Validate::email($_POST['email'], true)) {
        $errors[] = 'Please supply a valid email address.';
    } else {
        // Require Unique Email Address
        $query = "SELECT COUNT(`email`) AS `total` FROM `". LM_TABLE_LEADS . "` WHERE `email` = '" . mysql_real_escape_string($_POST['email']) . "';";
        if ($result = mysql_query($query)) {
            $checkEmail = mysql_fetch_assoc($result);
            if (!empty($checkEmail['total'])) {
                $errors[] = 'That email belongs to another user already.';
            }
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

    if (!empty($_POST['phone']) && !Validate::phone($_POST['phone'])) {
        $errors[] = __('The primary phone number entered is invalid. ');
    }
    if (!empty($_POST['phone_cell']) && !Validate::phone($_POST['phone_cell'])) {
        $errors[] = __('The secondary phone number entered is invalid. ');
    }
    if (!empty($_POST['phone_work']) && !Validate::phone($_POST['phone_work'])) {
        $errors[] = __('The work phone number entered is invalid. ');
    }
    if (!empty($_POST['phone_fax']) && !Validate::phone($_POST['phone_fax'])) {
        $errors[] = __('The fax number entered is invalid. ');
    }


    // Create Extra Query Params
    $extraParams = [];

    // Upload Agent Photo (Resize to 150 by 150)
    if ($_FILES['lead_photo']['size'] > 0) {
        $extention = end(explode('.', $_FILES['lead_photo']['name']));
        if (!in_array($extention, array('jpeg', 'jpg', 'gif', 'png', 'JPEG', 'JPG', 'GIF', 'PNG'))) {
            $errors[] = 'The photo must be a JPG, GIF, or PNG file.';
        } else {
            $imageName = mt_rand() . '.' . $extention;
            move_uploaded_file($_FILES['lead_photo']['tmp_name'], DIR_LEAD_IMAGES . $imageName);
            $query_extras .= "`image`=:image, ";
            $extraParams['image'] = $imageName;
        }
    }

    // Price range can only contain numbers
    $_POST['search_minimum_price'] = preg_replace('/[^0-9]/', '', $_POST['search_minimum_price']);
    $_POST['search_maximum_price'] = preg_replace('/[^0-9]/', '', $_POST['search_maximum_price']);

    // Minimum search price cannot exceed maximum search price
    $search_minimum_price = (int) $_POST['search_minimum_price'];
    $search_maximum_price = (int) $_POST['search_maximum_price'];
    if ($search_minimum_price > 0 && $search_maximum_price > 0 && $search_minimum_price > $search_maximum_price) {
        $errors[] = 'Minimum search price cannot exceed maximum search price.';
    }

    // Alternate email opt in settings
    if (empty($_POST['email_alt']) && !empty($_POST['email_alt_cc_searches'])) {
        $errors[] = 'Failed to opt-in alternate email for CC emails: A valid alternate email is required.';
    }
    $email_alt_cc_searches = (!empty($_POST['email_alt_cc_searches']) ? 'true' : 'false');

    // Check Errors
    if (empty($errors)) {
        // Extras
        $query_extras = '';

        // Check Search Preferences
        if (!empty($_POST['search_type']) ||
            !empty($_POST['search_city']) ||
            !empty($_POST['search_subdivision']) ||
            !empty($_POST['search_minimum_price']) ||
            !empty($_POST['search_maximum_price'])
        ) {
            $query_extras .= "`search_auto`          = 'false', ";
            $query_extras .= "`search_type`          = :search_type, ";
            $query_extras .= "`search_city`          = :search_city, ";
            $query_extras .= "`search_subdivision`   = :search_subdivision, ";
            $query_extras .= "`search_minimum_price` = :search_minimum_price, ";
            $query_extras .= "`search_maximum_price` = :search_maximum_price, ";

            $extraParams['search_type']          = $_POST['search_type'];
            $extraParams['search_city']          = $_POST['search_city'];
            $extraParams['search_subdivision']   = $_POST['search_subdivision'];
            $extraParams['search_minimum_price'] = $_POST['search_minimum_price'];
            $extraParams['search_maximum_price'] = $_POST['search_maximum_price'];
        }

        // Check Rejection Reason
        if ($_POST['status'] === 'rejected') {
            $query_extras .= "`rejectwhy`          = :rejectwhy, ";
            $extraParams['rejectwhy']          = $_POST['rejectwhy'];
        }

        // Add lead to Shark Tank
        if (!empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])
            && $leadsAuth->canAccessSharkTank($authuser)
            && $_POST['status'] === 'unassigned'
        ) {
            $_POST['in_shark_tank'] = ($_POST['in_shark_tank'] === 'true') ? 'true' : 'false';
            $query_extras .= " `in_shark_tank` = :in_shark_tank, ";
            $extraParams['in_shark_tank'] = $_POST['in_shark_tank'];
            // Only update the Shark Tank timestamp if the lead is actually being added in
            if ($_POST['in_shark_tank'] === 'true') {
                $query_extras .= " `timestamp_in_shark_tank` = NOW(), ";
            }
        }

        // Get Encrypted Password
        $password = $authuser->encryptPassword($_POST['password']);

        // Build INSERT Query
        try {
            $insertQuery = $db->prepare("INSERT INTO `" . LM_TABLE_LEADS . "` SET "
               . ($authuser->isAgent() ? "`agent` = '" . $authuser->info('id') . "', " : "") // Added By $authuser, See Code Below that Assigns the Lead If $can_assign
               . "`heat`               = :heat, "
               . "`status`             = :status, "
               . "`first_name`         = :first_name, "
               . "`last_name`          = :last_name, "
               . "`email`              = :email, "
               . "`email_alt`          = :email_alt, "
               . "`email_alt_cc_searches`      = :email_alt_cc_searches, "
               . "`password`           = :password, "
               . "`notes`              = :notes,"
               . "`remarks`            = :remarks,"
               . "`referer`            = :referer,"
               . "`keywords`           = :keywords,"
               . "`phone`              = :phone, "
               . "`phone_home_status`  = :phone_home_status, "
               . "`phone_cell`         = :phone_cell, "
               . "`phone_cell_status`  = :phone_cell_status, "
               . "`phone_work`         = :phone_work, "
               . "`phone_work_status`  = :phone_work_status, "
               . "`phone_fax`          = :phone_fax, "
               . "`contact_method`     = :contact_method, "
               . "`address1`           = :address1, "
               . "`address2`           = :address2, "
               . "`city`               = :city, "
               . "`state`              = :state, "
               . "`notify_favs`        = :notify_favs, "
               . "`notify_searches`    = :notify_searches, "
               . "`zip`                = :zip, "
               . "`manual`             = 'yes', "
               . "`verified`           = 'yes', "
               . "`opt_marketing`      = 'in', "
               . "`opt_searches`       = 'in', "
               . $query_extras
               . "`auto_rotate`        = 'false', " // Don't Auto-Rotate This (Manually Created) Lead
               . "`timestamp_assigned` = NOW(), "
               . "`timestamp`          = NOW();");

            $insertParams = [
                'heat'       => $_POST['heat'],
                'status'     => $_POST['status'],
                'first_name' => $_POST['first_name'],
                'last_name'  => $_POST['last_name'],
                'email'      => $_POST['email'],
                'email_alt'  => $_POST['email_alt'],
                'email_alt_cc_searches'  => $email_alt_cc_searches,
                'password'   => $password,
                'notes'      => $_POST['notes'],
                'remarks'    => $_POST['remarks'],
                'referer'    => $_POST['referer'],
                'keywords'   => $_POST['keywords'],
                'phone'      => $_POST['phone'],
                'phone_home_status' => $_POST['phone_home_status'],
                'phone_cell' => $_POST['phone_cell'],
                'phone_cell_status' => $_POST['phone_cell_status'],
                'phone_work' => $_POST['phone_work'],
                'phone_work_status' => $_POST['phone_work_status'],
                'phone_fax' => $_POST['phone_fax'],
                'contact_method' => $_POST['contact_method'],
                'address1' => $_POST['address1'],
                'address2' => $_POST['address2'],
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'notify_favs' => $_POST['notify_favs'],
                'notify_searches' => $_POST['notify_searches'],
                'zip' => $_POST['zip']
            ];
            $insertParams = array_merge($insertParams, $extraParams);

            // Execute Update Query
            $insertQuery->execute($insertParams);

            // Insert ID
            $insert_id = $db->lastInsertId();

            // Validate, Parse and Save Custom Fields
            if (!empty($customFields)) {
                foreach ($customFields as $customField) {
                    $value = $_POST[$customField->getName()];
                    if (!empty($value)) {
                        try {
                            $customField->validateValue($value);
                            $value = $customField->parseValue($value);
                            $customField->saveValue($insert_id, $value);
                        } catch (\InvalidArgumentException $e) {
                            $errors[] = 'Error Occurred while updating the '.$customField->getTitle().' custom field: ' . $e->getMessage();
                        } catch (\Exception $e) {
                            $errors[] = 'Error Occurred while updating the '.$customField->getTitle().' custom field.';
                        }
                    }
                }
            }

            // Select Updated Row
            $result = mysql_query("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = '" . $insert_id . "';");
            $lead = mysql_fetch_assoc($result);

            // Log Event: New Lead Created
            $event = new History_Event_Create_Lead(array(
                'lead_id' => $lead['id']
            ), array (
                new History_User_Lead($lead['id']),
                $authuser->getHistoryUser()
            ));

            // Save to DB
            $event->save();

            // Create Lead Object
            $backend_lead = new Backend_Lead($lead);

            // Run hook
            Hooks::hook(Hooks::HOOK_LEAD_FORM_SUBMISSION)->run($_POST, 'IDX Registration', $lead);

            // Assign Agent
            if (!empty($can_assign) && !empty($_POST['agent'])) {
                // Backend_Agent
                $agent = Backend_Agent::load($_POST['agent']);

                // Assign Lead to Agent
                $agent->assign($backend_lead, $authuser, $errors);
            }

            // Assign Lender
            if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
                if (!empty($can_assign) && !empty($_POST['lender'])) {
                    // Backend_Lender
                    $lender = Backend_Lender::load($_POST['lender']);

                    // Assign Lead to Lender
                    $lender->assign($backend_lead, $authuser, $errors);
                }
            }

            // Reset Status (So Next Line of Code Runs)
            $backend_lead->info('status', '');

            try {
                // Set Lead Status
                $backend_lead->status($lead['status'], $authuser);

            // DB Error Caught
            } catch (PDOException $e) {
                $errors[] = 'Error Occurred while Updating Lead Status: ' . $lead['status'];
                Log::error($e);
            }

            // Assign Lead to Groups
            if (!empty($_POST['groups']) && is_array($_POST['groups'])) {
                foreach ($_POST['groups'] as $group) {
                    try {
                        $group = $db->fetch("SELECT `id`, `name`, `agent_id`, `user` FROM `groups` WHERE `id` = '" . $group . "';");
                        if (!empty($group)) {
                            $backend_lead->assignGroup($group, $authuser);
                        }
                    } catch (PDOException $e) {
                        $errors[] = 'Error Occurred while Assigning Lead Group: ' . $group['name'];
                        Log::error($e);
                    }
                }
            }

            // Handle Action Plans Assignments
            if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
                if (!empty($action_plans)) {
                    foreach ($action_plans as $action_plan) {
                        // Load Action Plan Object
                        $action_plan = Backend_ActionPlan::load($action_plan['id']);
                        // Assign
                        if (is_array($_POST['action_plans']) && in_array($action_plan->info('id'), $_POST['action_plans'])) {
                            // Check if They're Already Assigned to the Action Plan
                            try {
                                $check_ap = $db->fetch("SELECT `actionplan_id` FROM `users_action_plans` WHERE `actionplan_id` = :ap_id AND `user_id` = :user_id LIMIT 1; ", array(
                                    'ap_id' => $action_plan->info('id'),
                                    'user_id' => $backend_lead->getId()
                                ));
                                if (empty($check_ap)) {
                                    if (!$action_plan->assign($backend_lead->getId(), $authuser)) {
                                        $errors[] = 'Failed to assign action plan: ' . $action_plan->info('name');
                                    }
                                }
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        }
                    }
                }
            }

            // Sync Partner Groups
            if (!isset($agent)) $agent = Backend_Agent::load($authuser->info('id'));
            $groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $backend_lead->getId());
            Hooks::hook(Hooks::HOOK_LEAD_SYNC_PARTNER_UPDATING)->run($backend_lead, $agent, $groups);

            // Run Lead Created Hook
            Hooks::hook(Hooks::HOOK_LEAD_CREATED)->run($backend_lead, true);

            // Set success message and save notifications
            $success[] = 'Lead has successfully been created.';
            $authuser->setNotices($success, $errors);

            // Redirect to new lead's summary page
            header('Location: ../lead/summary/?id=' . $lead['id']);
            exit;

        // Query Error
        } catch (PDOException $e) {
            $errors[] = 'Error occurred, Lead could not be added. Please try again.';
        }
    }
} else {
    // Load Backend_Agent
    $agent = Backend_Agent::load($authuser->info('id'));

    // Notification Settings: Notify Listings
    $check = $agent->getNotifications()->checkIncoming(Backend_Agent_Notifications::INCOMING_LISTING_SAVED);
    $_POST['notify_favs'] = !empty($check['email']) ? 'yes' : 'no';

    // Notification Settings: Notify Searches
    $check = $agent->getNotifications()->checkIncoming(Backend_Agent_Notifications::INCOMING_SEARCH_SAVED);
    $_POST['notify_searches'] = !empty($check['email']) ? 'yes' : 'no';
}

// Available Agents
$agents = array();
$query = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `" . LM_TABLE_AGENTS . "` ORDER BY `last_name` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $agents[] = $row;
    }
} else {
    $errors[] = 'An error occurred while loading Available Agents.';
}

// Available Lenders
$lenders = array();
if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
    $query = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `lenders` ORDER BY `last_name` ASC;";
    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($result)) {
            $lenders[] = $row;
        }
    } else {
        $errors[] = 'An error occurred while loading Available Lenders.';
    }
}

// Lead Groups
$groups = Backend_Group::getGroups($errors);

 // Use $_POST Data
$lead = $_POST;
if (!is_array($lead['groups'])) {
    $lead['groups'] = [];
}

// Accepted by Default
$lead['status'] = !empty($lead['status']) ? $lead['status'] : 'accepted';