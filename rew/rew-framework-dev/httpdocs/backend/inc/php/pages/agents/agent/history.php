<?php

// Get Database
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
$agent = Backend_Agent::load($_GET['id']);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to view agent history
if (!$agentAuth->canViewHistory()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view agent history.')
    );
}

// Agent History
$history = array();

// Types of Events
$filters = array(
    'Lead Activity' => array(
        array('value' => 'Action_ViewedLead',   'title' => __('Viewed Leads')),
        array('value' => 'Create_Lead',         'title' => __('Created Leads')),
        array('value' => 'Create_LeadReminder', 'title' => __('Lead Reminders')),
        array('value' => 'Create_LeadNote',     'title' => __('Lead Notes')),
    ),
    'Updates & Changes' => array(
        array('value' => 'Update_Lead',        'title' => __('Edits')),
        array('value' => 'Update_Assign',      'title' => __('Assign')),
        array('value' => 'Update_UnAssign',    'title' => __('Un-Assign')),
        array('value' => 'Update_Rejected',    'title' => __('Rejected')),
        array('value' => 'Update_Status',      'title' => __('Status')),
        array('value' => 'Update_GroupAdd',    'title' => __('Assign to Group')),
        array('value' => 'Update_GroupRemove', 'title' => __('Remove from Group'))
    ),
    'Emails' => array(
        array('value' => 'Email_AutoResponder', 'title' => __('Auto-Responder')),
        array('value' => 'Email_Campaign',      'title' => __('Campaign')),
        array('value' => 'Email_Delayed',       'title' => __('Delayed')),
        array('value' => 'Email_Bounce,Email_FBL', 'title' => __('Bounced')),
        array('value' => 'Email_Listings', 'title' => __('Listings')),
        array('value' => 'Email_Sent,Email_Direct,Email_Lender,Email_Agent,Email_Associate,Email_Verification', 'title' => __('Sent'))
    ),
    'Calls' => array(
        array('value' => 'Phone_Contact',   'title' => __('Contacted')),
        array('value' => 'Phone_Attempt',   'title' => __('Attempted')),
        array('value' => 'Phone_Voicemail', 'title' => __('Voicemail')),
        array('value' => 'Phone_Invalid',   'title' => __('Bad Number'))
    ),
    'Texts' => array(
        array('value' => 'Text_Outgoing',   'title' => __('Outgoing')),
        array('value' => 'Text_Incoming',   'title' => __('Incoming')),
        array('value' => 'Text_OptOut',     'title' => __('Opt-Out')),
        array('value' => 'Text_OptIn',      'title' => __('Opt-In'))
    )
);

// Search Query
$sql_where = array();
$sql_where_values = array();

// Filter by Type
if (!empty($_GET['filters'])) {
    $_GET['filters'] = is_array($_GET['filters']) ? $_GET['filters'] : explode(',', $_GET['filters']);
    if (!empty($_GET['filters']) && is_array($_GET['filters'])) {
        $sql_where[] = "CONCAT(`he`.`type`, '_', `he`.`subtype`) IN (" . implode(", ", array_map(function ($filter) use(&$sql_where_values){
            return implode("', '", array_map(function ($filter) use(&$sql_where_values) {
                $sql_where_values[] = $filter;
                return '?';
            }, explode(',', $filter)));
        }, $_GET['filters'])) . ")";
    }
}

// Stringify
$sql_where = !empty($sql_where) ? implode(' AND ', $sql_where) : '';

try {
    // Count History
    $count = $db->fetch("SELECT COUNT(*) AS `total` FROM (SELECT `he`.`id` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` `he` LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `hu` ON `he`.`id` = `hu`.`event` WHERE `hu`.`user` = ? AND `hu`.`type` = 'Agent'" . (!empty($sql_where) ? ' AND ' . $sql_where : '') . " GROUP BY `he`.`id`) `his`;", array_merge([
        $agent['id']
    ], $sql_where_values));

    // Search Limit
    $limit = 250;
    if ($count['total'] > $limit) {
        $limitvalue = (($_GET['p'] - 1) * $limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . $limit;
    }

    // Query String
    list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
    parse_str($query, $query_string);

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $limit, $query_string);

    // Order Data
    $sql_sort  = ($_GET['sort'] == 'ASC') ? 'ASC' : 'DESC';
    $sql_order = " ORDER BY `he`.`timestamp` " . $sql_sort . ", `he`.`id` " . $sql_sort;

    try {
        // Select Agent History from Database
        $query = $db->prepare("SELECT `he`.`id` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` `he` LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `hu` ON `he`.`id` = `hu`.`event` WHERE `hu`.`user` = ? AND `hu`.`type` = 'Agent'" . (!empty($sql_where) ? ' AND ' . $sql_where : '') . " GROUP BY `he`.`id`" . $sql_order . $sql_limit . ";");
        $query->execute(array_merge([
            $agent['id']
        ], $sql_where_values));
        while($event = $query->fetch()) {
            // Load History Event
            $event = History_Event::load($event['id']);

            // Add to Collection, Use Date as Key
            $history[date('d-m-Y', $event->getTimestamp())][] = $event;
        }
    } catch (PDOException $e) {
        // Query Error
        $errors[] = __('Error Occurred while Loading History Events');
    }
} catch (PDOException $e) {
    // Query Error
    $errors[] = __('Error Occurred while Loading History Events');
}
