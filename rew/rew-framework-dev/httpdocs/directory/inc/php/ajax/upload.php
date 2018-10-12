<?php

// Require Composer Vendor Auto loader
require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

/**
 * New Upload
 */
if (isset($_GET['upload'])) {
    try {
        // Get File Uploader
        $uploader = Backend_Uploader::get();

        // Allowed File Extensions
        $uploader->setAllowedExtensions(($_GET['upload'] == 'doc') ? array('doc', 'docx', 'rtf', 'pdf', 'txt', 'xls', 'xlsx', 'odt') : (($_GET['upload'] == 'audio') ? array('mp3') : array ('jpg', 'jpeg', 'png', 'gif')));

        // Process Upload
        $path = Settings::getInstance()->DIRS['UPLOADS'];
        $upload = $uploader->handleUpload($path);
        $path = str_replace(Settings::getInstance()->DIRS['ROOT'], '/', $path);

        // Check Success
        if (!empty($upload)) {
            if (in_array($uploader->getExtension(), array('jpg', 'jpeg', 'png'))) {
                $full_image_path = Settings::getInstance()->DIRS['UPLOADS'] . $uploader->getName();

                if (in_array($uploader->getExtension(), array('jpg', 'jpeg'))) {
                    // Use ImageMagick To Compress Image and Convert To Progressive
                    exec("convert -strip -interlace line " . escapeshellarg($full_image_path) . " " . escapeshellarg($full_image_path));
                } else if ($uploader->getExtension() === 'png') {
                    // Use OptiPNG To Compress Image
                    exec("optipng -strip all -backup " . escapeshellarg($full_image_path) . " -clobber " . escapeshellarg($full_image_path));
                }

                $filesize = filesize($full_image_path);
            } else {
                $filesize = $uploader->getSize();
            }

            // Upload Details
            $upload = array (
                'success'   => true,
                'name'      => $uploader->getName(),
                'file'      => $path . $uploader->getName(),
                'ext'       => $uploader->getExtension(),
                'size'      => $filesize
            );

            // DB connection
            $db = DB::get();

            // Type of Upload
            $type = !empty($_GET['type']) ? $db->quote($_GET['type']) : 'NULL';

            // Assign to Row
            $row = !empty($_GET['row']) ? $db->quote($_GET['row']) : 'NULL';

            // Our Uploaded File
            $file = $upload['name'];

            // Get Order Value
            $result = $db->query("SELECT MAX(`order`) + 1 AS `order` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "`;");
            $order  = $result->fetch(PDO::FETCH_ASSOC);
            $upload['order'] = $order['order'];

            // Generate INSERT Query
            $query = "INSERT INTO `" . Settings::getInstance()->TABLES['UPLOADS'] . "` SET "
                   . "`type`      = " . $type . ", "
                   . "`row`       = " . $row . ", "
                   . "`file`      = " . $db->quote($upload['name']) . ", "
                   . "`size`      = " . $db->quote($upload['size']) . ", "
                   . "`order`     = " . $db->quote($upload['order']) . ", "
                   . "`timestamp` = NOW();";

            // Execute Query
            if ($db->query($query)) {
                // Return Upload ID
                $upload['id'] = $db->lastInsertId();

            // Query Error
            } else {
                $upload = array('error' => 'Error Occurred while Saving Upload');
            }
        }

    // Error Occurred
    } catch (Exception $e) {
        $upload = array('error' => $e->getMessage());
    }

    /* Return Upload as JSON */
    $json = $upload;
}

/**
 * Delete Upload
 */
if (isset($_GET['delete'])) {
    $json = array();

    $upload = trim($_POST['upload']);

    if (is_numeric($upload)) {
        // DB connection
        $db = DB::get();

        /* Select Row */
        $query = "SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = " . $db->quote($upload) . ";";
        if ($result = $db->query($query)) {
            /* Fetch Row */
            $upload = $result->fetch(PDO::FETCH_ASSOC);
            if (!empty($upload)) {
                /* Generate DELETE Query */
                $query = "DELETE FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = '" . $upload['id'] . "';";

                /* Execute Query */
                if ($db->query($query)) {
                    /* Success */
                    $json['success'] = true;

                    /* Delete from Filesystem */
                    @unlink(Settings::getInstance()->DIRS['UPLOADS'] . $upload['file']);

                    // Find & Delete Cacheb Thumbnails
                    $thumbDirs = glob($_SERVER['DOCUMENT_ROOT'] . '/inc/cache/img/*/uploads/' . $upload['file']);
                    if (!empty($thumbDirs)) {
                        foreach ($thumbDirs as $thumbImg) {
                            if (file_exists($thumbImg)) {
                                @unlink($thumbImg);
                            }
                        }
                    }
                } else {
                    /* Query Error */
                    $json['error'] = 'Query Error';
                }
            }
        } else {
            /* Query Error */
            $json['error'] = 'Query Error';
        }
    } else {
        /* Missing Required Data */
        $json['error'] = 'Incomplete Request';
    }
}

/**
 * Sort Uploads
 */
if (isset($_GET['sort'])) {
    $json = array();

    if (!empty($_POST['order'])) {
        // DB connection
        $db = DB::get();

        /* Updates */
        $uploads = explode(',', $_POST['order']);

        /* Update Order */
        foreach ($uploads as $order => $upload) {
            /* Generate UPDATE Query */
            $query = "UPDATE `" . Settings::getInstance()->TABLES['UPLOADS'] . "` SET "
                   . "`order` = " . $db->quote($order + 1)
                   . " WHERE `id` = " . $db->quote($upload) . ";";

            /* Execute Query */
            if ($db->query($query)) {
                /* Success */
                $json['success'] = true;
            } else {
                /* Query Error */
                $json['error'] = 'Query Error';
            }
        }
    } else {
        /* Missing Required Data */
        $json['error'] = 'Incomplete Request';
    }
}

/* Send as JSON */
//header('Content-type: application/json');
header('Content-type: text/html'); // STUPID IE!

/* Encode as JSON */
$json = json_encode($json);

/* Return JSON */
echo htmlspecialchars($json, ENT_NOQUOTES);

/* Exit Script */
exit;
