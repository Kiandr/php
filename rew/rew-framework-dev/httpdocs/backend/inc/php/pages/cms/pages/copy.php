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
        __('You do not have permission to copy CMS pages.')
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Show Form
$show_form = true;

// Page ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Site URL
$url = $subdomain->getLink();

// Load Page
try {
    $copy_page = $db->fetch("SELECT * FROM `" . TABLE_PAGES . "` WHERE "
        . $subdomain->getOwnerSql()
        . " AND `page_id` = :page_id;", [
        "page_id" => $_GET['id']
    ]);
} catch (PDOException $e) {}

// Throw Missing Page Exception
if (empty($copy_page)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingPageException();
}

// Cannot Copy Certain Pages
if (in_array($copy_page['file_name'], unserialize(REQUIRED_PAGES))) {
    $errors[] = __('This page cannot be copied.');
    unset($copy_page);
    return;
}

// Process Submit
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Require Valid File Name
    $file_name = Format::slugify($_POST['file_name']);
    if (empty($file_name)) {
        $errors[] = __('You must supply a valid Filename.');
    } else {
        try {
            // Check for Duplicates
            $duplicate = $db->fetch("SELECT `page_id` FROM `" . TABLE_PAGES . "` WHERE "
                . $subdomain->getOwnerSql()
                . " AND `file_name` = :file_name;", [
                "file_name" => $file_name
            ]);

            if (!empty($duplicate)) {
                $errors[] = __('This filename is already taken, try another.');
            }
        } catch(PDOException $e) {}
    }

    // Require Link Name
    if (!Validate::stringRequired($_POST['link_name'])) {
        $errors[] = __('Link Name is a required field.');
    }

    // Require Page Title
    if (!Validate::stringRequired($_POST['page_title'])) {
        $errors[] = __('Page Title is a required field.');
    }

    // Check Errors
    if (empty($errors)) {
        // Main Page
        $is_main_cat = !empty($_POST['category']) ? 'f' : 't';
        if ($is_main_cat == 't') {
            // Category Link
            $category = $_POST['file_name'];

            // Category Order
            try {
                $category_order = $db->fetch("SELECT MAX(`category_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql() . ";");
                $category_order = $category_order['value'] + 1;
            } catch (PDOException $e) {}

            // Subcategory Order
            $subcategory_order = 0;

        // Sub Page
        } else {
            // Category Link
            $category = $_POST['category'];

            // Category Order
            try {
                $category_order = $db->fetch("SELECT `category_order` AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql()
                    . "  AND `file_name` = :file_name;", [
                    "file_name" => $category
                ]);
                $category_order = $category_order['value'];
            } catch (PDOException $e) {}

            // Subcategory Order
            try {
                $subcategory_order = $db->fetch("SELECT MAX(`subcategory_order`) AS `value` FROM `" . TABLE_PAGES . "` WHERE "
                    . $subdomain->getOwnerSql()
                    . " AND `category` = :category;", [
                        "category" => $category
                    ]);
                $subcategory_order = $subcategory_order['value'] + 1;
            } catch (PDOException $e) {}
        }

        // Page Variables
        if (!empty($copy_page['variables'])) {
            try {
                // Page Variables (Decode from JSON)
                $page_variables = json_decode($copy_page['variables'], true);

                // Load Page Instance
                $temp_page = new Page(array('skin' => Settings::getInstance()->SKIN));
                $temp_page->loadTemplate($copy_page['template']);
                $temp_page->loadVariables($copy_page['variables']);

                // Get Template Variables
                $template   = $temp_page->getTemplate();
                $variables  = $template->getVariables();
                if (!empty($variables)) {
                    foreach ($variables as $variable) {
                        // Copy Image
                        if ($variable instanceof Page_Variable_Image) {
                            $upload = $variable->getUpload();
                            if (!empty($upload)) {
                                unset($upload['id'], $upload['order'], $upload['timestamp']);
                                $file = Settings::getInstance()->DIRS['UPLOADS'] . $upload['file'];
                                $copy = Settings::getInstance()->DIRS['UPLOADS'] . mt_rand() . '-' . $upload['file'];
                                if (file_exists($file) && copy($file, $copy)) {
                                    $upload['file'] = basename($copy);
                                    $upload = DB::get('cms')->getCollection('cms_uploads')->insert($upload);
                                    if (!empty($upload)) {
                                        $page_variables[$variable->getName()] = $upload['id'];
                                    }
                                }
                            }
                        }
                    }
                }

                // Execute any current hooks to modify saved page variables
                $page_variables = Hooks::hook(Hooks::HOOK_CMS_PAGE_VARIABLES_SAVE)->run($page_variables, $template);

                // Page Variables (Encode to JSON)
                $copy_page['variables'] = json_encode($page_variables);
            } catch (Exception $e) {
                $errors[] = __('An error occurred while copying page\'s template variables.');
                //$errors[] = $e->getMessage();
            }
        }

        // Execute Query
        try {
            $db->prepare(
                "INSERT INTO `" . TABLE_PAGES . "` SET 
                    `category`			= :category, 
                    `category_order`	= :category_order, 
                    `subcategory_order`	= :subcategory_order, 
                    `is_main_cat`		= :is_main_cat, 
                    `file_name`			= :file_name, 
                    `link_name`			= :link_name, 
                    `page_title`		= :page_title, 
                    " . $subdomain->getAssignSql() ."
                    `meta_tag_desc`		= :meta_tag_desc, 
                    `footer`			= :footer, 
                    `category_html`		= :category_html, 
                    `template`			= :template, 
                    `variables`			= :variables, 
                    `features`			= :features, 
                    `hide`				= :hide, 
                    `hide_sitemap`		= :hide_sitemap, 
                    `timestamp_created`	= NOW();")->execute([
                // Page Level
                "category"          => $category,
                "category_order"    => $category_order,
                "subcategory_order" => $subcategory_order,
                "is_main_cat"       => $is_main_cat,
                // New Page Information
                "file_name"         => $_POST['file_name'],
                "link_name"         => $_POST['link_name'],
                "page_title"        => $_POST['page_title'],
                // Copy Page Information
                "meta_tag_desc"     => $copy_page['meta_tag_desc'],
                "footer"            => $copy_page['footer'],
                "category_html"     => $copy_page['category_html'],
                "template"          => $copy_page['template'],
                "variables"         => $copy_page['variables'],
                "features"          => $copy_page['features'],
                "hide"              => $copy_page['hide'],
                "hide_sitemap"      => $copy_page['hide_sitemap']
            ]);
            // Insert ID
            $page_id = $db->lastInsertId();

            // Success
            $success[] = __('Page has successfully been copied.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $page_id . $subdomain->getPostLink(true) . '&success');
            exit;

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('An error has occurred, Page could not be copied.');
        }
    }
} else {
    // Default $_POST Data
    $_POST['category']      = ($copy_page['category'] != $copy_page['file_name']) ? $copy_page['category'] : false;
    $_POST['file_name']     = $copy_page['file_name'];
    $_POST['link_name']     = $copy_page['link_name'];
    $_POST['page_title']    = $copy_page['page_title'];
}

// Page Level
try {
    $pages = $db->fetchAll("SELECT * FROM `" . TABLE_PAGES . "` WHERE "
        . $subdomain->getOwnerSql()
        . " AND `category` = `file_name` AND `is_link` = 'f' ORDER BY `link_name`");
} catch (PDOException $e) {
    $pages = [];
}

