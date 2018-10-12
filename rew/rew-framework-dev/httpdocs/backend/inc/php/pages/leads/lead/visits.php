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

// Not authorized to view all leads
if (!$leadAuth->canViewLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view lead transactions'
    );
}

/* Lead Visits */
$sessions = array();

/* Select Visits */
$query = "SELECT `id`, `referer`, `keywords`, INET_NTOA(`ip`) AS `ip`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `" . LM_TABLE_VISITS . "` WHERE `user_id` = '" . $lead['id'] . "' ORDER BY `timestamp` DESC;";
if ($result = mysql_query($query)) {
    /* Build Collection */
    while ($visit = mysql_fetch_assoc($result)) {
        $visit['source'] = !empty($visit['keywords']) ? $visit['referer'] . ' for "' . $visit['keywords'] . '"' : $visit['referer'];

        /* Select Visited Pages */
        $query = "SELECT COUNT(`v`.`id`) AS total, UNIX_TIMESTAMP(v.`timestamp`) AS `date`, p.`url`, r.`url` AS `referer`"
               . " FROM `" . LM_TABLE_PAGEVIEWS . "` `v`"
               . " LEFT JOIN `" . LM_TABLE_PAGES . "` `p` ON `v`.`page_id`    = `p`.`id`"
               . " LEFT JOIN `" . LM_TABLE_PAGES . "` `r` ON `v`.`referer_id` = `r`.`id`"
               . " WHERE `v`.`session_id` = '" . $visit['id'] . "'"
               . " GROUP BY `v`.`page_id`"
               . " ORDER BY `v`.`timestamp` ASC;";

        if ($pages = mysql_query($query)) {
            /* Keep Count */
            $count = 1;

            /* Build Collection */
            while ($hit = mysql_fetch_assoc($pages)) {
                /* Remove Junk */
                $hit['url'] = preg_replace('/(\\?|&)uid=([A-Za-z0-9]{40})/', '', $hit['url']);
                $hit['url'] = preg_replace('/(\\?|&)facebox_Frame(=(true|false))?/', '', $hit['url']);
                $hit['url'] = preg_replace('/(\\?|&)popup/', '', $hit['url']);
                $hit['url'] = trim($hit['url'], '?');

                /* Add Page to Visit */
                $visit['pages'][] = $hit;

                /* Increment Count */
                $count++;
            }
        }

        /* Visit Timestamp */
        $datestamp = date('d-m-Y', $visit['timestamp']);

        /* Add to Collection (Group by Date) */
        $sessions[$datestamp][] = $visit;
    }
} else {
    /* Query Error */
    $errors[] = 'Error Occurred while loading Lead Visits.';
}
