<?php

// App DB
$db = DB::get();

// Get Authorization Managers
$settings = Settings::getInstance();
$agentsAuth = new REW\Backend\Auth\AgentsAuth($settings);

// Authorized to Email Leads
if (!$agentsAuth->canViewAgents($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view agents.')
    );
}

// Authorized to Email Agents
$can_email = $agentsAuth->canEmailAgents($authuser);

// Success
$success = array();

// Errors
$errors = array();

// Can manage all agents
if ($agentsAuth->canManageAgents($authuser)) {
    // Authorized to add new agents
    $canAdd = true;

    // Display Columns
    $display = array();
    $display['login']          = array('title' => __('Last Logged In'), 'selector' => 'th.login, td.login');
    $display['leads']          = array('title' => __('Assigned Leads'), 'selector' => 'th.leads, td.leads');
    $display['leads-accepted'] = array('title' => __('Accepted Leads'), 'selector' => 'th.leads-accepted, td.leads-accepted');
    $display['leads-pending']  = array('title' => __('Pending Leads'),  'selector' => 'th.leads-pending, td.leads-pending');
    $display['auto_assign']    = array('title' => __('Auto Assign'),    'selector' => 'th.auto_assign, td.auto_assign');
    $display['auto_rotate']    = array('title' => __('Auto Rotate'),    'selector' => 'th.auto_rotate, td.auto_rotate');
    $display['auto_optout']    = array('title' => __('Auto Optout'),    'selector' => 'th.auto_optout, td.auto_optout');

    // Active Columns
    $active = $authuser->info('columns_agents');
    $active = !empty($active) ? explode(',', $active) : array('leads', 'login');

    // AJAX Request - Save Display Columns
    if (isset($_POST['ajax']) && isset($_GET['save'])) {
        // Columns
        $columns = array();

        // Ensure Valid Columns
        $cols = Format::htmlspecialchars($_POST['columns']);
        $cols = is_array($cols) ? $cols : explode(',', $cols);
        foreach ($cols as $col) {
            if (!empty($display[$col])) {
                $columns[] = $col;
            }
        }

        // Stringify
        $columns = implode(',', $columns);

        // Save Changes
        try {
            $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET `columns_agents` = :columns_agents WHERE `id` = :id;")
            ->execute([
                'columns_agents' => $columns,
                'id' => $authuser->info('id')
            ]);
        } catch (PDOException $e) {
            echo 'Error Updating Columns';
        }

        exit;
    }
}

// Filter by Office
try {
    $office = $db->fetchAll("SELECT `id`, `title` FROM `" . TABLE_FEATURED_OFFICES . "` WHERE `id` = :id;", ['id' => $_GET['office']]);
} catch (PDOException $e) {}

// Search Criteria
$sql_where = array();
$sql_where_vals = [];
$search_criteria = array();

// Only Show Super Admin if in Admin Mode & is Super Admin
if (!$agentsAuth->canManageSuperAgent($authuser)) {
    $sql_where[] = "a.`id` > 1";
}

// Filter by Office
if (!empty($office)) {
    $sql_where[] = "`a`.`office` = :office";
    $sql_where_vals['office'] = $office['id'];
}

// Filter Agents
if (!empty($_GET['filter'])) {
    if ($_GET['filter'] == 'auto_assign') {
        $search_criteria[] = 'Lead Auto-Assignment';
        $sql_where[] = "`a`.`auto_assign_admin` = 'true' AND `a`.`auto_assign_agent` = 'true'";
    } elseif ($_GET['filter'] == 'auto_rotate') {
        $search_criteria[] = 'In Lead Auto-Rotation';
        $sql_where[] = "`a`.`auto_rotate` = 'true' AND `a`.`auto_assign_agent` = 'true'";
    } elseif ($_GET['filter'] == 'auto_optout') {
        $search_criteria[] = 'In Automated Agent Opt-Out';
        $sql_where[] = "`a`.`auto_optout` = 'true' AND ((`a`.`auto_assign_admin` = 'true' AND `a`.`auto_assign_agent` = 'true') OR `a`.`auto_rotate` = 'true')";
    }
}

// Search by Letter
$sql_letter = '';
if (!empty($_GET['letter'])) {
    $_GET['letter'] = ($_GET['letter'] == 'num') ? '#' : $_GET['letter'];
    $sql_letter = "`a`.`last_name` LIKE :letter";
    $sql_where_vals['letter'] = $_GET['letter'] . "%";
}

// Search Query
$sql_where = !empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '';

