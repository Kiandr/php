<?php

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageDirectorySnippets');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to manage directory snippets'
    );
}
$subdomain->validateSettings();

/* Edit Main Site */
$agent_id = 1;

/* Authorized to Delete? */
$can_delete = $subdomainAuth->canDeleteSnippets();

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Row ID */
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

/* Select Row */
$result  = mysql_query("SELECT * FROM `" . TABLE_SNIPPETS . "` WHERE `agent` = '" . $agent_id . "' AND `name` = '" . mysql_real_escape_string($_GET['id']) . "';");
$snippet = mysql_fetch_assoc($result);

/* Throw Missing ID Exception */
if (empty($snippet)) {
    throw new \REW\Backend\Exceptions\MissingId\Directory\MissingSnippetException();
}

// Successful Action
if (!empty($_GET['success'])) {
    if ($_GET['success'] == 'add') {
        $success[] = 'Directory Snippet has successfully been created.';
    } else if ($_GET['success'] == 'copy') {
        $success[] = 'Directory Snippet has successfully been copied.';
    }
}

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'snippet_id',    'title' => 'Snippet Name');
    $required[] = array('value' => 'snippet_title', 'title' => 'Snippet Title');

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    /* Page Limit Between 1 and 48 */
    $page_limit = $_POST['page_limit'];
    if ($page_limit < 1 || $page_limit > 48) {
        $errors[] = 'Page Limit must be a number between 1 and 48.';
    }

    /* Generate Snippet */
    $code = serialize($_POST);

    /* No Errors, Insert New Row */
    if (empty($errors)) {
        /* Build UPDATE Query */
        $query = "UPDATE `" . TABLE_SNIPPETS . "` SET "
               . "`agent` = '" . $agent_id . "', "
               . "`name`  = '" . mysql_real_escape_string($_POST['snippet_id']) . "', "
               . "`code`  = '" . mysql_real_escape_string($code) . "' "
               . " WHERE `name` = '" . $snippet['name'] . "';";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Success */
            $success[] = 'Directory Snippet has successfully been updated.';

            /* Fetch Updated Row */
            $result  = mysql_query("SELECT * FROM `" . TABLE_SNIPPETS . "` WHERE `name` = '" . mysql_real_escape_string($_POST['snippet_id']) . "'");
            $snippet = mysql_fetch_assoc($result);
        } else {
            /* Query Error */
            $errors[] = 'Directory Snippet could not be saved, please try again.';
        }
    }
}

/* Snippet Criteria */
if (!empty($snippet['code'])) {
    $criteria = unserialize($snippet['code']);
}

/* Snippet Criteria (Require Array) */
if (!is_array($criteria)) {
    $criteria = array();
}

/* Search Options */
$options = array();

/* Categories */
$options['categories'] = array();
$directory_categories = mysql_query("SELECT `link`,`title` FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order` ASC, `title` ASC;");
while ($directory_category = mysql_fetch_array($directory_categories)) {
    /* Sub Categories */
    $directory_category['sub_cats'] = array();

    /* Select Rows */
    $sub_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $directory_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

    /* Build Collection */
    while ($sub_category = mysql_fetch_array($sub_categories)) {
        /* Tert Categories */
        $sub_category['tert_cats'] = array();

        /* Select Rows */
        $tert_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $sub_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

        /* Build Collection */
        while ($tert_category = mysql_fetch_array($tert_categories)) {
            /* Add to Collection */
            $sub_category['tert_cats'][] = $tert_category;
        }

        /* Add to Collection */
        $directory_category['sub_cats'][] = $sub_category;
    }

    /* Add to Collection */
    $options['categories'][] = $directory_category;
}

/* Sort Options */
$options['sort'] = array();
$options['sort'][] = array('value' => 'ASC-business_name', 'title' => 'Name, Ascending');
$options['sort'][] = array('value' => 'DESC-business_name',  'title' => 'Name, Descending');
$options['sort'][] = array('value' => 'ASC-primary_category', 'title' => 'Category, Ascending');
$options['sort'][] = array('value' => 'DESC-primary_category',  'title' => 'Category, Descending');

/* Pages using this Snippet */
$snippet['pages'] = array();

/* Check Homepage */
$query = "SELECT `agent` FROM `" . TABLE_SETTINGS . "` WHERE `agent` = '" . $agent_id . "' AND `category_html` LIKE '%#" . mysql_real_escape_string($snippet['name']) . "#%';";
if ($result = mysql_query($query)) {
    $row = mysql_fetch_assoc($result);
    if (!empty($row)) {
        $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/', 'text' => 'Homepage');
    }
} else {
    $errors[] = 'Error Searching Pages using this Snippet';
}

/* Locate Pages */
$query = "SELECT `page_id`, `link_name` FROM `" . TABLE_PAGES . "` WHERE `agent` = '" . $agent_id . "' AND `category_html` LIKE '%#" . mysql_real_escape_string($snippet['name']) . "#%' ORDER BY `link_name` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/pages/edit/?id=' . $row['page_id'], 'text' => $row['link_name']);
    }
} else {
    $errors[] = 'Error Searching Pages using this Snippet';
}
