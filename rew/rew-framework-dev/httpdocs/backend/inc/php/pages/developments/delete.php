<?php

/* @global Auth $authuser */

if (!Skin::hasFeature(Skin::REW_DEVELOPMENTS)) {
    throw new \REW\Backend\Exceptions\PageNotFoundException();
}

// Full width page
$body_class = 'full';

// Get Authorization Managers
$developmentsAuth = new REW\Backend\Auth\DevelopmentsAuth(Settings::getInstance());

// Authorized to Edit all Leads
if (!$developmentsAuth->canDeleteDevelopments($authuser)) {
    // Require permission to edit self
    if (!$developmentsAuth->canManageOwnDevelopments($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to delete developments.'
        );
    } else {
        // Restrict to owned
        $agent_id = $authuser->info('id');
    }
}
// Notices
$success = array();
$errors = array();

// DB connection
$db = DB::get();

// Record ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Locate development record
$query = $db->prepare("SELECT `id`, `title` FROM `developments` WHERE `id` = ?"
    . ($agent_id ? sprintf(' AND `agent_id` = %d', $agent_id) : '')
. " LIMIT 1;");
$query->execute([$_GET['id']]);
$development = $query->fetch();

/* Throw Missing Agent Exception */
if (empty($development)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingDevelopmentException();
}

// Prepare query to find primary photo
$find_photo = $db->prepare(sprintf(
    "SELECT `file` FROM `%s` WHERE `type` = 'development' AND `row` = ? ORDER BY `order` ASC LIMIT 1;",
    Settings::getInstance()->TABLES['UPLOADS']
));

// Fetch primary photo
$find_photo->execute([$development['id']]);
$development['image'] = $find_photo->fetchColumn();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['delete'])) {
    try {
        // Delete record from database
        $delete = $db->prepare("DELETE FROM `developments` WHERE `id` = ?;");
        $delete->execute([$development['id']]);

        // Success message
        $success[] = sprintf(
            '%s  has successfully been deleted.',
            Format::htmlspecialchars($development['title'])
        );

        // Redirect up a level to management list
        $authuser->setNotices($success, $errors);
        header('Location: ../');
        exit;

    // Database error occurred
    } catch (\PDOException $e) {
        $errors[] = 'An error occurred while attempting to delete the selected development.';
    }
}
