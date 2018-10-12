<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$lead = $db->fetch("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;", ['id' => $_GET['id']]);

/* Throw Missing $lead Exception */
if (empty($lead)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
}

// Create lead instance
$lead = new Backend_Lead($lead);

// Get Lead Authorization
$leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);

// Not authorized to view all leads
if (!$leadAuth->canEditLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to edit this lead'
    );
}

// Load Custom Fields
try {
    // Get Field
    $customFieldFactory= Container::getInstance()->get(REW\Backend\Leads\CustomFieldFactory::class);
    $customFields = $customFieldFactory->loadCustomFields(true);

    // Get Value
    $customValues = [];
    foreach ($customFields as $customField) {
        $customValues[$customField->getName()] = $_POST[$customField->getName()] ?: $customField->loadValue($lead['id']);
    }
} catch (\Exception $e) {
    $errors[] = 'Failed to load custom fields.';
}

// Available Action Plans
if (!empty($settings->MODULES['REW_ACTION_PLANS'])) {
    try {
        $action_plans = $db->fetchAll("SELECT * FROM `action_plans` ORDER BY `name` ASC;");
    } catch (PDOException $e) {
        $errors[] = 'Failed to load action plans.';
        Log::error($e);
    }
}

// Delete Photo
if (isset($_GET['deletePhoto']) && !empty($lead['image'])) {
    $updatePhotoQuery = $db->prepare("UPDATE `" . LM_TABLE_LEADS . "` SET `image` = '' WHERE `id` = :id;");
    if ($updatePhotoQuery->execute(['id' => $lead['id']])) {
        if (file_exists(DIR_LEAD_IMAGES . $lead['image'])) {
            unlink(DIR_LEAD_IMAGES . $lead['image']);
        }
        $success[] = 'Lead Photo has successfully been removed.';
        unset($lead['image']);
    } else {
        $errors[] = 'Lead Photo could not be removed.';
    }
}

$unallowedFields = array();
if (!$leadAuth->canManageLead()) {
    $unallowedFields = array(
        'heat',
        'status',
        'rejectwhy',
        'first_name',
        'last_name',
        'email',
        'update_password',
        'new_password',
        'phone',
        'groups',
        'notify_favs',
        'notify_searches',
        'share_lead',
        'action_plans',
    );
}

