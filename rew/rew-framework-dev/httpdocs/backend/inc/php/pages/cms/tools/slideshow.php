<?php

// Get Database
$db = DB::get();

// Create Auth Classes
$toolsAuth = new REW\Backend\Auth\ToolsAuth(Settings::getInstance());
if (!$toolsAuth->canManageSlideshow($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage slideshows.')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Hide Form
$show_form = false;

// Show caption field on form
// ** This is an LEC 2015 design feature
$show_caption = (Settings::getInstance()->SKIN === 'lec-2015');

// Update Slideshow Order
if (isset($_GET['updateOrder'])) {
    if (!empty($_GET['images'])) {
        foreach ($_GET['images'] as $order => $image) {
            try {
                $db->prepare("UPDATE `" . TABLE_SLIDESHOW_IMAGES . "` SET `order` = :order WHERE `id` = :id;")
                ->execute([
                    "order" => $order + 1,
                    "id" => $image
                ]);
                $success[] = __('Slideshow image #%s set as #%s', $image, $order);
            } catch (PDOException $e) {
                $errors[] = __('Slideshow image #%s could not be set as #%s', $image, $order);
            }
        }
    }

    // Send JSON Response
    header('Content-type: application/json');
    die(json_encode(array(
        'errors'        => $errors,
        'success'       => $success,
        'returnCode'    => 200
    )));
}

// Delete Slideshow Image
if (!empty($_GET['delete'])) {
    // Require Record from Database
    try{
        $delete_row  = $db->fetch("SELECT * FROM `" . TABLE_SLIDESHOW_IMAGES . "` WHERE `id` = :id;", ["id" => $_GET['delete']]);
    } catch (PDOException $e) {}

    if (!empty($delete_row)) {
        try {
            $db->prepare("DELETE FROM `" . TABLE_SLIDESHOW_IMAGES . "` WHERE `id` = :id LIMIT 1;")
                ->execute([
                   "id" => $delete_row['id']
                ]);
            @unlink(DIR_SLIDESHOW_IMAGES . $delete_row['image']);
            $success[] = __('The selected slideshow image has successfully been deleted.');
        } catch (PDOException $e) {
            $errors[] = __('The selected slideshow image could not be deleted.');
        }
    } else {
        $errors[] = __('The selected slideshow image could not be found. Please try again.');
    }

    // Save notices & redirect to list
    $authuser->setNotices($success, $errors);
    header('Location: ?delete');
    exit;
}

// Edit Slideshow Image
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];
if (!empty($_GET['edit'])) {
    // Require Record from Database
    try {
        $edit_row = $db->fetch("SELECT * FROM `" . TABLE_SLIDESHOW_IMAGES . "` WHERE `id` = :id LIMIT 1;", ["id" => $_GET['edit']]);
    } catch (PDOException $e) {}

    if (!empty($edit_row)) {
        $show_form = true;

        // Process Form Submission
        if (isset($_GET['submit'])) {
            // Upload Slideshow Image
            ini_set('memory_limit', (64 * 1024 * 1024));
            if (isset($_FILES) && count($_FILES) > 0 && is_uploaded_file($_FILES['image']['tmp_name'])) {
                try {
                    // Read Uploaded Image
                    $imageName  = $_FILES['image']['name'];
                    $imageExt   = substr(strrchr($imageName, '.'), 1);
                    $imageName  = mt_rand() . '.' . $imageExt;

                    // Save Uploaded Image
                    $uploadedImage = new Image;
                    $uploadedImage->readFile($_FILES['image']['tmp_name']);
                    $uploadedImage->setName($imageName);
                    $uploadedImage->write(DIR_SLIDESHOW_IMAGES);

                    // Delete Previous Image
                    if (!empty($edit_row['image'])) {
                        if (file_exists(DIR_SLIDESHOW_IMAGES . $edit_row['image'])) {
                            @unlink(DIR_SLIDESHOW_IMAGES . $edit_row['image']);
                        }
                    }

                    // Update Previous Image
                    $edit_row['image'] = $uploadedImage->getName();

                // Error Occurred
                } catch (Exception $e) {
                    $errors[] = __("Image upload failed during communication with the server.");
                    //$errors[] = $e->getMessage();
                }
            }

            // Check Errors
            if (empty($errors)) {
                try {
                    $show_caption_var = $show_caption ? ["caption" => $_POST['caption']] : [];

                    // Build UPDATE Query
                    $db->prepare("UPDATE `" . TABLE_SLIDESHOW_IMAGES . "` SET "
                        . "`image` = :image, "
                        . "`link` = :link, "
                        . ($show_caption ? "`caption` = :caption, " : "")
                        . "`timestamp_updated` = NOW()"
                    . " WHERE `id` = :id;")
                    ->execute(array_merge([
                        "image" => $edit_row['image'],
                        "link" => $_POST['link'],
                        "id" => $edit_row['id']
                    ], $show_caption_var));

                    // Success
                    $success[] = __('Slide Image has successfully been saved.');

                    // Save notices & redirect to list
                    $authuser->setNotices($success, $errors);
                    header('Location: ?success');
                    exit;

                // Query Error
                } catch (PDOException $e) {
                    $errors[] = __('Error occurred, Slideshow Image could not be saved.');
                }
            }
        }

    // Record not Found
    } else {
        $errors[] = __('The selected slideshow image could not be found. Please try again.');
    }
}

// Add Slideshow Image
$_GET['add'] = isset($_POST['add']) ? $_POST['add'] : $_GET['add'];
if (isset($_GET['add'])) {
    $show_form = true;

    // Process Form Submission
    if (isset($_GET['submit'])) {
        // Upload Slideshow Image
        ini_set('memory_limit', (64 * 1024 * 1024));
        if (isset($_FILES) && count($_FILES) > 0 && is_uploaded_file($_FILES['image']['tmp_name'])) {
            try {
                // Read Uploaded Image
                $imageName  = $_FILES['image']['name'];
                $imageExt   = substr(strrchr($imageName, '.'), 1);
                $imageName  = mt_rand() . '.' . $imageExt;

                // Save Uploaded Image
                $uploadedImage = new Image;
                $uploadedImage->readFile($_FILES['image']['tmp_name']);
                $uploadedImage->setName($imageName);
                $uploadedImage->write(DIR_SLIDESHOW_IMAGES);

                // Delete Previous Image
                if (!empty($_POST['image'])) {
                    if (file_exists(DIR_SLIDESHOW_IMAGES . $_POST['image'])) {
                        @unlink(DIR_SLIDESHOW_IMAGES . $_POST['image']);
                    }
                }

                // Update Previous Image
                $_POST['image'] = $uploadedImage->getName();

            // Error Occurred
            } catch (Exception $e) {
                $errors[] = __('Image upload failed during communication with the server.');
                //$errors[] = $e->getMessage();
            }

        // No Image Selected
        } else {
            $errors[] = __('You must select the image that you would like to upload.');
        }

        // Check Errors
        if (empty($errors)) {
            try {
                $show_caption_var = $show_caption ? ["caption" => $_POST['caption']] : [];

                // Build INSERT Query
                $db->prepare("INSERT INTO `" . TABLE_SLIDESHOW_IMAGES . "` SET "
                    . "`image` = :image,"
                    . "`link` = :link,"
                    . ($show_caption ? "`caption` = :caption, " : "")
                    . "`timestamp_created` = NOW()"
                . ";")
                ->execute(array_merge([
                    "image" => $_POST['image'],
                    "link" => $_POST['link']
                ], $show_caption_var));

                // Success
                $success[] = __('Slideshow Image has successfully been uploaded.');

                // Save notices & redirect to list
                $authuser->setNotices($success, $errors);
                header('Location: ?success');
                exit;

            // Query Error
            } catch (PDOException $e) {
                $errors[] = __('Error occurred, Slideshow Image could not be saved.');
            }
        }
    }
}

// Slideshow Images
try {
    $slideshow_images = $db->fetchAll("SELECT * FROM `" . TABLE_SLIDESHOW_IMAGES . "` ORDER BY `order` ASC;");
} catch (PDOException $e) {
    $slideshow_images = array();
}

