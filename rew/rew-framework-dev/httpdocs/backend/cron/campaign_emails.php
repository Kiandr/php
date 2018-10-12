<?php

// Restrict Access
if (isset($_SERVER['HTTP_HOST'])) {
    // Require Composer Vendor Auto loader
    require_once __DIR__ . '/../../../boot/app.php';

    // Running from REW Office
    if (Settings::isREW()) {
        // Serve as Plaintext
        header('Content-Type: text/plain');
    } else {
        // Not Authorized
        die('Not Authorized');
    }

// Set ENV Variables
} else {
    // Set HTTP Host & Document Root
    $_SERVER['DOCUMENT_ROOT'] = $argv[1];
    $_SERVER['HTTP_HOST'] = $argv[2];
    $_SERVER['REQUEST_SCHEME'] = $argv[3];
}

// Start Time
$start = time();

// Include Common File
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

// Output
echo 'Starting Campaign Emailer!' . PHP_EOL . PHP_EOL;

// Select Active Campaigns
$getCampaigns = mysql_query("SELECT *, UNIX_TIMESTAMP(`starts`) AS `starts` FROM `" . LM_TABLE_CAMPAIGNS . "` WHERE `active` = 'Y';");
while ($campaign = mysql_fetch_array($getCampaigns)) {
    // Output
    echo PHP_EOL . 'Campaign: ' . $campaign['name'] . PHP_EOL . PHP_EOL;

    // Get Agent
    $query = "SELECT `a`.`id`, `a`.`first_name`, `a`.`last_name`, `a`.`permissions_user`, `t`.`TZ` FROM `" . LM_TABLE_AGENTS . "` `a` LEFT JOIN `timezones` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`id` = '" . $campaign['agent_id'] . "';";
    if ($result = mysql_query($query)) {
        $agent  = mysql_fetch_assoc($result);
        // Check Agent's Permissions
        if (!($agent['permissions_user'] & Auth::PERM_LEADS_CAMPAIGNS || $campaign['agent_id'] == 1)) {
            echo 'Campaign\'s Agent does not have permissions.' . PHP_EOL;
            continue;
        }
    } else {
        echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
        continue;
    }

    // Use Agent's Timezone
    //echo "\t" . 'Timezone: ' . $agent['TZ'] . PHP_EOL;
    date_default_timezone_set($agent['TZ']);
    mysql_query("SET `time_zone` = '" . $agent['TZ'] . "';");

    // Campaign Start Date
    if (!empty($campaign['starts'])) {
        // Check Dates
        $current = new DateTime('today');
        $starts = new DateTime();
        $starts->setTimestamp($campaign['starts']);

        // Has Not Started
        if ($current < $starts) {
            $diff = $current->diff($starts);
            echo "\t" . 'Campaign does not start until: ' . $starts->format('Y-m-d') .  ' (' . $diff->format('in %a days') . ')' . PHP_EOL;
            continue;
        }
    }

    // Select Campaign Groups
    $query = "SELECT `group_id` FROM `" . LM_TABLE_CAMPAIGNS_GROUPS . "` WHERE `campaign_id` = '" . $campaign['id'] . "';";
    if ($groups_res = mysql_query($query)) {
        $campaign_groups = array();
        while ($row = mysql_fetch_assoc($groups_res)) {
            $campaign_groups[] = $row['group_id'];
        }
    }

    // Require Campaign Groups
    if (!empty($campaign_groups)) {
        // Select Opt-In Leads in Campaign Groups
        $query = "SELECT DISTINCT
		      u.`id`,
		      u.`first_name`,
		      u.`last_name`,
		      u.`email`,
		      u.`agent`,
			  u.`verified`,
			  u.`bounced`,
			  u.`fbl`,
			  u.`guid`,
		      IF (cu.`timestamp` IS NULL, IF (DATEDIFF(NOW(), u.`timestamp`) > 0, 1, 0), DATEDIFF(NOW(), cu.`timestamp`)) AS 'campaign_days'
            FROM users u
              JOIN " . LM_TABLE_USER_GROUPS . " ug
                ON u.`id` = ug.`user_id` AND ug.`group_id` IN ( " . implode(",", $campaign_groups) . " )
              LEFT JOIN " . LM_TABLE_CAMPAIGNS_USERS . " cu
                ON u.`id` = cu.`user_id` AND cu.`campaign_id` = '" . $campaign['id'] . "'
            WHERE `opt_marketing` = 'in'";

        // Only send to Agent's leads
        if ($campaign['agent_id'] != 1) {
            $query .= " AND u.`agent` = " . $campaign['agent_id'];
        }

        // Process Leads
        if ($leads = mysql_query($query)) {
            while ($lead = mysql_fetch_array($leads)) {
                // Check if lead has bounced
                if ($lead['bounced'] == 'true') {
                    echo "\t" . $lead['email'] . '\'s e-mail bounced - skipping automated e-mail' . PHP_EOL;
                    continue;
                }

                // Check if lead has repored SPAM
                if ($lead['fbl'] == 'true') {
                    echo "\t" . $lead['email'] . ' has reported us for SPAM - skipping automated e-mail' . PHP_EOL;
                    continue;
                }

                // Check if e-mail host is blocked
                if (Validate::verifyWhitelisted($lead['email'])) {
                    echo "\t" . $lead['email'] . '\'s e-mail provider is on the server block list - skipping automated e-mail' . PHP_EOL;
                    continue;
                }

                // Check if e-mail host requires verification
                if (Validate::verifyRequired($lead['email']) || !empty(Settings::getInstance()->SETTINGS['registration_verify'])) {
                    // User still not verified?
                    if ($lead['verified'] != 'yes') {
                        echo "\t" . $lead['email'] . '\'s e-mail provider is set to require e-mail verification on this server, but the account has not been verified yet - skipping automated e-mail' . PHP_EOL;
                        continue;
                    }
                }

                // Fix Negative Days
                $lead['campaign_days'] = ($lead['campaign_days'] < 0) ? 0 : $lead['campaign_days'];

                // Output
                echo "\t" . $lead['email'] . ' is ' . $lead['campaign_days'] . ' days old: ' . PHP_EOL;

                // Get Campaign Emails
                $getCampaignEmails = mysql_query("SELECT * FROM `" . LM_TABLE_CAMPAIGNS_EMAILS . "` WHERE `campaign_id` = '" . $campaign['id'] . "' AND `doc_id` IS NOT NULL ORDER BY `send_delay`;");
                $allow_reset = true;
                if (mysql_num_rows($getCampaignEmails) != 0) {
                    while ($campaignEmails = mysql_fetch_array($getCampaignEmails)) {
                        // Check if email has already been sent
                        $check_sent = mysql_query("SELECT * FROM " . LM_TABLE_CAMPAIGNS_SENT . " WHERE email_id = '" . $campaignEmails['id'] . "' AND user_id = '" . $lead['id'] . "'");
                        $check_sent = mysql_fetch_array($check_sent);
                        if (!empty($check_sent)) {
                            echo "\t\t". 'has already received email #' . $campaignEmails['id'] . ' - ' . $campaignEmails['subject'] . ' (' . $campaignEmails['send_delay'] . ')' . PHP_EOL;
                            continue;
                        }

                        // should this email be sent?
                        if ($campaignEmails['send_delay'] <= $lead['campaign_days']) {
                            // email should have been sent at an earlier date.. we need to "fix" the campaign date for this user
                            if ($campaignEmails['send_delay'] < $lead['campaign_days']) {
                                // figure out when email should have been sent
                                $campaign_days = $lead['campaign_days'] - $campaignEmails['send_delay'];

                                // has a later email been sent?
                                $result = mysql_query("SELECT es.* FROM " . LM_TABLE_CAMPAIGNS_SENT . " es LEFT JOIN " . LM_TABLE_CAMPAIGNS_EMAILS . " ce ON es.email_id = ce.id WHERE ce.campaign_id = '" . $campaign['id'] . "' AND es.user_id = '" . $lead['id'] . "' AND ce.send_delay > '" . $campaignEmails['send_delay'] . "' ORDER BY ce.send_delay DESC LIMIT 1");
                                $dont_reset = mysql_fetch_array($result);
                                if (!empty($dont_reset)) {
                                    echo "\t\t". 'this email should have been sent ' . $campaign_days . ' days ago. email #' . $campaignEmails['id'] . ' - ' . $campaignEmails['subject'] . ' (' . $campaignEmails['send_delay'] . ')' . PHP_EOL;
                                    continue;
                                } else {
                                    // Output
                                    echo "\t\t".'this email should have been sent ' . $campaign_days . ' days ago. resetting campaign start time.' . PHP_EOL;

                                    // update stuff
                                    $campaign_days = date('Y-m-d H:i:s', strtotime($campaignEmails['send_delay'] . ' days ago'));
                                    $query = "REPLACE INTO " . LM_TABLE_CAMPAIGNS_USERS . " SET timestamp = '" . $campaign_days . "', campaign_id= '" . $campaign['id'] . "', user_id = '" . $lead['id'] . "'";
                                    mysql_query($query) or print (mysql_error() . PHP_EOL . $query);
                                    $lead['campaign_days'] = $campaignEmails['send_delay'];
                                }
                            }

                            // Load Document (Email's Message)
                            $query = "SELECT * FROM `" . LM_TABLE_DOCS . "` WHERE `id` = '" . mysql_real_escape_string($campaignEmails['doc_id']) . "';";
                            $selectDoc = mysql_query($query);
                            $doc = mysql_fetch_assoc($selectDoc);

                            // Force Sender
                            $campaign['sender'] = in_array($campaign['sender'], array('admin', 'agent', 'custom')) ? $campaign['sender'] : 'agent';

                            // Only Super Admin can set Sender, Otherwise force 'agent' as Sender
                            $campaign['sender'] = ($campaign['agent_id'] == 1) ? $campaign['sender'] : 'agent';

                            // Select Sender
                            $agent = false;
                            switch ($campaign['sender']) {
                                // Use Campaign Settings
                                case 'custom':
                                    break;

                                // Send from Super Admin
                                case 'admin':
                                    // Get Admin
                                    $result = mysql_query("SELECT `id`, `first_name`, `last_name`, `email`, `signature`, `add_sig` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
                                    $agent  = mysql_fetch_assoc($result);

                                    // Set Sender
                                    $campaign['sender_name']  = $agent['first_name'] . ' ' . $agent['last_name'];
                                    $campaign['sender_email'] = $agent['email'];
                                    break;

                                // Send from Assigned Agent
                                case 'agent':
                                default:
                                    // Get Agent
                                    $result = mysql_query("SELECT `id`, `first_name`, `last_name`, `email`, `signature`, `add_sig` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = '" . $lead['agent'] . "';");
                                    $agent  = mysql_fetch_assoc($result);

                                    // Set Sender
                                    $campaign['sender_name']  = $agent['first_name'] . ' ' . $agent['last_name'];
                                    $campaign['sender_email'] = $agent['email'];
                                    break;
                            }

                            // Setup Mailer
                            $mailer = new Backend_Mailer(array(
                                'html'          => ($doc['is_html'] !== 'false'),   // HTML vs Plaintext
                                'subject'       => $campaignEmails['subject'],      // Email Subject
                                'message'       => $doc['document'],                // Email Message
                                'template'      => $campaign['tempid'],             // Load Template
                                'cc_email'      => $campaign['cc_email'],           // CC Recipient
                                'bcc_email'     => $campaign['bcc_email'],          // BCC Recipient
                                'signature'     => $agent['signature'],             // Signature
                                'append'        => ($agent['add_sig'] == 'Y'),      // Append Signature
                                'unsubscribe'   => true                             // Require Unsubscribe Link
                            ));

                            // Set Sender
                            $mailer->setSender($campaign['sender_email'], $campaign['sender_name']);

                            // Set Recipient
                            $mailer->setRecipient($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

                            // Mailer Tags
                            $tags = array(
                                'first_name'=> $lead['first_name'],
                                'last_name' => $lead['last_name'],
                                'email'     => $lead['email'],
                                'guid'      => Format::toGuid($lead['guid']),
                                'verify'    => Settings::getInstance()->SETTINGS['URL_IDX'] . 'verify.html?verify=' . Format::toGuid($lead['guid']),
                            );

                            // Send Mail
                            if ($mailer->Send($tags)) {
                                // Success
                                echo "\t\t" . 'was sent email #' . $campaignEmails['id'] . ' - ' . $campaignEmails['subject'] . ' (' . $campaignEmails['send_delay'] . ')' . PHP_EOL;

                                // Track Sent Email
                                mysql_query("INSERT IGNORE INTO `" . LM_TABLE_CAMPAIGNS_SENT . "` SET `email_id` = '" . $campaignEmails['id'] . "', `user_id` = '" . $lead['id'] . "';");

                                // Log Event: Campaign Email sent to Lead
                                $event = new History_Event_Email_Campaign(array(
                                    'campaign'  => $campaign['name'],
                                    'plaintext' => !$mailer->isHTML(),
                                    'subject'   => $mailer->getSubject(),
                                    'message'   => $mailer->getMessage(),
                                    'tags'      => $mailer->getTags()
                                ), array(
                                    new History_User_Lead($lead['id']),
                                    (!empty($agent) ? new History_User_Agent($agent['id']) : new History_User_Agent($campaign['agent_id']))
                                ));

                                // Save to DB
                                $event->save();

                                // Delay Script by 1 Second
                                sleep(1);

                            // Error
                            } else {
                                echo "\t\t" . 'Error: failed to send email. #' . $campaignEmails['id'] . ' - ' . $campaignEmails['subject'] . ' (' . $campaignEmails['send_delay'] . ')' . PHP_EOL;
                            }

                        // email doesn't need to be sent
                        } else {
                            echo "\t\t" . 'does not meet time requirement of #' . $campaignEmails['id'] . ' - ' . $campaignEmails['subject'] . ' (' . $campaignEmails['send_delay'] . ')' . PHP_EOL;
                            break; // Since we sorted by send_delay, future iterations will always be false so lets be a bit quieter
                        }
                    }
                } else {
                    // Output
                    echo "\t" . 'No emails to send.' . PHP_EOL;
                }

                // Set the time that they first were in the campaign
                $query = "INSERT IGNORE INTO `" . LM_TABLE_CAMPAIGNS_USERS . "` SET `timestamp` = DATE_SUB(NOW(), INTERVAL 1 DAY), `campaign_id` = '" . $campaign['id'] . "', `user_id` = '" . $lead['id'] . "';";
                mysql_query($query);

                // Output
                echo PHP_EOL;
            }
        }
    } else {
        // Output
        echo 'No Groups in this campaign.' . PHP_EOL;
    }

    // Output
    echo PHP_EOL;
}

// Output
echo PHP_EOL . PHP_EOL . Log::stopWatch($start) . PHP_EOL;
