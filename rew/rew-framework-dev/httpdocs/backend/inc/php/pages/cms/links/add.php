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
        'You do not have permission to add CMS links.'
    );
}
$subdomain->validateSettings();

// Success Collection
$success = array();

// Error Collection
$errors = array();

// Use $_POST or $_GET
$_POST['category'] = isset($_POST['category']) ? $_POST['category'] : $_GET['category'];

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
            $errors[] = $require['value'] . ' is a required field.';
        }
    }

    // Check Errors
    if (empty($errors)) {
        $is_main_cat = !empty($_POST['category']) ? 'f' : 't';

        // Main Link
        if ($is_main_cat == 't') {
            // Category Order
            $category_order = $db->fetch(
                "SELECT MAX(`category_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                . $subdomain->getOwnerSql() . ";"
            );
            $category_order = $category_order['value'] + 1;

            $category = $_POST['file_name'];

            $subcategory_order = 0;

        // Sub Link
        } else {
            // Category Order
            $category_order = $db->fetch(
                "SELECT `category_order` AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql()
                    . " AND `file_name` = :fileName;",
                ['fileName' => $_POST['category']]
            );
            $category_order = $category_order['value'];

            $category = $_POST['category'];

            // Subcategory Order
            $subcategory_order = $db->fetch(
                "SELECT MAX(`subcategory_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                . $subdomain->getOwnerSql()
                . " AND `category` = :category;",
                ['category' => $_POST['category']]
            );
            $subcategory_order = $subcategory_order['value'] + 1;
        }

        // Check Input
        $_POST['hide'] = ($_POST['hide'] == 't') ? 't' : 'f';

        // Build INSERT Query
        $stmt = $db->prepare(
            "INSERT INTO `" . TABLE_PAGES . "` SET "
                . $subdomain->getAssignSql()
                . "`file_name`         = :fileName, "
                . "`link_name`         = :linkName, "
                . "`footer`            = :footer, "
                . "`category`          = :category, "
                . "`category_order`    = :categoryOrder, "
                . "`subcategory_order` = :subCategoryOrder, "
                . "`is_main_cat`       = :isMainCat, "
                . "`is_link`           = 't', "
                . "`hide`              = 'f'"
        );

        try {
            $stmt->execute([
                'fileName' => $_POST['file_name'],
                'linkName' => $_POST['link_name'],
                'footer' => $_POST['footer'],
                'category' => $category,
                'categoryOrder' => $category_order,
                'subCategoryOrder' => $subcategory_order,
                'isMainCat' => $is_main_cat
            ]);

            $insert_id = $db->lastInsertId();

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $insert_id . $subdomain->getPostLink(true) . '&success=add');

            // Exit Script
            exit;
        } catch (PDOException $e) {
            // Error
            $errors[] = 'Error occurred, CMS Link could not be created.';
        }
    }
}

// Link Level
$pages = $db->fetchAll(
    "SELECT * FROM `" . TABLE_PAGES . "` WHERE "
    . $subdomain->getOwnerSql()
    . " AND `category` = `file_name` AND `is_link` = 'f' ORDER BY `link_name`"
);
