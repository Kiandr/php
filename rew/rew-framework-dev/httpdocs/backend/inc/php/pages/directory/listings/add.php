<?php

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$directoryAuth = new REW\Backend\Auth\DirectoryAuth(Settings::getInstance());

// Authorized to manage directories
if (!$directoryAuth->canManageListings($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to manage listings'
    );
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Set Primary Category */
    if (empty($_POST['primary_category']) && is_array($_POST['categories']) && count($_POST['categories']) == 1) {
        $_POST['primary_category'] = $_POST['categories'][0];
    }

    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'business_name', 'title' => 'Busines Name');
    $required[] = array('value' => 'primary_category', 'title' => 'Primary Category');

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    /* Require Category */
    if (empty($_POST['categories'])) {
        $errors[] = 'You must select at least one category for this directory listing.';
    }

    /* Primary Category Selected */
    if (!empty($_POST['categories']) && !in_array($_POST['primary_category'], $_POST['categories'])) {
        $errors[] = 'The primary category must be one of the selected categories for the listing.';
    }

    /* Check Duplicate Business Name */
    $result = mysql_query("SELECT `business_name` FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `business_name` = '" . mysql_real_escape_string($_POST['business_name']) . "';");
    $duplicate = mysql_fetch_array($result);
    if (!empty($duplicate)) {
        $errors[] = 'A listings with this business name already exists.';
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Create Link */
        $_POST['link'] = Format::slugify($_POST['business_name']);

        /* Require ENUM */
        $_POST['pending']      = ($_POST['pending']      == 'N') ? 'N' : 'Y';
        $_POST['featured']     = ($_POST['featured']     == 'Y') ? 'Y' : 'N';
        $_POST['pending']      = ($_POST['pending']      == 'Y') ? 'Y' : 'N';
        $_POST['no_follow']    = ($_POST['no_follow']    == 'Y') ? 'Y' : 'N';

        /* Require String */
        $_POST['categories'] = is_array($_POST['categories'])? implode(',', $_POST['categories']) : $_POST['categories'];

        /* Geocode Address */
        if (empty($_POST['latitude']) && empty($_POST['longitude']) && !empty($_POST['address'])) {
            $address = $_POST['address'] . ' ' . $_POST['city'] . ' ' . $_POST['state'] . ' ' . $_POST['zip'];
            $geoinfo = Map::geocode($address);
            if (!empty($geoinfo)) {
                $_POST['latitude']  = $geoinfo['latitude'];
                $_POST['longitude'] = $geoinfo['longitude'];
            }
        }

        /* Build INSERT Query */
        $query = "INSERT INTO `" . TABLE_DIRECTORY_ENTRIES . "` SET "
               . "`agent`             = '" . $authuser->info('id') . "', "
               . "`link`              = '" . mysql_real_escape_string($_POST['link']) . "', "
               . "`primary_category`  = '" . mysql_real_escape_string($_POST['primary_category']) . "', "
               . "`business_name`     = '" . mysql_real_escape_string($_POST['business_name']) . "', "
               . "`address`           = '" . mysql_real_escape_string($_POST['address']) . "', "
               . "`city`              = '" . mysql_real_escape_string($_POST['city']) . "', "
               . "`state`             = '" . mysql_real_escape_string($_POST['state']) . "', "
               . "`zip`               = '" . mysql_real_escape_string($_POST['zip']) . "', "
               . "`phone`             = '" . mysql_real_escape_string($_POST['phone']) . "', "
               . "`alt_phone`         = '" . mysql_real_escape_string($_POST['alt_phone']) . "', "
               . "`fax`               = '" . mysql_real_escape_string($_POST['fax']) . "', "
               . "`toll_free`         = '" . mysql_real_escape_string($_POST['toll_free']) . "', "
               . "`website`           = '" . mysql_real_escape_string($_POST['website']) . "', "
               . "`description`       = '" . mysql_real_escape_string($_POST['description']) . "', "
               . "`categories`        = '" . mysql_real_escape_string($_POST['categories']) . "', "
               . "`contact_name`      = '" . mysql_real_escape_string($_POST['contact_name']) . "', "
               . "`contact_phone`     = '" . mysql_real_escape_string($_POST['contact_phone']) . "', "
               . "`contact_email`     = '" . mysql_real_escape_string($_POST['contact_email']) . "', "
               . "`longitude`         = '" . mysql_real_escape_string($_POST['longitude']) . "', "
               . "`latitude`          = '" . mysql_real_escape_string($_POST['latitude']) . "', "
               . "`website_link`      = '" . mysql_real_escape_string($_POST['website_link']) . "', "
               . "`featured`          = '" . mysql_real_escape_string($_POST['featured']) . "', "
               . "`page_title`        = '" . mysql_real_escape_string($_POST['page_title']) . "', "
               . "`pending`           = '" . mysql_real_escape_string($_POST['pending']) . "', "
               . "`no_follow`         = '" . mysql_real_escape_string($_POST['no_follow']) . "', "
               . "`timestamp_created` = NOW();";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Insert ID */
            $insert_id = mysql_insert_id();

            /* Assign Uploads to Listing */
            if (!empty($_POST['uploads'])) {
                foreach ($_POST['uploads'] as $upload) {
                    $query = "UPDATE `" . Settings::getInstance()->TABLES['UPLOADS']  . "` SET `row` = '" . mysql_real_escape_string($insert_id) . "' WHERE `id` = '" . mysql_real_escape_string($upload) . "';";
                    mysql_query($query);
                }
            }

            /* Redirect to Edit Form */
            header('Location: ../edit/?id=' . $insert_id . '&success=add');

            /* Exit Script */
            exit;
        } else {
            /* Query Error */
            $errors[] = 'Error occurred, Directory Listing could not be saved.';
        }
    }
} else {
    $_POST['website_link'] = (isset($_POST['website_link'])) ? $_POST['website_link'] : 'Y';
}

/* Categories */
$categories = array();
$all_categories = array();

/* Select Rows */
$directory_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order` ASC, `title` ASC;");

/* Build Collections */
while ($directory_category = mysql_fetch_array($directory_categories)) {
    /* Add to Collection */
    $all_categories[] = $directory_category;

    /* Select Rows */
    $sub_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $directory_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

    /* Build Collection */
    $directory_category['sub_categories'] = array();
    while ($sub_category = mysql_fetch_array($sub_categories)) {
        /* Add to Collection */
        $all_categories[] = $sub_category;

        /* Select Rows */
        $tert_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $sub_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

        /* Build Collection */
        $sub_category['tert_categories'] = array();
        while ($tert_category = mysql_fetch_array($tert_categories)) {
            /* Add to Collection */
            $all_categories[] = $tert_category;

            /* Add to Collection */
            $sub_category['tert_categories'][] = $tert_category;
        }

        /* Add to Collection */
        $directory_category['sub_categories'][] = $sub_category;
    }

    /* Add to Collection */
    $categories[] = $directory_category;
}

/* Publish by Default */
$_POST['pending'] = isset($_POST['pending']) ? $_POST['pending'] : 'N';

/* Listing Photos */
$uploads = array();
$logo_uploads = array();
if (!empty($_POST['uploads'])) {
    foreach ($_POST['uploads'] as $upload) {
        $query = "SELECT `id`, `file`, `type` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `id` = '" . mysql_real_escape_string($upload) . "' ORDER BY `order` ASC;";
        if ($result = mysql_query($query)) {
            while ($row = mysql_fetch_assoc($result)) {
                if ($row['type'] == "directory_logo") {
                    $logo_uploads[] = $row;
                } else {
                    $uploads[] = $row;
                }
            }
        }
    }
}
