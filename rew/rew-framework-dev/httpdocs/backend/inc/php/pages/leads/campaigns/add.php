<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Campaigns
if (!$leadsAuth->canManageCampaigns($authuser)) {
    // Authorized to Manage Own Campaigns
    if (!$leadsAuth->canManageOwnCampaigns($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to add campaigns.')
        );
    }
}

// Success
$success = array();

// Error
$errors = array();

// Force Sender
$_POST['sender'] = in_array($_POST['sender'], array('admin', 'agent', 'custom')) ? $_POST['sender'] : 'agent';

// Process Submit
if (isset($_GET['submit'])) {
    // Only Super Admin can set Sender, Otherwise force 'agent' as Sender
    $_POST['sender'] = $leadsAuth->canManageCampaigns($authuser) ? $_POST['sender'] : 'agent';

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

        // Campaign Start Date
        $starts = !empty($_POST['starts']) ? date('Y-m-d', strtotime($_POST['starts'])) : null;

        // Campaign Status
        $active = ($_POST['active'] == 'Y') ? 'Y' : 'N';

        // Campaign Template
        $template = (!empty($_POST['tempid'])) ? $_POST['tempid'] : 'NULL';

        // Sender Information
        $_POST['sender_name']  = ($_POST['sender'] == 'custom') ? $_POST['sender_name']  : '';
        $_POST['sender_email'] = ($_POST['sender'] == 'custom') ? $_POST['sender_email'] : '';

        // Build INSERT Query
        $query = "INSERT INTO `" . LM_TABLE_CAMPAIGNS . "` SET "
               . "`name`			= '" . mysql_real_escape_string($_POST['name']) . "', "
               . "`description`		= '" . mysql_real_escape_string($_POST['description']) . "', "
               . "`sender`			= '" . mysql_real_escape_string($_POST['sender']) . "', "
               . "`sender_name`		= '" . mysql_real_escape_string($_POST['sender_name']) . "', "
               . "`sender_email`	= '" . mysql_real_escape_string($_POST['sender_email']) . "', "
               . "`starts`			= " . (is_null($starts) ? "NULL" : "'" . mysql_real_escape_string($starts) . "'") . ", "
               . "`active`			= '" . $active   . "', "
               . "`tempid`			= "  . $template . ", "
               . "`agent_id`		= '" . $authuser->info('id') . "', "
               . "`timestamp`		= NOW();";

        // Execute Query
        if (mysql_query($query)) {
            // Insert ID
            $campaign_id = mysql_insert_id();

            // Campaign Groups
            if (!empty($_POST['groups']) && is_array($_POST['groups'])) {
                foreach ($_POST['groups'] as $group) {
                    mysql_query("INSERT INTO `" . LM_TABLE_CAMPAIGNS_GROUPS . "` SET `group_id` = '" . $group . "', `campaign_id` = '" . $campaign_id . "';");
                }
            }

            // Campaign Emails
            if (!empty($_POST['emails']) && is_array($_POST['emails'])) {
                foreach ($_POST['emails'] as $email) {
                    mysql_query("INSERT INTO `" . LM_TABLE_CAMPAIGNS_EMAILS . "` SET "
                           . "`campaign_id`	= '" . $campaign_id . "', "
                           . "`subject`		= '" . mysql_real_escape_string($email['subject']) . "', "
                           . "`doc_id`		= '" . mysql_real_escape_string($email['doc_id']) . "', "
                           . "`send_delay`	= '" . mysql_real_escape_string($email['send_delay']) . "';");
                }
            }

            // Commit DB Transaction
            mysql_query('COMMIT;');

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $campaign_id . '&success=add');
            exit;
        } else {
            // Query Error
            $errors[] = __('Error occurred, Campaign could not be added. Please try again.');

            // Rollback DB
            mysql_query('ROLLBACK;');
        }
    }
}

// Super Admin
if ($leadsAuth->canManageCampaigns($authuser)) {
    $result = mysql_query("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
    $super_admin = mysql_fetch_assoc($result);
}

// Available Groups
$groups = Backend_Group::getGroups($errors);
if (!is_array($_POST['groups'])) {
    $_POST['groups'] = [];
}


// Email Documents
$docs = array();
$query = "SELECT `c`.`id` AS `cat_id`, `c`.`name` AS `cat_name`, `d`.`id` AS `doc_id`, `d`.`name` AS `doc_name`"
       . " FROM `" . LM_TABLE_DOC_CATEGORIES . "` `c` LEFT JOIN `" . LM_TABLE_DOCS . "` `d` ON `c`.`id` = `d`.`cat_id`"
       . " WHERE (`d`.`share` = 'true' OR `c`.`agent_id` = '" . $authuser->info('id') . "')"
       . " ORDER BY `cat_name` ASC, `doc_name` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $docs[$row['cat_id']]['name'] = $row['cat_name'];
        $docs[$row['cat_id']]['docs'][$row['doc_id']] = $row['doc_name'];
    }
}

// Email Templates
$templates = array();
$result = mysql_query("SELECT `id`, `name` FROM `" . LM_TABLE_DOC_TEMPLATES . "` WHERE (`agent_id` = '" . $authuser->info('id') . "' OR `share` = 'true') ORDER BY `name` ASC;");
while ($row = mysql_fetch_assoc($result)) {
    $templates[] = $row;
}

// Campaign Emails
$emails = isset($_POST['emails']) ? $_POST['emails'] : array(array('send_delay' => 1));

// Default Settings
$_POST['starts'] = isset($_POST['starts']) ? strtotime($_POST['starts']) : time();
$_POST['active'] = isset($_POST['active']) ? $_POST['active'] : 'Y';
