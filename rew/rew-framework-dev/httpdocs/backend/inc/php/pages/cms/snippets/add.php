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
        __('You do not have permission add CMS snippets.')
    );
}
$subdomain->validateSettings();

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'snippet_id', 'title' => __('Snippet Name'));

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Snippet information
    $snippet_name = $_POST['snippet_id'];
    $snippet_code = $_POST['code'];

    /* Check Duplicate */
    try {
        $duplicate = $db->fetch("SELECT `name` FROM `" . TABLE_SNIPPETS . "` WHERE ("
            . $subdomain->getOwnerSql()
            . " OR (`agent` IS NULL AND `team` IS NULL)) AND `name` = :name;", ["name" => $snippet_name]);
        if (!empty($duplicate)) {
            $errors[] = __('A snippet with this name already exists.');
        }
    } catch (PDOException $e) {}

    try {
        // Trigger hook to validate snippet before saving
        Hooks::hook(Hooks::HOOK_CMS_SNIPPET_VALIDATE)->run(array(
            'name' => $snippet_name,
            'code' => $snippet_code
        ), null);

    // Hook threw validation error
    } catch (InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
    }

    /* Check Errors */
    if (empty($errors)) {
        try {
            /* Build INSERT Query */
            $db->prepare("INSERT INTO `" . TABLE_SNIPPETS . "` SET "
                   . $subdomain->getAssignSql()
                   . "`name`  = :name, "
                   . "`code`  = :code, "
                   . "`type`  = 'cms';")
            ->execute([
                "name" => $snippet_name,
                "code" => $snippet_code,
            ]);

            // Trigger hook on successful snippet save
            Hooks::hook(Hooks::HOOK_CMS_SNIPPET_SAVED)->run(array(
                'name' => $snippet_name,
                'code' => $snippet_code
            ), null);

            // Success
            $success[] = __('CMS Snippet has successfully been created.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $snippet_name . $subdomain->getPostLink(true));
            exit;
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('Error occurred, CMS Snippet could not be saved.');
        }
    }
}