// Alpha Bar Letters
$letters = array();
try {
    foreach($db->fetchAll("SELECT UPPER(SUBSTR(`a`.`last_name`, 1, 1)) AS `letter` FROM `" . LM_TABLE_AGENTS . "` `a`" . $sql_where . " GROUP BY `letter` ORDER BY `letter` ASC;", $sql_where_vals) as $letter) {
        if (!empty($letter['letter'])) {
            $letters[] = $letter['letter'];
        }
    }

} catch (PDOException $e) {}

// Filter by Letter
if (!empty($sql_letter)) {
    $sql_where .= (!empty($sql_where) ? " AND " : " WHERE ") . $sql_letter;
}

// Filter by Search
if (isset($_GET['search'])) {
    $sql_search = '';
    if (
        !empty($_GET['first_name']) ||
        !empty($_GET['last_name'])
    ) {
        $searchString = '';
        if (!empty($_GET['first_name'])) {
            $searchString .= "`a`.`first_name` LIKE :first_name AND ";
            $sql_where_vals['first_name'] = '%' . $_GET['first_name'] . '%';
        }
        if (!empty($_GET['last_name'])) {
            $searchString .= "`a`.`last_name` LIKE :last_name AND ";
            $sql_where_vals['last_name'] = '%' . $_GET['last_name'] . '%';
        }
        $sql_search = "(" . rtrim($searchString," AND ") . ")";
        $sql_where .= (!empty($sql_where) ? " AND " : " WHERE ") . $sql_search;
    }
}

// Total Agents
try {
    $total = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . LM_TABLE_AGENTS . "` `a`;");
} catch (PDOException $e) {}

// Search Count
try {
    $count = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . LM_TABLE_AGENTS . "` `a`" . $sql_where, $sql_where_vals);
} catch (PDOException $e) {}

// Query String
list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
parse_str($query, $query_string);

// Sort Order
$_GET['sort']  = ($_GET['sort'] === 'ASC') ? 'ASC' : 'DESC';
$_GET['order'] = !empty($_GET['order']) ? $_GET['order'] : 'name';
switch ($_GET['order']) {
    case 'title':
        $sql_order = " ORDER BY `a`.`title` " . $_GET['sort'];
        break;
    case 'leads':
        $sql_order = " ORDER BY `leads` " . $_GET['sort'];
        break;
    case 'login':
        $sql_order = " ORDER BY `date` " . $_GET['sort'];
        break;
    case 'leads-accepted':
        $sql_order = " ORDER BY `leads-accepted` " . $_GET['sort'];
        break;
    case 'leads-pending':
        $sql_order = " ORDER BY `leads-pending` " . $_GET['sort'];
        break;
    case 'auto_assign':
        $sql_order = " ORDER BY `auto_assign_agent` " . $_GET['sort'] . " , `auto_assign_admin` " . $_GET['sort'];
        break;
    case 'auto_rotate':
        $sql_order = " ORDER BY `auto_assign_agent` " . $_GET['sort'] . " , `auto_rotate` " . $_GET['sort'];
        break;
    case 'auto_optout':
        $sql_order = " ORDER BY `auto_optout` " . $_GET['sort'];
        break;
    case 'name':
    default:
        $sql_order = " ORDER BY `a`.`last_name` " . $_GET['sort'] . ", `a`.`first_name` " . $_GET['sort'];
        break;
}

// Manage Agents
$agents = array();

// Check Count
if (!empty($count['total'])) {
    // SQL Limit
    $page_limit = 25;
    if ($count['total'] > $page_limit) {
        $limitvalue = (($_GET['p'] - 1) * $page_limit);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
    }

    // Pagination
    $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

    // Select Agents
    try {
        $agents = $db->fetchAll("SELECT `a`.*"
        . ", COUNT(`u`.`id`) as `leads`"
        . ", COUNT(IF(`u`.`status` = 'accepted', 1, NULL)) AS `leads-accepted`"
        . ", COUNT(IF(`u`.`status` = 'pending', 1, NULL)) AS `leads-pending`"
        . ", UNIX_TIMESTAMP(`auth`.`last_logon`) AS `date`"
        . " FROM `" . LM_TABLE_AGENTS . "` `a`"
        . " LEFT JOIN `" . LM_TABLE_LEADS . "` `u` ON `u`.`agent` = a.`id`"
        . " LEFT JOIN `" . Auth::$table . "` `auth` ON `a`.`auth` = `auth`.`id`"
        . $sql_where . " GROUP BY a.`id`" . $sql_order . $sql_limit . ";", $sql_where_vals);
    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while loading agents.');
    }
}

// Sort Direction
$url_sort = (($_GET['sort'] == 'DESC') ? 'ASC' : 'DESC');
