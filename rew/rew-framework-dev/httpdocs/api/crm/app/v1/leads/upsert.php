<?php
// Fetch requested lead
$db = DB::get('users');

// Match type
$matchType = true;

// Request types
if ($app->request->isPut()) {
    $requestData = $app->request->put();
} else if ($app->request->isPost()) {
    $requestData = $_POST;
} else {
    $matchType = false;
}

// Required parameters
$required = array('email');

// Received email as get parameter on the legacy update
if(!empty($email)) {
    $requestData["email"] = $email;
}

// Check POST
foreach ($required as $field) {
    if (!isset($requestData[$field])) {
        $errors[] = 'Required parameter is missing: \'' . $field . '\'';
    }
}

// Require no errors
if (!empty($errors)) {
    return;
}

// Search where
$where = array(
    '$eq' => array(
        'email' => $requestData["email"],
    ),
);

// Fetch lead row
$isNewLead = empty($lead = $db->{'users'}->search($where)->fetch());

// Catch unsupported request types
if (!$matchType) {
    if (!empty($email) && $isNewLead) {
        $app->response->status(404);
        $errors[] = 'The specified Lead could not be found';
        return;
    } elseif (empty($email) && !$isNewLead) {
        //Lead already exists
        $app->response->status(409);
        $errors[] = 'A lead with the specified e-mail address already exists';
        return;
    }
}

if($isNewLead) {
    // Verify Email Address if it is a new lead
    if (Validate::email($requestData['email'], true) === false) {
        $errors[] = 'The supplied e-mail address is invalid';
        return;
    }
}

// Verify phone numbers
if (!empty($requestData['phone']) && !Validate::phone($requestData['phone'])) {
    $errors[] = __('The primary phone number entered is invalid. ');
    return;
}
if (!empty($requestData['phone_cell']) && !Validate::phone($requestData['phone_cell'])) {
    $errors[] = __('The secondary phone number entered is invalid. ');
    return;
}
if (!empty($requestData['phone_work']) && !Validate::phone($requestData['phone_work'])) {
    $errors[] = __('The work phone number entered is invalid. ');
    return;
}
if (!empty($requestData['phone_fax']) && !Validate::phone($requestData['phone_fax'])) {
    $errors[] = __('The fax number entered is invalid. ');
    return;
}

// Verify Agent
if (!empty($requestData['agent_id'])) {
    if (!$agent = Backend_Agent::load($requestData['agent_id'])) {
        $errors[] = 'The specified agent is invalid';
        return;
    }
}

// Verify Lender
if (!empty($requestData['lender_id'])) {
    if (!$lender = Backend_Lender::load($requestData['lender_id'])) {
        $errors[] = 'The specified lender is invalid';
        return;
    }
}

// Verify Heat
if (isset($requestData["heat"])) {
    $heat_values = ["", "hot", "mediumhot", "warm", "lukewarm", "cold"];
    if (!in_array($requestData["heat"], $heat_values)) {
        $errors[] = 'The specified lead heat is invalid';
        return;
    }
}

// Verify Preferred Contact Method
if (!empty($requestData["contact_method"])) {
    $contact_values = ["email", "phone", "text"];
    if (!in_array($requestData["contact_method"], $contact_values)) {
        $errors[] = 'The specified preferred contact method is invalid';
        return;
    }
}

// SET data
$data = $isNewLead ? array(
    'email'         => $requestData['email'],
    'verified'      => 'yes',
) : [];


// Optional data
foreach ([
    "first_name", "last_name", "email_alt",
    "city", "state", "zip", "phone", "phone_cell",
    "phone_work", "phone_fax", "contact_method", "comments",
    "keywords", "image", "source_user_id", "password", "heat"
] as $field) {
    if (!empty($requestData[$field])) {
        $data[$field] = $requestData[$field];
    }
}

// Optional data - API naming exception
if (!empty($requestData["address"])) {
    $data["address1"] = $requestData["address"];
}

if (!empty($requestData["origin"])) {
    $data["referer"] = $requestData["origin"];
}

// Opt-in fields
if (!empty($requestData['opt_marketing']) || $isNewLead) {
    $data['opt_marketing'] = $requestData['opt_marketing'] === 'in' ? 'in' : 'out';
}
if (!empty($requestData['opt_searches']) || $isNewLead) {
    $data['opt_searches'] = $requestData['opt_searches'] === 'in' ? 'in' : 'out';
}
if (!empty($requestData['opt_texts']) || $isNewLead) {
    $data['opt_texts'] = $requestData['opt_texts'] === 'in' ? 'in' : 'out';
}
if (!empty($requestData['auto_rotate']) || $isNewLead) {
    $data['auto_rotate'] = $requestData['auto_rotate'] === 'true' ? 'true' : 'false';
}
if (!empty($requestData['num_visits']) || $isNewLead) {
    $data['num_visits'] = isset($requestData['num_visits']) ? intval($requestData['num_visits']) : 0;
}

