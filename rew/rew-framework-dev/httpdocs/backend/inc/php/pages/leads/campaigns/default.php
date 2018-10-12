<?php

// Get DB
$db = DB::get();

// Get agent
$agent = [];
if (is_numeric($_GET['agent'])) {
    try {
        $agent = $db->fetch(
            "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) as `text` FROM `agents` WHERE `id` = :agent;",
            ['agent' => $_GET['agent']]
        );
    } catch (PDOException $exception) {
        $errors[] = __('Could not load agent Details');
    }
}

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Get Agent Filter
$_GET['personal'] = isset($_POST['personal'])
    ? $_POST['personal'] : $_GET['personal'];
if (isset($_GET['personal'])) {
    $sql_agent = "`%s`.`agent_id` = '" . $authuser->info('id') . "'";
    $filter = __('My Campaigns');

// Authorized to Manage All Campaigns
} else if ($leadsAuth->canManageCampaigns($authuser) && !empty($_GET['agent']) || $authuser->info('id') == 1) {
    if (!empty($agent)) {
        $sql_agent = "`%s`.`agent_id` = '" . $_GET['agent'] . "'";
        $filter = __('Campaigns: ') . htmlspecialchars($agent['text']);
    } else {
        $filter = __('All Campaigns');
    }

// Authorized to Manage All Campaigns
} else if (!$leadsAuth->canManageCampaigns($authuser)) {
    // Authorized to Manage Own Campaigns
    if (!$leadsAuth->canManageOwnCampaigns($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage campaigns.')
        );
    }
    $sql_agent = "`%s`.`agent_id` = '" . $authuser->info('id') . "'";
    $filter = __('My Campaigns');
}

// Success
$success = !empty($_SESSION['success']) ? $_SESSION['success'] : array();
unset($_SESSION['success']);

// Error
$errors = !empty($_SESSION['errors']) ? $_SESSION['errors'] : array();
unset($_SESSION['errors']);

// Delete Campaign
if (!empty($_GET['delete'])) {
    $query = "DELETE FROM `" . LM_TABLE_CAMPAIGNS . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "';";
    if (mysql_query($query)) {
        $success[] = __('The selected campaign has successfully been deleted.');
    } else {
        $errors[] = __('An error occurred while trying to delete the selected campaign.');
    }
}

