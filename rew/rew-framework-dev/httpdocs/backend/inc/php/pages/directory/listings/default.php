<?php

// Get Authorization Managers
$directoryAuth = new REW\Backend\Auth\DirectoryAuth(Settings::getInstance());

// Require permission to edit all associates
if (!$directoryAuth->canManageListings($authuser)) {
    // Require permission to edit self
    if (!$directoryAuth->canManageOwnListings($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to manage calendars.'
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

/* Delete Row */
if (!empty($_GET['delete'])) {
    /* Authorized? */
    if (!empty($can_delete)) {
        try {
            $uploads = new Helper_Uploads(DB::get(), Settings::getInstance());
            $uploads->remove($_GET['delete'], 'directory');
            $uploads->remove($_GET['delete'], 'directory_logo');

            /* Build DELETE Query */
            $query = "DELETE FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "'" . (!empty($sql_agent) ? " AND " . $sql_agent : '') . ";";
            if (!mysql_query($query)) {
                throw new Exception;
            }

            /* Success */
            $success[] = 'The selected directory listing has successfully been deleted.';
        } catch (Exception $e) {
            /* Error */
            $errors[] = 'The selected directory listing could not be deleted. Please try again.';
        }
    } else {
        /* Permission Error */
        $errors[] = 'You do not have the proper permissions to perform this action.';
    }
}

/* Directory Listings */
$manage_listings = array();

/* SQL Filter */
$sql_where  = (!empty($_GET['pending']) == 'show') ? " AND `pending` = 'Y'" : '';
$sql_where .= (!empty($_GET['category'])) ? " AND FIND_IN_SET('" . $_GET['category'] . "', `categories`)" : '';
$sql_where .= (!empty($_GET['name'])) ? " AND `business_name` LIKE '%" . mysql_real_escape_string($_GET['name']) . "%'" : '';
$sql_where .= !empty($sql_agent) ? ' AND ' . $sql_agent : '';

/* Count Rows */
$result = mysql_query("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `preview` != 'Y'" . $sql_where . ";");
$count_listings = mysql_fetch_array($result);

/* Query String */
$query_string = array();
if (strpos($_SERVER['REQUEST_URI'], '?')) {
    parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_string);
}

/* Limit */
if ($count_listings['total'] > 20) {
    $limitvalue = (($_GET['p'] - 1) * 20);
    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
    $sql_limit = " LIMIT " . $limitvalue . "," . 20;
}

/* Pagination */
$pagination = generate_pagination($count_listings['total'], $_GET['p'], 20, $query_string);

/* Select Rows */
$query = "SELECT *, UNIX_TIMESTAMP(`timestamp_created`) AS `date` FROM `" . TABLE_DIRECTORY_ENTRIES . "` WHERE `preview` != 'Y'" . $sql_where . " ORDER BY `business_name`" . $sql_limit . ";";
if ($directory_listings = mysql_query($query)) {
    /* Build Collection */
    while ($directory_listing = mysql_fetch_array($directory_listings)) {
        /* Build Collection */
        $categories = explode(",", $directory_listing['categories']);
        $directory_listing['categories'] = array();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                /* Select Category */
                $result = mysql_query("SELECT `id`, `link`, `title` FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `link` = '" . $category . "';");
                $row    = mysql_fetch_assoc($result);

                /* Add to Collection */
                if (!empty($row)) {
                    $directory_listing['categories'][] = $row;
                }
            }
            $directory_listing['main_cat'] = $categories[0];
        }

        /* Select Agent */
        $result = mysql_query("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = '" . $directory_listing['agent'] . "';");
        $row    = mysql_fetch_array($result);
        $directory_listing['agent'] = $row;

        /* Locate Image */
        $query = "SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'directory_logo' AND `row` = '" . $directory_listing['id'] . "' ORDER BY `order` ASC LIMIT 1;";
        if ($image = mysql_query($query)) {
            $image = mysql_fetch_assoc($image);
            if (!empty($image['file'])) {
                $directory_listing['logo'] = 'uploads/' . $image['file'];
            } else {
                $query = "SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'directory' AND `row` = '" . $directory_listing['id'] . "' ORDER BY `order` ASC LIMIT 1;";
                if ($image = mysql_query($query)) {
                    $image = mysql_fetch_assoc($image);
                    if (!empty($image['file'])) {
                        $directory_listing['logo'] = 'uploads/' . $image['file'];
                    }
                }
            }
        }

        /* Add to Collection */
        $manage_listings[] = $directory_listing;
    }
} else {
    /* Query Error */
    $errors[] = 'Error occurred while loading Directory Listings.';
}
