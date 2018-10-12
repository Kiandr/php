<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Campaigns
if (!$leadsAuth->canManageGroups($authuser)) {
    // Authorized to Manage Own Campaigns
    if (!$leadsAuth->canManageOwnGroups($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage groups.')
        );
    }

    // View Agent: Restrict to Agent Data & Global Events
    if ($authuser->isAgent()) {
        $query_extras = "(`g`.`agent_id` = '" . $authuser->info('id') . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))";
    // View ISA:  Restrict to Associate Data & Global Events
    } elseif ($authuser->isAssociate()) {
        $query_extras = "(`g`.`associate` = '" . $authuser->info('id') . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))";
    }
} else {
    // Get Agent Filter
    $_GET['personal'] = isset($_POST['personal'])
        ? $_POST['personal'] : $_GET['personal'];
    if (isset($_GET['personal'])) {
        if ($authuser->isAssociate()) {
            $query_extras = "(`g`.`associate` = '" . $authuser->info('id') . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))";
        } else {
            $query_extras = "(`g`.`agent_id` = '" . $authuser->info('id') . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))";
        }
    }
}

// Success
$success = array();

// Error
$errors = array();

// Delete a Group
if (!empty($_GET['delete'])) {
    // Delete Group
    $query = "DELETE FROM `g` USING `" . LM_TABLE_GROUPS . "` AS `g` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "'" . (!empty($query_extras) ? " AND " . $query_extras : "") . ";";
    if (mysql_query($query)) {
        $success[] = __('The selected group has successfully been deleted.');
    } else {
        $errors[] = __('An error occurred while trying to delete the selected group.');
    }

    // Save Notices
    $authuser->setNotices($success, $errors);

    // Redirect to Page
    header('Location: ?');
    exit;
}

// Sort Order
$_GET['sort'] = !empty($_GET['sort']) ? mysql_real_escape_string($_GET['sort']) : 'ASC';

// Sort By
$_GET['order'] = !empty($_GET['order']) ? mysql_real_escape_string($_GET['order']) : 'name';
switch ($_GET['order']) {
    case 'leads':
        $sql_order = "`leads` " . $_GET['sort'];
        break;
    case 'agent':
        $sql_order = "`g`.`agent_id` " . $_GET['sort'];
        break;
    case 'color':
        $sql_order = "`g`.`style` " . $_GET['sort'];
        break;
    case 'name':
    default:
        $sql_order = "`g`.`name`" . $_GET['sort'];
        break;
}

// Groups
$groups = array();

// Select Groups
$query = "SELECT *, COUNT(DISTINCT `ug`.`user_id`) AS `leads`"
    . " FROM `" . LM_TABLE_GROUPS . "` `g`"
    . " LEFT JOIN `" . LM_TABLE_USER_GROUPS . "` `ug` ON `g`.`id` = `ug`.`group_id`"
    . (!empty($query_extras) ? " WHERE " . $query_extras : "")
    . " GROUP BY g.`id`"
    . " ORDER BY g.`agent_id` IS NULL DESC"
    . (!empty($sql_order) ? ", " . $sql_order : "")
. ";";

// Build Collection
if ($result = mysql_query($query)) {
    while ($group = mysql_fetch_assoc($result)) {
        // Allowed to Edit Group
        $group['can_edit'] = ($group['user'] == 'true'
            && ($leadsAuth->canManageGroups($authuser)
            || ($authuser->isAgent() && $group['agent_id'] == $authuser->info('id'))
            || ($authuser->isAssociate() && $group['associate'] == $authuser->info('id')))
        );

        // Group Owner: Agent
        if (!empty($group['agent_id'])) {
            $owner = mysql_query("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = '" . $group['agent_id'] . "';");
            $owner = mysql_fetch_assoc($owner);
            $group['owner'] = $owner['name'];

        // Group Owner: Associate
        } elseif (!empty($group['associate'])) {
            $owner = mysql_query("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates` WHERE `id` = '" . $group['associate'] . "';");
            $owner = mysql_fetch_assoc($owner);
            $group['owner'] = $owner['name'];
        }

        // Group Title (for Mouseover)
        $group['title'] = (is_null($group['owner'])
            ? ($group['user'] == 'false' ? '(Global)' : '(Shared)')
            : ($leadsAuth->canManageGroups($authuser) ? '(' . $group['owner'] . ')' : '')
        );

        // Add Group
        $groups[] = $group;
    }

    // Sort by Owner
    if ($_GET['order'] == 'owner') {
        usort($groups, function ($a, $b) {
            $a = strtolower($a['owner']);
            $b = strtolower($b['owner']);
            if ($a === $b) {
                return 0;
            }
            return ($a > $b) ? ($_GET['sort'] == 'ASC' ? 1 : -1) : ($_GET['sort'] == 'ASC' ? -1 : 1);
        });
    }

// Query Error
} else {
    $errors[] = __('Error occurred while loading groups.');
}

// Query String
list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
parse_str($query, $query_string);

// Sort Direction
$url_sort = ($_GET['sort'] == 'DESC') ? 'ASC' : 'DESC';
