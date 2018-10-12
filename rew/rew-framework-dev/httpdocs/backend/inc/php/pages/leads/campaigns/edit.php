<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Campaigns
$can_manage_all = $leadsAuth->canManageCampaigns($authuser);
if (!$can_manage_all) {
    // Authorized to Manage Own Campaigns
    if (!$leadsAuth->canManageOwnCampaigns($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage campaigns.')
        );
    }
}

// Success
$success = array();

// Error
$errors = array();

// Row ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Select Campaign
$result = mysql_query("SELECT * FROM `" . LM_TABLE_CAMPAIGNS . "` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "';");
$campaign = mysql_fetch_assoc($result);

/* Throw Missing Agent Exception */
if (empty($campaign)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingCampaignException();
}

// New Row Successful
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = __('Campaign has successfully been created.');
}

// Process Submit
if (isset($_GET['submit'])) {
    // Force Sender
    $_POST['sender'] = in_array($_POST['sender'], array('admin', 'agent', 'custom')) ? $_POST['sender'] : 'agent';

    // Only Super Admin can set Sender, Otherwise force 'agent' as Sender
    $_POST['sender'] = ($can_manage_all && $campaign['agent_id'] == 1) ? $_POST['sender'] : 'agent';

    // Require Campaign Name
    if (empty($_POST['name'])) {
        $errors[] = __('Please supply a name for the campaign.');
    }

    // Require Sender Information
    if ($_POST['sender'] == 'custom') {
        // Require Sender Name
        if (empty($_POST['sender_name'])) {
            $errors[] = __('Please supply the sender name for the campaign.');
        }

        // Require Valid Sender Email
        if (!Validate::email($_POST['sender_email'])) {
            $errors[] = __('Please supply the sender email for the campaign.');
        }
    }

    // Require Campaign Email
    if (!empty($_POST['emails']) && is_array($_POST['emails'])) {
        foreach ($_POST['emails'] as $email) {
            if (empty($email['send_delay']) || empty($email['subject']) || empty($email['doc_id'])) {
                $errors[] = __('You must provide more details for your campaign email.');
            }
        }
    } else {
        $errors[] = __('You must have at least one campaign email.');
    }

    // Check Errors
    if (empty($errors)) {
        // Start DB Transaction
        mysql_query('START TRANSACTION;');

        // Campaign Groups
        if ($can_manage_all) {
            mysql_query("DELETE FROM `" . LM_TABLE_CAMPAIGNS_GROUPS . "` WHERE `campaign_id` = '" . $campaign['id'] . "';");
            if (is_array($_POST['groups'])) {
                foreach ($_POST['groups'] as $group) {
                    mysql_query("INSERT INTO `" . LM_TABLE_CAMPAIGNS_GROUPS . "` SET `group_id` = '" . $group . "', `campaign_id` = '" . $campaign['id'] . "';");
                }
            }
        } else {
            mysql_query("DELETE `cg` FROM `" . LM_TABLE_CAMPAIGNS_GROUPS . "` `cg` LEFT JOIN `" . LM_TABLE_GROUPS . "` `g` ON `cg`.`group_id` = g.`id` WHERE (g.`agent_id` = '" . $authuser->info('id') . "' OR (g.`agent_id` IS NULL AND g.`associate` IS NULL)) AND cg.`campaign_id` = '" . $campaign['id'] . "';");
            if (is_array($_POST['groups'])) {
                foreach ($_POST['groups'] as $group) {
                    mysql_query("INSERT INTO `" . LM_TABLE_CAMPAIGNS_GROUPS . "` SET `group_id` = '" . $group . "', `campaign_id` = '" . $campaign['id'] . "';");
                }
            }
        }

        // Campaign Start Date
        $starts = !empty($_POST['starts']) ? date('Y-m-d', strtotime($_POST['starts'])) : null;

        // Campaign Status
        $active = ($_POST['active'] == 'Y') ? 'Y' : 'N';

        // Campaign Template
        $template = (!empty($_POST['tempid'])) ? $_POST['tempid'] : 'NULL';

        // Only Update Sender Information If Set to 'custom'
        $_POST['sender_name']  = ($_POST['sender'] == 'custom') ? $_POST['sender_name']  : $campaign['sender_name'];
        $_POST['sender_email'] = ($_POST['sender'] == 'custom') ? $_POST['sender_email'] : $campaign['sender_email'];

        // Build UPDATE Query
        $query = "UPDATE `" . LM_TABLE_CAMPAIGNS . "` SET "
               . "`name`			= '" . mysql_real_escape_string($_POST['name']) . "', "
               . "`description`		= '" . mysql_real_escape_string($_POST['description']) . "', "
               . "`sender`			= '" . mysql_real_escape_string($_POST['sender']) . "', "
               . "`sender_name`		= '" . mysql_real_escape_string($_POST['sender_name']) . "', "
               . "`sender_email`	= '" . mysql_real_escape_string($_POST['sender_email']) . "', "
               . "`starts`			= " . (is_null($starts) ? "NULL" : "'" . mysql_real_escape_string($starts) . "'") . ", "
               . "`active`			= '" . mysql_real_escape_string($active) . "', "
               . "`tempid`			= "  . mysql_real_escape_string($template)
               . " WHERE "
               . "`id` = '" . $campaign['id'] . "';";

        // Execute Query
        if (mysql_query($query)) {
            // Success
            $success[] = __('Your changes have successfully been saved.');

            // Process Campaign Emails
            if (is_array($_POST['emails']) && !empty($_POST['emails'])) {
                // Keep These Emails
                $emails = array();

                foreach ($_POST['emails'] as $email) {
                    // Update Email
                    if (!empty($email['id'])) {
                        $query = "UPDATE `" . LM_TABLE_CAMPAIGNS_EMAILS . "` SET "
                               . "`campaign_id`	= '" . $campaign['id'] . "', "
                               . "`subject`		= '" . mysql_real_escape_string($email['subject']) . "', "
                               . "`doc_id`		= '" . mysql_real_escape_string($email['doc_id']) . "', "
                               . "`send_delay`	= '" . mysql_real_escape_string($email['send_delay']) . "'"
                               . " WHERE `id`	= '" . mysql_real_escape_string($email['id']) . "';";

                    // Create Email
                    } else {
                        $query = "INSERT INTO `" . LM_TABLE_CAMPAIGNS_EMAILS . "` SET "
                               . "`campaign_id`	= '" . $campaign['id'] . "', "
                               . "`subject`		= '" . mysql_real_escape_string($email['subject']) . "', "
                               . "`doc_id`		= '" . mysql_real_escape_string($email['doc_id']) . "', "
                               . "`send_delay`	= '" . mysql_real_escape_string($email['send_delay']) . "';";
                    }

                    // Execute Query
                    if (mysql_query($query)) {
                        $emails[] = !empty($email['id']) ? $email['id'] : mysql_insert_id();

                    // Query Error
                    } else {
                        $errors[] = __('Error Occurred while trying to save Campaign Emails.');
                    }
                }

                // Delete Old campaign Emails
                $extra = !empty($emails) ? " AND `id` NOT IN ('" . implode("', '", $emails) . "')" : '';
                $query = "DELETE FROM `" . LM_TABLE_CAMPAIGNS_EMAILS . "` WHERE `campaign_id` = '" . $campaign['id'] . "'" . $extra;
                if (!mysql_query($query)) {
                    $errors[] = __('Error Occurred while trying to remove Campaign Emails.');
                }
            }

            // Commit DB Transaction
            mysql_query('COMMIT;');

            // Fetch Updated Row
            $result = mysql_query("SELECT * FROM `" . LM_TABLE_CAMPAIGNS . "` WHERE `id` = '" . mysql_real_escape_string($campaign['id']) . "';");
            $campaign = mysql_fetch_assoc($result);
        } else {
            // Error
            $errors[] = __('Error occurred, Campaign could not be updated. Please try again.');

            // Rollback DB
            mysql_query('ROLLBACK;');
        }
    }

    // Use $_POST Data
    foreach ($campaign as $k => $v) {
        $campaign[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Has Admin Permissions
if ($can_manage_all) {
    // Campaign Owner
    if ($authuser->info('id') != $campaign['agent_id']) {
        $result = mysql_query("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = '" . mysql_real_escape_string($campaign['agent_id']) . "';");
        $agent = mysql_fetch_assoc($result);
    }

    // Super Admin Permissions
    if ($campaign['agent_id'] == 1) {
        // Super Admin Details
        $result = mysql_query("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
        $super_admin = mysql_fetch_assoc($result);
    }
}

// Campaign Groups
$campaign['groups'] = [];
$campaignGroups = Backend_Group::getGroups($errors, Backend_Group::CAMPAIGN, $campaign['id']);
foreach ($campaignGroups as $campaignGroup) {
    $campaign['groups'][] = $campaignGroup['id'];
}

// Available Groups (for Campaign's Agent)
$groups = Backend_Group::getGroups($errors, Backend_Group::AGENT, $campaign['agent_id']);

// Campaign Emails
$emails = isset($_POST['emails']) ? $_POST['emails'] : array();
if (empty($emails)) {
    $result = mysql_query("SELECT * FROM `" . LM_TABLE_CAMPAIGNS_EMAILS . "` WHERE `campaign_id` = '" . mysql_real_escape_string($campaign['id']) . "' ORDER BY `send_delay`;");
    while ($row = mysql_fetch_assoc($result)) {
        $emails[] = $row;
    }
}

// Email Documents
$docs = array();
$query = "SELECT `c`.`id` AS `cat_id`, `c`.`name` AS `cat_name`, `d`.`id` AS `doc_id`, `d`.`name` AS `doc_name`"
       . " FROM `" . LM_TABLE_DOC_CATEGORIES . "` `c` LEFT JOIN `" . LM_TABLE_DOCS . "` `d` ON `c`.`id` = `d`.`cat_id`"
       . " WHERE (`d`.`share` = 'true' OR `c`.`agent_id` = '" . mysql_real_escape_string($campaign['agent_id']) . "')"
       . " ORDER BY `cat_name` ASC, `doc_name` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $docs[$row['cat_id']]['name'] = $row['cat_name'];
        $docs[$row['cat_id']]['docs'][$row['doc_id']] = $row['doc_name'];
    }
}

// Email Templates
$templates = array();
$result = mysql_query("SELECT `id`, `name` FROM `" . LM_TABLE_DOC_TEMPLATES . "` WHERE (`agent_id` = '" . mysql_real_escape_string($campaign['agent_id']) . "' OR `share` = 'true') ORDER BY `name` ASC;");
while ($row = mysql_fetch_assoc($result)) {
    $templates[] = $row;
}
