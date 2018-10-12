<?php

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$directoryAuth = new REW\Backend\Auth\DirectoryAuth(Settings::getInstance());

// Require permission to edit all associates
$can_manage_all = $directoryAuth->canManageListings($authuser);
if (!$can_manage_all) {
    // Require permission to edit self
    if (!$directoryAuth->canManageOwnListings($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to manage listings.'
        );
    } else {
        // Restrict to Agent's Data
        $sql_agent = "`agent` = '" . $authuser->info('id') . "'";

        // Agents Can Delete Their Own Calendars
        $can_delete = true;
    }
} else if (!empty($_GET['filter'])) {
    // Set Agent Filter
    $filterAgent = Backend_Agent::load($_GET['filter']);
    if (isset($filterAgent) && $filterAgent instanceof Backend_Agent) {
        // Restrict to Agent's Data
        $sql_agent = "`agent` = '" . $authuser->info('id') . "'";

        // Agents Can Delete Their Own Calendars
        $can_delete = true;
    }
} else {
    // Can Agent Delete Calendar Events
    $can_delete = $directoryAuth->canDeleteListings($authuser);
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Row ID */
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

/* Select Row */
$result = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'" . (!empty($sql_agent) ? " AND " . $sql_agent : '') . ";");
$edit_listing = mysql_fetch_array($result);

/* Throw Missing ID Exception */
if (empty($edit_listing)) {
    throw new \REW\Backend\Exceptions\MissingId\Directory\MissingListingException();
}

/* New Row Successful */
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = 'Directory Listing has successfully been created.';
}

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
    $result = mysql_query("SELECT `business_name` FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `business_name` = '" . mysql_real_escape_string($_POST['business_name']) . "' AND `id` != '" . $edit_listing['id'] . "';");
    $duplicate = mysql_fetch_array($result);
    if (!empty($duplicate)) {
        $errors[] = 'A directory listing with this business name already exists.';
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Require String */
        $_POST['categories'] = is_array($_POST['categories']) ? implode(',', $_POST['categories']) : $_POST['categories'];

        /* Require ENUM */
        $_POST['pending']      = ($_POST['pending']      == 'N') ? 'N' : 'Y';
        $_POST['no_follow']    = ($_POST['no_follow']    == 'Y') ? 'Y' : 'N';
        $_POST['website_link'] = ($_POST['website_link'] == 'Y') ? 'Y' : 'N';
        $_POST['featured']     = ($_POST['featured']     == 'Y') ? 'Y' : 'N';

        /* Geocode Address */
        if (empty($_POST['latitude']) && empty($_POST['longitude']) && !empty($_POST['address'])) {
            $address = $_POST['address'] . ' ' . $_POST['city'] . ' ' . $_POST['state'] . ' ' . $_POST['zip'];
            $geoinfo = Map::geocode($address);
            if (!empty($geoinfo)) {
                $_POST['latitude']  = $geoinfo['latitude'];
                $_POST['longitude'] = $geoinfo['longitude'];
            }
        }

        /* Build UPDATE Query */
        $query = "UPDATE `" . TABLE_DIRECTORY_ENTRIES . "` SET "
               . "`business_name`     = '" . mysql_real_escape_string($_POST['business_name']) . "', "
               . "`primary_category`  = '" . mysql_real_escape_string($_POST['primary_category']) . "', "
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
               . "`pending`           = '" . $_POST['pending'] . "', "
               . "`no_follow`         = '" . $_POST['no_follow'] . "', "
               . "`website_link`      = '" . $_POST['website_link'] . "', "
               . "`featured`          = '" . $_POST['featured'] . "', "
               . "`page_title`      = '" . mysql_real_escape_string($_POST['page_title']) . "', "
               . "`timestamp_updated` = NOW()"
               . " WHERE "
               . "`id` = '" . $edit_listing['id'] . "';";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Success */
            $success[] = 'Directory Listing has successfully been saved.';

            /* Fetch Updated Row */
            $result    = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `id` = '" . $edit_listing['id'] . "';");
            $edit_listing = mysql_fetch_array($result);
        } else {
            /* Query Error */
            $errors[] = 'Directory Listing could not be saved, please try again.';
        }
    }

    /* Use $_POST */
    foreach ($edit_listing as $k => $v) {
        $edit_listing[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

/* Require Array */
$edit_listing['categories'] = is_array($edit_listing['categories']) ? $edit_listing['categories'] : explode(',', $edit_listing['categories']);

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

/* Authorized to show agent */
if ($can_manage_all) {
    /* Agent */
    $query = "SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = '" . $edit_listing['agent'] . "';";
    if ($result = mysql_query($query)) {
        $agent = mysql_fetch_assoc($result);
    } else {
        $errors[] = 'Error Occurred while loading Listing Agent.';
    }
}

/* Listing Photos */
$uploads = array();
$query = "SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` = 'directory' AND `row` = '" . mysql_real_escape_string($edit_listing['id']) . "' ORDER BY `order` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $uploads[] = $row;
    }
}

/* Listing Logo */
$logo_uploads = array();
$query = "SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` = 'directory_logo' AND `row` = '" . mysql_real_escape_string($edit_listing['id']) . "' ORDER BY `order` ASC LIMIT 1;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $logo_uploads[] = $row;
    }
}
