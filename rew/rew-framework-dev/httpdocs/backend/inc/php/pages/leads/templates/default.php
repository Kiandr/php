<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Documents
if (!$leadsAuth->canManageDocuments($authuser)) {
    if (!$leadsAuth->canViewOwn($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to manage documents.'
        );
    }
    $sql_agent = "`%s`.`agent_id` = '" . $authuser->info('id') . "' ";
} else {

    // Get Agent Filter
    $_GET['personal'] = isset($_POST['personal'])
        ? $_POST['personal'] : $_GET['personal'];
    if (isset($_GET['personal'])) {
        $sql_agent = "`%s`.`agent_id` = '" . $authuser->info('id') . "' ";
    } else {
        $can_manage_all = true;
    }
}

// Success
$success = array();

// Errors
$errors = array();

// Delete Record
if (!empty($_GET['delete'])) {

    $type = 'template';
    $table = LM_TABLE_DOC_TEMPLATES;

    // Check Errors
    if (empty($errors)) {

        // Locate Record
        $query = "SELECT `id` FROM `" . $table . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "';";
        if ($result = mysql_query($query)) {
            $delete = mysql_fetch_assoc($result);
            if (!empty($delete)) {
                // Delete Record from Database
                $query = "DELETE FROM `" . $table . "` WHERE `id` = '" . mysql_real_escape_string($delete['id']) . "';";
                if (mysql_query($query)) {
                    $success[] = 'The selected ' . $type . ' has successfully been deleted.';

                    // Query Error
                } else {
                    $errors[] = 'An error occurred while trying to delete the selected ' . $type . '.';

                }
            }

            // Query Error
        } else {
            $errors[] = 'An error occurred while locating selected ' . $type . '.';

        }

    }

}

// Manage Templates

// Templates
$tmps = array();
$query = "SELECT `t`.`id`, `t`.`name`, `t`.`agent_id`, `t`.`share` FROM `" . LM_TABLE_DOC_TEMPLATES . "` `t`"
    . (!empty($sql_agent) ? " WHERE (" . sprintf($sql_agent, 't') . " OR `t`.`share` = 'true')" : '')
    . " ORDER BY LENGTH(`t`.`name`) ASC, `t`.`name` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {

        // Select Agent
        if (!$_GET['personal']) {
            $agent = mysql_query("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name`, `image` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = '" . $row['agent_id'] . "';");
            $row['agent'] = mysql_fetch_assoc($agent);
        }

        // Add to Collection
        $tmps[] = $row;
    }

    // Query Error
} else {
    $errors[] = 'Error Occurred while loading Templates.';

}