// Process Submit
if (isset($_GET['submit'])) {
    // Trim Whitespaces
    $fields = array('first_name', 'last_name', 'email');
    if ($_POST['update_password']) {
        array_merge($fields, array('new_password','confirm_password'));
    }
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $_POST[$field] = rtrim($_POST[$field], ' ');
        }
    }

    // Required Fields
    $required   = array();
    $required[] = array('value' => 'status', 'title' => 'Status');
    if (!in_array('email', $unallowedFields)) {
        $required[] = array('value' => 'email', 'title' => 'Email Address');
    }

    // Require Reason for Rejection
    if ($_POST['status'] == 'rejected' && !in_array('rejectwhy', $unallowedFields)) {
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

    // Fields Unallowed to be edited by team members
    foreach ($unallowedFields as $unallowedField) {
        if (isset($_POST[$unallowedField])) {
            $_POST[$unallowedField] = $lead[$unallowedField];
            $errors[] = $unallowedField . ' can not be edited by team members.';
        }
    }

    // Require Valid Email Address
    if (!in_array('email', $unallowedFields)) {
        if (!Validate::email($_POST['email'], true)) {
            $errors[] = 'Please supply a valid email address.';
        } else {
            // Require Unique Email Address
            $checkEmail = $db->fetch(
                "SELECT COUNT(`email`) AS `total` FROM `" . LM_TABLE_LEADS . "` WHERE `email` = :email AND `id` != :id;",
                ['email' => $_POST['email'], 'id' => $lead['id']]
            );
            if (!empty($checkEmail)) {
                if (!empty($checkEmail['total'])) {
                    $errors[] = 'That email belongs to another user already.';
                }
            }
        }
    }

    // Require Password
    if ($_POST['update_password'] && $leadAuth->canManageLead()) {
        if (empty($_POST['new_password'])) {
            $errors[] = 'Please supply a password';
        }
        if (empty($_POST['confirm_password'])) {
            $errors[] = 'Please confirm your new password';
        }
        if ($_POST['new_password'] != $_POST['confirm_password']) {
            $errors[] = 'Password confirmation does not match';
        }

        //Encrypt provided password.  If this fails abort password/username update
        $password = $authuser->encryptPassword($_POST['new_password']);
        if (empty($password)) {
            $errors[] = 'Password could not be updated.';
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

    // Upload Lead Photo
    if ($_FILES['lead_photo']['size'] > 0) {
        try {
            $uploader = new Backend_Uploader_Form('lead_photo', 'images');
            $uploader->handleUpload($settings['DIRS']['UPLOADS_LEADS'], false);
            $lead->info('image', $uploader->getName());
            $lead->save();
        } catch (Exception $e) {
            $errors[] = __("Could not upload lead photo. " . $e->getMessage());
        }
    }

    // Check Error
    if (empty($errors)) {
        $query_extras = [];
        $query_extra_params = [];

        // Include Restircted Fields
        if ($leadAuth->canManageLead()) {
            $query_extras = [
                "`heat`            = :heat",
                "`first_name`      = :first_name",
                "`last_name`       = :last_name",
                "`email`           = :email",
                "`phone`           = :phone",
                "`phone_cell`           = :phone_cell",
                "`notify_favs`     = :notify_favs",
                "`notify_searches` = :notify_searches",
                "`rejectwhy`       = :rejectwhy"
            ];
            $query_extra_params = [
                'heat' => $_POST['heat'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'phone_cell' => $_POST['phone_cell'],
                'notify_favs' => ($_POST['notify_favs'] == 'yes' ? 'yes' : 'no'),
                'notify_searches' => ($_POST['notify_searches'] == 'yes' ? 'yes' : 'no'),
                'rejectwhy' => $_POST['rejectwhy']
            ];

            if (!empty($password)) {
                $query_extras []= "`password` = :password";
                $query_extra_params['password'] = $password;
            }
        }

        // Track Changes Made
        $changes = array();
        foreach ($lead->getRow() as $k => $v) {
            if (isset($_POST[$k]) && ($_POST[$k] != $v)) {
                // Skip These Ones, They are Handled Differently
                if (in_array($k, array('rejectwhy', 'agent', 'lender', 'status'))) {
                    continue;
                }
                // Store Changes
                if($v != $_POST[$k]) {
                    $changes[$k] = array('field' => $k, 'old' => $v, 'new' => $_POST[$k]);
                }
                // Log Event: Track Lead Change
                $event = new History_Event_Update_Lead($changes[$k], array(
                    new History_User_Lead($lead['id']),
                    $authuser->getHistoryUser()
                ));
                // Save Event
                $event->save();
            }
        }

        if (strtoupper($lead['email']) != strtoupper($_POST['email'])) {
            $query_extras []= "`bounced` = 'false'";
        } else if (!empty($_POST['bounced'])) {
            $query_extras []= "`bounced` = :bounced";
            $query_extra_params['bounced'] = $_POST['bounced'];
        }

        // Search Preferences
        $search_preferences = md5(implode(array($_POST['search_type'], $_POST['search_city'], $_POST['search_subdivision'], $_POST['search_minimum_price'], $_POST['search_maximum_price'])));
        if (!empty($changes['search_type']) || !empty($changes['search_city']) || !empty($changes['search_subdivision']) || !empty($changes['search_minimum_price']) || !empty($changes['search_maximum_price'])) {
            if ($_POST['search_preferences'] !== $search_preferences) {
                $query_extras = array_merge($query_extras, [
                    "`search_auto` = 'false'",
                    "`search_type`          = :search_type",
                    "`search_city`          = :search_city",
                    "`search_subdivision`   = :search_subdivision",
                    "`search_minimum_price` = :search_minimum_price",
                    "`search_maximum_price` = :search_maximum_price"
                ]);

                $query_extra_params = array_merge($query_extra_params, [
                    'search_type' => $_POST['search_type'],
                    'search_city' => $_POST['search_city'],
                    'search_subdivision' => $_POST['search_subdivision'],
                    'search_minimum_price' => $_POST['search_minimum_price'],
                    'search_maximum_price' => $_POST['search_maximum_price']
                ]);
            } else {
                unset($_POST['search_type'], $_POST['search_city'], $_POST['search_subdivision'], $_POST['search_minimum_price'], $_POST['search_maximum_price']);
            }
        }

        // Can lead be shared
        if ($leadAuth->canManageLead() && !empty($settings->MODULES['REW_TEAMS']) && isset($_POST['share_lead'])) {
            $_POST['share_lead'] = !empty($_POST['share_lead']) ? 1 : 0;
            $query_extras []= "`share_lead` = :share_lead";
            $query_extra_params['share_lead'] = $_POST['share_lead'];
        }

        // Add lead to Shark Tank
        if (!empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])
            && $leadsAuth->canAccessSharkTank($authuser)
            && $leadAuth->canAssignAgentToLead()
            && (
                (!empty($_POST['status']) && $_POST['status'] === 'unassigned')
                || (empty($_POST['status']) && $lead['status'] === 'unassigned')
            )
        ) {
            $_POST['in_shark_tank'] = ($_POST['in_shark_tank'] === 'true') ? 'true' : 'false';
            $query_extras[] = " `in_shark_tank` = :in_shark_tank ";
            $query_extra_params['in_shark_tank'] = $_POST['in_shark_tank'];
            // Only update the Shark Tank timestamp if the lead is actually being added in
            if ($_POST['in_shark_tank'] === 'true' && $lead['in_shark_tank'] !== 'true') {
                $query_extras[] = " `timestamp_in_shark_tank` = NOW() ";
            }
        }

        // Build UPDATE Query
        $query = "UPDATE `" . LM_TABLE_LEADS . "` SET "
               . (!empty($query_extras) ? (implode(', ', $query_extras) . ', ') : '')
               . "`email_alt`          = :email_alt, "
               . "`email_alt_cc_searches`      = :email_alt_cc_searches, "
               . "`notes`              = :notes, "
               . "`remarks`            = :remarks, "
               . "`referer`            = :referer, "
               . "`keywords`           = :keywords, "
               . "`phone_home_status`  = :phone_home_status, "
               . "`phone_cell_status`  = :phone_cell_status, "
               . "`phone_work`         = :phone_work, "
               . "`phone_work_status`  = :phone_work_status, "
               . "`phone_fax`          = :phone_fax, "
               . "`contact_method`     = :contact_method, "
               . "`address1`           = :address1, "
               . "`address2`           = :address2, "
               . "`city`               = :city, "
               . "`state`              = :state, "
               . "`zip`                = :zip"
               . " WHERE "
               . "`id` = :id;";

       // Buld Update Params
        $queryParams = array_merge($query_extra_params, [
            'email_alt'          => $_POST['email_alt'],
            'email_alt_cc_searches'      => $email_alt_cc_searches,
            'notes'              => $_POST['notes'],
            'remarks'            => $_POST['remarks'],
            'referer'            => $_POST['referer'],
            'keywords'           => $_POST['keywords'],
            'phone_home_status'  => $_POST['phone_home_status'],
            'phone_cell_status'  => $_POST['phone_cell_status'],
            'phone_work'         => $_POST['phone_work'],
            'phone_work_status'  => $_POST['phone_work_status'],
            'phone_fax'          => $_POST['phone_fax'],
            'contact_method'     => $_POST['contact_method'],
            'address1'           => $_POST['address1'],
            'address2'           => $_POST['address2'],
            'city'               => $_POST['city'],
            'state'              => $_POST['state'],
            'zip'                => $_POST['zip'],
            'id'                 => $lead['id']
        ]);

        // Execute Query
        $updateQuery = $db->prepare($query);
        if ($updateQuery->execute($queryParams)) {
            // Original Email
            $lead_email = $lead['email'];

            // Notify partners
            $partners = array();

            // Updated Lead
            $lead = $db->getCollection('users')->getRow($lead['id']);

            // Create Lead Object
            $lead = new Backend_Lead($lead);

            // Validate, Parse and Save Custom Fields
            if (!empty($customFields)) {
                foreach ($customFields as $customField) {
                    $value = $_POST[$customField->getName()];
                    if (!empty($value) && $customField->loadValue($lead['id']) != $value) {
                        try {
                            // Log Event: Custom Event Chagne
                            $event = new History_Event_Update_Lead(
                                [
                                    'field' => $customField->getTitle(),
                                    'old' => $customField->loadValue($lead['id']),
                                    'new' => $value
                                ],
                                [
                                    new History_User_Lead($lead['id']),
                                    $authuser->getHistoryUser()
                                ]
                            );
                            $event->save();

                            $customField->validateValue($value);
                            $value = $customField->parseValue($value);
                            $customField->saveValue($lead['id'], $value);

                        } catch (\InvalidArgumentException $e) {
                            $errors[] = 'Error Occurred while updating the '.$customField->getTitle().' custom field: ' . $e->getMessage();
                        } catch (\Exception $e) {
                            $errors[] = 'Error Occurred while updating the '.$customField->getTitle().' custom field.';
                        }
                    }
                }
            }

            // Team Agents can not manage groups
            if ($leadAuth->canManageLead()) {
                // Build Group List
                $groups = $db->fetchAll("SELECT `g`.`id`, `g`.`name`, `g`.`agent_id`, `g`.`user`, `g`.`style`, !ISNULL(`ug`.`user_id`) AS `checked`"
                    . " FROM `groups` `g` LEFT JOIN `users_groups` `ug` ON `g`.`id` = `ug`.`group_id` AND `ug`.`user_id` = '" . $lead['id'] . "'"
                    . " WHERE (`g`.`agent_id` = '" . $lead['agent'] . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))"
                    . ($authuser->info('mode') == 'admin' ? " OR `g`.`agent_id` = 1 OR `g`.`agent_id` IS NULL" : "")
                    . ($authuser->isAssociate() ? " OR `g`.`associate` = '" . $authuser->info('id') . "'" : "")
                    . " ORDER BY `g`.`name` ASC;");

                // Update Lead Groups
                foreach ($groups as $group) {
                    // Remove from Group
                    if (!empty($group['checked']) && (empty($_POST['groups']) || (is_array($_POST['groups']) && !in_array(
                        $group['id'],
                        $_POST['groups']
                    )))
                    ) {
                        try {
                            $lead->removeGroup($group, $authuser);
                        } catch (PDOException $e) {
                            $errors[] = 'Error Occurred while Removing Lead Group: ' . $group['name'];
                            Log::error($e);
                        }

                        // Add to New Group
                    } elseif (empty($group['checked']) && is_array($_POST['groups']) && in_array(
                        $group['id'],
                        $_POST['groups']
                    )
                    ) {
                        try {
                            $lead->assignGroup($group, $authuser);
                        } catch (PDOException $e) {
                            $errors[] = 'Error Occurred while Assigning Lead Group: ' . $group['name'];
                            Log::error($e);
                        }
                    }
                }
            }

            // Handle Action Plans Assignments
            if (!empty($settings->MODULES['REW_ACTION_PLANS'])) {
                if ($leadAuth->canAssignActionPlans()) {
                    if (!empty($action_plans)) {
                        try {
                            // Get List Of Action Plans They're Already Assigned To
                            $results = $db->fetchAll("SELECT `actionplan_id` FROM `users_action_plans` WHERE `user_id` = :user_id; ", array(
                                'user_id' => $lead->getId()
                            ));

                            $assigned_action_plans = array();

                            foreach ($results as $result) {
                                $assigned_action_plans[] = $result['actionplan_id'];
                            }

                            foreach ($action_plans as $action_plan) {
                                if (is_array($_POST['action_plans']) && in_array($action_plan['id'], $_POST['action_plans'])) {
                                    // Assign If Not Already Assigneds
                                    if (!in_array($action_plan['id'], $assigned_action_plans)) {
                                        // Load Action Plan Object
                                        $action_plan = Backend_ActionPlan::load($action_plan['id']);

                                        if (!$action_plan->assign($lead->getId(), $authuser)) {
                                            $errors[] = 'Failed to assign action plan: ' . $action_plan->info('name');
                                        }
                                    }

                                // Un-Assign
                                } else if (in_array($action_plan['id'], $assigned_action_plans)) {
                                    // Load Action Plan Object
                                    $action_plan = Backend_ActionPlan::load($action_plan['id']);

                                    if (!$action_plan->unassign($lead->getId(), $authuser)) {
                                        $errors[] = 'Failed to unassign action plan:' . $action_plan->info('name');
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    }
                }
            }

            // Sync Partner Groups
            $leadAgent = !empty($_POST['agent']) ? $_POST['agent'] : $lead['agent'];
            $agent = Backend_Agent::load($leadAgent);
            $groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $lead['id']);
            Hooks::hook(Hooks::HOOK_LEAD_SYNC_PARTNER_UPDATING)->run($lead, $agent, $groups);

            // Was Lead Just Rejected?
            $rejected = ($lead['status'] != 'rejected' && $_POST['status'] == 'rejected' && $leadAuth->canManageLead());

            // If Not Rejected, Assign Lead to Selected Agent
            if (empty($rejected) && $leadAuth->canAssignAgentToLead() && ($lead['agent'] != $_POST['agent']) && !empty($_POST['agent']) && $leadAuth->canManageLead()) {

                // Assign Lead to Agent
                $lead->assign($agent, $authuser);

                // Notify Agent
                $agent->notifyAgent(array($lead), $authuser);

                // Success
                $success[] = $lead->getNameOrEmail() . ' has been assigned to Agent: ' . $agent->getName() . '. ';
            }

            // Assign Lead to Selected Lender
            if ($leadAuth->canAssignLenderToLead() && $lead['lender'] != $_POST['lender'] && $leadAuth->canManageLead()) {
                // Assign Lender
                if (!empty($_POST['lender'])) {
                    // Backend_Lender
                    $lender = Backend_Lender::load($_POST['lender']);

                    // Assign Lead to Lender
                    $assigned = $lender->assign($lead, $authuser, $errors);

                    // Success
                    if (!empty($assigned)) {
                        $success[] = $lead->getNameOrEmail() . ' has been assigned to Lender: ' . $lender->getName() . '. ';
                    }
                } else {
                    // Un-Assign Lender
                    $lead->assignLender(null, $authuser);
                }
            }

            // Update Lead Status
            if ($leadAuth->canManageLead() && !empty($_POST['status'])) {
                $lead->status($_POST['status'], $authuser);
            }

            // Success
            $success[] = 'Your changes have successfully been saved.';

            // Rejected Lead, Redirect
            if (!empty($rejected)) {
                header('Location: ' . URL_BACKEND . 'leads/');
                exit;
            }

            // Save notices & redirect on success
            $authuser->setNotices($success, $errors);
            header('Location: ?id='  . $lead['id'] . '&success');
            exit;

        // Query Error
        } else {
            $errors[] = 'An error occurred, your changes could not be saved.';
        }
    }

    // Use $_POST
    foreach ($lead as $k => $v) {
        $lead[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Available Agents
try {
    $agents = $db->fetchAll(
        "SELECT `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`, `r`.`why`"
        . " FROM `" . LM_TABLE_AGENTS . "` `a`"
        . " LEFT JOIN `" . LM_TABLE_REJECTED . "` `r` ON `a`.`id` = `r`.`agent_id` AND `r`.`user_id` = :user_id"
        . " ORDER BY `last_name` ASC, `first_name` ASC;",
        ['user_id' => $lead['id']]
    );
} catch (\Exception $e) {
    $errors[] = 'An error occurred while loading Available Agents.';
}

// Available Lenders
if (!empty($settings->MODULES['REW_LENDERS_MODULE'])) {
    try {
        $lenders = $db->fetchAll("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `lenders` ORDER BY `last_name` ASC;");
    } catch (\Exception $e) {
        $errors[] = 'An error occurred while loading Available Lenders.';
    }
}

// Lead Groups
$lead['groups'] = [];
$leadGroups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $lead['id']);
foreach ($leadGroups as $leadGroup) {
    $lead['groups'][] = $leadGroup['id'];
}

// Available Groups (for Assigned Agent)
$groups = Backend_Group::getGroups($errors, Backend_Group::AGENT, $lead['agent'])
        + Backend_Group::getGroups($errors);

// Track Lead's Assigned Action Plans
$lead['action_plans'] = array();
if (!empty($settings->MODULES['REW_ACTION_PLANS'])) {
    try {
        $results = $db->fetchAll("SELECT `actionplan_id` AS `id` FROM `users_action_plans` WHERE `user_id` = :user_id;", array('user_id' => $_GET['id']));
        if (!empty($results)) {
            foreach ($results as $result) {
                $lead['action_plans'][] = $result['id'];
            }
        }
    } catch (PDOException $e) {
        $errors[] = 'Failed to load assigned action plans.';
        Log::error($e);
    }
}

// Search Preferences
$search_preferences = md5(implode(array($lead['search_type'], $lead['search_city'], $lead['search_subdivision'], $lead['search_minimum_price'], $lead['search_maximum_price'])));

// Remember Last Submission
if (is_array($_POST)) {
    foreach ($_POST as $key => $value) {
        $lead[$key] = $value;
    }
}
