<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Full Page
$body_class = 'full';

// Get Authorization
$associatesAuth = new REW\Backend\Auth\AssociateAuth(Settings::getInstance());

// Require permission to edit all associates
if (!$associatesAuth->canDeleteAssociates($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to delete associates.')
    );
}

// Associate ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Success
$success = array();

// Errors
$errors = array();

// Load Backend_Associate
$associate = Backend_Associate::load($_GET['id']);

// Load single associate authorization
$associateAuth = new REW\Backend\Auth\Associates\AssociateAuth($settings, $authuser, $associate);

// Check associate edit/delete permissions
$can_edit = $associateAuth->canEditAssociate();
$can_delete = $associateAuth->canDeleteAssociate();

/* Throw Missing ID Exception */
if (empty($associate)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAssociateException();
}

// Confirm Delete
if (!empty($_POST['delete'])) {
    try {
        // Delete Auth Record
        $query = $db->prepare("DELETE FROM `" . Auth::$table . "` WHERE `id` = :id;");
        $query->execute(["id" => $associate['auth']]);

        try {
            // Delete Associate
            $query = $db->prepare("DELETE FROM `associates` WHERE `id` = :id;");
            $query->execute(["id" => $associate->getId()]);

            $success[] = __('%s has successfully been deleted.', Format::htmlspecialchars($associate->getName()));

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Manage Associates
            header('Location: ' . URL_BACKEND . 'associates/');
            exit;

            // Query Error
        } catch (PDOException $e) {
            $errors[] = __('An error occurred while trying to delete this ISA.');
        }

        // Query Error
    } catch (PDOException $e) {
        $errors[] = __('An error occurred while attempting to delete the selected account.');
    }
}
