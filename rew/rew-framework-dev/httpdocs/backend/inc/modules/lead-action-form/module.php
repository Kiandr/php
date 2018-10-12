<?php

// Auth User
$authuser = Auth::get();

// Can Share
$can_share = $authuser->isSuperAdmin() || $authuser->isAssociate();


// Page Instance
$page = $this->getContainer()->getPage();

/* Action Type */
$action = $this->config('action');

/* Require Lead */
$lead_id = $this->config('lead');
if (!empty($lead_id)) {
    /* Select Lead from Database */
    $query = "SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = '" . mysql_real_escape_string($lead_id) . "';";
    if ($result = mysql_query($query)) {
        /* Fetch Lead Row */
        $lead = mysql_fetch_assoc($result);
    }
}

// Ensure we have access before making the AJAX request
// Google Calendar Object
if (!empty(Settings::getInstance()->MODULES['REW_GOOGLE_CALENDAR']) && $authuser->info('google_calendar_sync') == 'true') {
    $google_calendar = new OAuth_Calendar_Google($page, $authuser);
}
    
// Microsoft Calendar Object
if (!empty(Settings::getInstance()->MODULES['REW_OUTLOOK_CALENDAR']) && $authuser->info('microsoft_calendar_sync') == 'true') {
    $microsoft_calendar = new OAuth_Calendar_Microsoft($page, $authuser);
}

/**
 * Lead Phone Form
 */

    $phone = array();

    /* Types of Phone Calls */
    $phone['types'] = array(
        array('value' => 'call',      'title' => __('Talked to Lead')),
        array('value' => 'attempt',   'title' => __('Attempted')),
        array('value' => 'voicemail', 'title' => __('Voicemail')),
        array('value' => 'invalid',   'title' => __('Wrong Number'))
    );

/**
 * Lead Reminder Form
 */

    $reminder = array();

    /* Reminder Dates */
    $reminder['dates'] = array(
        array('timestamp' => strtotime('now'),      'title' => __('Today')),
        array('timestamp' => strtotime('+1 day'),   'title' => __('Tomorrow')),
        //array('timestamp' => strtotime('+2 days'),  'title' => '2 Days'),
        //array('timestamp' => strtotime('+3 days'),  'title' => '3 Days'),
        array('timestamp' => strtotime('+1 week'),  'title' => __('1 Week')),
        //array('timestamp' => strtotime('+2 weeks'), 'title' => '2 Weeks'),
        array('timestamp' => strtotime('+1 month'), 'title' => __('1 Month'))
    );

    /* Default Timestamp */
    $reminder['timestamp'] = $reminder['dates'][0]['timestamp'];

    /* Reminder Types */
    $reminder['types'] = array();
    $query = "SELECT `id`, `title` FROM `" . TABLE_CALENDAR_TYPES . "`;";
    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($result)) {
            $reminder['types'][] = array('value' => $row['id'], 'title' => $row['title']);
        }
    } else {
        $errors[] = __('Error Loading Reminder Types');
    }
