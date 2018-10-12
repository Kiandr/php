<?php

// Get Database
$db = DB::get();

// Create Auth Classes
$settings = Settings::getInstance();

// Get Subdomain being Edited
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageSnippets');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit CMS snippets.')
    );
}
$subdomain->validateSettings();
$subdomainAuth = $subdomain->getAuth();

// Can Add CMS Snippets
$can_add = $subdomain->getAuth()->canManageSnippets();

// Can Delete Domain CMS Snippets
$can_delete = $subdomain->getAuth()->canDeleteSnippets();

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/**
 * Delete CMS Snippet
 */
if (!empty($_GET['delete'])) {
    /* Authorized? */
    if (!empty($can_delete)) {
        try {
            /* Fetch Row */
            $delete = $db->fetch("SELECT `name`, `type` FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql() . " AND `name` = :name LIMIT 1;", ["name" => $_GET['delete']]);

            if (!empty($delete)) {
                /* Check Snippet Type */
                if (in_array($delete['type'], array('form', 'module', 'old-module'))) {
                    $errors[] = __('You are unable to delete the %s snippet from the CMS.', '<strong>#' . $delete['name'] . '#</strong>');
                } else {
                    try {
                        /* Delete Row */
                        $db->prepare("DELETE FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql() . " AND `name` = :name LIMIT 1;")
                        ->execute(["name" => $delete['name']]);
                        /* Success */
                        $success[] = __('The %s snippet has successfully been deleted.', '<strong>#' . $delete['name'] . '#</strong>');
                    } catch (PDOException $e) {
                        /* Query Error */
                        $errors[] = __('The %s snippet could not be deleted. Please try again.', '<strong>#' . $delete['name'] . '#</strong>');
                    }
                }
            }
        } catch (PDOException $e) {}
    } else {
        /* Permission Error */
        $errors[] = __('You do not have the proper permissions to perform this action.');
    }
}

// Pagination
// Cursor details
$beforeCursor = $_GET['before'];
$afterCursor = $_GET['after'];
$primaryKey = 'id';
$searchLimit = 10;
$orderBy = ['type', 'name'];
$sortDir = ['ASC', 'ASC'];

// Next
if (!empty($afterCursor)) {
    $cursor = REW\Pagination\Cursor\After::decode($afterCursor);

    // Prev
} else if (!empty($beforeCursor)) {
    $cursor = REW\Pagination\Cursor\Before::decode($beforeCursor);

    // First
} else {
    $cursor = new REW\Pagination\Cursor($primaryKey, $searchLimit, $orderBy, $sortDir);

}

// Create pagination instance
$pagination = new REW\Pagination\Pagination($cursor);

/* Default Filter */
$_GET['filter'] = isset($_GET['filter'])  ? $_GET['filter'] : $_SESSION['snippet-filter'];
$_GET['filter'] = !empty($_GET['filter']) ? $_GET['filter'] : 'all';
$_SESSION['snippet-filter'] = $_GET['filter'];

/* Count Snippets */
$snippets = array('cms' => 0, 'idx' => 0, 'form' => 0, 'module' => 0, 'directory' => 0, 'bdx' => 0, 'rt' => 0);
try {
    foreach($db->fetchAll("SELECT `type`, COUNT(`name`) AS `total` FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql(true) . " GROUP BY `type`;") as $count) {
        $snippets[$count['type']] = $count['total'];
    }
} catch (PDOException $e) {}

/* Snippet Filters */
$filters[] = array('href' => $url . 'cms/snippets/?filter=all' . $subdomain->getPostLink(true),    'text' => __('All'),    'count' => array_sum($snippets), 'current' => ($_GET['filter'] == 'all'));
$filters[] = array('href' => $url . 'cms/snippets/?filter=cms' . $subdomain->getPostLink(true),    'text' => __('CMS'),    'count' => $snippets['cms'],     'current' => ($_GET['filter'] == 'cms'));
if (!empty(Settings::getInstance()->MODULES['REW_IDX_SNIPPETS'])) {
    $filters[] = array('href' => $url . 'cms/snippets/?filter=idx' . $subdomain->getPostLink(true),    'text' => __('IDX'),    'count' => $snippets['idx'],     'current' => ($_GET['filter'] == 'idx'));
}
$filters[] = array('href' => $url . 'cms/snippets/?filter=form' . $subdomain->getPostLink(true),   'text' => __('Forms'),   'count' => $snippets['form'],    'current' => ($_GET['filter'] == 'form'));
$filters[] = array('href' => $url . 'cms/snippets/?filter=module' . $subdomain->getPostLink(true), 'text' => __('Modules'), 'count' => $snippets['module'],  'current' => ($_GET['filter'] == 'module'));
// BDX, Directory and RT Snippets Disabled for Phase 1
/*
if ($subdomain->isPrimary() && !empty(Settings::getInstance()->MODULES['REW_DIRECTORY'])) {
    $filters[] = array('href' => $url . 'cms/snippets/?filter=directory' . $subdomain->getPostLink(true), 'text' => 'Directory', 'count' => $snippets['directory'],  'current' => ($_GET['filter'] == 'directory'));
}
if (!empty(Settings::getInstance()->MODULES['REW_BUILDER'])) {
    $filters[] = array('href' => $url . 'cms/snippets/?filter=bdx' . $subdomain->getPostLink(true), 'text' => 'BDX', 'count' => $snippets['bdx'],  'current' => ($_GET['filter'] == 'bdx'));
}
if (!empty(Settings::getInstance()->MODULES['REW_RT'])) {
    $filters[] = array('href' => $url . 'cms/snippets/?filter=rt' . $subdomain->getPostLink(true), 'text' => 'RT', 'count' => $snippets['rt'],  'current' => ($_GET['filter'] == 'rt'));
}
*/

/* Search Filters */
$sql_where = array();
$sql_where_values = array();

/* Agent or Team Filter */
$sql_where[] = $subdomain->getOwnerSql(true);

/* Search Filter */
$filter = $_GET['filter'];
$titles = [
    'cms'       => __('CMS Snippets'),
    'idx'       => __('IDX Snippets'),
    'form'      => __('Form Snippets'),
    'module'    => __('Module Snippets'),
    'directory' => __('Directory Snippets'),
    'bdx'       => __('BDX Snippets'),
    'rt'        => __('Realty Trac Snippets')
];

if(empty($titles[$filter])) {
    $title = __('Snippets');
    $filter = $_GET['filter'] = 'all';
} else {
    $title = $titles[$filter];
    $sql_where[] = "`type` = ?";
    $sql_where_values[] = $filter;
}

// Snippets to Exclude
$exclude = getExcludedSnippets();
if (!empty($exclude)) {
    $sql_where[] = "`name` NOT IN (" . implode(", ", array_fill(0, count($exclude), '?')) . ")";
    $sql_where_values = array_merge($sql_where_values, $exclude);
}

// Snippet Search
if (isset($_GET['search'])) {
    if (!empty($_GET['snippet_name'])) {
        $sql_where[] = "`name` LIKE ?";
        $sql_where_values[] = '%' . Format::slugify($_GET['snippet_name']) . '%';
    }
}

/* SQL WHERE */
$sql_where = !empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '';

try {

    $limit = $pagination->getLimit();
    $limitQuery = $limit ? " LIMIT " . $limit : "";
    $order = $pagination->getOrder();
    $orderQuery = "";
    foreach ($order as $sort => $dir) {
        $sortString = "`" . $sort . "` ";
        // Need to CAST field `type` to a CHAR as it is an enum and will cause ordering issues
        if ($sort === 'type') $sortString = "CAST(`" . $sort . "` AS CHAR) ";
        $orderQuery .= $sortString . $dir . ", ";
    };
    $orderQuery = rtrim(" ORDER BY " . $orderQuery, ", ");
    $paginationWhere = $pagination->getWhere();
    $params = $pagination->getParams();
    $sql_where_values = array_merge($sql_where_values, $params);
    if (!empty($paginationWhere)) {
        $sql_where = !empty($sql_where) ? $sql_where . " AND " . $paginationWhere : " WHERE " . $paginationWhere;
    }

    $snippets = $db->fetchAll("SELECT `id`, `agent`, `team`, `type`, `name`, `locked` FROM `" . TABLE_SNIPPETS . "`" . $sql_where . $orderQuery . $limitQuery . ";", $sql_where_values);

    $pagination->processResults($snippets);

} catch (PDOException $e) {
    /* Query Error */
    $errors[] = __('Error Occurred while loading Snippets.');
}

// Pagination link URLs
$appendFilter = !empty($filter) ? '&filter=' . $filter : '';
$appendFilter .= !empty($subdomain->getPostLink(true)) ? $subdomain->getPostLink(true) : '';
$appendFilter .= !empty($_GET['snippet_name']) ? '&snippet_name=' . urlencode($_GET['snippet_name']) . '&search=' : '';
$nextLink = $pagination->getNextLink();
$nextLink .= !empty($nextLink) ? $appendFilter : '';
$prevLink = $pagination->getPrevLink();
$prevLink .= !empty($prevLink) ? $appendFilter : '';
$paginationLinks = ['nextLink' => $nextLink, 'prevLink' => $prevLink];

// Can Snippets be Edited
$get = $_GET;
$post = $_POST;
$snippets = array_map(function ($snippet) use ($subdomainFactory, $subdomain, $appendFilter) {

    $snippetSubdomain = $subdomainFactory->buildSubdomainFromArray($snippet);
    $snippetSubdomainAuth = $snippetSubdomain->getAuth();

    // Delete Link
    $deleteLink = sprintf('delete=%s%s', $snippet['name'], $appendFilter);
    if (!empty($_GET['after'])) {
        $snippet['deleteLink'] = sprintf('?after=%s&%s', $_GET['after'], $deleteLink);
    } else if (!empty($_GET['before'])) {
        $snippet['deleteLink'] = sprintf('?before=%s&%s', $_GET['before'], $deleteLink);
    } else {
        $snippet['deleteLink'] = sprintf('?%s', $deleteLink);
    }

    switch ($snippet['type']) {
        case 'idx':
            $snippet['can_edit'] = $snippetSubdomainAuth->canManageIDXSnippets();
            break;

        case 'directory':
            $snippet['can_edit'] = $snippetSubdomainAuth->canManageDirectorySnippets();
            break;
        case 'cms':
            $snippet['can_edit'] = ($snippet['locked'] === 'false' || Settings::isREW());
            break;

        case 'module':
            $snippet['can_edit'] = false;
            break;

        default:
            $snippet['can_edit'] = $snippetSubdomainAuth->canManageSnippets();
            $shared = true;

            // Check for shared snippets
            foreach ($subdomainFactory->getTypes() as $subdomainType) {
                if (isset($snippet[$subdomainType])) {
                    // Check perms only if this isn't a shared snippet
                    $shared = false;
                    break;
                }
            }

            // Only allow editing of shared shippets on root domain
            if ($shared && !$subdomain->isPrimary()) {
                // If not (shared snippet), use domain auth to check if we're looking at the root domain.
                $snippet['can_edit'] = false;
            }
    }
    return $snippet;
}, $snippets);

$subdomains = $subdomainFactory->getSubdomainList('canManageSnippets');
