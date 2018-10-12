<?php

// Get Database
$db = DB::get();

// Create Auth Classes
$settings = Settings::getInstance();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManagePages');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit CMS pages.')
    );
}
$subdomain->validateSettings();

$can_delete = $subdomain->getAuth()->canDeletePages();

// Success
$success = array();

// Errors
$errors = array();

// Page ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Select Page
$edit_page    = $db->fetch(
    "SELECT * FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql()
        . " AND `page_id` = :id",
    ['id' => $_GET['id']]
);

// Throw Missing Page Exception
if (empty($edit_page)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingPageException();
}

// Redirect CMS Links
if ($edit_page['is_link'] == 't') {
    header('Location: ' . URL_BACKEND . 'cms/links/edit/?id=' . $edit_page['page_id'] . $subdomain->getPostLink(true));
    exit;
}

// Page URL
$url = $subdomain->getLink();
$link = $url . $edit_page['file_name'].'.php';

// Skin's Page Templates
$skin = Skin::load();
$templates = $skin->getSelectableTemplates();

// Process Submit
if (isset($_GET['submit'])) {
    // Escape HTML
    $_POST['file_name'] = htmlspecialchars($_POST['file_name']);
    $_POST['link_name'] = htmlspecialchars($_POST['link_name']);

    // Required Fields
    $required   = array();
    $required[] = array('value' => 'file_name',  'title' => __('File Name'));
    $required[] = array('value' => 'link_name',  'title' => __('Link Name'));
    $required[] = array('value' => 'page_title', 'title' => __('Page Title'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check Duplicate File Name
    $duplicate = $db->fetch(
        "SELECT `page_id` FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql()
            . " AND `file_name` = :file_name AND `page_id` != :page_id;",
        [
            'file_name' => $_POST['file_name'],
            'page_id' => $edit_page['page_id']
        ]
    );
    if (!empty($duplicate)) {
        $errors[] = __('A page with this file name already exists.');
    }

    // Check Errors
    if (empty($errors)) {
        // Start MySQL Transaction
        $db->beginTransaction();

        // if file_name is changed.. category needs to be updated. also on the subpages.
        $sql = "SELECT category, is_main_cat FROM `" . TABLE_PAGES . "` WHERE "
            . $subdomain->getOwnerSql()
            . " AND `page_id` = :page_id;";
        $cat = $db->fetch($sql, ['page_id' => $edit_page['page_id']]);

        // Change all the subcategories values
        if ($cat['is_main_cat'] == 't') {
            $category = $_POST['file_name'];
            $sqlParams = ['category' => $category, 'old_category' => $cat['category']];
            $sql = "UPDATE `" . TABLE_PAGES . "` SET `category` = :category WHERE "
                . $subdomain->getOwnerSql() . " AND `category` = :old_category";
            $stmt = $db->prepare($sql);
            $stmt->execute($sqlParams);
        } else {
            $category = $cat['category'];
        }

        // Move Page
        if (!empty($_POST['category'])) {
            if ($_POST['category'] != $edit_page['category']) {
                $categy = $db->fetch(
                    "SELECT * FROM `" . TABLE_PAGES . "` WHERE "
                        . $subdomain->getOwnerSql()
                        . " AND `file_name` = :file_name AND `page_id` != :page_id",
                    [
                        'file_name' => $_POST['category'],
                        'page_id' => $edit_page['page_id']
                    ]
                );
                if (!empty($categy)) {
                    // Move the page to be a subpage of $categy

                    // Category Order
                    $category_order = $db->fetch(
                        "SELECT `category_order` AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                            . $subdomain->getOwnerSql() . " AND `file_name` = :file_name",
                        [
                            'file_name' => $categy['file_name']
                        ]
                    );
                    $category_order = $category_order['value'];

                    // Subcategory Order
                    $subcategory_order = $db->fetch(
                        "SELECT MAX(`subcategory_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                            . $subdomain->getOwnerSql() . " AND `category` = :file_name",
                        [
                            'file_name' => $categy['file_name']
                        ]
                    );
                    $subcategory_order = $subcategory_order['value'] + 1;
                    $query = "UPDATE `" . TABLE_PAGES . "` SET `category` = :category,"
                        . " `category_order` = :category_order, `subcategory_order` = :subcategory_order,"
                        . " `is_main_cat` = 'f' WHERE `page_id` = :page_id";
                    $stmt = $db->prepare($query);
                    try {
                        $stmt->execute([
                            'category' => $categy['file_name'],
                            'category_order' => $category_order,
                            'subcategory_order' => $subcategory_order,
                            'page_id' => $edit_page['page_id']
                        ]);

                        $success[] = __(
                            '%s successfully been moved into %s.',
                            '<b>' . $edit_page['link_name'] . '</b>',
                            '<b>' . $categy['link_name'] . '</b>'
                        );
                    } catch (Exception $e) {
                        $errors[] = __('The selected page could not be moved. Please try again.');
                    }
                    $category = $categy['file_name'];
                } else {
                    // Make it a main page

                    // Category Order
                    $category_order = $db->fetch(
                        "SELECT MAX(`category_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                            . $subdomain->getOwnerSql()
                    );
                    $category_order = $category_order['value'] + 1;
                    $stmt = $db->prepare(
                        "UPDATE `" . TABLE_PAGES . "` SET `category` = :category,"
                            . " `category_order` = :category_order, `subcategory_order` = 0, `is_main_cat` = 't'"
                            . " WHERE " . $subdomain->getOwnerSql() . " AND `page_id` = :page_id"
                    );
                    try {
                        $stmt->execute([
                            'category' => $_POST['file_name'],
                            'category_order' => $category_order,
                            'page_id' => $edit_page['page_id']
                        ]);

                        $success[] = __(
                            '%s successfully been set as a Main Page.',
                            '<b>' . $edit_page['link_name'] . '</b>'
                        );
                    } catch (Exception $e) {
                        $errors[] = __('The selected page could not be moved. Please try again.');
                    }
                    $category = $_POST['file_name'];
                }
            }
        }

        // Extra MySQL
        $sqlParams = [];

        // Page Template
        $template = $_POST['template'];
        if (!empty($template) && !empty($templates[$template])) {
            $sqlParams['template'] = $template;
            // Page Variables
            $variables = $_POST['variables'][$template];
            $variables = is_array($variables) ? $variables : array();
            // Execute any current hooks to modify saved page variables
            $variables = Hooks::hook(Hooks::HOOK_CMS_PAGE_VARIABLES_SAVE)->run($variables, $templates[$template]);
            $sqlParams['variables'] = json_encode($variables);
        }

        // ENUM Values
        $_POST['hide']           = ($_POST['hide'] == 't')           ? 't' : 'f';
        $_POST['hide_sitemap']   = ($_POST['hide_sitemap'] == 't')   ? 't' : 'f';

        // Build UPDATE Query
        $sqlParams = array_merge($sqlParams, [
            'file_name' => $_POST['file_name'],
            'category' => $category,
            'link_name' => $_POST['link_name'],
            'page_title' => $_POST['page_title'],
            'hide' => $_POST['hide'],
            'hide_sitemap' => $_POST['hide_sitemap'],
            'meta_tag_desc' => $_POST['meta_tag_desc'],
            'category_html' => $_POST['category_html'],
            'footer' => $_POST['footer'],
            'page_id' => $edit_page['page_id']
        ]);

        $query = "UPDATE `" . TABLE_PAGES . "` SET ";
        foreach ($sqlParams as $field => $value) {
            $query .= "`" . $field . "` = :" . $field . ", ";
        }
        $query = rtrim($query, ", ");
        $query .= " WHERE " . $subdomain->getOwnerSql() . " AND `page_id` = :page_id";
        $stmt = $db->prepare($query);

        // Execute Query
        try {
            $stmt->execute($sqlParams);

            // Action Message
            $success[] = __('CMS Page has successfully been saved.');

            // Commit MySQL Transaction
            $db->commit();

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Edit Form
            header('Location: ?id=' . $edit_page['page_id'] . $subdomain->getPostLink(true) . '&success');
            exit;
        } catch (Exception $e) {
            // Error Occurred
            $errors[] = __('CMS Page could not be saved, please try again.');

            // Rollback MySQL Transaction
            $db->rollBack();
        }
    }

    // Set $_POST Data
    foreach ($edit_page as $k => $v) {
        $edit_page[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
    // Set Page Variables
    if (isset($_POST['variables'][$_POST['template']])) {
        $edit_page['variables'] =  $_POST['variables'][$_POST['template']];
    }
}

// Main Pages
$pages = array();
$pages = $db->fetchAll(
    "SELECT * FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql()
        . " AND `category` = `file_name` AND `is_link` = 'f' AND `file_name`"
        . " NOT IN ('" . implode("', '", unserialize(REQUIRED_PAGES)) . "')"
        . " AND `file_name` != :file_name ORDER BY `link_name`;",
    [
        'file_name' => $edit_page['file_name']
    ]
);

// Locate Sub Pages
$subpages = $db->fetch(
    "SELECT COUNT(`page_id`) AS `total` FROM `" . TABLE_PAGES . "` WHERE "
        . $subdomain->getOwnerSql() . " AND `category` = :file_name AND `category` != `file_name`",
    [
        'file_name' => $edit_page['file_name']
    ]
);
if (!empty($subpages['total'])) {
    $pages = __('This page currently has %s subpages. You must first move the subpages before moving this page.', $subpages['total']);
}

// Snippets Used on this Page
preg_match_all("!#([a-zA-Z0-9_-]+)#!", $edit_page['category_html'], $matches);
if (!empty($matches)) {
    $edit_page['snippets'] = array();
    $snippetStmt = $db->prepare(
        "SELECT `name`, `type` FROM `" . TABLE_SNIPPETS . "` WHERE"
            . $subdomain->getOwnerSql(true) . " AND `name` = :match"
    );
    $communityStmt = $db->prepare(
        "SELECT `id`, `snippet` as `name`, 'Featured Community' AS `type` FROM"
            . " `" . TABLE_FEATURED_COMMUNITIES . "` WHERE `snippet` = :match;"
    );
    foreach ($matches[1] as $match) {
        $snippetStmt->execute(['match' => $match]);
        $snippet = $snippetStmt->fetch();
        if (!empty($snippet)) {
            $edit_page['snippets'][] = $snippet;
        } else {
            if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES']) && $subdomain->isPrimary()) {
                $communityStmt->execute(['match' => $match]);
                $snippet = $communityStmt->fetch();
                if (!empty($snippet)) {
                    $edit_page['snippets'][] = $snippet;
                }
            }
        }
    }
}

// Check if Required Page
$required = (in_array($edit_page['file_name'], (is_array(unserialize(REQUIRED_PAGES)) ? unserialize(REQUIRED_PAGES) : array('404', 'error', 'unsubscribe')))) ? true : false;

// Open graph images
$og_image = $db->fetchAll(
    "SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'page:og:image'"
        . " AND `row` = :row ORDER BY `order` ASC;",
    [
        'row' => $edit_page['page_id']
    ]
);
