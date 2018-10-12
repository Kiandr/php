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

// Not authorized to view lead
if (!$leadAuth->canViewLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view this leads history'
    );
}

// Lead History
$history = array();

// Types of Events
$filters = array(
    'Lead Activity' => array(
        array('value' => 'Action_Login',          'title' => 'Login'),
        array('value' => 'Action_Logout',         'title' => 'Logout'),
        array('value' => 'Action_Unsubscribe',    'title' => 'Unsubscribe'),
        array('value' => 'Action_FormSubmission', 'title' => 'Inquiries'),
        array('value' => 'Action_ViewedListing',  'title' => 'Viewed Listings'),
        array('value' => 'Action_DismissListing,Delete_DismissListing', 'title' => 'Dismiss Listings'),
        array('value' => 'Action_SavedListing,Delete_SavedListing',   'title' => 'Saved Listings'),
        array('value' => 'Action_SavedSearch,Delete_SavedSearch',    'title' => 'Saved Searches'),
        array('value' => 'Action_Connected',      'title' => 'Connected'),
    ),
    'Agent Activity' => array(
        (!$authuser->isLender() ? array('value' => 'Action_ViewedLead',   'title' => 'Viewed by Agent') : null),
        array('value' => 'Create_Lead',         'title' => 'Lead Created'),
        (!$authuser->isLender() ? array('value' => 'Create_LeadReminder', 'title' => 'Reminders') : null),
        array('value' => 'Create_LeadNote',     'title' => 'Notes'),
        (!$authuser->isLender() ? array('value' => 'Create_LeadTransaction', 'title' => 'Transactions') : null),
    ),
    'Updates & Changes' => (!$authuser->isLender() ? array(
        array('value' => 'Update_Lead',        'title' => 'Edits'),
        array('value' => 'Update_Assign',      'title' => 'Assign'),
        array('value' => 'Update_UnAssign',    'title' => 'Un-Assign'),
        array('value' => 'Update_Rejected',    'title' => 'Rejected'),
        array('value' => 'Update_Status',      'title' => 'Status'),
        array('value' => 'Update_GroupAdd',    'title' => 'Assign to Group'),
        array('value' => 'Update_GroupRemove', 'title' => 'Remove from Group')
    ) : null),
    'Emails' => array(
        array('value' => 'Email_AutoResponder', 'title' => 'Auto-Responder'),
        array('value' => 'Email_Campaign',      'title' => 'Campaign'),
        array('value' => 'Email_Delayed',       'title' => 'Delayed'),
        array('value' => 'Email_Bounce,Email_FBL', 'title' => 'Bounced'),
        array('value' => 'Email_Listings', 'title' => 'Listings'),
        array('value' => 'Email_Sent,Email_Direct,Email_Verification', 'title' => 'Sent')
    ),
    'Calls' => array(
        array('value' => 'Phone_Contact',   'title' => 'Contacted'),
        array('value' => 'Phone_Attempt',   'title' => 'Attempted'),
        array('value' => 'Phone_Voicemail', 'title' => 'Voicemail'),
        array('value' => 'Phone_Invalid',   'title' => 'Bad Number')
    ),
    'Texts' => array(
        array('value' => 'Text_Outgoing,Text_Listing', 'title' => 'Outgoing'),
        array('value' => 'Text_Incoming,Text_OptIn,Text_OptOut', 'title' => 'Incoming')
    )
);

// Search Query
$sql_where = array();

// Search Criteria
$criteria = array();

// Filter by Type
if (!empty($_GET['filters'])) {
    $_GET['filters'] = is_array($_GET['filters']) ? $_GET['filters'] : explode(',', $_GET['filters']);
    if (!empty($_GET['filters']) && is_array($_GET['filters'])) {
        $sql_where[] = "CONCAT(`he`.`type`, '_', `he`.`subtype`) IN ('" . implode("', '", array_map(function ($filter) {
            return implode("', '", array_map(function ($filter) {
                return mysql_real_escape_string($filter);
            }, explode(',', $filter)));
        }, $_GET['filters'])) . "')";
    }
}

// Event Filter
if (!empty($_GET['filter'])) {
    $title = false;
    switch ($_GET['filter']) {
        case 'inquiries':
            $_GET['filters'] = array('Action_FormSubmission');
            $title = 'Inquiries';
            $sql_where[] = "`he`.`type` = 'Action' AND `he`.`subtype` = 'FormSubmission'";
            break;
        case 'texts':
            $title = 'Text Message History';
            $sql_where[] = "`he`.`type` = 'Text'";
            $_GET['filters'] = array();
            if (!empty($filters['Texts'])) {
                foreach ($filters['Texts'] as $filter) {
                    $_GET['filters'][] = $filter['value'];
                }
            }
            break;
        case 'calls':
            $title = 'Phone Calls';
            $sql_where[] = "`he`.`type` = 'Phone'";
            $_GET['filters'] = array();
            if (!empty($filters['Calls'])) {
                foreach ($filters['Calls'] as $filter) {
                    $_GET['filters'][] = $filter['value'];
                }
            }
            break;
        case 'emails':
            $title = 'Email History';
            $sql_where[] = "`he`.`type` = 'Email'";
            $_GET['filters'] = array();
            if (!empty($filters['Emails'])) {
                foreach ($filters['Emails'] as $filter) {
                    $_GET['filters'][] = $filter['value'];
                }
            }
            break;
        default:
            unset($_GET['filter']);
            break;
    }
}

// JOIN Tables
$sql_join = array();

