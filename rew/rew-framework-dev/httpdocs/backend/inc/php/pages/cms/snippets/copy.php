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
        __('You do not have permission to copy CMS snippets.')
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Show Form
$show_form = true;

// Snippet ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Load Snippet
try {
    $snippet = $db->fetch("SELECT * FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql(true)
        . " AND `name` = :name;", ["name" => $_GET['id']]);
} catch (PDOException $e) {}

// Throw Missing Snippet Exception
if (empty($snippet)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingSnippetException();
}

// Require Snippet
if (!empty($snippet)) {
    // Restrict Snippet Type
    if (in_array($snippet['type'], array('module', 'form'))) {
        $errors[] = __('This type of snippet cannot be copied.');
        unset($snippet);
        return;
    }

    // Process Submit
    if (isset($_GET['submit'])) {
        // Require Valid Snippet Name
        $name = Format::slugify($_POST['snippet_id']);
        if (empty($name)) {
            $errors[] = __('You must supply a valid Snippet Name.');
        } else {
            // Check for Duplicates
            try {
                $duplicate = $db->fetch("SELECT `id` FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql(true)
                    . " AND `name` = :name;", ["name" => $name]);
                if (!empty($duplicate)) {
                    $errors[] = __('A snippet with this name already exists.');
                }
            } catch (PDOException $e) {}
        }

        try {
            // Trigger hook to validate snippet before saving
            Hooks::hook(Hooks::HOOK_CMS_SNIPPET_VALIDATE)->run(array(
                'name' => $name,
                'code' => $snippet['code']
            ), null);

        // Hook threw validation error
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        // Check Errors
        if (empty($errors)) {
            try {
                // Build INSERT Query
                $db->prepare("INSERT INTO `" . TABLE_SNIPPETS . "` SET "
                    . $subdomain->getAssignSql()
                    . "`name`  = :name, "
                    . "`code`  = :code, "
                    . "`type`  = :type;")
                ->execute([
                    "name" => $name,
                    "code" => $snippet['code'],
                    "type" => $snippet['type']
                ]);

                // Trigger hook on CMS snippet save
                if ($snippet['type'] === 'cms') {
                    Hooks::hook(Hooks::HOOK_CMS_SNIPPET_SAVED)->run(array(
                        'name' => $name,
                        'code' => $snippet['code']
                    ), $snippet);
                }

                // Insert ID
                $insert_id = $db->lastInsertId();

                // Success
                $success[] = __('Snippet has successfully been copied.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                header('Location: ../edit/?id=' . $name . $subdomain->getPostLink(true));
                exit;

            // Query Error
            } catch (PDOException $e) {
                $errors[] = __('An error has occurred, Snippet could not be copied.');
            }
        }
    } else {
        // Default Name
        $name = $snippet['name'];
    }
}
