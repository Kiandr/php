<?php

// App DB
$db = DB::get();

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$blogAuth = new REW\Backend\Auth\BlogsAuth(Settings::getInstance());

// Require permission to edit all associates
if (!$blogAuth->canManageEntries($authuser)) {
    // Require permission to edit self
    if (!$blogAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit blog entries.')
        );
    } else {
        // Agent Mode, Only show agent's blog entries
        $sql_agent = "`be`.`agent` = " . (int)$authuser->info('id');
    }
// Filter By Agent
} else if (!empty($_GET['filter'])) {
    // Set Agent Filter
    $filterAgent = Backend_Agent::load($_GET['filter']);
    if (isset($filterAgent) && $filterAgent instanceof Backend_Agent) {
        $sql_agent = "`be`.`agent` = " . (int)$filterAgent->getId();
    }	
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Delete Row */
if (!empty($_POST['delete'])) {

   //force blog-rss-reader to refresh cache
    $index =  Http_Host::getDomainUrl() . 'blog/rss/';
    Cache::deleteCache($index);

    /* Build DELETE Query */
    try {
        $params = ["id" => $_POST['delete']];

        $sql = "DELETE `be` FROM `" . TABLE_BLOG_ENTRIES . "` `be` WHERE `be`.`id` = :id" . (!empty($sql_agent) ? " AND " . $sql_agent : '') . ";";
        $db->prepare($sql)->execute($params);

        /* Success */
        $success[] = __('The selected blog entry has successfully been deleted.');
    } catch (PDOException $e) {
        /* Query Error */
        $errors[] = __('The selected blog entry could not be deleted.');
    }
}

/* Publish Row */
if (!empty($_GET['publish'])) {
    /* Select Row */
    $params = ["id" => $_GET['publish']];

    try {
        $sql = "SELECT `be`.* FROM `" . TABLE_BLOG_ENTRIES . "` `be` WHERE `be`.`id` = :id" . (!empty($sql_agent) ? " AND " . $sql_agent : '') . ";";
        $row = $db->fetch($sql, $params);
    } catch (PDOException $e) {}

   /* Require Row */
   if (!empty($row)) {
       //force blog-rss-reader to refresh cache
       $index =  Http_Host::getDomainUrl() . 'blog/rss/';
       Cache::deleteCache($index);

       /* Build UPDATE Query */
       try {
           $db->prepare("UPDATE `" . TABLE_BLOG_ENTRIES . "` SET `published` = 'true', `timestamp_published` = NOW() WHERE `id` = :id;")->execute(["id" => $row['id']]);

           /* Success */
           $success[] = 'Your blog entry "<a href="' . sprintf(URL_BLOG_ENTRY, $row['link']) . '" target="_blank">' . $row['title'] . '</a>" has successfully been published.';
       } catch (PDOException $e) {
           /* Query Error */
           $errors[] = __('The selected blog entry could not be published.');
       }
   } else {
       /* Error */
       $errors[] = __('The selected blog entry could not be found.');
   }
}

/* Blog Entries */
$entries = array();

/* Default Order */
$_GET['order'] = !empty($_GET['order']) ? $_GET['order'] : 'published';

/* Sort Order */
$_GET['sort'] = in_array($_GET['sort'], array('ASC', 'DESC')) ? $_GET['sort'] : 'DESC';

/* Sort by Column */
switch ($_GET['order']) {
    case 'title':
        $orderBy = 'title';
        $sortDir = $_GET['sort'];
        break;
    case 'author':
        $orderBy = 'author_name';
        $sortDir = $_GET['sort'];
        break;
    case 'published':
    default:
        $orderBy = ['published', 'timestamp_published'];
        $sortDir = [$_GET['sort'], $_GET['sort']];
        break;
}

// Pagination
// Cursor details
$beforeCursor = $_GET['before'];
$afterCursor = $_GET['after'];
$primaryKey = 'id';
$searchLimit = 10;

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

/* Select Rows */
$params = [];

$limit = $pagination->getLimit();
$limitQuery = $limit ? " LIMIT " . $limit : "";
$order = $pagination->getOrder();
$orderQuery = "";
foreach ($order as $sort => $dir) {
    $sortString = $sort . " ";
    // Need to CAST field `published` to a CHAR as it is an enum and can cause ordering issues
    if ($sort === 'published') $sortString = "CAST(" . $sort . " AS CHAR) ";
    $orderQuery .= $sortString . $dir . ", ";
};
$orderQuery = rtrim(" ORDER BY " . $orderQuery, ", ");
$whereColumns = [
    'id'                    => 'be.id',
    'title'                 => 'be.title',
    'published'             => 'be.published',
    'timestamp_published'   => 'be.timestamp_published'
];
$paginationWhere = $pagination->getWhere($whereColumns);
$paramsPagination = $pagination->getParams();
$params = array_merge($params, $paramsPagination);
// Filter by Search
if (isset($_GET['search'])) {
    $sql_search = '';
    if (
        !empty($_GET['title']) ||
        !empty($_GET['first_name']) ||
        !empty($_GET['last_name']) ||
        !empty($_GET['tags'])
    ) {
        $sql_search =
            "(" .
            rtrim(
                (!empty($_GET['title']) ? sprintf("`be`.`title` LIKE '%%%s%%' AND ", $_GET['title']) : '') .
                (!empty($_GET['first_name']) ? sprintf("`a`.`first_name` LIKE '%%%s%%' AND ", $_GET['first_name']) : '') .
                (!empty($_GET['last_name']) ? sprintf("`a`.`last_name` LIKE '%%%s%%' AND ", $_GET['last_name']) : '') .
                (!empty($_GET['tags']) ? sprintf("`be`.`tags` LIKE '%%%s%%'", $_GET['tags']) : '')
                ," AND ") .
            ")";
    }
}
if (!empty($paginationWhere)) {
    $sql_agent = !empty($sql_agent) ? $sql_agent . " AND " . $paginationWhere : $paginationWhere;
}
if (!empty($sql_agent)) {
    if (!empty($sql_search)) {
        $sql_agent .= " AND " . $sql_search;
    }
} else {
    $sql_agent = $sql_search;
}
$query = "SELECT `be`.*, UNIX_TIMESTAMP(`be`.`timestamp_published`) AS `date`, `a`.`id` AS `author_id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `author_name`"
      . " FROM `" . TABLE_BLOG_ENTRIES . "` `be`"
      . " LEFT JOIN `" . TABLE_BLOG_AUTHORS . "` `a` ON `be`.`agent` = `a`.`id`"
      . (!empty($sql_agent) ? " WHERE " . $sql_agent : "")
      . $orderQuery
      . $limitQuery;
$appendFilter = !empty($_GET['order']) ? '&order=' . $_GET['order'] : '';
$appendFilter .= !empty($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';
if (isset($_GET['search'])) {
    $search_filter = '';
    $search_filter .= !empty($_GET['title']) ? '&title=' . urlencode($_GET['title']) : '';
    $search_filter .= !empty($_GET['first_name']) ? '&first_name=' . urlencode($_GET['first_name']) : '';
    $search_filter .= !empty($_GET['last_name']) ? '&last_name=' . urlencode($_GET['last_name']) : '';
    $search_filter .= !empty($_GET['tags']) ? '&tags=' . urlencode($_GET['tags']) : '';
    $search_filter .= !empty($_GET['title']) || !empty($_GET['first_name']) || !empty($_GET['last_name']) || !empty($_GET['tags']) ? '&search=' : '';
}
$appendFilter .= $search_filter;
try {
    /* Build Collection */
    foreach ($db->fetchAll($query, $params) as $manage_entry) {
        /* Add to Collection */
        $entries[] = $manage_entry;
    }
    $pagination->processResults($entries);

    // Remove timestamp_published for unpublished entries
    for ($i = 0; $i < count($entries); $i++) {
        // Unpublished
        if ($entries[$i]['published'] == 'false') {
            unset($entries[$i]['timestamp_published']);
        }
        // Delete Link
        $deleteLink = sprintf('delete=%s%s', $entries[$i]['id'], $appendFilter);
        if (!empty($_GET['after'])) {
            $entries[$i]['deleteLink'] = sprintf('?after=%s&%s', $_GET['after'], $deleteLink);
        } else if (!empty($_GET['before'])) {
            $entries[$i]['deleteLink'] = sprintf('?before=%s&%s', $_GET['before'], $deleteLink);
        } else {
            $entries[$i]['deleteLink'] = sprintf('?%s', $deleteLink);
        }
    }
} catch (PDOException $e) {
    /* Query Error */
    $errors[] = __('Error Occurred while Loading Blog Entries');
}

// Pagination link URLs
$nextLink = $pagination->getNextLink();
$nextLink .= !empty($nextLink) ? $appendFilter : '';
$prevLink = $pagination->getPrevLink();
$prevLink .= !empty($prevLink) ? $appendFilter : '';
$paginationLinks = ['nextLink' => $nextLink, 'prevLink' => $prevLink];

/* Sort Direction */
$url_sort = (($_GET['sort'] == 'DESC') ? 'ASC' : 'DESC');
