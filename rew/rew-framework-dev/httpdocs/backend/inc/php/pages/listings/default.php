<?php

// App DB
$db = DB::get();

// Get Authorization Managers
$listingAuth = new REW\Backend\Auth\ListingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$listingAuth->canManageListings($authuser)) {
    if (!$listingAuth->canManageOwnListings($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to manage listings'
        );
    }
    /* Restrict to Agent's Data */
    $sql_agent = "`agent` = :agent";
}

/* Authorized to Delete? */
$can_delete = $listingAuth->canDeleteListings($authuser);

// Can Import Listings (Super Admin Only)
$can_import = $listingAuth->canImportListings($authuser);

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/**
 * Delete Listing
 */
if (!empty($_GET['delete'])) {
    /* Authorized? */
    if (!empty($can_delete) || $delete['agent'] === $authuser->info('id')) {
        try {
            /* Select Row */
            $params = ["id" => $_GET['delete']];
            $and_sql_agent = "";
            if (!empty($sql_agent)) {
                $and_sql_agent = "AND $sql_agent";
                $params["agent"] = $authuser->info('id');
            }
            $delete = $db->fetch("SELECT `id` FROM `" . TABLE_LISTINGS . "` WHERE `id` = :id $and_sql_agent;", $params);

            /* Require Row */
            if (!empty($delete)) {
                try {
                    $uploads = new Helper_Uploads(DB::get(), Settings::getInstance());
                    $uploads->remove($delete['id'], 'listing');

                    /* Delete Listing */
                    try {
                        $db->prepare("DELETE FROM `" . TABLE_LISTINGS . "` WHERE `id` = :id;")->execute(["id" => $delete['id']]);
                    } catch (PDOException $e) {
                        throw new Exception;
                    }

                    /* Success */
                    $success[] = 'The selected listing has successfully been deleted.';

                    $authuser->setNotices($success, $errors);

                    $url = strtok($_SERVER["REQUEST_URI"], '?');
                    if (isset($_GET['p']) && !empty($_GET['p'])) {
                        $url = $url . '?p=' . $_GET['p'];
                    }

                    // Redirect to Edit Form
                    header(sprintf('Location: %s', $url));
                    exit;
                } catch (Exception $e) {
                    /* Query Error */
                    $errors[] = 'The selected listing could not be deleted. Please try again.';
                }
            } else {
                /* Listing Not Found Error */
                $errors[] = 'The selected listing could not be deleted.';
            }
        } catch (PDOException $e) {}
    } else {
        /* Permission Error */
        $errors[] = 'You do not have the proper permissions to perform this action.';
    }
}

/* Search Filters */
$sql_where = array();

$params = [];

/* Agent Filter */
if (!empty($sql_agent)) {
    $sql_where[] = $sql_agent;
    $params["agent"] = $authuser->info('id');
}

/* Search Filter */
$filter = $_GET['filter'];
switch ($filter) {
    case false:
        $filter = $_GET['filter'] = 'all';
    case 'all':
        $title = 'All Listings';
        break;
    default:
        /* Listing Status */
        try {
            $status = $db->fetch("SELECT `value` FROM `" . TABLE_LISTING_FIELDS . "` WHERE `field` = 'status' AND `value` = :value;", ["value" => $filter]);
        } catch (PDOException $e) {}

        /* Status Filter */
        if (!empty($status)) {
            $title = $status['value'] . ' Listings';
            $sql_where[] = "`status` = :status";
            $params["status"] = $status['value'];
        }

        break;
}

/* SQL WHERE */
$sql_where = !empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '';

// CMS Listings
$listings = array();

// Count Listings
try {
    // Listing Count
    $count = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_LISTINGS . "`" . $sql_where . ";", $params);
    if (!empty($count['total'])) {
        // Page Limit
        $page_limit = 25;

        // SQL Limit
        if ($count['total'] > $page_limit) {
            $limitvalue = (($_GET['p'] - 1) * $page_limit);
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
            $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
        }

        // Pagination
        $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit);

        // Select Listings
        try {
            $result = $db->fetchAll("SELECT *, UNIX_TIMESTAMP(`timestamp_created`) AS `date` FROM `" . TABLE_LISTINGS . "`" . $sql_where . $sql_limit . ";", $params);
            if (count($result) === 0 && (isset($_GET['p']) && $_GET['p'] > 1)) {
                $url = strtok($_SERVER["REQUEST_URI"]) . '?p=' . ($_GET['p'] - 1);

                // Redirect to Edit Form
                header(sprintf('Location: %s', $url));
                exit;
            }

            // Build Collection
            foreach ($result as $listing) {
                // Select Agent
                try {
                    $listing['agent'] = $db->fetch("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :id;", ["id" => $listing['agent']]);
                } catch (PDOException $e) {}

                // Locate Listing Image
                try {
                    $image = $db->fetch("SELECT `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'listing' AND `row` = :row ORDER BY `order` ASC LIMIT 1;", ["row" => $listing['id']]);
                    if (!empty($image['file'])) {
                        $listing['image'] = $image['file'];
                    }
                } catch (PDOException $e) {}

                if (!$can_delete && $listing['agent'] === $authuser->info('id')) {
                    $listing['can_delete'] = true;
                }

                // Add to Collection
                $listings[] = $listing;
            }

        // Query Error
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while loading listings.';
        }
    }

// Query Error
} catch (PDOException $e) {
    $errors[] = 'An error occurred while loading listings.';
}
