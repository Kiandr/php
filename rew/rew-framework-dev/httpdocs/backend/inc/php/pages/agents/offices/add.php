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

// Get database and hooks
$container = Container::getInstance();
$db = $container->get(\REW\Core\Interfaces\DBInterface::class);
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);

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

        $insertParams = [
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
            'image' => $image_file
        ];

        // Execute Query
        try {
            $insertParams = $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVE)
                ->run($insertParams, \REW\Core\Interfaces\Definitions\ModelInterface::OFFICE);

            $sqlInsert = "INSERT INTO `" . TABLE_FEATURED_OFFICES . "` SET `timestamp_created` = NOW()";
            foreach ($insertParams as $field => $value) {
                $sqlInsert .= ", `" . $field . "` = :" . $field;
            }
            $stmtInsert = $db->prepare($sqlInsert);
            $stmtInsert->execute($insertParams);

            // Insert ID
            $insert_id = $db->lastInsertId();

            $insertParams = $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVED)
                ->run(
                    array_merge(['id' => $insert_id], $insertParams),
                    \REW\Core\Interfaces\Definitions\ModelInterface::OFFICE
                );

            // Success
            $success[] = __('Office has successfully been created.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $insert_id);
            exit;

        // Query Error
        } catch (Exception $e) {
            $errors[] = __('Error occurred, Office could not be saved.');
        }
    }
}

// States
$states = $db->fetchAll("SELECT `country`, `state` AS `value`, `state` AS `title` FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `local` = '' ORDER BY `country` DESC, `state` ASC;");
