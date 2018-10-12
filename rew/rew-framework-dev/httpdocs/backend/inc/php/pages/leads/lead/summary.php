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
if (!$leadAuth->canViewLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view this lead'
    );
}

$can_assign_action_plans = $leadAuth->canAssignActionPlans();
$can_manage_action_plans = $leadsAuth->canManageActionPlans($authuser);

// Use Lead Object
$lead = new Backend_Lead($lead);

// Load Custom Fields
try {
    // Get Field
    $customFieldFactory= Container::getInstance()->get(REW\Backend\Leads\CustomFieldFactory::class);
    $customFields = $customFieldFactory->loadCustomFields(true);

    // Get Value
    $customValues = [];
    foreach ($customFields as $customField) {
        $customValues[$customField->getName()] = $customField->loadValue($lead['id']);
    }
} catch (\Exception $e) {
    $errors[] = 'Failed to load custom fields.';
}

// Get Partner Authorization
$partnersAuth = new \REW\Backend\Auth\PartnersAuth($settings);

// Update Score
$lead->updateScore();

// Get Viewed Leads
$viewed = $authuser->data('viewed');
$viewed = is_array($viewed) ? $viewed : array();
if (!in_array($lead['id'], $viewed)) {
    // Log Event: Agent Viewed Lead
    $event = new History_Event_Action_ViewedLead(null, array(
        new History_User_Lead($lead['id']),
        $authuser->getHistoryUser()
    ));

    // Save to DB
    $event->save();

    // Add to Viewed Leads
    $viewed[] = $lead['id'];

    // Update Viewed Leads
    $authuser->data('viewed', $viewed);
}

// Assigned Agent
$agent = Backend_Agent::load($lead['agent']);

// Assigned Lender
if (!empty($settings->MODULES['REW_LENDERS_MODULE'])) {
    $lender = !empty($lead['lender']) ? Backend_Lender::load($lead['lender']) : false;
}

// Lead Groups
if ($authuser->isAgent() || $authuser->isAssociate()) {
    $lead['groups'] = Backend_Group::getGroups($errors, Backend_Group::LEAD, $lead->getId());
}

if (!empty($settings->MODULES['REW_ACTION_PLANS'])) {
    // Get Lead's Assigned Action Plans
    $lead['action_plans'] = $lead->getActionPlanInfo($authuser, $leadAuth->canManageLead());
}

// Tracked Visits
$query = "SELECT COUNT(`t1`.`id`) AS `num_pages`, COUNT(DISTINCT `t2`.`id`) AS `num_visits` FROM `" . LM_TABLE_PAGEVIEWS . "` `t1` LEFT JOIN `" . LM_TABLE_VISITS . "` `t2` ON `t1`.`session_id` = `t2`.`id` WHERE `t2`.`user_id` = '" . $lead['id'] . "' GROUP BY `t2`.`user_id`;";
if ($result = mysql_query($query)) {
    $visit_info = mysql_fetch_assoc($result);
    $lead['num_visits'] = $visit_info['num_visits'];
    $lead['num_pages']  = $visit_info['num_pages'];
} else {
    $errors[] = 'Error Occurred while loading Tracked Visits.';
}

// # of Viewed Listings
$query = "SELECT COUNT(DISTINCT `id`) as 'num_listings' FROM `" . LM_TABLE_VIEWED_LISTINGS . "` WHERE `user_id` = '" . $lead['id'] . "';";
if ($result = mysql_query($query)) {
    $favs = mysql_fetch_assoc($result);
    $lead['num_listings'] = $favs['num_listings'];
} else {
    $errors[] = 'Error Occurred while loading Viewed Listings.';
}

// # of Saved Favorites & Recommended Listings
$query = "SELECT COUNT(IF(`agent_id` IS NULL AND `associate` IS NULL, 1, NULL)) as 'num_favorites', COUNT(IF(`agent_id` IS NULL AND `associate` IS NULL, NULL, 1)) as 'num_recommended' FROM `" . LM_TABLE_SAVED_LISTINGS . "` WHERE `user_id` = '" . $lead['id'] . "';";
if ($result = mysql_query($query)) {
    $listings = mysql_fetch_assoc($result);
    $lead['num_favorites'] = $listings['num_favorites'];
    $lead['num_recommended'] = $listings['num_recommended'];
} else {
    $errors[] = 'Error occurred while loading listing count.';
}

// # of Viewed Searches & Suggested Searches
$query = "SELECT SUM(IF(`agent_id` IS NULL AND `associate` IS NULL, 1, NULL)) AS `num_searches`, SUM(IF(`agent_id` IS NULL AND `associate` IS NULL, NULL, 1)) AS `num_suggested` FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `user_id` = '" . $lead['id'] . "';";
if ($result = mysql_query($query)) {
    $searches = mysql_fetch_assoc($result);
    $lead['num_searches'] = $searches['num_searches'];
    $lead['num_suggested'] = $searches['num_suggested'];
} else {
    $errors[] = 'Error occurred while loading listing count.';
}

