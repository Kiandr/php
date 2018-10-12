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

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Show Form */
$show_form = true;

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

    /* Check Errors */
    if (empty($errors)) {
        /* Build INSERT Query */
        $query = "INSERT INTO `" . TABLE_SNIPPETS . "` SET "
               . "`agent` = '" . $agent_id . "', "
               . "`name`  = '" . mysql_real_escape_string($_POST['snippet_id']) . "', "
               . "`code`  = '" . mysql_real_escape_string($code) . "', "
               . "`type`  = 'directory';";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Redirect to Edit Form */
            header('Location: ../edit/?id=' . $_POST['snippet_id'] . '&success=add');

            /* Exit Script */
            exit;
        } else {
            /* Query Error */
            $errors[] = 'Directory Snippet could not be created, please try again.';
        }
    }
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

/* Page Limit */
$_POST['page_limit'] = !empty($_POST['page_limit']) && ($_POST['page_limit'] > 0) ? $_POST['page_limit'] : 6;
