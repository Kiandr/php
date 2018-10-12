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
        __('You do not have permission to add CMS pages.')
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Category Selected
$_POST['category'] = isset($_GET['category']) ? $_GET['category'] : $_POST['category'];

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
            . " AND `file_name` = :file_name;",
        [
            'file_name' => $_POST['file_name']
        ]
    );
    if (!empty($duplicate)) {
        $errors[] = __('A page with this file name already exists.');
    }

    // Check Errors
    if (empty($errors)) {
        // Main Page or Sub Page
        $is_main_cat = !empty($_POST['category']) ? 'f' : 't';

        // Main Page
        if ($is_main_cat == 't') {
            // Category Order
            $category_order = $db->fetch(
                "SELECT MAX(`category_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql()
            );
            $category_order = $category_order['value'] + 1;

            // Category Link
            $category = $_POST['file_name'];

            // Subcategory Order
            $subcategory_order = 0;

        // Sub Page
        } else {
            // Category Order
            $category_order = $db->fetch(
                "SELECT `category_order` AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql() . " AND `file_name` = :file_name",
                [
                    'file_name' => $_POST['category']
                ]
            );
            $category_order = $category_order['value'];

            // Category Link
            $category = $_POST['category'];

            // Subcategory Order
            $subcategory_order = $db->fetch(
                "SELECT MAX(`subcategory_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql() . " AND `category` = :file_name",
                [
                    'file_name' => $_POST['category']
                ]
            );
            $subcategory_order = $subcategory_order['value'] + 1;
        }

        // Extra MySQL
        $sqlParams = [];

        // Handle selected page template
        $template = $_POST['template'];
        if (!empty($template) && !empty($templates[$template])) {
            $sqlParams['template'] = $template;
            // Require array of page template's variables
            $variables = $_POST['variables'][$template];
            $variables = is_array($variables) ? $variables : array();
            // Execute any current hooks to modify saved page variables
            $variables = Hooks::hook(Hooks::HOOK_CMS_PAGE_VARIABLES_SAVE)->run($variables, $templates[$template]);
            $sqlParams['variables'] = json_encode($variables);
        }

        // ENUM Values
        $_POST['hide']           = ($_POST['hide'] == 't')           ? 't' : 'f';
        $_POST['hide_sitemap']   = ($_POST['hide_sitemap'] == 't')   ? 't' : 'f';

        $sqlParams = array_merge($sqlParams, [
            'file_name' => $_POST['file_name'],
            'link_name' => $_POST['link_name'],
            'page_title' => $_POST['page_title'],
            'hide' => $_POST['hide'],
            'hide_sitemap' => $_POST['hide_sitemap'],
            'meta_tag_desc' => $_POST['meta_tag_desc'],
            'category_html' => $_POST['category_html'],
            'footer' => $_POST['footer'],
            'category' => $category,
            'category_order' => $category_order,
            'subcategory_order' => $subcategory_order,
            'is_main_cat' => $is_main_cat
        ]);

        // Build INSERT Query
        $query = "INSERT INTO `" . TABLE_PAGES . "` SET "
               . $subdomain->getAssignSql();
        foreach ($sqlParams as $field => $value) {
            $query .= "`" . $field . "` = :" . $field . ', ';
        }
        $query = rtrim($query, ', ');
        $stmt = $db->prepare($query);

        // Execute Query
        try {
            $stmt->execute($sqlParams);

            // Success
            $success[] = __('CMS Page has successfully been created.');

            // Insert ID
            $insert_id = $db->lastInsertId();

            // Update og:image records
            if (!empty($_POST['og_image']) && is_array($_POST['og_image'])) {
                $stmt = $db->prepare("UPDATE `" . Settings::getInstance()->TABLES['UPLOADS'] . "`"
                    . " SET `row` = :row"
                    . " WHERE `id` = :id"
                    . ";");

                foreach ($_POST['og_image'] as $og_image) {
                    $stmt->execute([
                        'row' => $insert_id,
                        'id' => $og_image
                    ]);
                }
            }

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $insert_id . $subdomain->getPostLink(true) . '&success');
            exit;

        // Query Error
        } catch (Exception $e) {
            $errors[] = __('Error occurred, CMS Page could not be saved.');
        }
    }
}

// Page Level
$pages = array();
$pages = $db->fetchAll(
    "SELECT * FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql()
        . " AND `category` = `file_name` AND `is_link` = 'f' ORDER BY `link_name`"
);

// Open graph images
$og_image = array();
if (!empty($_POST['og_image']) && is_array($_POST['og_image'])) {
    $query = "SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = :id LIMIT 1;";
    $stmt = $db->prepare($query);

    foreach ($_POST['og_image'] as $og_image_id) {
        if (is_numeric($og_image_id)) {
            $stmt->execute(['id' => $og_image_id]);
            $og_image = array_merge($og_image, $stmt->fetchAll());
        }
    }
}