// Filter by Agent
if (!empty($_GET['agent'])) {
    $sql_join[] = " JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent' AND `a`.`user` = '" . mysql_real_escape_string($_GET['agent']) . "'";
    $criteria[] = 'Agent\'s ' . (!empty($_GET['filter']) ? ucwords(trim($_GET['filter'], 's')) . ' ' : '') . 'Activity';

// Filter by Lender
} elseif (!empty($_GET['lender'])) {
    $sql_join[] = " JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender' AND `l`.`user` = '" . mysql_real_escape_string($_GET['lender']) . "'";
    $criteria[] = 'Lender\'s ' . (!empty($_GET['filter']) ? ucwords(trim($_GET['filter'], 's')) . ' ' : '') . 'Activity';
} else {
    // Is Agent: Show History for $authuser
    if ($authuser->isAgent() && !$authuser->isSuperAdmin()) {
        // Show Calls & Emails
        if (in_array($_GET['filter'], array('calls', 'emails'))) {
            // Not Performed by $authuser's
            if (isset($_GET['other'])) {
                $sql_join[] = " LEFT JOIN `history_users` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent'";
                $sql_where[] = "(`a`.`user` IS NULL OR `a`.`user` != '" . $authuser->info('id') . "')";
                $criteria[] = 'Other Activity';

            // Performed by $authuser
            } else {
                $sql_join[] = " JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent' AND `a`.`user` = '" . $authuser->info('id') . "'";
                $criteria[] = 'Your Activity';
            }

        // Only show events that are performed by $authuser, Assigned Agent, or by No-one
        } elseif ($_GET['filters'] != 'inquiries') {
            $sql_join[] = " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent'";
            $sql_join[] = " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender'";
            $sql_join[] = " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `isa` ON `he`.`id` = `isa`.`event` AND `isa`.`type` = 'Associate'";
            $sql_where[] = "(`isa`.`user` IS NULL OR `he`.`type` IN ('Phone', 'Email'))";
            $sql_where[] = "(`l`.`user` IS NULL OR (`l`.`user` = '" . $lead['lender'] . "' AND `he`.`type` IN ('Phone', 'Email')))";
            $sql_where[] = "(`a`.`user` IS NULL OR `a`.`user` = '" . $authuser->info('id') . "')";
        }

    // Is Lender: Show History for $authuser
    } elseif ($authuser->isLender()) {
        // Show Calls & Emails
        if (in_array($_GET['filter'], array('calls', 'emails'))) {
            // Not Performed by $authuser's
            if (isset($_GET['other'])) {
                $sql_join[] = " LEFT JOIN `history_users` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender'";
                $sql_where[] = "(`l`.`user` IS NULL OR  `l`.`user` != '" . $authuser->info('id') . "')";
                $criteria[] = 'Other Activity';

            // Performed by $authuser
            } else {
                $sql_join[] = " JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender' AND `l`.`user` = '" . $authuser->info('id') . "'";
                $criteria[] = 'Your Activity';
            }

        // Only show events that are performed by $authuser, Assigned Agent, or by No-one
        } elseif ($_GET['filters'] != 'inquiries') {
            $sql_join[] = " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `a` ON `he`.`id` = `a`.`event` AND `a`.`type` = 'Agent'";
            $sql_join[] = " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `l` ON `he`.`id` = `l`.`event` AND `l`.`type` = 'Lender'";
            $sql_join[] = " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `isa` ON `he`.`id` = `isa`.`event` AND `isa`.`type` = 'Associate'";
            $sql_where[] = "(`isa`.`user` IS NULL OR `he`.`type` IN ('Phone', 'Email'))";
            $sql_where[] = "(`a`.`user` IS NULL OR (`a`.`user` = '" . $lead['agent'] . "' AND `he`.`type` IN ('Phone', 'Email')))";
            $sql_where[] = "(`l`.`user` IS NULL OR `l`.`user` = '" . $authuser->info('id') . "')";
        }
    }
}

// Stringify
$sql_join = !empty($sql_join) ? implode(' ', $sql_join) : '';

// Stringify
$sql_where = !empty($sql_where) ? implode(' AND ', $sql_where) : '';

// Count History
$query = "SELECT COUNT(`he`.`id`) AS `total` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` `he`"
    . " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `hu` ON `he`.`id` = `hu`.`event`"
    . $sql_join
    . " WHERE `hu`.`user` = '" . $lead['id'] . "' AND `hu`.`type` = 'Lead'"
    . (!empty($sql_where) ? ' AND ' . $sql_where : '') . ";";
if ($result = mysql_query($query)) {
    // Select Count
    $count = mysql_fetch_assoc($result);

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

    // Select Lead History from Database
    $query = "SELECT `he`.`id` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` `he`"
        . " LEFT JOIN `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` `hu` ON `he`.`id` = `hu`.`event`"
        . $sql_join
        . " WHERE `hu`.`user` = '" . $lead['id'] . "' AND `hu`.`type` = 'Lead'"
        . (!empty($sql_where) ? ' AND ' . $sql_where : '')
        . $sql_order
        . $sql_limit
        . ";";
    if ($result = mysql_query($query)) {
        // Process Events
        while ($event = mysql_fetch_assoc($result)) {
            // Load History Event
            $event = History_Event::load($event['id']);

            // Add to Collection
            $history[date('d-m-Y', $event->getTimestamp())][] = $event;
        }
    } else {
        // Query Error
        $errors[] = 'Error Occurred while Loading History Events';
    }
} else {
    // Query Error
    $errors[] = 'Error Occurred while Loading History Events';
}
