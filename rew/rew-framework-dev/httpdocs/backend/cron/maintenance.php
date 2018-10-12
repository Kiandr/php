<?php

/**
 * Don't Execute through Apache
 */
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
} else {
    /* Set HTTP Host & Document Root */
    $_SERVER['HTTP_HOST']     = basename($_SERVER['HOME']);

    // Require Composer Vendor Auto loader
    require_once __DIR__ . '/../../../boot/app.php';

    if (!Http_Host::isDev()) {
        $_SERVER['HTTP_HOST'] = 'www.' . $_SERVER['HTTP_HOST'];
        // Reset class caches
        Http_Host::isDev(true);
    }
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['HOME'] . '/app/httpdocs';

    // SSL
    $_SERVER['REQUEST_SCHEME'] = (Settings::getInstance()->SSL ? 'https' : 'http');

    /* Sleep before starting - 0-5 minutes */
    sleep(mt_rand(0, 300));
}

/* Start Time */
$start = time();

/* Include Common File */
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

define('MAINT_DEBUG', Settings::isREW()); // Turn on extra output

/* Cron Settings*/
$cron = array(
    'score'  => true,
    'search' => true,
    'rotate' => true,
    'reminder' => true,
    'delayed' => true,
    'optout' => true,
    'tasks' => true,
    'dotloop_sync' => true,
);

