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
        'You do not have permission to view this leads searches'
    );
}

// Can Create or Edit Saved Searches
$can_create = $can_edit = $leadAuth->canEditLead();

// Can Delete Saved Searches
$can_delete = $leadAuth->canManageLead();

// Can Suggest Search: Must be Opt-In to Searches & Not Flagged as Bounced or FBL
$can_suggest = (!empty($can_create) && $lead['opt_searches'] == 'in' && $lead['bounced'] == 'false' && $lead['fbl'] == 'false') ? true : false;

// Suggest Saved Search
if (!empty($_GET['suggest'])) {
    // Check If Can Suggest...
    if (empty($can_suggest)) {
        // Lead Opt-Out of Searches
        if ($lead['opt_searches'] != 'in') {
            $errors[] = 'This lead has unsubscribed from saved searches: You cannot suggest a search to this lead.';

        // Flagged as Bounced
        } elseif ($lead['bounced'] != 'false') {
            $errors[] = 'This lead\'s email address has bounced: You cannot suggest a search to this lead.';

        // Flagged as Reported
        } elseif ($lead['fbl'] != 'false') {
            $errors[] = 'This lead has reported an email as SPAM: You cannot suggest a search to this lead.';
        }
    } else {
        // Select Viewed Search
        $query = "SELECT * FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE (`agent_id` IS NULL AND `associate` IS NULL) AND `id` = '" . mysql_real_escape_string($_GET['suggest']) . "' AND `user_id` = '" . $lead['id'] . "';";
        if ($result = mysql_query($query)) {
            // Require Search
            $search = mysql_fetch_assoc($result);
            if (!empty($search)) {
                // Search Criteria
                $criteria = unserialize($search['criteria']);
                $criteria = is_array($criteria) ? $criteria : array();

                // Setup Mailer
                $mailer = new Backend_Mailer_SavedSearchSuggestion(array(
                    'url' => (($authuser->info('cms') == 'true' && $authuser->info('cms_link') != '') ? sprintf(Settings::getInstance()->SETTINGS['URL_AGENT_SITE'], $authuser->info('cms_link')) : Settings::getInstance()->SETTINGS['URL']),
                    'criteria' => array_merge($criteria, array(
                        'suggested' => $search['id']
                    )),
                    'lead'      => $lead,                               // Lead Data
                    'signature' => $authuser->info('signature'),        // Signature
                    'append'    => ($authuser->info('add_sig') == 'Y')  // Append Signature
                ));

                // Load Agent
                $agent = new Backend_Agent($authuser->getInfo());

                // Check Outgoing Notification Settings
                $mailer = $agent->checkOutgoingNotifications($mailer, Backend_Agent_Notifications::OUTGOING_SEARCH_SUGGEST);

                // Set Sender
                $mailer->setSender($authuser->info('email'), $authuser->getName());

                // Set Recipient
                $mailer->setRecipient($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

                // Send Email
                if ($mailer->Send()) {
                    // Update viewed search to be tracked as suggested search
                    if ($authuser->isAgent()) {
                        mysql_query("UPDATE `" . TABLE_VIEWED_SEARCHES . "` SET `agent_id` = '" . $authuser->info('id') . "' WHERE `id` = '" . $search['id'] . "';");
                    } elseif ($authuser->isAssociate()) {
                        mysql_query("UPDATE `" . TABLE_VIEWED_SEARCHES . "` SET `associate` = '" . $authuser->info('id') . "' WHERE `id` = '" . $search['id'] . "';");
                    }

                    // Set `auto_search` to to prevent automated smart search from being generated
                    mysql_query("UPDATE `" . LM_TABLE_LEADS . "` SET `auto_search` = 'Y' WHERE `id` = '" . $lead['id'] . "';");

                    // Success
                    $success[] = 'Search has successfully been suggested. An email has been sent to ' . $lead['email'] . '.';

                    // Log Event: Email Sent to Lead
                    $event = new History_Event_Email_Sent(array(
                        'subject' => $mailer->getSubject(),
                        'message' => $mailer->getMessage()
                    ), array(
                        new History_User_Lead($lead['id']),
                        $authuser->getHistoryUser(),
                    ));

                    // Save to DB
                    $event->save();

                // Mailer Error
                } else {
                    $errors[] = 'An error occurred while trying to suggest the selected recent search.';
                }
            }
        }
    }
}

// Un-Suggest Saved Search
if (!empty($_GET['unsuggest']) && !empty($can_suggest)) {
    $query = "SELECT * FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE (`agent_id` IS NOT NULL OR `associate` IS NOT NULL) AND `id` = '" . mysql_real_escape_string($_GET['unsuggest']) . "' AND `user_id` = '" . $lead['id'] . "';";
    if ($result = mysql_query($query)) {
        $search = mysql_fetch_assoc($result);
        if (!empty($search)) {
            $query = "DELETE FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `id` = '" . $search['id'] . "';";
            if (mysql_query($query)) {
                $success[] = 'The selected search has successfully been removed.';
            } else {
                $errors[] = 'An error occurred while trying to remove the selected search.';
            }
        }
    }
}

// Delete Saved Search
if (!empty($_GET['delete']) && !empty($can_delete)) {
    // Select Search
    $query = "SELECT * FROM `" . LM_TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "' AND `user_id` = '" . $lead['id'] . "';";
    if ($result = mysql_query($query)) {
        $delete = mysql_fetch_assoc($result);

        // Require Search
        if (!empty($delete)) {
            // Delete Search
            $query = "DELETE FROM `" . LM_TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . mysql_real_escape_string($delete['id']) . "';";
            if (mysql_query($query)) {
                // Success
                $success[] = 'The selected saved search has been deleted.';

                // Decrease Favorite Count
                mysql_query("UPDATE `" . LM_TABLE_LEADS . "` SET `num_saved` = IF(`num_saved` > 0, `num_saved` - 1, 0) WHERE `id` = '" . $lead['id'] . "';");
                $lead['num_saved'] = $lead['num_saved'] > 0 ? $lead['num_saved']-- : 0;

                // Log Event: Agent Deleted a Lead's Saved Search
                $event = new History_Event_Delete_SavedSearch(array(
                    'search' => $delete
                ), array(
                    new History_User_Lead($lead['id']),
                    $authuser->getHistoryUser()
                ));

                // Save to DB
                $event->save();

            // Query Error
            } else {
                $errors[] = 'An error occurred while trying to delete the selected search.';
            }
        }
    }
}

// Agents (to Cache)
$agents = array();

// Associates (to Cache)
$associates = array();

// IDX Searches
$searches = array(
    'suggested' => array(),
    'viewed' => array(),
    'saved'  => array()
);

// Select Recently Viewed Searches & Agent/Associate Suggested Searches
if ($result = mysql_query("SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, IF(`agent_id` IS NULL AND `associate` IS NULL, 0, 1) AS `weight` FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `user_id` = '" . $lead['id'] . "' ORDER BY `weight` DESC, `timestamp` DESC;")) {
    while ($viewed_search = mysql_fetch_assoc($result)) {
        // Suggested by Agent
        if (!empty($viewed_search['agent_id'])) {
            $agent = $agents[$viewed_search['agent_id']];
            if (!empty($agent)) {
                $viewed_search['agent'] = $agent;
            } else {
                $query = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = '" . mysql_real_escape_string($viewed_search['agent_id']) . "';";
                if ($agent = mysql_query($query)) {
                    $agent = mysql_fetch_assoc($agent);
                    if (!empty($agent)) {
                        $viewed_search['agent'] = $agents[$agent['id']] = $agent;
                    }
                }
            }
        // Suggested by Inside Sales Associate
        } else if (!empty($viewed_search['associate'])) {
            $associate = $associates[$viewed_search['associate']];
            if (!empty($associate)) {
                $viewed_search['associate'] = $associate;
            } else {
                $query = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates` WHERE `id` = '" . mysql_real_escape_string($viewed_search['associate']) . "';";
                if ($associate = mysql_query($query)) {
                    $associate = mysql_fetch_assoc($associate);
                    if (!empty($associate)) {
                        $viewed_search['associate'] = $associate[$associate['id']] = $associate;
                    }
                }
            }
        }

        // Unserialize Criteria
        $viewed_search['criteria'] = unserialize($viewed_search['criteria']);

        // Parse Criteria to Generate Readable String
        $viewed_search['criteria'] = Util_IDX::parseCriteria($viewed_search['criteria'], $viewed_search['idx']);
        $viewed_search['criteria'] = !empty($viewed_search['criteria']) ? $viewed_search['criteria'] : 'No Search Criteria';

        // Search URL
        $viewed_search['url_view'] = Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'] . '?' . $viewed_search['url'];

        // Suggested By Agent
        if (!empty($viewed_search['agent_id']) || !empty($viewed_search['associate'])) {
            // Add to Suggested Searches
            $searches['suggested'][] = $viewed_search;
        } else {
            // Add to Viewed Searches
            $searches['viewed'][] = $viewed_search;

            // Limit to Last 10 Viewed Searches
            if (count($searches['viewed']) >= 10) {
                break;
            }
        }
    }

// Query Error
} else {
    $errors[] = 'Error Occurred while loading Recent Searches.';
}

// Select Saved Searches
if ($result = mysql_query("SELECT *, UNIX_TIMESTAMP(`timestamp_sent`) AS `timestamp_sent`, UNIX_TIMESTAMP(`timestamp_created`) AS `timestamp_created`, UNIX_TIMESTAMP(`timestamp_updated`) AS `timestamp_updated` FROM `" . LM_TABLE_SAVED_SEARCHES . "` WHERE `user_id` = '" . $lead['id'] . "' ORDER BY `timestamp_created` DESC;")) {
    while ($saved_search = mysql_fetch_assoc($result)) {
        // Suggested by Agent
        if (!empty($saved_search['agent_id'])) {
            $agent = $agents[$saved_search['agent_id']];
            if (!empty($agent)) {
                $saved_search['agent'] = $agent;
            } else {
                $query = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = '" . mysql_real_escape_string($saved_search['agent_id']) . "';";
                if ($agent = mysql_query($query)) {
                    $agent = mysql_fetch_assoc($agent);
                    if (!empty($agent)) {
                        $saved_search['agent'] = $agents[$agent['id']] = $agent;
                    }
                }
            }
        // Suggested by Inside Sales Associate
        } else if (!empty($saved_search['associate'])) {
            $associate = $associates[$saved_search['associate']];
            if (!empty($associate)) {
                $saved_search['associate'] = $associate;
            } else {
                $query = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates` WHERE `id` = '" . mysql_real_escape_string($saved_search['associate']) . "';";
                if ($associate = mysql_query($query)) {
                    $associate = mysql_fetch_assoc($associate);
                    if (!empty($associate)) {
                        $saved_search['associate'] = $associates[$associate['id']] = $associate;
                    }
                }
            }
        }

        // Unserialize Criteria
        $saved_search['criteria'] = unserialize($saved_search['criteria']);

        // Search URLs
        if ($saved_search['criteria']['search_by'] == 'map') {
            $saved_search['url_edit'] = Settings::getInstance()->URLS['URL_IDX_MAP'] . '?edit_search=true&saved_search_id=' . $saved_search['id'] . '&lead_id=' . $lead['id'];
            $saved_search['url_view'] = Settings::getInstance()->URLS['URL_IDX_MAP'] . '?' . http_build_query($saved_search['criteria']);
        } else {
            $saved_search['url_edit'] = Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'] . '?edit_search=true&saved_search_id=' . $saved_search['id'] . '&lead_id=' . $lead['id'];
            $saved_search['url_view'] = Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'] . '?' . http_build_query($saved_search['criteria']);
        }

        // Parse Criteria to Generate Readable String
        $saved_search['criteria'] = Util_IDX::parseCriteria($saved_search['criteria'], $saved_search['idx']);
        $saved_search['criteria'] = !empty($saved_search['criteria']) ? $saved_search['criteria'] : 'No Search Criteria';

        // Email Frequency (Weekly)
        $search['frequency'] = !empty($search['frequency']) ? $search['frequency'] : 'Weekly';

        // Add to Saved Searches
        $searches['saved'][] = $saved_search;
    }

// Query Error
} else {
    $errors[] = 'Error Occurred while loading Saved Searches.';
}
