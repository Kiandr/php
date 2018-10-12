<?php

// Get Database
$db = DB::get();

// Create Auth Classes
$settings = Settings::getInstance();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageSnippets');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit CMS snippets.')
    );
}
$subdomain->validateSettings();

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Show Form */
$show_form = true;

/* Listing ID */
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

/* Get Selected Row */
try {
$snippet  = $db->fetch("SELECT * 
                        FROM `" . TABLE_SNIPPETS . "` 
                        WHERE " . $subdomain->getOwnerSql(true) . "
                          AND `name` = :name;", [
        "name" => $_GET['id']
]);
} catch (PDOException $e) {}


// Throw Missing Page Exception
if (empty($snippet)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingSnippetException();
}

// Redirect IDX Snippets
if ($snippet['type'] == 'idx') {
    header("Location: " . URL_BACKEND . 'idx/snippets/edit/?id=' . $snippet['name'] . $subdomain->getPostLink(true) . (!empty($_GET['success']) ? '&success=' . $_GET['success'] : ''));
    exit;
// Redirect Directory Snippets
} else if ($snippet['type'] == 'directory') {
    header("Location: " . URL_BACKEND . 'directory/snippets/edit/?id=' . $snippet['name'] . $subdomain->getPostLink(true) . (!empty($_GET['success']) ? '&success=' . $_GET['success'] : ''));
    exit;
// Non-Editable
} else if (in_array($snippet['type'], array('module'))) {
    $errors[] = __('This snippet is not editable from the CMS.');
    unset($snippet);
    return;
// Redirect BDX Snippets
} else if ($snippet['type'] == 'bdx') {
    header("Location: " . URL_BACKEND . 'bdx/snippets/edit/?id=' . $snippet['name'] . $subdomain->getPostLink(true) . (!empty($_GET['success']) ? '&success=' . $_GET['success'] : ''));
    exit;
}

// Locate framework snippet file
$framework_snippet = Installer::getSnippet($snippet['name'], $_GET['agent'] > 1);

// Allowed to revert framework snippets
$can_revert = !empty($framework_snippet);

/* Process Submit */
if (isset($_GET['saveSnippet'])) {
    /* Revert Snippet to Original */
    if (!empty($_POST['revert']) && $can_revert) {
        /* Get Snippet Code */
        $code = file_get_contents($framework_snippet);

        /* Build UPDATE Query */
        try {
            $db->prepare("UPDATE `" . TABLE_SNIPPETS . "` SET "
                   . "`code`  = :code "
                   . " WHERE " . $subdomain->getOwnerSql(true)
                   . " AND `name` = :name;")
            ->execute([
                "code" => $code,
                "name" => $snippet['name']
            ]);

            // Trigger hook on successful snippet save
            Hooks::hook(Hooks::HOOK_CMS_SNIPPET_SAVED)->run(array(
                'name' => $snippet['name'],
                'code' => $code
            ), $snippet);

            // Success
            $success[] = __('#%s# has successfully been reverted.', $snippet['name']);

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?id=' . $snippet['name'] . $subdomain->getPostLink(true) . '&success');
            exit;
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('#%s# could not be reverted.', $snippet['name']);
        }

    /* Save Changes */
    } else {
        /* Required Fields */
        $required   = array();
        $required[] = array('value' => 'snippet_id', 'title' => 'Snippet Name');

        /* Process Required Fields */
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Snippet information
        $snippet_name = $_POST['snippet_id'];
        $snippet_code = $_POST['code'];

        /* Check Duplicate Rows */
        try {
            $duplicate = $db->fetch("SELECT `name` FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql(true)
            . " AND `name` = :name_new AND `name` != :name_old",[
                "name_new" =>  $snippet_name,
                "name_old" => $snippet['name']
            ]);

            if (!empty($duplicate)) {
                $errors[] = __('A snippet with this name already exists.');
            }
        } catch (PDOException $e) {}

        try {
            // Trigger hook to validate snippet before saving
            Hooks::hook(Hooks::HOOK_CMS_SNIPPET_VALIDATE)->run(array(
                'name' => $snippet_name,
                'code' => $snippet_code
            ), $snippet);

        // Hook threw validation error
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        /* Check Errors */
        if (empty($errors)) {
            try {
                /* Build UPDATE Query */
                $db->prepare("UPDATE `" . TABLE_SNIPPETS . "` SET "
                       . "`name`  = :name_new, "
                       . "`code`  = :code "
                       . " WHERE " . $subdomain->getOwnerSql(true) . " AND `name` = :name_old;")
                ->execute([
                    "name_new" =>  $snippet_name,
                    "code"     =>  $snippet_code,
                    "name_old" =>  $snippet['name']
                ]);

                // Trigger hook on successful snippet save
                Hooks::hook(Hooks::HOOK_CMS_SNIPPET_SAVED)->run(array(
                    'name' => $snippet_name,
                    'code' => $snippet_code
                ), $snippet);

                // Success
                $success[] = __('#%s# has successfully been updated.', $snippet['name']);

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect Back to Form
                header('Location: ?id=' . $snippet_name . $subdomain->getPostLink(true) . '&success');
                exit;
            } catch (PDOException $e) {
                /* Query Error */
                $errors[] = __('#%s# could not be saved, please try again.', $snippet['name']);
            }
        }
    }
}

/* Pages using this Snippet */
$snippet['pages'] = array();

/* Check Homepage */
try {
    $row = $db->fetch("SELECT `agent` FROM `" . TABLE_SETTINGS . "` WHERE " . $subdomain->getOwnerSql() . " AND `category_html` LIKE :category_html;", [
        "category_html" => "%#" . $snippet['name'] . "#%"
    ]);

    if (!empty($row)) {
        $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/' . $subdomain->getPostLink(), 'text' => 'Homepage');
    }
} catch (PDOException $e) {
    $errors[] = __('Error Searching Pages using this Snippet');
}

/* Locate Pages */
try {
    foreach($db->fetchAll("SELECT `page_id`, `link_name` FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql() . " AND `category_html` LIKE :category_html ORDER BY `link_name` ASC;", ["category_html" => "%#" . $snippet['name'] . "#%"]) as $row) {
        $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/pages/edit/?id=' . $row['page_id'] . $subdomain->getPostLink(true), 'text' => $row['link_name']);
    }
} catch (PDOException $e) {
    $errors[] = __('Error Searching Pages using this Snippet');
}