/**
 * Automated Agent Opt-Out
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Automated Agent Opt-Out: ';
}
if (empty($cron['optout'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    // Set Debug Mode
    Backend_Agent_OptOut::setDebug(MAINT_DEBUG);

    // Agent Opt-Out Feature
    $optout = new Backend_Agent_OptOut();
    $optout->execute();

    // Disable Debug Mode
    Backend_Agent_OptOut::setDebug(false);
}

/**
 * Lead Auto-Rotation
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Lead Auto-Rotation: ';
}
if (empty($cron['rotate'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    // Select Auto-Rotate Settings
    $query = "SELECT `auto_rotate`, `auto_rotate_agent`, `auto_rotate_days`, `auto_rotate_hours`, `auto_rotate_frequency`, `auto_rotate_unassign` FROM `" . TABLE_SETTINGS . "` WHERE `agent` = 1;";
    if ($settings = mysql_query($query)) {
        $settings = mysql_fetch_assoc($settings);

        // Auto-Rotate is Off
        if ($settings['auto_rotate'] == 'false') {
            if (MAINT_DEBUG) {
                echo 'Off' . PHP_EOL;
            }

        // Check Rotation Days
        } elseif (empty($settings['auto_rotate_days']) || !in_array(date('w'), explode(',', $settings['auto_rotate_days']))) {
            if (MAINT_DEBUG) {
                echo date('w (l)') . ' is not in Rotation Days (' . $settings['auto_rotate_days'] . ')' . PHP_EOL;
            }

        // Check Rotation Hours
        } elseif (!in_array(date('G'), explode(',', $settings['auto_rotate_hours']))) {
            if (MAINT_DEBUG) {
                echo date('G:i') . ' is not in Rotation Hours (' . $settings['auto_rotate_hours'] . ')' . PHP_EOL;
            }
        } else {
            // Rotate Frequency
            $m = intval($settings['auto_rotate_frequency']);
            $d = floor($m / 1440);
            $m -= $d * 1440;
            $h = floor($m / 60);
            $m -= $h * 60;
            $frequency = implode(', ', array_filter(array(
                (!empty($d) ? $d . ' ' . Format::plural($d, 'days', 'day') : ''),
                (!empty($h) ? $h . ' ' . Format::plural($h, 'hours', 'hour') : ''),
                (!empty($m) ? $m . ' ' . Format::plural($m, 'minutes', 'minute') : '')
            )));

            // Generate Auto-Rotate Pools
            $auto_rotate_pools = array('global' => 0);
            if ($apps_result = mysql_query("SELECT `id` FROM `api_applications`;")) {
                while ($app_row = mysql_fetch_assoc($apps_result)) {
                    // Pool ID
                    $pool_id = intval($app_row['id']);
                    $pool_last_agent = 0;

                    // Get last agent for this pool
                    $sql = "SELECT `last_agent_id` FROM `agents_auto_rotate` WHERE `source_app_id` = '" . $app_id . "';";
                    if ($rotate_result = mysql_query($sql)) {
                        $rotate_row = mysql_fetch_assoc($rotate_result);
                        if (!empty($rotate_row)) {
                            $pool_last_agent = intval($rotate_row['last_agent_id']);
                        }
                    }

                    // Set pool last agent
                    $auto_rotate_pools[$pool_id] = $pool_last_agent;
                }
            }

            // Last agent for global pool
            if ($global_result = mysql_query("SELECT `last_agent_id` FROM `agents_auto_rotate` WHERE `source_app_id` IS NULL;")) {
                $global_row = mysql_fetch_assoc($global_result);
                if (!empty($global_row)) {
                    $auto_rotate_pools['global'] = intval($global_row['last_agent_id']);
                }
            }

            // Select Auto-Rotate Agents
            $query = "SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate` = 'true' AND `auto_assign_agent` = 'true';";
            if ($agents = mysql_query($query)) {
                // For Auto-Rotation, Require More Than 1 Agent
                if (($count = mysql_num_rows($agents)) < 2 && $settings['auto_rotate_unassign'] != 'true') {
                    if (MAINT_DEBUG) {
                        echo "\t" . 'Require at least 2 or more Agents in Rotation to Continue' . PHP_EOL;
                    }
                } else {
                    // Un-Assign Rotated Leads
                    if ($settings['auto_rotate_unassign'] == 'true') {
                        if (MAINT_DEBUG) {
                            echo "\t" . 'Un-Assign Rotated Leads' . PHP_EOL . PHP_EOL;
                        }

                        // Fetch Super Admin
                        $result = mysql_query("SELECT * FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
                        $admin = mysql_fetch_assoc($result);
                    }

                    if (MAINT_DEBUG) {
                        echo "\t" . 'Running... (' . $count . ' Agents in Rotation, ' . ucwords($frequency) . ' Frequency)' . PHP_EOL;
                    }

                    // Re-Assigned Leads
                    $reassigned = array();

                    // Auto Un-Assigned Leads
                    $unassigned = array();

                    // Process Agent
                    while ($agent = mysql_fetch_assoc($agents)) {
                        if (MAINT_DEBUG) {
                            echo PHP_EOL . "\t" . 'Agent ID #' . $agent['id'] . ': ' . $agent['first_name'] . ' ' . $agent['last_name'] . PHP_EOL . PHP_EOL;
                        }

                        // Select Leads to Rotate
                        $query = "SELECT *, UNIX_TIMESTAMP(`timestamp_assigned`) AS `timestamp_assigned` FROM `" . LM_TABLE_LEADS . "` WHERE `timestamp_assigned` <= FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - " . ($settings['auto_rotate_frequency'] * 60) . ") AND `auto_rotate` = 'true' AND (`status` = 'pending' OR `status` = 'unassigned') AND `agent` = '" . $agent['id'] . "';";
                        if ($leads = mysql_query($query)) {
                            // Process Leads
                            while ($lead = mysql_fetch_assoc($leads)) {
                                // Lead name
                                $leadname = Format::trim($lead['first_name'] . ' ' . $lead['last_name']);

                                if (MAINT_DEBUG) {
                                    echo "\t\t" . 'Lead ID #' . $lead['id'] . ': ' . (!empty($leadname)? $lead['first_name'] . ' ' . $lead['last_name'] : $lead['email']) . PHP_EOL;
                                }

                                // Un-Assign Rotate Leads
                                if ($settings['auto_rotate_unassign'] == 'true' && (empty($lead['auto_rotate_team']) || empty(Settings::getInstance()->MODULES['REW_TEAMS']))) {
                                    // Already Un-Assigned
                                    if ($agent['id'] == $admin['id']) {
                                        if (MAINT_DEBUG) {
                                            echo "\t\t" . 'Already Un-Assigned' . PHP_EOL . PHP_EOL;
                                        }
                                        continue;
                                    } else {
                                        if (MAINT_DEBUG) {
                                            echo "\t\t" . 'Un-Assigned from Agent' . PHP_EOL . PHP_EOL;
                                        }
                                    }

                                    try {
                                        // Backend_Lead
                                        $backend_lead = new Backend_Lead($lead);

                                        // Reset Status to Unassigned and Assign Lead to Admin
                                        $backend_lead->status('unassigned');
                                        $backend_lead->assign(new Backend_Agent($admin));

                                        // Notify Admin that Leads were Un-Assigned from Agent
                                        $unassigned[$agent['id']][] = $lead;

                                        // Notify Agent that Leads were Un-Assigned
                                        $reassigned[$agent['id']]['unassigned'][] = $lead;

                                    // Database Error
                                    } catch (PDOException $e) {
                                        echo "\t\t" . "DB Error: " . $e->getMessage() . PHP_EOL;
                                        Log::error($e);
                                    }

                                // Rotate Amongst Team
                                } else if (!empty($lead['auto_rotate_team']) && !empty(Settings::getInstance()->MODULES['REW_TEAMS'])) {
                                    // Team subdomain
                                    $team = Backend_Team::load($lead['auto_rotate_team']);
                                    if (isset($team) && $team instanceof Backend_Team) {
                                        // Get Team Agents
                                        $agentId = $lead['agent'];
                                        $teamAgentsCollection = $team->getAgentCollection()->filterByGrantedPermissions([Backend_Team::PERM_ASSIGN]);
                                        $teamAgents = $teamAgentsCollection->getAllAgents();
                                        $teamAgents = array_filter($teamAgents, function ($teamAgent) use ($agentId) {
                                            return $teamAgent !== $agentId;
                                        });

                                        if (!empty($teamAgents)) {
                                            $teamAgent = $db->fetch(
                                                "SELECT `id`, `agent_id` FROM `team_agents`"
                                                . " WHERE `team_id` = ". $db->quote($team->getId())
                                                . " AND `agent_id` IN (".implode(',', array_map([$db, 'quote'], $teamAgents)).")"
                                                . " ORDER BY `auto_assign_time` ASC LIMIT 1;"
                                            );
                                            if (!empty($teamAgent)) {
                                                $agent_id = $teamAgent['agent_id'];
                                                $agent_query = $db->prepare(
                                                    "SELECT * FROM `" . LM_TABLE_AGENTS . "`"
                                                    . " WHERE `auto_assign_agent` = 'true'"
                                                    . " AND `id` = :id"
                                                    . " ORDER BY `id` ASC LIMIT 1;"
                                                );
                                                $agent_query->execute(['id' => $agent_id]);
                                                $rotate_agent = $agent_query->fetch();

                                                // Require Agent to Rotate To
                                                if (empty($rotate_agent)) {
                                                    echo "\t\t" . 'Error: Could Not Load Next Agent in Rotation' . PHP_EOL . PHP_EOL;
                                                    continue;
                                                }

                                                // Assign to Lead
                                                try {
                                                    // Create Lead Object
                                                    $backend_lead = new Backend_Lead($lead);

                                                    // Assign Lead to Agent
                                                    $backend_lead->assign(new Backend_Agent($rotate_agent));

                                                // Database Error
                                                } catch (PDOException $e) {
                                                    echo "\t\t" . "DB Error: " . $e->getMessage() . PHP_EOL;
                                                    Log::error($e);
                                                    continue;
                                                }

                                                $result = $db->prepare("UPDATE `team_agents` SET `auto_assign_time` = NOW() WHERE `id` = :id;");
                                                $result->execute(['id' => $teamAgent['id']]);

                                                // Output
                                                if (MAINT_DEBUG) {
                                                    echo PHP_EOL . "\t\t\t" . 'Assigned On: ' . date('M jS, g:iA', $lead['timestamp_assigned']);
                                                }
                                                if (MAINT_DEBUG) {
                                                    echo PHP_EOL . "\t\t\t" . 'Re-Assigned To: ' . $rotate_agent['first_name'] . ' ' . $rotate_agent['last_name'] . PHP_EOL;
                                                }
                                                if (MAINT_DEBUG) {
                                                    echo PHP_EOL;
                                                }

                                                // Re-Assigned Leads
                                                $reassigned[$rotate_agent['id']]['assigned'][] = $lead;

                                                // Un-Assigned Leads
                                                $reassigned[$agent['id']]['unassigned'][] = $lead;
                                            } else {
                                                if (MAINT_DEBUG) {
                                                    echo "\t" . 'Require at least 2 or more Agents in Team to Continue' . PHP_EOL;
                                                }
                                                continue;
                                            }
                                        }
                                    } else {
                                        if (MAINT_DEBUG) {
                                            echo "\t\t" . 'Could not  reassign' . PHP_EOL . PHP_EOL;
                                        }
                                        continue;
                                    }
                                } else {
                                    // Auto-Rotate Pool
                                    $rotate_pool = 'global';
                                    if (!empty($lead['source_app_id']) && !empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
                                        $rotate_pool = intval($lead['source_app_id']);
                                    }

                                    // Ensure there are at least 2 agents in the pool
                                    $pool_agents_count = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS `total` FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate_app_id` " . ($rotate_pool == 'global' ? "IS NULL" : "= '" . $rotate_pool . "'") . ";"));
                                    if ($pool_agents_count['total'] == 1) {
                                        // Get the only agent
                                        $sql = "SELECT `id` FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate_app_id` " . ($rotate_pool == 'global' ? "IS NULL" : "= '" . $rotate_pool . "'") . ";";
                                        $lone_agent = mysql_fetch_assoc(mysql_query($sql));
                                        if ($lone_agent['id'] == $lead['agent']) {
                                            if (MAINT_DEBUG) {
                                                echo "\t" . 'Require at least 2 or more Agents in Rotation pool \'' . $rotate_pool . '\' to Continue' . PHP_EOL;
                                            }
                                            continue;
                                        }
                                    } else if ($pool_agents_count['total'] == 0) {
                                        if ($rotate_pool == 'global') {
                                            if (MAINT_DEBUG) {
                                                echo "\t" . 'Require at least 2 or more Agents in Rotation to Continue' . PHP_EOL;
                                            }
                                            continue;
                                        } else {
                                            $global_pool_agents_count = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS `total` FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate_app_id` IS NULL AND `id` != '" . $lead['agent'] . "';"));
                                            if ($global_pool_agents_count['total'] < 1) {
                                                if (MAINT_DEBUG) {
                                                    echo "\t" . 'Require at least 1 Agent in global pool to fall back to from pool \'' . $rotate_pool . '\' to Continue' . PHP_EOL;
                                                }
                                                continue;
                                            }

                                            // Fall back to global pool
                                            $rotate_pool = 'global';
                                        }
                                    }

                                    /**
                                     * Select Next Agent in Rotation
                                     */

                                    // Lead came from API source - select next agent for matching source/pool
                                    if ($rotate_pool !== 'global') {
                                        $query = "SELECT * FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate` = 'true'  AND `auto_assign_agent` = 'true' AND `auto_rotate_app_id` = '" . $rotate_pool . "' AND `id` > " . $auto_rotate_pools[$rotate_pool] . " AND `id` != '" . $agent['id'] . "' ORDER BY `id` ASC LIMIT 1;";

                                        // Roll over to first agent
                                        $result = mysql_query($query);
                                        if (mysql_num_rows($result) == 0) {
                                            $auto_rotate_pools[$rotate_pool] = 0;
                                            $query = "SELECT * FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate` = 'true'  AND `auto_assign_agent` = 'true' AND `auto_rotate_app_id` = '" . $rotate_pool . "' AND `id` > 0 AND `id` != '" . $agent['id'] . "' ORDER BY `id` ASC LIMIT 1;";
                                        }
                                    } else { // Local lead
                                        $query = "SELECT * FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate` = 'true'  AND `auto_assign_agent` = 'true' AND `auto_rotate_app_id` IS NULL AND `id` > " . $auto_rotate_pools[$rotate_pool] . " AND `id` != '" . $agent['id'] . "' ORDER BY `id` ASC LIMIT 1;";

                                        // Roll over to first agent
                                        $result = mysql_query($query);
                                        if (mysql_num_rows($result) == 0) {
                                            $auto_rotate_pools[$rotate_pool] = 0;
                                            $query = "SELECT * FROM `" . LM_TABLE_AGENTS . "` WHERE `auto_rotate` = 'true'  AND `auto_assign_agent` = 'true' AND `auto_rotate_app_id` IS NULL AND `id` > 0 AND `id` != '" . $agent['id'] . "' ORDER BY `id` ASC LIMIT 1;";
                                        }
                                    }

                                    // Execute query
                                    if ($result = mysql_query($query)) {
                                        // Next Agent to Rotate To
                                        $rotate_agent = mysql_fetch_assoc($result);

                                        // Require Agent to Rotate To
                                        if (empty($rotate_agent)) {
                                            echo "\t\t" . 'Error: Could Not Load Next Agent in Rotation' . PHP_EOL . PHP_EOL;
                                            continue;
                                        }

                                        try {
                                            // Create Lead Object
                                            $backend_lead = new Backend_Lead($lead);

                                            // Assign Lead to Agent
                                            $backend_lead->assign(new Backend_Agent($rotate_agent));

                                        // Database Error
                                        } catch (PDOException $e) {
                                            echo "\t\t" . "DB Error: " . $e->getMessage() . PHP_EOL;
                                            Log::error($e);
                                        }

                                        // Update Auto-Rotate Agent
                                        $query = "DELETE FROM `agents_auto_rotate` WHERE `source_app_id` " . ($rotate_pool == 'global' ? "IS NULL" : "= '" . $rotate_pool . "'") . ";";
                                        mysql_query($query) or die('MySQL Error: ' . mysql_error() . PHP_EOL . 'MySQL Query: ' . $query . PHP_EOL);
                                        $query = "INSERT INTO `agents_auto_rotate` SET `source_app_id` = " . ($rotate_pool == 'global' ? "NULL" : "'" . $rotate_pool . "'") . ", `last_agent_id` = '" . $rotate_agent['id'] . "';";
                                        mysql_query($query) or die('MySQL Error: ' . mysql_error() . PHP_EOL . 'MySQL Query: ' . $query . PHP_EOL);

                                        // Update Last Rotated Agent in memory
                                        $auto_rotate_pools[$rotate_pool] = intval($rotate_agent['id']);

                                        // Output
                                        if (MAINT_DEBUG) {
                                            echo PHP_EOL . "\t\t\t" . 'Assigned On: ' . date('M jS, g:iA', $lead['timestamp_assigned']);
                                        }
                                        if (MAINT_DEBUG) {
                                            echo PHP_EOL . "\t\t\t" . 'Re-Assigned To: ' . $rotate_agent['first_name'] . ' ' . $rotate_agent['last_name'] . PHP_EOL;
                                        }
                                        if (MAINT_DEBUG) {
                                            echo PHP_EOL;
                                        }

                                        // Re-Assigned Leads
                                        $reassigned[$rotate_agent['id']]['assigned'][] = $lead;

                                        // Un-Assigned Leads
                                        $reassigned[$agent['id']]['unassigned'][] = $lead;

                                    // Query Error
                                    } else {
                                        echo "\t\t" . 'MySQL Error: ' . mysql_error() . PHP_EOL;
                                        echo "\t\t" . 'MySQL Query: ' . $query . PHP_EOL;
                                        exit;
                                    }
                                }
                            }

                        // Query Error
                        } else {
                            echo "\t" . 'MySQL Error: ' . mysql_error() . PHP_EOL;
                            echo "\t" . 'MySQL Query: ' . $query . PHP_EOL;
                            exit;
                        }
                    }

                    // Auto Un-Assign Notifications
                    if (!empty($unassigned)) {
                        if (MAINT_DEBUG) {
                            echo "\t" . 'Auto Un-Assign Complete:' . PHP_EOL.PHP_EOL;
                        }

                        // Generate Report for Email
                        $count = 0;
                        $report = '';
                        foreach ($unassigned as $agent => $leads) {
                            $agent = Backend_Agent::load($agent);
                            $report .= '<p>The following ' . Format::plural(count($leads), 'leads were', 'lead was') . ' un-assigned from <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=' . $agent['id'] . '">' . $agent['first_name'] . ' ' . $agent['last_name'] . '</a>:</p>';
                            foreach ($leads as $lead) {
                                // Lead link
                                $leadlink = Format::trim($lead['first_name'] . ' ' . $lead['last_name']);

                                $report .= '<========================><br>' . PHP_EOL;
                                if (!empty($leadlink)) {
                                    $report .= '<strong>Name:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . $leadlink . '</a><br>';
                                }
                                if (!empty($leadlink)) {
                                    $report .= '<strong>Email:</strong> ' . $lead['email'] . '<br>';
                                }
                                if (empty($leadlink)) {
                                    $report .= '<strong>Email:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . $lead['email'] . '</a><br>';
                                }
                                if (!empty($lead['phone'])) {
                                    $report .= '<strong>Phone #:</strong> ' . $lead['phone'] . '<br>';
                                }
                                if (!empty($lead['phone_cell'])) {
                                    $report .= '<strong>Cell #:</strong> ' . $lead['phone_cell'] . '<br>';
                                }
                                if (!empty($lead['comments'])) {
                                    $report .= '<strong>User\'s Comments:</strong> ' . htmlspecialchars($lead['comments']) . '<br>';
                                }
                                if (!empty($lead['notes'])) {
                                    $report .= '<strong>Quick Notes:</strong> ' . htmlspecialchars($lead['notes']) . '<br>';
                                }
                                if (!empty($lead['remarks'])) {
                                    $report .= '<strong>Agent Remarks:</strong> ' . htmlspecialchars($lead['remarks']) . '<br>';
                                }
                                $report .= '<strong>Assigned:</strong> ' . Format::dateRelative($lead['timestamp_assigned']) . '<br>';
                                $count++;
                            }
                            $report .= '<========================>';
                        }

                        // Generate Email Message
                        $message = '<p>Hello ' . $admin['first_name'] . ' ' . $admin['last_name'] . ',</p>';
                        $message .= '<p><strong>' . number_format($count) . ' ' . Format::plural($count, 'leads were', 'lead was') . ' un-assigned because their status has been pending for longer than ' . $frequency . '.</strong></p>';
                        $message .= $report;
                        $message .= '<p><a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/?view=unassigned">Click here</a> to manage all un-assigned leads.</p>';
                        $message .= '<p>Have a nice day!</p>';

                        // Create Mailer
                        $mailer = new Backend_Mailer();

                        // Mailer Subject
                        $mailer->setSubject('Auto-Rotation: ' . number_format($count) . ' Un-Assigned ' . Format::plural($count, 'Leads', 'Lead'));

                        // Mailer Message
                        $mailer->setMessage($message);

                        // Email Recipient
                        $mailer->setRecipient($admin['email'], $admin['first_name'] . ' ' . $admin['last_name']);

                        // Send Email
                        if ($mailer->Send()) {
                            if (MAINT_DEBUG) {
                                echo "\t\t" . 'Email Sent To: ' . $admin['first_name'] . ' ' . $admin['last_name'] . ' (' . $admin['email'] . ')' . PHP_EOL . PHP_EOL;
                            }

                        // Mailer Error
                        } else {
                            echo "\t\t" . 'Error Sending Email To: ' . $admin['first_name'] . ' ' . $admin['last_name'] . ' (' . $admin['email'] . ')' . PHP_EOL . PHP_EOL;
                        }
                    }

                    // Re-Assignment Notifications
                    if (!empty($reassigned)) {
                        if (MAINT_DEBUG) {
                            echo "\t" . 'Auto-Rotation Complete:' . PHP_EOL;
                        }

                        foreach ($reassigned as $agent => $reassign) {
                            // Load Agent
                            $agent = Backend_Agent::load($agent);

                            // Output
                            if (MAINT_DEBUG) {
                                echo PHP_EOL;
                            }
                            if (MAINT_DEBUG) {
                                echo "\t\t" . 'Agent ID #' . $agent['id'] . ': ' . $agent['first_name'] . ' ' . $agent['last_name'] . PHP_EOL;
                            }
                            if (MAINT_DEBUG) {
                                echo "\t\t" . 'Re-Assigned Leads: ' . count($reassign['unassigned']) . PHP_EOL;
                            }
                            if (MAINT_DEBUG) {
                                echo "\t\t" . 'Assigned Leads: ' . count($reassign['assigned']) . PHP_EOL;
                            }
                            if (MAINT_DEBUG) {
                                echo PHP_EOL;
                            }

                            // Un-Assigned Leads
                            if (!empty($reassign['unassigned'])) {
                                // Send Notification Email to Agent
                                $mailer = new Backend_Mailer_AgentUnAssigned(array(
                                    'leads' => $reassign['unassigned']
                                ));

                                // Email Recipient
                                $mailer->setRecipient($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);

                                // Email Subject
                                $mailer->setSubject('Auto-Rotation Notification: ' . count($reassign['unassigned']) . ' Un-Assigned ' . Format::plural(count($reassign['unassigned']), 'Leads', 'Lead'));

                                // Send Email
                                if ($mailer->Send()) {
                                    if (MAINT_DEBUG) {
                                        echo "\t\t" . 'Email Sent To: ' . $agent['first_name'] . ' ' . $agent['last_name'] . ' (' . $agent['email'] . ')' . PHP_EOL;
                                    }

                                // Mailer Error
                                } else {
                                    echo "\t\t" . 'Error Sending Email To: ' . $agent['first_name'] . ' ' . $agent['last_name'] . ' (' . $agent['email'] . ')' . PHP_EOL;
                                }
                            }

                            // Newly Assigned Leads
                            if (!empty($reassign['assigned'])) {
                                // Send Notification Email to Agent
                                $mailer = new Backend_Mailer_AgentAssigned(array(
                                    'leads' => $reassign['assigned']
                                ));

                                // Email Subject
                                $mailer->setSubject('Auto-Rotation Notification: ' . count($reassign['assigned']) . ' New ' . Format::plural(count($reassign['assigned']), 'Leads', 'Lead'));

                                // Check Incoming Notification Settings
                                $check = $agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LEAD_ASSIGNED);

                                // Send Email
                                if ($mailer->Send()) {
                                    if (MAINT_DEBUG) {
                                        echo "\t\t" . 'Email Sent To: ' . $agent['first_name'] . ' ' . $agent['last_name'] . ' (' . $agent['email'] . ')' . PHP_EOL;
                                    }

                                // Mailer Error
                                } else {
                                    echo "\t\t" . 'Error Sending Email To: ' . $agent['first_name'] . ' ' . $agent['last_name'] . ' (' . $agent['email'] . ')' . PHP_EOL;
                                }
                            }
                        }
                    }
                }

            // Query Error
            } else {
                echo 'MySQL Error: ' . mysql_error() . PHP_EOL;
                echo 'MySQL Query: ' . $query . PHP_EOL;
            }
        }

    // Query Error
    } else {
        echo 'MySQL Error: ' . mysql_error() . PHP_EOL;
        echo 'MySQL Query: ' . $query . PHP_EOL;
    }
}

