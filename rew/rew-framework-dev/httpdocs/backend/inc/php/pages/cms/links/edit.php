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
        'You do not have permission to edit CMS links.'
    );
}
$subdomain->validateSettings();

// Success Collection
$success = array();

// Error Collection
$errors = array();

// Link ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

$selectStmt = $db->prepare(
    "SELECT * FROM `" . TABLE_PAGES . "` WHERE "
        . $subdomain->getOwnerSql()
        . " AND `page_id` = :pageId AND `is_link` = 't'"
);

// Select Row
$selectStmt->execute(
    ['pageId' => $_GET['id']]
);
$edit_link = $selectStmt->fetch();

// Throw Missing Page Exception
if (empty($edit_link)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLinkException();
}

// Require Row
if (!empty($edit_link)) {
    // New Row Successful
    if (!empty($_GET['success']) && $_GET['success'] == 'add') {
        $success[] = 'CMS Link has successfully been created.';
    }

    // Process Submit
    if (isset($_GET['submit'])) {
        // Escape HTML
        $_POST['file_name'] = htmlspecialchars($_POST['file_name']);
        $_POST['link_name'] = htmlspecialchars($_POST['link_name']);

        // Required Fields
        $required   = array();
        $required[] = array('value' => 'file_name',  'title' => 'Link URL');
        $required[] = array('value' => 'link_name',  'title' => 'Link Name');

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = $require['title'] . ' is a required field.';
            }
        }

        // Move Page
        if (!empty($_POST['category'])) {
            if ($_POST['category'] != $edit_link['category']) {
                $categy = $db->fetch(
                    "SELECT * FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql()
                    . " AND `file_name` = :category AND `page_id` != :pageId",
                    ['category' => $_POST['category'], 'pageId' => $edit_link['page_id']]
                );

                $updateCategoryStmt = $db->prepare(
                    "UPDATE `" . TABLE_PAGES . "`"
                    . " SET `category` = :category, `category_order` = :categoryOrder,"
                    . " `subcategory_order` = :subCategoryOrder, `is_main_cat` = :isMainCat WHERE "
                    . $subdomain->getOwnerSql()
                    . " AND `page_id` = :pageId"
                );

                if (!empty($categy)) {
                    // Move the page to be a subpage of $category

                    // Category Order
                    $category_order = $db->fetch(
                        "SELECT `category_order` AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                        . $subdomain->getOwnerSql()
                        . " AND `file_name` = :fileName;",
                        ['fileName' => $categy['file_name']]
                    );
                    $category_order = $category_order['value'];

                    // Subcategory Order
                    $subcategory_order = $db->fetch(
                        "SELECT MAX(`subcategory_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                        . $subdomain->getOwnerSql()
                        . " AND `category` = :category;",
                        ['category' => $categy['file_name']]
                    );
                    $subcategory_order = $subcategory_order['value'] + 1;

                    // Build UPDATE Query
                    try {
                        $updateCategoryStmt->execute([
                            'category' => $categy['file_name'],
                            'categoryOrder' => $category_order,
                            'subCategoryOrder' => $subcategory_order,
                            'pageId' => $edit_link['page_id'],
                            'isMainCat' => 'f',
                        ]);

                        // Success
                        $success[] = '<b>' . $edit_link['link_name'] . '</b> successfully been moved into <b>' . $categy['link_name'] . '</b>.';
                    } catch (PDOException $e) {
                        // Error
                        $errors[] = 'The selected link could not be moved. Please try again.';
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

                    // Build UPDATE Query
                    $stmt = $db->prepare(
                        "UPDATE `" . TABLE_PAGES . "`"
                        . " SET `category` = :category, `category_order` = :categoryOrder,"
                        . " `subcategory_order` = 0, `is_main_cat` = 'f' WHERE "
                        . $subdomain->getOwnerSql()
                        . " AND `page_id` = :pageId"
                    );

                    try {
                        $updateCategoryStmt->execute([
                            'category' => $_POST['file_name'],
                            'categoryOrder' => $category_order,
                            'subCategoryOrder' => 0,
                            'pageId' => $edit_link['page_id'],
                            'isMainCat' => 't',
                        ]);

                        // Success
                        $success[] = '<b>' . $edit_link['link_name'] . '</b> successfully been set as a Main Link.';
                    } catch (PDOException $e) {
                        // Error
                        $errors[] = 'The selected link could not be moved. Please try again.';
                    }

                    $category = $_POST['file_name'];
                }
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Category seems to sometimes not be set above so try get from request..
            $category = empty($category)? $_POST['category'] : $category;

            // Require ENUM
            $_POST['hide'] = ($_POST['hide'] == 't') ? 't' : 'f';

            // Build UPDATE Query
            $stmt = $db->prepare(
                "UPDATE `" . TABLE_PAGES . "` SET "
                    . "`file_name` = :fileName, "
                    . "`category`  = :category, "
                    . "`link_name` = :linkName, "
                    . "`footer`    = :footer"
                    . " WHERE "
                    . $subdomain->getOwnerSql()
                    . " AND `page_id` = :pageId AND `is_link` = 't';"
            );

            try {
                $stmt->execute([
                    'fileName' => $_POST['file_name'],
                    'category' => $category,
                    'linkName' => $_POST['link_name'],
                    'footer' => $_POST['footer'],
                    'pageId' => $edit_link['page_id']
                ]);

                // Success
                $success[] = 'CMS Link has successfully been updated.';

                // Fetch Updated Row
                $selectStmt->execute(
                    ['pageId' => $edit_link['page_id']]
                );
                $edit_link = $selectStmt->fetch();
            } catch (PDOException $e) {
                // Error
                $errors[] = 'CMS Link could not be updated, please try again.';
            }
        }
    }

    // Use $_POST
    $edit_link['file_name'] = isset($_POST['file_name']) ? $_POST['file_name'] : $edit_link['file_name'];
    $edit_link['link_name'] = isset($_POST['link_name']) ? $_POST['link_name'] : $edit_link['link_name'];
    $edit_link['footer']    = isset($_POST['footer'])    ? $_POST['footer']    : $edit_link['footer'];

    // Page Level
    $pages = $db->fetchAll(
        "SELECT * FROM `" . TABLE_PAGES . "` WHERE "
            . $subdomain->getOwnerSql()
            . " AND `category` = `file_name` AND `is_link` = 'f' AND `file_name` != :fileName"
            . " ORDER BY `category_order`",
        ['fileName' => $edit_link['file_name']]
    );

    $subpages = $db->fetch(
        "SELECT COUNT(`page_id`) AS `total` FROM `" . TABLE_PAGES . "` WHERE "
        . $subdomain->getOwnerSql()
        . " AND `category` = :category AND `category` != `file_name`",
        ['category' => $edit_link['file_name']]
    );

    if (!empty($subpages['total'])) {
        $pages = 'This link currently has ' . $subpages['total'] . ' subpages. You must first move the subpages before moving this link.';
    }
}
