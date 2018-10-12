<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$agentsAuth = new REW\Backend\Auth\AgentsAuth(Settings::getInstance());

// Authorized to Manage Lead Networks
if (!$agentsAuth->canManageOffices($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit agent offices.')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Office ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Get database and hooks
$container = Container::getInstance();
$db = $container->get(\REW\Core\Interfaces\DBInterface::class);
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);

// Require Record from Database
$edit_office = $db->fetch("SELECT * FROM `" . TABLE_FEATURED_OFFICES . "` WHERE `id` = :id", ['id' => $_GET['id']]);

/* Throw Missing ID Exception */
if (empty($edit_office)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingOfficeException(
        __('No office with the provided ID exists.')
    );
}

// Process Form Submission
if (isset($_GET['submit'])) {
    // Require Office Title
    if (empty($_POST['title'])) {
        $errors[] = __('Office Title is a required field.');
    }

    // Upload Image
    $image_file = !empty($_POST['image']) ? $_POST['image'] : '';
    if (isset($_FILES) && count($_FILES) > 0) {
        if (is_uploaded_file($_FILES['upload']['tmp_name'])) {
            try {
                // Get File Uploader
                $uploader = new Backend_Uploader_Form('upload');
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
                $uploader->handleUpload(DIR_OFFICE_IMAGES . $_POST['upload']);

                $compressor = new REW\Backend\Utilities\ImageCompressor(DIR_OFFICE_IMAGES . $uploader->getName() . '.' . $uploader->getExtension);
                $compressor->compress();

                // Save Image
                $_POST['upload'] = $uploader->getName();

                // Grab File Name For Referencing In Office DB Entry
                $image_file = $uploader->getName();

            // Error Occurred
            } catch (Exception $e) {
                $errors[] = __("Photo upload failed during communication with the server.");
            }
        }
    }

    // Check Errors
    if (empty($errors)) {
        // ENUM Value
        $_POST['display'] = ($_POST['display'] == 'Y') ? 'Y' : 'N';

        $updateParams = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'fax' => $_POST['fax'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'zip' => $_POST['zip'],
            'display' => $_POST['display'],
            'image' => $image_file,
            'id' => $edit_office['id']
        ];

        try {
            $updateParams = $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVE)
                ->run($updateParams, \REW\Core\Interfaces\Definitions\ModelInterface::OFFICE);

            $sqlUpdate = "UPDATE `" . TABLE_FEATURED_OFFICES . "` SET `timestamp_updated` = NOW()";
            foreach ($updateParams as $field => $value) {
                $sqlUpdate .= ", `" . $field . "` = :" . $field;
            }
            $sqlUpdate .= " WHERE `id` = :id";
            $stmtUpdate = $db->prepare($sqlUpdate);

            $stmtUpdate->execute($updateParams);

            $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVED)
                ->run($updateParams, \REW\Core\Interfaces\Definitions\ModelInterface::OFFICE);

            // Success
            $success[] = __('Office has successfully been updated.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?id=' . $edit_office['id']);
            exit;
        } catch (Exception $e) {
            $errors[] = __('Error Occurred, Office could not be saved.');
        }
    }

    // Use $_POST Data
    foreach ($edit_office as $k => $v) {
        $edit_office[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// States
$states = $db->fetchAll("SELECT `country`, `state` AS `value`, `state` AS `title` FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `local` = '' ORDER BY `country` DESC, `state` ASC;");