/**
 * Calendar Reminders
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Calendar Reminders: ';
}
if (empty($cron['reminder'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    // Select Calendar Settings
    $query = "SELECT `calendar_notifications` FROM `" . TABLE_SETTINGS . "` WHERE `agent` = 1;";
    if ($settings = mysql_query($query)) {
        $settings = mysql_fetch_assoc($settings);

        // Only send reminders if enabled
        if ($settings['calendar_notifications'] == 'true') {

            /**
             * Collection of Emails to Send
             */
            $emails = array();

            // Build SELECT Query
            $query = "SELECT "
                . "CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `lead_name`, `u`.`email`, `u`.`status`, `u`.`timestamp`, `u`.`agent` AS `assigned`, "
                . "`r`.`id`, `r`.`agent`, `r`.`associate`, `r`.`user_id`, `r`.`timestamp`, `r`.`details`, `r`.`share`, "
                . "`t`.`title` AS `type` "
                . " FROM `" . LM_TABLE_LEADS . "` `u`"
                . " LEFT JOIN `" . LM_TABLE_REMINDERS . "` `r` ON `u`.`id` = `r`.`user_id`"
                . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `r`.`type` = `t`.`id`"
                . " WHERE "
                . "`r`.`user_id` IS NOT NULL AND "
                . "`r`.`timestamp` <= NOW() AND "
                . "`r`.`sent` = 'N' AND "
                . "`r`.`completed` = 'false'"
                . " ORDER BY `r`.`timestamp` ASC;";

            // Execute Query
            if ($reminders = mysql_query($query)) {
                // Output
                if (MAINT_DEBUG) {
                    echo "\t" . 'Reminders: ' . number_format(mysql_num_rows($reminders)) . PHP_EOL;
                }

                // Process Lead Reminders
                while ($reminder = mysql_fetch_assoc($reminders)) {
                    $reminder['lead_name'] = Format::trim($reminder['lead_name']);
                    $reminder['lead_name'] = ($reminder['lead_name'])?:$reminder['email'];

                    // Reminder Owner (Agent)
                    if (!empty($reminder['agent'])) {
                        $owner = mysql_query("SELECT 'Agent' AS `type`, `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name`, `a`.`email`, `a`.`permissions_admin` FROM `" . LM_TABLE_AGENTS . "` `a` WHERE `a`.`id` = '" . $reminder['agent'] . "';");
                        $owner = mysql_fetch_assoc($owner);
                    // Reminder Owner (ISA)
                    } else if (!empty($reminder['associate'])) {
                        $owner = mysql_query("SELECT 'Associate' AS `type`, `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name`, `a`.`email` FROM `associates` `a` WHERE `a`.`id` = '" . $reminder['associate'] . "';");
                        $owner = mysql_fetch_assoc($owner);
                    }

                    // Assigned Agent
                    $agent = mysql_query("SELECT `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name`, `a`.`email` FROM `" . LM_TABLE_AGENTS . "` `a` WHERE `a`.`id` = '" . $reminder['assigned'] . "';");
                    $agent = mysql_fetch_assoc($agent);

                    // Email Recipients
                    $recipients = array();

                    // Shared Reminder, Send to Reminder Owner & Assigned Agent
                    if ($reminder['share'] == 'true') {
                        $recipients[] = $owner;
                        if ($owner['id'] != $agent['id'] && $owner['type'] != $agent['type']) {
                            $recipients[] = $agent;
                        }

                    // Reminder Owner is Assigned Agent, Super Admin, or has Admin Permissions
                    } else if ($owner['type'] == 'Agent' && ($owner['id'] == $agent['id'] || $owner['id'] == 1 || $owner['permissions_admin'] & Auth::PERM_LEADS_ALL)) {
                        $recipients[] = $owner;

                    // Reminder Owner is ISA
                    } else if ($owner['type'] == 'Associate') {
                        $recipients[] = $owner;
                    }

                    // No Recipients, Skip this Reminder
                    if (empty($recipients)) {
                        if (MAINT_DEBUG) {
                            echo "\t" . 'No Recipients for this Reminder' . PHP_EOL;
                        }
                        continue;
                    }

                    // Build Email for Each Recipient
                    foreach ($recipients as $recipient) {
                        // Get Email
                        $id = $recipient['type'] . '-' . $recipient['id'];
                        $email = $emails[$id] ? $emails[$id] : array();

                        // Email Recipient
                        $email['name']  = $recipient['agent_name'];
                        $email['email'] = $recipient['email'];

                        // Email Message (HTML)
                        $email['Body']    .= '<p>This is a lead reminder notification for <a href="' . URL_BACKEND . 'leads/lead/reminders/?id=' . $reminder['user_id'] . '" target="_blank">' . $reminder['lead_name'] . '</a> (<b>' . $reminder['email'] . '</b>).</p>';
                        $email['Body']    .= '<p>';
                        $email['Body']    .= '<b>Reminder Date & Time:</b> ' . date('D, M. jS - g:i A', strtotime($reminder['timestamp'])) . '<br />';
                        if (!empty($reminder['details'])) {
                            $email['Body']    .= '<b>Reminder Details:</b> ' . nl2br($reminder['details']) . '<br />';
                        }
                        if (!empty($reminder['type'])) {
                            $email['Body']    .= '<b>Reminder Type:</b> ' . $reminder['type'] . '<br />';
                        }
                        $email['Body']    .= '</p>';

                        // Add Email to Collection
                        $emails[$id] = $email;
                    }

                    // Mark Reminder as Sent
                    mysql_query("UPDATE `" . LM_TABLE_REMINDERS . "` SET `sent` = 'Y' WHERE `id` = '" . $reminder['id'] . "';");
                }

            // MySQL Query Error
            } else {
                echo 'MySQL Error: ' . mysql_error() . PHP_EOL;
                echo 'MySQL Query: ' . $query . PHP_EOL;
            }

            /**
             * Select Calendar Reminders
             */
            $query = "SELECT
		                CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name`,
		                `a`.`email`,
		                `e`.`agent`,
		                `e`.`type`,
		                `e`.`title`,
		                `e`.`body`,
		                `r`.`id`,
		                `r`.`reminder_type`,
		                `r`.`reminder_time`,
		                `r`.`reminder_interval`,
		                `d`.`start`,
		                `d`.`end`,
		                IF(`d`.`all_day` = 'true', 1, 0) AS `all_day`,
		                CASE
		                    r.reminder_interval
		                    WHEN 'minutes' THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` MINUTE)
		                    WHEN 'hours'   THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` HOUR)
		                    WHEN 'days'    THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` DAY)
		                    WHEN 'weeks'   THEN DATE_SUB(`d`.`start`, INTERVAL (`r`.`reminder_time` * 7) DAY)
		                    WHEN 'months'  THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` MONTH)
		                END AS `timestamp`,
		                DATEDIFF(CURDATE(), `d`.`start`) AS `days`"
                 . " FROM `" . TABLE_CALENDAR_EVENTS . "` `e`"
                 . " LEFT JOIN `" . TABLE_CALENDAR_REMINDERS . "` `r` ON `e`.`id` = `r`.`event`"
                 . " LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `d` ON `e`.`id` = `d`.`event`"
                 . " LEFT JOIN `" . LM_TABLE_AGENTS . "` `a` ON `e`.`agent` = `a`.`id`"
                 . " WHERE `r`.`id` IS NOT NULL "
                 . " AND `r`.`sent` = 'N'"
                 . " AND `r`.`reminder_type` = 'Email'"
                 . " HAVING `timestamp` <= NOW()"
                 . " ORDER BY `d`.`start` ASC";

            $event_reminders = mysql_query($query) or die(mysql_error());

            // Check Count
            $count = @mysql_num_rows($event_reminders);
            if ($count > 0) {
                // Output
                if (MAINT_DEBUG) {
                    echo "\t" . 'Reminders: ' . number_format($count) . PHP_EOL;
                }

                // Loop through Event Reminders
                while ($event_reminder = mysql_fetch_assoc($event_reminders)) {
                    // Date Format
                    $format = !empty($event_reminder['all_day']) ? 'D, M. jS' : 'D, M. jS @ g:i A';

                    // Get Email
                    $id = 'Agent-' . $event_reminder['agent'];
                    $email = $emails[$id] ? $emails[$id] : array();

                    // Email Recipient
                    $email['name']  = $event_reminder['agent_name'];
                    $email['email'] = $event_reminder['email'];

                    // Email Message (HTML)
                    $email['Body']    .= '<p>This is a reminder notification for your event titled <b>' . $event_reminder['title'] . '</b>.</p>';
                    $email['Body']    .= '<p><b>Event Occurs:</b> ' . date($format, strtotime($event_reminder['start'])) . ' - ' . date($format, strtotime($event_reminder['end'])) . '</p>';
                    if (!empty($event_reminder['body'])) {
                        $email['Body']    .= '<p><b>Event Details:</b> ' . nl2br($event_reminder['body']) . '</p>';
                    }

                    // Add Email to Collection
                    $emails[$id] = $email;

                    // Update Reminder
                    mysql_query("UPDATE `" . TABLE_CALENDAR_REMINDERS . "` SET `sent` = 'Y' WHERE `id` = '" . $event_reminder['id'] . "'");
                }
            }

            // Loop Through Emails
            foreach ($emails as $recipient => $email) {
                // Recipient ID
                list ($type, $id) = explode('-', $recipient);

                // Output
                if (MAINT_DEBUG) {
                    echo 'Sending Reminder Email to ' . $email['name'] . ' (' . $email['email'] . '): ';
                }

                // Create Mailer
                $mailer = new Backend_Mailer(array(
                    'subject' => 'Reminder Notification',
                    'message' => $email['Body']
                ));

                // Email Recipient
                $mailer->setRecipient($email['email'], $email['name']);

                // Send Email
                if ($mailer->Send()) {
                    // Success
                    if (MAINT_DEBUG) {
                        echo 'Success' . PHP_EOL;
                    }

                    // Log Event: Email sent to Agent
                    $event = new History_Event_Email_Reminder(array(
                        'subject' => $mailer->getSubject(),
                        'message' => $mailer->getMessage()
                    ), array(
                        ($type == 'Agent' ? new History_User_Agent($id) : null),
                        ($type == 'Associate' ? new History_User_Associate($id) : null)
                    ));

                    //
                    $event->save();

                // Mailer Error
                } else {
                    echo 'Error: Sending Email' . PHP_EOL;
                }
            }
        } else {
            if (MAINT_DEBUG) {
                echo 'Calendar notifications have been disabled in the backend settings.' . PHP_EOL;
            }
        }
    }
}

/**
 * Update Lead Scores
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Updating Lead Scores: ';
}
if (empty($cron['score'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    /* Select Leads */
    $query = "SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `timestamp_score` <= `timestamp_active` OR `timestamp_score` < SUBTIME( NOW(), '1 0:0:0') ORDER BY `timestamp_active` DESC;";
    if ($leads = mysql_query($query)) {
        if (MAINT_DEBUG) {
            echo "\t" . 'Leads: ' . number_format(mysql_num_rows($leads)) . PHP_EOL;
        }

        /* Process Leads */
        while ($lead = mysql_fetch_assoc($leads)) {
            // Use Lead Object
            $lead = new Backend_Lead($lead);

            // Update Score
            $lead->updateScore();

            // Output
            if (MAINT_DEBUG) {
                echo "\t" . 'Lead ID #' . $lead['id'] . ': ' . $lead['first_name'] . ' ' . $lead['last_name'] . PHP_EOL;
            }
            if (MAINT_DEBUG) {
                echo "\t" . 'Lead Score: ' . $lead['score'] . PHP_EOL;
            }
        }

        // Query Error
    } else {
        echo "\t" . 'MySQL Error: ' . mysql_error() . PHP_EOL;
        echo "\t" . 'MySQL Query: ' . $query . PHP_EOL;
    }
}

/**
 * Update Lead Search Preferences
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Updating Lead Search Preferences: ';
}
if (empty($cron['search'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    /* Select Viewed Listings */
    $query = "SELECT `u`.`id`, `u`.`first_name`, `u`.`last_name` FROM `" . LM_TABLE_LEADS . "` `u` LEFT JOIN `" . LM_TABLE_VIEWED_LISTINGS . "` `vl` ON `u`.`id` = `vl`.`user_id` WHERE `u`.`num_listings` > 0 AND `u`.`search_auto` = 'true' AND `u`.`timestamp_active` >= DATE_SUB(NOW(), INTERVAL 1 HOUR) AND `vl`.`timestamp` >= DATE_SUB(NOW(), INTERVAL 1 HOUR) GROUP BY `u`.`id`;";
    if ($leads = mysql_query($query)) {
        if (MAINT_DEBUG) {
            echo "\t" . 'Leads: ' . number_format(mysql_num_rows($leads)) . PHP_EOL;
        }

        while ($lead = mysql_fetch_assoc($leads)) {
            /* Select Stats from Viewed Listings */
            $query = "SELECT "
            . "GROUP_CONCAT(DISTINCT `type` ORDER BY `views` DESC SEPARATOR ', ') AS `search_type`, "
            . "GROUP_CONCAT(DISTINCT `city` ORDER BY `views` DESC SEPARATOR ', ') AS `search_city`, "
            . "GROUP_CONCAT(DISTINCT `subdivision` ORDER BY `views` DESC SEPARATOR ', ') AS `search_subdivision`, "
            . "GROUP_CONCAT(`price`) AS `prices`, "
            . "MIN(`price`) AS `search_minimum_price`, "
            . "MAX(`price`) AS `search_maximum_price`"
            . " FROM `" . LM_TABLE_VIEWED_LISTINGS . "` WHERE `user_id` = '" . $lead['id'] . "';";

            if ($result = mysql_query($query)) {
                /* Fetch Data */
                $data = mysql_fetch_assoc($result);

                /* Find Median Price (Lead Value) */
                $prices = explode(',', $data['prices']);
                rsort($prices);
                $middle = round(count($prices) / 2);
                $median = $prices[$middle - 1];

                /* Update Collection */
                $tmp = array();
                foreach (explode(', ', $data['search_city']) as $string) {
                    $string = ucwords(strtolower(trim($string)));
                    if (!empty($string)) {
                        $tmp[] = $string;
                    }
                }
                $data['search_city'] = implode(', ', $tmp);

                /* Update Collection */
                $tmp = array();
                foreach (explode(', ', $data['search_subdivision']) as $string) {
                    $string = ucwords(strtolower(trim($string)));
                    if (!empty($string)) {
                        $tmp[] = $string;
                    }
                }
                $data['search_subdivision'] = implode(', ', $tmp);

                /* Add to Lead */
                $lead = array_merge($lead, $data);

                /* Update Lead Row */
                $query = "UPDATE `" . LM_TABLE_LEADS . "` SET "
                . "`value`                = '" . mysql_real_escape_string($median) . "', "
                . "`search_type`          = '" . mysql_real_escape_string($data['search_type']) . "', "
                . "`search_city`          = '" . mysql_real_escape_string($data['search_city']) . "', "
                . "`search_subdivision`   = '" . mysql_real_escape_string($data['search_subdivision']) . "', "
                . "`search_minimum_price` = '" . mysql_real_escape_string($data['search_minimum_price']) . "', "
                . "`search_maximum_price` = '" . mysql_real_escape_string($data['search_maximum_price']) . "'"
                . " WHERE `id` = '" . $lead['id'] . "';";

                /* Execute Query */
                if (mysql_query($query)) {
                    /* Output */
                    if (MAINT_DEBUG) {
                        echo "\t" . 'Lead ID #' . $lead['id'] . ': ' . $lead['first_name'] . ' ' . $lead['last_name'] . PHP_EOL;
                    }

                    // Query Error
                } else {
                    echo "\t" . 'MySQL Error: ' . mysql_error() . PHP_EOL;
                    echo "\t" . 'MySQL Query: ' . $query . PHP_EOL;
                    continue;
                }

                // Query Error
            } else {
                echo "\t" . 'MySQL Error: ' . mysql_error() . PHP_EOL;
                echo "\t" . 'MySQL Query: ' . $query . PHP_EOL;
                continue;
            }
        }

        // Query Error
    } else {
        echo 'MySQL Error: ' . mysql_error() . PHP_EOL;
        echo 'MySQL Query: ' . $query . PHP_EOL;
    }
}

/**
 * Delayed Emails
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Delayed Emails: ';
}
if (empty($cron['delayed'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    // Select Delayed Mailers
    $delayed_mailers = mysql_query("SELECT * "
                                 . " FROM `" . LM_TABLE_DELAYED_EMAILS . "` `e`"
                                 . " WHERE "
                                 . " `e`.`timestamp` <= NOW()"
                                 . " AND `e`.`sent` = 'N'"
                                 . " ORDER BY `e`.`timestamp` ASC"
                                 . " LIMIT 250") or die(mysql_error());
    // Check Count
    $count = @mysql_num_rows($delayed_mailers);
    if (MAINT_DEBUG) {
        echo "\t" . 'Emails: ' . number_format($count) . PHP_EOL;
    }
    if ($count > 0) {
        // Loop through Results
        while ($delayed = mysql_fetch_assoc($delayed_mailers)) {
            // Unserialize \PHPMailer\RewMailer
            $mailer = unserialize($delayed['mailer']);

            // Verify \PHPMailer\RewMailer Instance
            // @IMPORTANT: We are checking \PHPMailer as the instance here
            // @cont: This is because of legacy support. So in the future it will still work.
            // \PHPMailer\RewMailer is an instance of \PHPMailer.
            if ($mailer instanceof \PHPMailer || $mailer instanceof PHPMailer\RewMailer) {
                if (MAINT_DEBUG) {
                    echo 'Sending Email "' . $mailer->Subject . '" to ' . $mailer->to[0][1] . ' (' . $mailer->to[0][0] . '): ' . PHP_EOL;
                }
            } else {
                echo 'ERROR: Delayed Email #' . $delayed['id'] . ' is not PHPMailer instance.' . PHP_EOL;
                continue;
            }

            // Send Email
            if ($mailer->Send()) {
                // Success
                if (MAINT_DEBUG) {
                    echo 'Success.';
                }

                // Update Mailer
                mysql_query("UPDATE `" . LM_TABLE_DELAYED_EMAILS . "` SET `sent` = 'Y' WHERE `id` = '" . $delayed['id'] . "'");

                // Update Leads (Loop trough Recipients)
                foreach ($mailer->to as $to) {
                    // Recipient Details
                    list ($email, $name) = $to;

                    // Find Lead by Email
                    $result = mysql_query("SELECT `id` FROM `" . LM_TABLE_LEADS . "` WHERE `email` = '" . mysql_real_escape_string($email) . "'");
                    $lead   = mysql_fetch_assoc($result);

                    // Require Row
                    if (!empty($lead)) {
                        // Log Event: Delayed Email sent to Lead
                        $event = new History_Event_Email_Sent(array(
                            'delayed'   => true,
                            'plaintext' => ($mailer->ContentType == 'text/plain'),
                            'subject'   => $mailer->Subject,
                            'message'   => (!empty($delayed['message']) ? $delayed['message'] : $mailer->Body),
                            'tags'      => (!empty($delayed['tags']) ? json_decode($delayed['tags'], true) : null)
                        ), array(
                            new History_User_Lead($lead['id']),
                            (!empty($delayed['agent'])      ? new History_User_Agent($delayed['agent'])         : null),
                            (!empty($delayed['lender'])     ? new History_User_Lender($delayed['lender'])       : null),
                            (!empty($delayed['associate'])  ? new History_User_Associate($delayed['associate']) : null)
                        ));

                        // Save to DB
                        $event->save();
                    }
                }
            } else {
                // Error
                echo 'ERROR (' . $mailer->ErrorInfo . ')';
            }
        }
    }
}

/**
 * REW Action Plan Tasks
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Managing Tasks: ';
}
if (empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
    if (MAINT_DEBUG) {
        echo 'MODULE DISABLED' . PHP_EOL;
    }
} else if (empty($cron['tasks'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    $db = DB::get();

    /**
     * Autocomplete Tasks
     */
    if (MAINT_DEBUG) {
        echo PHP_EOL . 'Autocompleting Tasks...' . PHP_EOL;
    }
    $count = 0;
    $result = $db->query("SELECT `t`.`id`, `ut`.`user_id` "
        . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
        . " JOIN `" . TABLE_TASKS . "` `t` ON `ut`.`task_id` = `t`.`id` "
        . " WHERE `t`.`automated` = 'Y' "
        . " AND `ut`.`status` = 'Pending' "
        . " AND `ut`.`timestamp_due` < NOW()"
        . " AND `ut`.`timestamp_expire` >= NOW()"
        . ";");
    while ($row = $result->fetch()) {
        $task = Backend_Task::load($row['id']);
        $task->processAndResolve($row['user_id'], true, true);
        $count++;
    }
    if (MAINT_DEBUG) {
        echo $count . ' tasks autocompleted' . PHP_EOL;
    }

    /**
     * Expire Tasks
     */
    if (MAINT_DEBUG) {
        echo PHP_EOL . 'Expiring Tasks...' . PHP_EOL;
    }
    $count = 0;
    $result = $db->query("SELECT `t`.`id`, `ut`.`user_id` "
        . " FROM `" . TABLE_USERS_TASKS . "` `ut` "
        . " JOIN `" . TABLE_TASKS . "` `t` ON `ut`.`task_id` = `t`.`id` "
        . " WHERE `ut`.`status` = 'Pending' "
        . " AND `ut`.`timestamp_expire` < NOW()"
        . ";");
    while ($row = $result->fetch()) {
        $task = Backend_Task::load($row['id']);

        // Determine who was responsible for this task for stats/records
        $performer = $task->info('performer');
        $agent_id = false;

        if ($performer == 'Agent') {
            // Get assigned agent
            if ($agent = $db->fetch("SELECT `agent` AS `id` FROM `users` WHERE `id` = '" . $row['user_id'] . "';")) {
                $agent_id = $agent['id'];
            }
        } else if ($performer == 'Lender') {
            // Get assigned lender
            if ($agent = $db->fetch("SELECT `lender` AS `id` FROM `users` WHERE `id` = '" . $row['user_id'] . "';")) {
                $agent_id = $agent['id'];
            }
        }

        $performer = array('id' => $agent_id, 'type' => $performer);

        $task->resolve($row['user_id'], $performer, 'Expired');
        $count++;
    }
}

/**
 * REW DotLoop Integration - Sync local DB Data With DotLoop Data
 */
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Pulling Latest DotLoop Data Into Local DB Storage';
}
if (empty(Settings::getInstance()->MODULES['REW_PARTNERS_DOTLOOP'])) {
    if (MAINT_DEBUG) {
        echo 'MODULE DISABLED' . PHP_EOL;
    }
} else if (empty($cron['dotloop_sync'])) {
    if (MAINT_DEBUG) {
        echo 'OFF' . PHP_EOL;
    }
} else {
    if (MAINT_DEBUG) {
        echo PHP_EOL . PHP_EOL;
    }

    // DotLoop Sync Constants
    define(DOTLOOP_LOCK_FILE, __DIR__ . '/dotloop.lock');
    define(DOTLOOP_API_CALL_REATTEMPT_FAILURE, 'api_call_reattempt_failure');
    define(DOTLOOP_RATE_LIMIT_MINIMUM, 20); // Leave 20 API Calls for CRM Users
    define(DOTLOOP_ACCOUNT_SYNC_LIMIT, 3600); // Minimum time to wait after an account has been successfully synced before we allow another sync attempt

    // Use a Lockfile to Avoid Running Overlapping DotLoop Syncs
    if (MAINT_DEBUG) {
        echo 'Creating DotLoop sync lock file.' . PHP_EOL;
    }
    if ($dotloop_lock = fopen(DOTLOOP_LOCK_FILE, 'w')) {
        if (flock($dotloop_lock, LOCK_EX | LOCK_NB)) {

            /**
             * Check Rate Limit && Access Token Status - Handle Accordingly
             *
             * - Sleep Until Reset if Exceeded
             * - Fail if We Don't Get Rate Limit Info
             * - Proceed if We're Within the Limit
             */
            function checkAndHandleFailedAPICall($dotloop, $dotloop_account_id, $rate_limit_info = [], $api_method, &$rerun_result_var, ...$api_method_args)
            {
                // Determine whether the Access Token has expired - If it has we don't want to proceed with syncing for this account
                $api_response = !empty($dotloop->getLastAPIResponse()) ? json_decode($dotloop->getLastAPIResponse(), true) : [];
                if (!empty($api_response)) {
                    if (!empty($api_response['error'])
                        && $api_response['error'] == 'invalid_token'
                    ) {
                        if (MAINT_DEBUG) {
                            echo sprintf('SYNC FAILED: Invalid DotLoop API access token. Account ID: %s', $dotloop_account_id) . PHP_EOL;
                        }
                        return DOTLOOP_API_CALL_REATTEMPT_FAILURE;
                    }
                }
                // Determine Success || Re-try Needed || Failure
                $return_val = 1;
                if (!empty($rate_limit_info)) {
                    if (intval($rate_limit_info['remaining']) <= DOTLOOP_RATE_LIMIT_MINIMUM) {
                        if (!empty($rate_limit_info['reset_countdown'])) {
                            // Sleep Until Rate Limit Resets
                            $sleep = (intval($rate_limit_info['reset_countdown'])/1000) + 5;
                            echo sprintf('RATE LIMIT EXCEEDED: Sleeping for %s seconds', $sleep);
                            sleep($sleep);
                            $return_val = 2;
                        } else {
                            $return_val = 0;
                        }
                    }
                } else {
                    $return_val = 0;
                }
                // Try Re-Running the last API Call After Sleep || Handle Failure
                switch ($return_val) {
                    // Rate Limit Was Expired - We Slept, We Try Again
                    case 2:
                        $rerun_result_var = call_user_func_array([$dotloop, $api_method], $api_method_args);
                        break;
                    // Rate Limit Data Unavailable - Likely Caused by API Issue - Should Abandon Account Sync
                    case 0:
                        echo sprintf('SYNC FAILED: Failed to receive or process rate limit response data. Account ID: %s', $dotloop_account_id) . PHP_EOL;
                        return DOTLOOP_API_CALL_REATTEMPT_FAILURE;
                        break;
                }
                return true;
            }

            /**
             * Update Local Loop and Loop Participant Records
             */
            function storeLoopAndParticipants($dotloop, $db, $local_profile_id, $dotloop_account_id, $dotloop_profile_id, $loop)
            {
                // If any of these are empty it's likely a demo/sample loop that we don't want anyways
                if (empty($loop['name']) || empty($loop['status']) || empty($loop['transactionType'])) {
                    if (MAINT_DEBUG) {
                        echo sprintf('SKIPPING LOOP: Invalid loop data - ID: %s.', $loop['id']) . PHP_EOL;
                    }
                    return false;
                }
                if (MAINT_DEBUG) {
                    echo 'Processing Loop: ' . $loop['name'] . ' (' . $loop['id'] . ')' . PHP_EOL;
                }

                // Store Loop Info
                if (($local_loop_id = $dotloop->updateLocalLoopRecord([
                    'id' => $loop['id'],
                    'name' => $loop['name'],
                    'transactionType' => $loop['transactionType'],
                    'status' => $loop['status'],
                    'totalTaskCount' => $loop['totalTaskCount'],
                    'completedTaskCount' => $loop['completedTaskCount'],
                    'created' => $loop['created'],
                    'updated' => $loop['updated'],
                ], $local_profile_id)) > 0) {
                    // Start Loop Participants Transaction
                    $db->beginTransaction();

                    // Default All Leads to "Removed" - Change back if they're returned in the API call
                    $removed_query = $db->prepare(
                        sprintf(
                            "UPDATE `%s` SET `removed_from_loop` = 'true' WHERE `loop_id` = :loop_id;",
                            Partner_DotLoop::TABLE_PARTICIPANTS
                        )
                    );
                    $removed_query->execute(['loop_id' => $local_loop_id]);

                    // Pull Loop Participants
                    $participants = $dotloop->getLoopParticipants($dotloop_profile_id, $loop['id']);
                    if (DOTLOOP_API_CALL_REATTEMPT_FAILURE === checkAndHandleFailedAPICall($dotloop, $partners['dotloop']['account_id'], $dotloop->getRateLimitStatus(), 'getLoopParticipants', $participants, $dotloop_profile_id, $loop['id'])) {
                        $db->rollback();
                        return false;
                    }
                    if (!empty($participants['data']) && is_array($participants['data'])) {
                        foreach ($participants['data'] as $participant) {
                            // If email is empty it's likely a demo participant - otherwise no point in storing it without the lookup value anyways
                            if (empty($participant['email'])) {
                                if (MAINT_DEBUG) {
                                    echo sprintf('SKIPPING PARTICIPANT: Missing email address for: %s.', $participant['name']) . PHP_EOL;
                                }
                                continue;
                            }
                            if (MAINT_DEBUG) {
                                echo 'Processing Participant: ' . $participant['fullName'] . ' (' . $participant['id'] . ')' . '(' .  $local_loop_id. ')' . PHP_EOL;
                            }
                            // Store Participant Info
                            if (($local_participant_id = $dotloop->updateLocalParticipantRecord([
                                'id' => $participant['id'],
                                'fullName' => $participant['fullName'],
                                'email' => $participant['email'],
                                'role' => $participant['role'],
                            ], $local_loop_id)) > 0) {
                                if (MAINT_DEBUG) {
                                    echo 'Participant record updated successfully.' . PHP_EOL;
                                }
                            } else {
                                if (MAINT_DEBUG) {
                                    echo 'Failed to update participant record.' . PHP_EOL;
                                }
                                continue;
                            }
                        }
                        // Commit Loop Participants Transaction
                        $db->commit();
                        return true;
                    } else {
                        if (MAINT_DEBUG) {
                            echo sprintf('Failed to load loop participants - Loop ID: %s', $loop['id']);
                        }
                    }
                } else {
                    if (MAINT_DEBUG) {
                        echo sprintf('Failed to update loop data - ID: %s', $loop['id']);
                    }
                }
                // If anything failed, revert the current loop + participant changes
                $db->rollback();
                return false;
            }

            // Init DB Object
            $db = DB::get();

            // Tracked Synced Accounts So We Don't Perform Duplicate Syncs on Shared Accounts
            $synced_accounts = [];

            // Loop Through All Agents With Partner Integrations
            $agents = $db->fetchAll("SELECT * FROM `agents` WHERE `partners` != '' AND `partners` IS NOT NULL ORDER BY `id`;");
            if (!empty($agents)) {
                foreach ($agents as $agent) {
                    if ($agent = Backend_Agent::load($agent['id'])) {
                        if ($partners = json_decode($agent->info('partners'), true)) {
                            // Check if agent has active dotloop integration
                            if (!empty($partners['dotloop'])) {
                                // Make Sure Partner Data is Valid
                                if (empty($partners['dotloop']['account_id']) || empty($partners['dotloop']['access_token'])) {
                                    if (MAINT_DEBUG) {
                                        echo sprintf('SKIPPING: Agent does not have a valid DotLoop integration - ID: %s', $agent['id']) . PHP_EOL;
                                    }
                                    continue;
                                }

                                // Check if account has been recently synced
                                try {
                                    $check_account_last_sync = $db->fetch(
                                        sprintf(
                                            "SELECT `dotloop_update_timestamp`, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`dotloop_update_timestamp`)) AS `secs_since_sync` "
                                            . " FROM `%s` WHERE `dotloop_account_id` = :account_id "
                                            . " ORDER BY `dotloop_update_timestamp` DESC "
                                            . " LIMIT 1 "
                                            . ";",
                                            Partner_DotLoop::TABLE_SYSTEM
                                        ),
                                        ['account_id' => $partners['dotloop']['account_id']]
                                    );
                                    if (!empty($check_account_last_sync['secs_since_sync'])) {
                                        if ($check_account_last_sync['secs_since_sync'] <= DOTLOOP_ACCOUNT_SYNC_LIMIT) {
                                            if (MAINT_DEBUG) {
                                                echo sprintf('SKIPPING ACCOUNT: DotLoop account has been recently synced. Account ID: %s', $partners['dotloop']['account_id']) . PHP_EOL;
                                            }
                                            continue;
                                        }
                                    }
                                } catch (PDOException $e) {
                                    echo sprintf('PDO EXCEPTION: %s', $e->getMessage()) . PHP_EOL;
                                    continue;
                                }

                                // Init DotLoop Object Using Agent's Account/Tokens
                                $dotloop = new Partner_DotLoop($agent, $db);

                                // Validate the Account's Access Token
                                $check_valid = $dotloop->validateAPIAccess();
                                if (DOTLOOP_API_CALL_REATTEMPT_FAILURE === checkAndHandleFailedAPICall($dotloop, $partners['dotloop']['account_id'], $dotloop->getRateLimitStatus(), 'validateAPIAccess', $check_valid)) {
                                    continue;
                                }
                                if ($check_valid) {
                                    try {
                                        // Pull + Store Accounts
                                        $account = $dotloop->getAccountInfo();
                                        if (DOTLOOP_API_CALL_REATTEMPT_FAILURE === checkAndHandleFailedAPICall($dotloop, $partners['dotloop']['account_id'], $dotloop->getRateLimitStatus(), 'getAccountInfo', $account)) {
                                            continue;
                                        }
                                        if (!empty($account)) {
                                            // Skip accounts that have already been updated
                                            if (in_array($account['id'], $synced_accounts)) {
                                                if (MAINT_DEBUG) {
                                                    echo sprintf('SKIPPING ACCOUNT: Already updated account from a previous agent\'s partner integration. Account ID: %s', $account['id']) . PHP_EOL;
                                                }
                                                continue;
                                            }

                                            // Store Account Info
                                            $account_query = $db->prepare(sprintf("INSERT INTO `%s` SET "
                                                . " `dotloop_account_id` = :account_id, "
                                                . " `email` = :account_email "
                                                . " ON DUPLICATE KEY UPDATE "
                                                . " `email` = :account_email, "
                                                . " `timestamp_updated` = NOW() "
                                                . ";", Partner_DotLoop::TABLE_ACCOUNTS));
                                            if ($account_query->execute([
                                                'account_id' => $account['id'],
                                                'account_email' => $account['email']
                                            ])) {
                                                // Get ID of last inserted/updated account row
                                                $local_account = $db->fetch(
                                                    sprintf(
                                                        "SELECT `id` FROM `%s` WHERE `dotloop_account_id` = :account_id LIMIT 1;",
                                                        Partner_DotLoop::TABLE_ACCOUNTS
                                                    ),
                                                    ['account_id' => $account['id']]
                                                );
                                                $local_account_id = $local_account['id'];
                                                if (!empty($local_account_id)) {
                                                    if (MAINT_DEBUG) {
                                                        echo sprintf('Successfully updated account row: %s', $local_account_id) . PHP_EOL;
                                                    }

                                                    // Pull + Store Profiles
                                                    $loops_options = [];
                                                    if (!empty($check_account_last_sync['dotloop_update_timestamp'])
                                                        && $check_account_last_sync['dotloop_update_timestamp'] != '0000-00-00 00:00:00'
                                                    ) {
                                                        $loops_options = [
                                                            'filter' => sprintf(
                                                                '?updated_min=%s',
                                                                date('Y-m-d\TH:i:s\Z', strtotime($check_account_last_sync['dotloop_update_timestamp']))
                                                            )
                                                        ];
                                                    }
                                                    $profiles = $dotloop->getProfilesLoops($loops_options);
                                                    if (DOTLOOP_API_CALL_REATTEMPT_FAILURE === checkAndHandleFailedAPICall($dotloop, $partners['dotloop']['account_id'], $dotloop->getRateLimitStatus(), 'getProfilesLoops', $profiles, $loops_options)) {
                                                        continue;
                                                    }
                                                    if (!empty($profiles)) {
                                                        foreach ($profiles as $profile) {
                                                            if (MAINT_DEBUG) {
                                                                echo 'Processing Profile: ' . $profile['name'] . ' (' . $profile['id'] . ')' . PHP_EOL;
                                                            }

                                                            // Store Profile Info
                                                            $profile_query = $db->prepare(sprintf("INSERT INTO `%s` SET "
                                                                . " `account_id` = :local_account_id, "
                                                                . " `dotloop_profile_id` = :profile_id, "
                                                                . " `name` = :profile_name "
                                                                . " ON DUPLICATE KEY UPDATE "
                                                                . " `name` = :profile_name, "
                                                                . " `timestamp_updated` = NOW() "
                                                                . ";", Partner_DotLoop::TABLE_PROFILES));
                                                            if ($profile_query->execute([
                                                                'local_account_id' => $local_account_id,
                                                                'profile_id' => $profile['id'],
                                                                'profile_name' => $profile['name']
                                                            ])) {
                                                                // Get ID of last inserted/updated account row
                                                                $local_profile = $db->fetch(
                                                                    sprintf(
                                                                        "SELECT `id` FROM `%s` "
                                                                        . " WHERE `account_id` = :local_account_id "
                                                                        . " AND `dotloop_profile_id` = :profile_id "
                                                                        . " LIMIT 1 "
                                                                        . ";",
                                                                        Partner_DotLoop::TABLE_PROFILES
                                                                    ),
                                                                    ['local_account_id' => $local_account_id, 'profile_id' => $profile['id']]
                                                                );
                                                                $local_profile_id = $local_profile['id'];

                                                                // Pull + Store Loops
                                                                if (!empty($profile['loops'])) {
                                                                    foreach ($profile['loops'] as $loop) {
                                                                        // Attempt to Store Each Loop and its Participants
                                                                        if (!storeLoopAndParticipants($dotloop, $db, $local_profile_id, $account['id'], $profile['id'], $loop)) {
                                                                            if (MAINT_DEBUG) {
                                                                                echo sprintf('Failed to store loop record - Loop ID: %s', $loop['id']) . PHP_EOL;
                                                                            }
                                                                        } else {
                                                                            if (MAINT_DEBUG) {
                                                                                echo sprintf('Successfully stored loop record - Loop ID: %s', $loop['id']) . PHP_EOL;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                if (MAINT_DEBUG) {
                                                                    echo sprintf('Failed to update local profile data - ID: %s', $profile['id']);
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        if (MAINT_DEBUG) {
                                                            echo 'No loop updates found. Proceeding to next account.' . PHP_EOL;
                                                        }
                                                    }
                                                } else {
                                                    if (MAINT_DEBUG) {
                                                        echo 'Failed to aquire account row ID';
                                                    }
                                                }
                                            } else {
                                                if (MAINT_DEBUG) {
                                                    echo 'Failed to update account data';
                                                }
                                            }

                                            // DotLoop Account Update Tracker
                                            $tracker_query = $db->prepare(sprintf("REPLACE INTO `%s` SET "
                                                . " `dotloop_account_id` = :account_id;", $dotloop::TABLE_SYSTEM));
                                            if ($tracker_query->execute(['account_id' => $account['id']])) {
                                                // Track Account ID to Avoid Duplicate Updates
                                                $synced_accounts[] = $account['id'];
                                            } else {
                                                if (MAINT_DEBUG) {
                                                    echo 'Failed to update tracking data' . PHP_EOL;
                                                }
                                            }

                                            // Force-sync Loops/Participants That Were Created Through the API, but Failed to Track Local Records
                                            $delayed_syncs = $db->fetchAll(sprintf(
                                                "SELECT `id`, `dotloop_account_id`, `dotloop_profile_id`, `dotloop_loop_id` "
                                                . " FROM `%s` "
                                                . " WHERE `dotloop_account_id` = :dotloop_account_id "
                                                . " ORDER BY `timestamp_created` ASC "
                                                . ";",
                                                $dotloop::TABLE_DELAYED_SYNCS
                                            ), [
                                                'dotloop_account_id' => $account['id']
                                            ]);
                                            if (!empty($delayed_syncs)) {
                                                foreach ($delayed_syncs as $delayed_sync) {
                                                    if (MAINT_DEBUG) {
                                                        echo sprintf('Attempting to sync delayed loop ID: %s', $delayed_sync['id']) . PHP_EOL;
                                                    }
                                                    if (!empty($delayed_sync['dotloop_account_id'])
                                                        && !empty($delayed_sync['dotloop_profile_id'])
                                                        && !empty($delayed_sync['dotloop_loop_id'])
                                                        && intval($delayed_sync['dotloop_account_id']) === intval($account['id'])
                                                    ) {
                                                        // Attempt to Store Loop and Loop Participant Records
                                                        if (null !== ($this_loop = $dotloop->getLoop($delayed_sync['dotloop_profile_id'], $delayed_sync['dotloop_loop_id']))) {
                                                            if (!storeLoopAndParticipants($dotloop, $db, $local_profile_id, $account['id'], $profile['id'], $this_loop)) {
                                                                if (MAINT_DEBUG) {
                                                                    echo sprintf('Failed to store loop record via delayed sync - Loop ID: %s', $this_loop['id']) . PHP_EOL;
                                                                }
                                                            } else {
                                                                if (MAINT_DEBUG) {
                                                                    echo sprintf('Successfully stored loop record via delayed sync - Loop ID: %s', $this_loop['id']) . PHP_EOL;
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        if (MAINT_DEBUG) {
                                                            echo sprintf('SKIPPING: Invalid delayed loop record. ID: %s', $delayed_sync['id']) . PHP_EOL;
                                                        }
                                                    }
                                                    // Delete Delayed Loop Record
                                                    $clear_delay = $db->prepare(sprintf(
                                                        "DELETE FROM `%s` WHERE `id` = :id;",
                                                        $dotloop::TABLE_DELAYED_SYNCS
                                                    ));
                                                    $clear_delay->execute(['id' => $delayed_sync['id']]);
                                                }
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo sprintf('PDO EXCEPTION: %s', $e->getMessage()) . PHP_EOL;
                                        continue;
                                    } catch (Exception $e) {
                                        if (MAINT_DEBUG) {
                                            echo sprintf('EXCEPTION: %s', $e->getMessage()) . PHP_EOL;
                                        }
                                        continue;
                                    }
                                } else {
                                    if (MAINT_DEBUG) {
                                        echo sprintf('ERROR: Agent does not have valid DotLoop access token - ID: %s', $agent['id']) . PHP_EOL;
                                    }
                                    continue;
                                }
                            } else {
                                if (MAINT_DEBUG) {
                                    echo sprintf('SKIPPING: Agent does not have active DotLoop integration - ID: %s', $agent['id']) . PHP_EOL;
                                }
                                continue;
                            }
                        } else {
                            echo sprintf('ERROR: Failed to json_decode agent dotloop settings - ID: %s', $agent['id']) . PHP_EOL;
                            continue;
                        }
                    } else {
                        echo sprintf('ERROR: Failed to init agent object - ID: %s', $agent['id']) . PHP_EOL;
                        continue;
                    }
                }
            } else {
                if (MAINT_DEBUG) {
                    echo 'SKIPPING DOTLOOP SYNC: No agents have active partner integrations.' . PHP_EOL;
                }
            }

            // Release and Delete the Lock File
            if (MAINT_DEBUG) {
                echo 'Releasing lock file.' . PHP_EOL;
            }
            flock($dotloop_lock, LOCK_UN);
            unlink(DOTLOOP_LOCK_FILE);
        } else {
            if (MAINT_DEBUG) {
                echo 'SKIPPING DOTLOOP SYNC: Lockfile found.' . PHP_EOL;
            }
        }
    } else {
        echo 'ERROR: Failed to create DotLoop Sync lockfile' . PHP_EOL;
    }
}

// Calculate Script Execution Time
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

// Output
if (MAINT_DEBUG) {
    echo PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
}
