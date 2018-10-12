<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Documents
if (!$leadsAuth->canManageDocuments($authuser)) {
    if (!$leadsAuth->canViewOwn($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage documents.')
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
    // Row Type
    switch ($_GET['tab']) {
        case 'categories':
            $type = 'category';
            $table = LM_TABLE_DOC_CATEGORIES;
            break;
        case 'documents':
            $type = 'document';
            $table = LM_TABLE_DOCS;
            break;
        case 'templates':
            $type = 'template';
            $table = LM_TABLE_DOC_TEMPLATES;
            break;
        default:
            $errors[] = __('Unknown Document Type');
            break;
    }

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
                    $success[] = __('The selected %s has successfully been deleted.', $type);

                    // Query Error
                } else {
                    $errors[] = __('An error occurred while trying to delete the selected %s.', $type);
                }
            }

            // Query Error
        } else {
            $errors[] = __('An error occurred while locating selected %s.', $type);
        }
    }
}

// Form Tab
$_GET['tab'] = isset($_POST['tab']) ? $_POST['tab'] : $_GET['tab'];
$_GET['tab'] = in_array($_GET['tab'], array('categories', 'documents', 'templates')) ? $_GET['tab'] : 'categories';

// Manage Documents & Categories
if (in_array($_GET['tab'], array('categories', 'documents'))) {
    // Documents (By Category)
    $docs = array();
    $query = "SELECT `c`.`id` AS `cat_id`, `c`.`name` AS `cat_name`, `c`.`agent_id`, `d`.`id` AS `doc_id`, `d`.`name` AS `doc_name`, `d`.`share`"
        . " FROM `" . LM_TABLE_DOC_CATEGORIES . "` `c` LEFT JOIN `" . LM_TABLE_DOCS . "` `d` ON `c`.`id` = `d`.`cat_id`"
        . ($_GET['tab'] === 'documents' && !empty($sql_agent) ? " WHERE (`d`.`share` = 'true' OR " . sprintf($sql_agent, 'c') . ")" : '')
        . ($_GET['tab'] === 'categories' && !empty($sql_agent) ? " WHERE " . sprintf($sql_agent, 'c') : '')
        . " ORDER BY LENGTH(`cat_name`) ASC, `cat_name` ASC, LENGTH(`doc_name`) ASC, `doc_name` ASC;";

    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($result)) {
            $docs[$row['cat_id']]['name'] = $row['cat_name'];
            $docs[$row['cat_id']]['agent_id'] = $row['agent_id'];
            if (!empty($row['doc_id'])) {
                $docs[$row['cat_id']]['docs'][$row['doc_id']] = array(
                    'agent_id' => $row['agent_id'],
                    'doc_name' => $row['doc_name'],
                    'shared' => ($row['share'] === 'true')
                );
            }
        }

        // Query Error
    } else {
        $errors[] = __('Error Occurred while loading Documents.');
    }

// Manage Templates
} else {
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
        $errors[] = __('Error Occurred while loading Templates.');
    }
}