// # of Saved Searches
$query = "SELECT COUNT(DISTINCT `id`) as 'num_saved' FROM `" . LM_TABLE_SAVED_SEARCHES . "` WHERE `user_id` = '" . $lead['id'] . "';";
if ($result = mysql_query($query)) {
    $searches = mysql_fetch_assoc($result);
    $lead['num_saved'] = $searches['num_saved'];
} else {
    $errors[] = 'Error Occurred while loading Saved Searches.';
}

// # of Form Submissions
$query = "SELECT COUNT(DISTINCT `id`) as 'num_forms' FROM `" . LM_TABLE_FORMS . "` WHERE `user_id` = '" . $lead['id'] . "';";
if ($result = mysql_query($query)) {
    $searches = mysql_fetch_assoc($result);
    $lead['num_forms'] = $searches['num_forms'];
} else {
    $errors[] = 'Error Occurred while loading Form Submissions.';
}

//Note Restrictions
$notesQuery = " FROM `" . LM_TABLE_NOTES . "` WHERE `user_id` = ?";
$notesParams = [$_GET['id']];
if ($authuser->isLender() || (!$leadAuth->canViewAllLeadContent())) {
    $notesQuery .= " AND (`" . ($authuser->isLender() ? 'lender' : 'agent_id') . "` = ?"
        ." OR `share` = 'true' OR (`agent_id` IS NULL AND `lender` IS NULL AND `associate` IS NULL))";
    $notesParams []= $authuser->info('id');
}

// Get Notes
$notesQuery = "SELECT `id`, `note`, `type`, `timestamp`" . $notesQuery . " ORDER BY `timestamp` DESC";
$notes = $db->fetchAll($notesQuery, $notesParams);

// Select Average Stats
$stats = array();
if ($authuser->isAgent() || $authuser->isAssociate()) {
    $averages = array('num_visits', 'num_pages', 'num_listings', 'num_favorites', 'num_searches', 'num_forms');
    foreach ($averages as $average) {
        $query = "SELECT AVG(`" . $average . "`) AS `average` FROM `" . LM_TABLE_LEADS . "` WHERE `" . $average . "` > 0;";
        if ($result = mysql_query($query)) {
            $data = mysql_fetch_assoc($result);
            if (!empty($data['average'])) {
                $stats[$average] = $data['average'];
            }
        } else {
            $errors[] = 'Error Occurred while loading average "' . $average . '".';
        }
    }
}

// Lead Potential & Commision
$lead['commission'] = $lead['value'] * 0.03;

// Lead Last Visit Timestamp nullify blank so it does not display
$lead['timestamp_active'] = $lead['timestamp_active'] === '0000-00-00 00:00:00' ? null : $lead['timestamp_active'];

// Phone # Status
$phone_status = Backend_Lead::$phone_status;
$lead['phone_home_status'] = isset($phone_status[$lead['phone_home_status']]) ? $phone_status[$lead['phone_home_status']] : '';
$lead['phone_cell_status'] = isset($phone_status[$lead['phone_cell_status']]) ? $phone_status[$lead['phone_cell_status']] : '';
$lead['phone_work_status'] = isset($phone_status[$lead['phone_work_status']]) ? $phone_status[$lead['phone_work_status']] : '';

// Social Networks
$networks = array(
    'network_facebook'  => array('title' => 'Facebook', 'image' => 'facebook_16x16.svg'),
    'network_microsoft' => array('title' => 'Windows Live', 'image' => 'windows_16x16.svg'),
    'network_google'    => array('title' => 'Google', 'image' => 'google_16x16.svg'),
    'network_linkedin'  => array('title' => 'LinkedIn', 'image' => 'linkedin_16x16.svg'),
    'network_twitter'   => array('title' => 'Twitter', 'image' => 'twitter_16x16.svg'),
    'network_yahoo'     => array('title' => 'Yahoo!', 'image' => 'yahoo_16x16.svg')
);

// Social Networks
$lead_networks = array();
foreach ($networks as $column => $network) {
    $data = json_decode($lead[$column], true);
    if (!empty($data)) {
        $lead_networks[] = array_merge($network, array('link' => $data['link']));
    }
}
$lead['networks'] = $lead_networks;

// Check User's DotLoop Permissions
$can_manage_dotloop = $partnersAuth->canManageDotLoop($authuser);
if ($can_manage_dotloop) {
    // Load new DotLoop Object With Agent's Credentials
    $dotloopApi = new Partner_DotLoop(Backend_Agent::load($authuser->info('id')), $db);
    // Check if Lead is Connected to Agent's DotLoop Account
    $dotloop_contact_data = $dotloopApi->getLeadConnectData($lead->getId());
    // Check API Access Status
    if (!($dotloop_validated = $dotloopApi->validateAPIAccess())) {
        if ($dotloopApi->getLastAPIErrorID() === Partner_DotLoop::API_ERRORS['RATE_LIMIT_EXCEEDED']) {
            // Get Latest Rate Limit Data
            $dotloop_rate_limit = $dotloopApi->getRateLimitStatus();
        } else if (!empty($authuser->info('partners.dotloop.token_updated'))) {
            $dotloop_token_expired = true;
        }
    }
}
