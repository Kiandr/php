<?php

/**
 * Include Common File
 */
require_once '../../../common.inc.php';

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
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);

        // Check Success
        if (!empty($upload)) {
            if (in_array($uploader->getExtension(), array('jpg', 'jpeg', 'png'))) {
                $full_image_path = Settings::getInstance()->DIRS['UPLOADS'] . $uploader->getName();

                if (in_array($uploader->getExtension(), array('jpg', 'jpeg'))) {
                    // Use ImageMagick To Compress Image and Convert To Progressive
                    exec("convert -interlace line " . escapeshellarg($full_image_path) . " " . escapeshellarg($full_image_path));
                } else if ($uploader->getExtension() === 'png') {
                    // Use OptiPNG To Compress Image
                    exec("optipng -backup " . escapeshellarg($full_image_path) . " -clobber " . escapeshellarg($full_image_path));
                }

                $filesize = filesize($full_image_path);
            } else {
                $filesize = $uploader->getSize();
            }

            if (!empty($_GET["name"]) && strpos($_GET["name"], "360") !== false) {
                $dimensions = [];
                list($dimensions["w"], $dimensions["h"]) = getimagesize($full_image_path);
                if ($dimensions["w"] > 3200 || $dimensions["h"] > 1600) {
                    throw new Exception('Image larger then the maximum dimensions. (Max. dimensions: 3200×1600px;)');
                }
            }

            // Upload Details
            $upload = array (
                'success'   => true,
                'name'      => $uploader->getName(),
                'file'      => $path . $uploader->getName(),
                'ext'       => $uploader->getExtension(),
                'size'      => $filesize
            );

            // Type of Upload
            $type = !empty($_GET['type']) ? "'" . mysql_real_escape_string($_GET['type']) . "'" : 'NULL';

            // Assign to Row
            $row = !empty($_GET['row']) ? "'" . mysql_real_escape_string($_GET['row']) . "'" : 'NULL';

            // Our Uploaded File
            $file = $upload['name'];

            // Get Order Value
            $result = mysql_query("SELECT MAX(`order`) + 1 AS `order` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "`;");
            $order  = mysql_fetch_assoc($result);
            $upload['order'] = $order['order'];

            // Generate INSERT Query
            $query = "INSERT INTO `" . Settings::getInstance()->TABLES['UPLOADS'] . "` SET "
                   . "`type`      = " . $type . ", "
                   . "`row`       = " . $row . ", "
                   . "`file`      = '" . mysql_real_escape_string($upload['name']) . "', "
                   . "`size`      = '" . mysql_real_escape_string($upload['size']) . "', "
                   . "`order`     = '" . mysql_real_escape_string($upload['order']) . "', "
                   . "`timestamp` = NOW();";

            // Execute Query
            if (mysql_query($query)) {
                // Return Upload ID
                $upload['id'] = mysql_insert_id();

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
 * New Thumb on Upload
 */
if (isset($_GET['thumb'])) {

    // Settings
    $settings = Settings::getInstance();
    // Use phpthumb to generate thumbnail?
    $phpthumbOnUpload = $settings->listings['phpthumbOnUpload'];
    // Thumb dimensions
    $thumbnails = $settings->listings['thumbnails'];

    if (!empty($phpthumbOnUpload) && !empty($thumbnails)) {

        // Set REQUEST_URI for use with phpthumb file
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_HOST'] . sprintf('/thumbs/%s', $thumbnails) . $_POST['upload']['file'];

        // Use phpthumb file to generate the thumbnail
        require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/lib/phpthumb/index.php';

    }

    $json = array();

}

/**
 * Delete Upload
 */
if (isset($_GET['delete'])) {
    $json = array();

    $upload = trim($_POST['upload']);

    if (is_numeric($upload)) {
        /* Select Row */
        $query = "SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = '" . mysql_real_escape_string($upload) . "';";
        if ($result = mysql_query($query)) {
            /* Fetch Row */
            $upload = mysql_fetch_assoc($result);
            if (!empty($upload)) {
                /* Generate DELETE Query */
                $query = "DELETE FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = '" . $upload['id'] . "';";

                /* Execute Query */
                if (mysql_query($query)) {
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
        /* Updates */
        $uploads = explode(',', $_POST['order']);

        /* Update Order */
        foreach ($uploads as $order => $upload) {
            /* Generate UPDATE Query */
            $query = "UPDATE `" . Settings::getInstance()->TABLES['UPLOADS'] . "` SET "
                   . "`order` = '" . mysql_real_escape_string($order + 1) . "'"
                   . " WHERE `id` = '" . mysql_real_escape_string($upload) . "';";

            /* Execute Query */
            if (mysql_query($query)) {
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

// Send back JSON content
header('Content-type: application/json');
echo json_encode($json);
exit;