try {
    if ($isNewLead) {
        if (!empty($requestData['agent_id'])) {
            $data['agent'] = $requestData['agent_id'];
        }
        if (!empty($requestData['lender_id'])) {
            $data['lender'] = $requestData['lender_id'];
        }
        if (!empty($requestData['source_user_id'])) {
            $data['source_user_id'] = $requestData['source_user_id'];
        }

        $data['status'] = ($requestData['agent_id'] > 1) ? 'pending' : 'unassigned';

        // Set API Application ID
        $request = $app->request();
        $headers = $request->headers();
        if ($application = $db->{'api_applications'}->search(array('$eq' => array('api_key' => $headers['X_REW_API_KEY'])))->fetch()) {
            $data['source_app_id'] = $application['id'];
        }

        // Load agent to get partner settings if a new lead
        if (!empty($agent)) {
            $partners = [];
            if (!empty($agent->info('partners'))) {
                // Parse Partners Json
                $partners = json_decode($agent->info('partners'), true);
            }

            // Check if partner is configured to use global assignment
            if (!empty($data['referer']) && !empty($partners[strtolower($data['referer'])]["global_assignment"]) && $partners[strtolower($data['referer'])]["global_assignment"] === "true") {
                $use_global_assign = true;
            }
        }

        if ($use_global_assign) {
            // Clear agent
            unset($data['agent']);
            unset($data['auto_rotate']);
            // Create user with original assign routine
            collectContactData($data, 0);

            // Fetch new Lead ID
            $leadQuery = $db->fetch("SELECT `id` FROM `users` WHERE `email` = :email", ['email' => $data['email']]);
            $new_id = $leadQuery['id'];

            //Load New Lead
            $backend_lead = (new Backend_Lead())->load($new_id);
        } else {
            // Create Lead Object
            $backend_lead = new Backend_Lead($data, $db);
            $backend_lead->save();
        }

        // Assign Lead to Agent
        $agent = Backend_Agent::load($backend_lead['agent']);
        $agent->notifyAgent(array($backend_lead));

        // Assign Lead to Lender
        if (!empty($backend_lead['lender'])) {
            $lender = Backend_Lender::load($backend_lead['lender']);
            if($lender) {
                $lender->notifyLender(array($backend_lead));
            }
        }
    } else {
        if (!empty($data) && !empty($where)) {
            // Update lead
            $db->{'users'}->update($data, $where);

            // Track Changes Made
            $changes = array();
            foreach ($lead as $k => $v) {
                if (isset($requestData[$k]) && ($requestData[$k] != $v)) {
                    // Skip special fields
                    if (in_array($k, array('agent', 'num_visits', 'auto_rotate'))) {
                        continue;
                    }
                    if (!isset($data[$k])) {
                        continue;
                    }

                    // Store Change
                    $changes[$k] = array('field' => $k, 'old' => $v, 'new' => $requestData[$k]);

                    // Log Event: Track Lead Change
                    $event = new History_Event_Update_Lead($changes[$k], array(
                        new History_User_Lead($lead['id']),
                    ));

                    // Save Event
                    $event->save();
                }
            }

            // Updated lead
            $lead = $db->{'users'}->search($where)->fetch();
        }

        // Create Lead Object
        $backend_lead = new Backend_Lead($lead);

        // Re-assign Agent
        if (!empty($requestData['agent_id']) && $lead['agent'] != $requestData['agent_id']) {
            // Assign Lead to Agent
            if (!empty($agent)) {
                $agent->assign($backend_lead, null, $errors);
                $lead['agent'] = $requestData['agent_id'];
            }
        }

        // Re-assign Lender
        if (!empty($requestData['lender_id']) && $lead['lender'] != $requestData['lender_id']) {
            // Assign Lead to Lender
            if (!empty($lender)) {
                $lender->assign($backend_lead, null, $errors);
                $lead['lender'] = $requestData['lender_id'];
            }
        }
    }
    // Lead Groups
    if (isset($requestData['groups'])) {
        $groups = $db->fetchAll("SELECT `g`.`id`, `g`.`name`, `g`.`style`, !ISNULL(`ug`.`user_id`) AS `checked`"
            . " FROM `groups` `g` LEFT JOIN `users_groups` `ug` ON `g`.`id` = `ug`.`group_id` AND `ug`.`user_id` = '" . $lead['id'] . "'"
            . " WHERE (`g`.`agent_id` = '" . $lead['agent'] . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))"
            . " OR `g`.`agent_id` = 1"
            . " ORDER BY `g`.`name` ASC;");

        // Update Lead Groups
        foreach ($groups as $group) {
            // Remove from Group
            if (!empty($group['checked']) && (empty($requestData['groups']) || (is_array($requestData['groups']) && !in_array($group['id'], $requestData['groups'])))) {
                try {
                    $backend_lead->removeGroup($group);
                } catch (PDOException $e) {
                    $errors[] = 'Error Occurred while Removing Lead Group: ' . $group['name'];
                }

                // Add to New Group
            } elseif (empty($group['checked']) && is_array($requestData['groups']) && in_array($group['id'], $requestData['groups'])) {
                try {
                    $backend_lead->assignGroup($group);
                } catch (PDOException $e) {
                    $errors[] = 'Error Occurred while Assigning Lead Group: ' . $group['name'];
                }
            }
        }
    }
} catch (PDOException $e) {
    $errors[] = 'The lead could not be updated: ' . $e->getMessage();
}
if (empty($errors)) {
    // Lead API object
    $object = new API_Object_Lead($db, $backend_lead->getRow());
    $json = $object->getData();
}