// Copy Campaign To Agent
if (isset($_GET['copy'])) {
    // Select Agent
    if ($_POST['agent_id'] == 'all') {
        $query = "SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "`;";
    } else {
        $query = "SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = '" . mysql_real_escape_string($_POST['agent_id']) . "';";
    }

    if ($agents = mysql_query($query)) {
        while ($agent  = mysql_fetch_assoc($agents)) {
            // Require Campaigns
            if (!empty($_POST['campaigns'])) {
                // Require Array
                $_POST['campaigns'] = is_array($_POST['campaigns']) ? $_POST['campaigns'] : explode(',', $_POST['campaigns']);
                foreach ($_POST['campaigns'] as $manage_campaign) {
                    $result = mysql_query("SELECT * FROM `" . LM_TABLE_CAMPAIGNS . "` WHERE `id` = '" . mysql_real_escape_string($manage_campaign) . "'");
                    $manage_campaign = mysql_fetch_assoc($result);

                    if (!empty($manage_campaign)) {
                        // If Copying to All Agents, Don't Copy to Campaign's Owner
                        if (($_POST['agent_id'] == 'all') && ($manage_campaign['agent_id'] == $agent['id'])) {
                            continue;
                        }

                        // Copy Campaign
                        $query = "INSERT INTO `" . LM_TABLE_CAMPAIGNS . "` SET "
                               . "agent_id = '" . $agent['id'] . "', "
                               . "name = '" . mysql_real_escape_string($manage_campaign['name']) . "', "
                               . "description = '" . mysql_real_escape_string($manage_campaign['description']) . "'"
                               . ";";

                        if (mysql_query($query)) {
                            // Success!
                            if ($_POST['agent_id'] !== 'all') {
                                $success[] = __('You have successfully copied the') . ' <strong>' . $manage_campaign['name'] . '</strong> ' . __('campaign to') . ' <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=' . $agent['id'] . '">' . $agent['first_name'] . ' ' . $agent['last_name'] . '</a>.';
                            }
                            // Campaign ID
                            $campaign_id = mysql_insert_id();

                            // Document Category
                            $name = $agent['first_name'] . ' ' . $agent['last_name'] . ' - ' . $manage_campaign['name'];

                            // Create Doc Category for Campaign Docs
                            $query = "INSERT INTO `" . LM_TABLE_DOC_CATEGORIES . "` SET "
                                   . "agent_id = '" . $agent['id'] . "', "
                                   . "name = '" . mysql_real_escape_string($name) . "', "
                                   . "description = 'Campaign Documents for " . mysql_real_escape_string($name) . "'"
                                   . ";";

                            if (mysql_query($query)) {
                                // Document Category ID
                                $category_id = mysql_insert_id();

                                // Copy Campaign Emails & Documents
                                $query = "SELECT t1.name, t1.document, t1.is_html, t2.subject, t2.send_delay FROM `" . LM_TABLE_DOCS . "` t1 LEFT JOIN `" . LM_TABLE_CAMPAIGNS_EMAILS . "` t2 ON t1.id = t2.doc_id WHERE t2.campaign_id = '" . $manage_campaign['id'] . "'";
                                if ($copy_documents = mysql_query($query)) {
                                    while ($copy_document = mysql_fetch_assoc($copy_documents)) {
                                        // Copy Campaign Document
                                        $query = "INSERT INTO `" . LM_TABLE_DOCS . "` SET "
                                               . "`cat_id`   = '" . $category_id . "', "
                                               . "`name`     = '" . mysql_real_escape_string($copy_document['name']) . "', "
                                               . "`document` = '" . mysql_real_escape_string($copy_document['document']) . "', "
                                               . "`is_html`  = '" . mysql_real_escape_string($copy_document['is_html']) . "'"
                                               . ";";

                                        if (mysql_query($query)) {
                                            // Document ID
                                            $doc_id = mysql_insert_id();

                                            // Copy Campaign Email
                                            $query = "INSERT INTO `" . LM_TABLE_CAMPAIGNS_EMAILS . "` SET "
                                                   . "`campaign_id`  = '" . $campaign_id . "', "
                                                   . "`doc_id`       = '" . $doc_id . "', "
                                                   . "`subject`      = '" . mysql_real_escape_string($copy_document['subject']) . "', "
                                                   . "`send_delay`   = '" . mysql_real_escape_string($copy_document['send_delay']) . "'"
                                                   . ";";

                                            if (!mysql_query($query)) {
                                                // Query Error
                                                $errors[] = __('Error Occurred while Inserting Campaign Email');
                                            }

                                        // Query Error
                                        } else {
                                            $errors[] = __('Error Occurred while Inserting Campaign Document');
                                        }
                                    }

                                // Query Error
                                } else {
                                    $errors[] = __('Error Occurred while Copying Campaign Emails');
                                }

                            // Query Error
                            } else {
                                $errors[] = __('Error Occurred while Inserting Document Category');
                            }

                        // Query Error
                        } else {
                            $errors[] = __('Error Occurred while Copying Email Campaign');
                        }
                    }
                }
                // Success
                if ($_POST['agent_id'] !== 'all') {
                    $success[] .= '<p><strong>' . __('Note') . ':</strong> ' . __('Agents will need to update copied campaigns to provide sender information and campaign groups.') . '</p>';
                }
            } else {
                // Error
                $errors[] = __('You must select at least one campaign to copy.');
            }
        }

        if ($_POST['agent_id'] == 'all') {
            $success[] = __('You have successfully copied the') . ' <strong>' . $manage_campaign['name'] . '</strong> ' . __('campaign to all agents');
            $success[] .= '<p><strong>' . __('Note') . ':</strong> ' . __('Agents will need to update copied campaigns to provide sender information and campaign groups.') . '</p>';
        }

    // Query Error
    } else {
        $errors[] = __('Error Loading Agents');
    }

    // AJAX Request, Return JSON
    if (!empty($_POST['ajax'])) {
        // JSON Response
        $json = array();

        // JSON Success
        if (!empty($success)) {
            $json['success'] = $success;
            $_SESSION['success'] = $success;
        }

        // JSON Errors
        if (!empty($errors)) {
            $json['errors'] = $errors;
            $_SESSION['errors'] = $errors;
        }

        // Send JSON Response
        header('Content-type: application/json');
        die(json_encode($json));
    }
}

// Campaigns
$campaigns = array();

// Select Campaigns
$query = "SELECT `c`.`id`, `c`.`name`, `c`.`active`"
       . " , `a`.`id` AS `agent_id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name`,"
       . " `a`.`first_name`, ' ', `a`.`last_name` "
       . " FROM `" . LM_TABLE_CAMPAIGNS . "` `c` "
       . " LEFT JOIN `" . LM_TABLE_AGENTS . "` `a` ON `c`.`agent_id` = `a`.`id`"
       . (!empty($sql_agent) ? " WHERE " . sprintf($sql_agent, 'c') : "")
       . " ORDER BY `c`.`timestamp` DESC;";

if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        // Campaign Agent
        $row['initials'] = substr($row['first_name'], 0, 1) . ' ' .  substr($row['last_name'], 0, 1);

        // Campaign Groups
        $row['groups'] = Backend_Group::getGroups($errors, Backend_Group::CAMPAIGN, $row['id']);

        // Campaign Emails
        if ($emails = mysql_query("SELECT COUNT(`ce`.`id`) AS `emails` FROM  " . LM_TABLE_CAMPAIGNS_EMAILS . " ce WHERE ce.`campaign_id` = '" . $row['id'] . "';")) {
            $row['emails'] = mysql_fetch_assoc($emails);
            $row['emails'] = $row['emails']['emails'];
        }

        // Campaign Users
        if (!empty($row['groups'])) {
            $query = "SELECT COUNT(DISTINCT `u`.`id`) AS `total` FROM `" . LM_TABLE_LEADS . "` `u` LEFT JOIN `" . LM_TABLE_USER_GROUPS . "` `ug` ON `u`.`id` = `ug`.`user_id` WHERE `ug`.`group_id` IN ('" . implode("', '", array_keys($row['groups'])) . "') AND `u`.`opt_marketing` = 'in'" . ($row['agent_id'] != 1 ? " AND `u`.`agent` = '" . mysql_real_escape_string($row['agent_id']) . "'" : "") . ";";
            if ($leads = mysql_query($query)) {
                $row['leads'] = mysql_fetch_assoc($leads);
                $row['leads'] = $row['leads']['total'];
            }
        }

        // Add Campaign
        $campaigns[] = $row;
    }

// Query Error
} else {
    $errors[] = __('Error Occurred while loading Campaigns.');
}

// Admin Mode
if ($leadsAuth->canManageCampaigns($authuser)) {
    // Select Agents
    $agents = array();
    $query = "SELECT `id`,`image`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` ORDER BY `first_name` ASC;";
    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['initials'] = substr($row['first_name'], 0, 1) . ' ' .  substr($row['last_name'], 0, 1);
            $agents[] = $row;
        }
    } else {
        $errors[] = __('Error Loading Available Agents.');
    }

    // Can Copy Campaigns to Agent?
    $can_copy = !empty($campaigns) && count($agents) > 1;
}
