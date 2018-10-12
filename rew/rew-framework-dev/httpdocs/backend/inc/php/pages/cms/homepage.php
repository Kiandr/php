<?php

// Get Database
$db = DB::get();

// Create Auth Classes
$settings = Settings::getInstance();

// Get Subdomain being Edited
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageHomepage');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit CMS pages.')
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Load Homepage
try {
    $defaults = $db->fetch("SELECT * FROM " . TABLE_SETTINGS . " WHERE " . $subdomain->getOwnerSql() . ";");
} catch (PDOException $e) {}

/* Throw Missing Default Settings Exception */
if (empty($defaults)) {
    throw new \REW\Backend\Exceptions\MissingSettings\MissingHomepageException();
}

// Skin's Page Templates
$skin = Skin::load();
$templates = $skin->getSelectableTemplates();

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'page_title',    'title' => __('Page Title'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Extra MySQL
    $sql_extra = '';
    $sql_extra_values = [];


    // Handle selected page template
    $template = $_POST['template'];
    if (!empty($template) && !empty($templates[$template])) {
        $sql_extra .= "`template` = :template, ";
        $sql_extra_values["template"] = $template;
        // Require array of page template's variables
        $variables = $_POST['variables'][$template];
        $variables = is_array($variables) ? $variables : array();
        // Execute any current hooks to modify saved page variables
        $variables = Hooks::hook(Hooks::HOOK_CMS_PAGE_VARIABLES_SAVE)->run($variables, $templates[$template]);
        $sql_extra .= "`variables` = :variables, ";
        $sql_extra_values["variables"] = json_encode($variables);
    }

    // Agent Mode
    if (!$subdomain->isPrimary() && Skin::hasFeature(Skin::SUBDOMAIN_FEATURE_IMAGE)) {
        // Agent Feature Photo
        if (isset($_FILES) && count($_FILES) > 0 && is_uploaded_file($_FILES['feature_image']['tmp_name'])) {
            try {
                // Get File Uploader
                $uploader = new Backend_Uploader_Form('feature_image', 'images');
                $uploader->setName($_POST['feature_image']);
                $uploader->handleUpload(DIR_FEATURED_IMAGES);

                $compressor = new REW\Backend\Utilities\ImageCompressor(DIR_FEATURED_IMAGES . $uploader->getName() . '.' . $uploader->getExtension);

                $compressor->compress();

                // Save Image
                $_POST['feature_image'] = $uploader->getName();

            // Error Occurred
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        $sql_extra .= "`feature_image` = :feature_image, ";
        $sql_extra_values["feature_image"] = $_POST['feature_image'];
    }

    // Check Errors
    if (empty($errors)) {
        try {
            // Build UPDATE Query
            $db->prepare("UPDATE `" . TABLE_SETTINGS . "` SET "
                . "`page_title`		= :page_title, "
                . "`meta_tag_desc`	 = :meta_tag_desc, "
                . "`category_html`	 = :category_html, "
                . $sql_extra
                . "`footer`			= :footer"
                . " WHERE " . $subdomain->getOwnerSql() . ";")
            ->execute(array_merge([
                "page_title" => $_POST['page_title'],
                "meta_tag_desc" => $_POST['meta_tag_desc'],
                "category_html" => $_POST['category_html'],
                "footer" => $_POST['footer']
            ], $sql_extra_values));

            // Success
            $success[] = __('CMS Homepage Settings have successfully been saved.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?success' . $subdomain->getPostLink(true));
            exit;

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('CMS Homepage Settings could not be saved, please try again.');
        }
    }
// Delete Featured Image for Subdomain
} elseif (isset($_GET['deleteFeaturedImage']) && !$subdomain->isPrimary() && Skin::hasFeature(Skin::SUBDOMAIN_FEATURE_IMAGE)) {
    try {
        // Delete Previous Image
        if (!empty($defaults['feature_image'])) {
            if (file_exists(DIR_FEATURED_IMAGES . $defaults['feature_image'])) {
                @unlink(DIR_FEATURED_IMAGES . $defaults['feature_image']);
            }
        }

        // Remove image from db
        $removeImage = $db->prepare("UPDATE `" . TABLE_SETTINGS . "` SET"
            . " `feature_image` = :feature_image"
            . " WHERE " . $subdomain->getOwnerSql() . ";");
        $removeImage->execute(['feature_image' => $_POST['feature_image']]);

        // Unset Image
        unset($defaults['feature_image']);

        $success[] = __('Featured Image has been removed.');
    } catch (Exception $e) {
        echo $e->getMessage();
        $errors[] = __('Image deletion failed.');
    }
}

// Set $_POST Data
$defaults['page_title']         = isset($_POST['page_title'])           ? $_POST['page_title']          : $defaults['page_title'];
$defaults['meta_tag_desc']      = isset($_POST['meta_tag_desc'])        ? $_POST['meta_tag_desc']       : $defaults['meta_tag_desc'];
$defaults['category_html']      = isset($_POST['category_html'])        ? $_POST['category_html']       : $defaults['category_html'];
$defaults['footer']             = isset($_POST['footer'])               ? $_POST['footer']              : $defaults['footer'];

// Agent Feature Image
if (!$subdomain->isPrimary() && Skin::hasFeature(Skin::SUBDOMAIN_FEATURE_IMAGE)) {
    $defaults['feature_image'] = isset($_POST['feature_image']) ? $_POST['feature_image'] : $defaults['feature_image'];
}

// Snippets Used on Homepage
preg_match_all("!#([a-zA-Z0-9_-]+)#!", $defaults['category_html'], $matches);
if (!empty($matches)) {
    $defaults['snippets'] = array();
    foreach ($matches[1] as $match) {
        try {
            $snippet = $db->fetch("SELECT `name`, `type` FROM `snippets` WHERE ((`agent` IS NULL AND `team` IS NULL) OR "
                . $subdomain->getOwnerSql()
                . ") AND `name` = :name", ["name" => $match]);
        } catch (PDOException $e) {}

        if (!empty($snippet)) {
            $defaults['snippets'][] = $snippet;
        } else {
            if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES']) && $subdomain->isPrimary()) {
                try {
                    $snippet = $db->fetch("SELECT `id`, `snippet` as `name`, 'Featured Community' AS `type` FROM `" . TABLE_FEATURED_COMMUNITIES . "` WHERE `snippet` = :snippet;", ["snippet" => $match]);
                } catch (PDOException $e) {}

                if (!empty($snippet)) {
                    $defaults['snippets'][] = $snippet;
                }
            }
        }
    }
}

// Open graph images
try {
    $og_image = $db->fetchAll("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "`"
        . $subdomain->getOgQuery() . " ORDER BY `order` ASC;");
} catch (PDOException $e) {
    $og_image = array();
}

