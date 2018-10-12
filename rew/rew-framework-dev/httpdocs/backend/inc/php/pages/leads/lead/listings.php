<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$lead = $db->fetch("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;", ['id' => $_GET['id']]);

/* Throw Missing $lead Exception */
if (empty($lead)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
}

// Create lead instance
$lead = new Backend_Lead($lead);

// Get Lead Authorization
$leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);

// Not authorized to view lead
if (!$leadAuth->canViewLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view this leads listings'
    );
}

// Can View Agent (Used for Linking to Agent Profile for Recommended Listings)
$agentAuth = new REW\Backend\Auth\AgentsAuth($settings);
$canViewAgent = $agentAuth->canViewAgents($authuser);

// Show dismissed listings (LEC 2015 feature)
$show_dismissed = ($settings->SKIN === 'lec-2015');

// Listing types
$types = array(
    'saved'         => Locale::spell('Favorites'),
    'recommended'   => 'Recommended',
    'dismissed'     => 'Dismissed',
    'viewed'        => 'Viewed'
);

// Hide dismissed filter
if (empty($show_dismissed)) {
    unset($types['dismissed']);
}

// Delete requested listing from database
if (!empty($_GET['delete']) && $leadAuth->canEditLead()) {
    try {
        // Type of record to remove
        $filter = $_GET['type'];
        switch ($filter) {
            // Save/recommended
            case 'recommended':
            case 'saved':
                $event_name = 'History_Event_Delete_SavedListing';
                $table_name = 'users_listings';
                break;

            // Dismissed listing
            case 'dismissed':
                $event_name = 'History_Event_Delete_DismissListing';
                $table_name = 'users_listings_dismissed';
                break;

            // Viewed listing
            case 'viewed':
                throw new UnexpectedValueException('Viewed listings cannot be removed');

            // Invalid
            default:
                throw new UnexpectedValueException('Invalid listing filter specified');
        }

        // Require listing record
        $query = $db->prepare("SELECT * FROM `" . $table_name . "` WHERE `id` = :id AND `user_id` = :user_id LIMIT 1;");
        $query->execute(array('id' => $_GET['delete'], 'user_id' => $lead['id']));
        if (!$delete = $query->fetch()) {
            throw new UnexpectedValueException('The selected listing could not be found.');
        }

        // Remove listing from database
        $db->prepare("DELETE FROM `" . $table_name . "` WHERE `id` = ?;")->execute(array($delete['id']));

        // Request was successful!
        $success[] = 'The selected listing has successfully been removed.';

        try {
            // Save history event
            if (!empty($event_name)) {
                // IDX resources
                $idx = Util_IDX::getIDX($delete['idx']);
                $db_idx = Util_IDX::getDatabase($delete['idx']);

                $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($delete['mls_number']) . "'";

                // Any global criteria
                $idx->executeSearchWhereCallback($search_where);

                // Find IDX listing
                $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns(). " FROM `" .  $idx->getTable() . "` WHERE " . $search_where . ";");
                if (!empty($listing)) {
                    // Parse listing details
                    $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                    // History event data
                    $history_data = array('listing' => $listing);
                    if ($filter === 'recommended') {
                        $history_data['recommended'] = true;
                    }

                    // Track history event
                    (new $event_name($history_data, array(
                        new History_User_Lead($lead['id']),
                        $authuser->getHistoryUser()
                    )))->save($db);
                }
            }

        // An error happened
        } catch (Exception $e) {
            //$errors[] = $e->getMessage();
        }

    // Validation error
    } catch (UnexpectedValueException $e) {
        $errors[] = $e->getMessage();

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred while trying to remove the selected listing.';
        //$errors[] = $e->getMessage();
    }

    // Redirect back to page
    $authuser->setNotices($success, $errors);
    header('Location: ?id=' . $lead['id'] . '&type=' . $_GET['type'] . (isset($_GET['popup']) ? '&popup' : ''));
    exit;
}

// Count # of listings available
$counts = array();
foreach ($types as $type => $title) {
    try {
        // # of recommended listings
        if ($type === 'recommended') {
            $query = $db->prepare("SELECT COUNT(`id`) FROM `users_listings` WHERE `user_id` = :user_id AND (`agent_id` IS NOT NULL OR `associate` IS NOT NULL) ORDER BY `timestamp` DESC;");

        // # of saved favorites
        } else if ($type === 'saved') {
            $query = $db->prepare("SELECT COUNT(`id`) FROM `users_listings` WHERE `user_id` = :user_id AND `agent_id` IS NULL AND `associate` IS NULL;");

        // # of dismissed listings
        } else if ($type === 'dismissed') {
            $query = $db->prepare("SELECT COUNT(`id`) FROM `users_listings_dismissed` WHERE `user_id` = :user_id ORDER BY `timestamp` DESC;");

        // # of viewed listings
        } else if ($type === 'viewed') {
            $query = $db->prepare("SELECT COUNT(`id`) FROM `users_viewed_listings` WHERE `user_id` = :user_id;");
        }

        // Return # of results found
        $query->execute(array('user_id' => $lead['id']));
        $counts[$type] = $query->fetchColumn();

    // Database query
    } catch (PDOException $e) {
        $errors[] = 'Error occurred while counting ' . strtolower($title);
        //$errors[] = $e->getMessage();
        $counts[$type] = 0;
    }
}

// Current selected view
$filter = !empty($types[$_GET['type']]) ? $_GET['type'] : 'saved';

try {
    // Prepare queries to find agent/ISA for recommended listings
    if ($filter === 'recommended') {
        $find_agent = $db->prepare("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = ? LIMIT 1;");
        $find_associate = $db->prepare("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `associates` WHERE `id` = ? LIMIT 1;");
    }

// Let's continue as-if nothing went wrong (we can do without this info)
} catch (PDOException $e) {
    Log::error($e);
    //throw $e;
}

try {
    // SQL limit
    $num_limit = 10;
    $num_page = $_GET['p'];
    $num_total = $counts[$filter];
    $sql_limit = " LIMIT " . (int) $num_limit;
    if ($num_total > $num_limit) {
        $num_offset = ($num_page - 1) * $num_limit;
        if ($num_offset > 0 && $num_offset < $num_total) {
            $sql_limit = " LIMIT " . (int) $num_offset . ", " . (int) $num_limit;
        } else {
            $num_page = 1;
        }
    }

    // Generate pagination links
    $pagination = generate_pagination($num_total, $num_page, $num_limit);

    // Saved favorites
    if ($filter === 'saved') {
        $query = $db->prepare("SELECT `id`, `idx`, `mls_number`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings` WHERE `user_id` = :user_id AND `agent_id` IS NULL AND `associate` IS NULL ORDER BY `timestamp` DESC" . $sql_limit . ";");

    // Dismissed listings
    } else if ($filter === 'dismissed') {
        $query = $db->prepare("SELECT `id`, `idx`, `mls_number`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings_dismissed` WHERE `user_id` = :user_id ORDER BY `timestamp` DESC" . $sql_limit . ";");

    // Recommended listings
    } else if ($filter === 'recommended') {
        $query = $db->prepare("SELECT `id`, `idx`, `mls_number`, `agent_id`, `associate`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings` WHERE `user_id` = :user_id AND (`agent_id` IS NOT NULL OR `associate` IS NOT NULL) ORDER BY `timestamp` DESC" . $sql_limit . ";");

    // Recently viewed listings
    } else if ($filter === 'viewed') {
        $query = $db->prepare("SELECT `idx`, `mls_number`, `views`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_viewed_listings` WHERE `user_id` = :user_id ORDER BY `timestamp` DESC" . $sql_limit . ";");
    }

    // Load available listings of selected type
    $listings = array();
    $query->execute(array('user_id' => $lead['id']));
    foreach ($query->fetchAll() as $result) {
        try {
            // IDX resources
            $idx = Util_IDX::getIDX($result['idx']);
            $db_idx = Util_IDX::getDatabase($result['idx']);

            // Fields to load
            $fields = array('ListingMLS', 'ListingPrice', 'AddressCity', 'AddressState', 'AddressZipCode', 'AddressSubdivision', 'NumberOfBedrooms', 'NumberOfBathrooms', 'NumberOfSqFt', 'ListingImage');
            $fields = array_merge(Lang::$lang['IDX_LISTING_TAGS'], $fields);

            $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($result['mls_number']) . "'";

            // Any global criteria
            $idx->executeSearchWhereCallback($search_where);

            // Load listing details
            $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns(null, $fields) . " FROM `" . $idx->getTable() . "` WHERE " . $search_where . " LIMIT 1;");
            if (!empty($listing)) {
                $listing = Util_IDX::parseListing($idx, $db_idx, $listing);
            }

            // Recommended by agent
            if (!empty($result['agent_id'])) {
                $find_agent->execute(array($result['agent_id']));
                $result['agent'] = $find_agent->fetch();

            // Recommended by ISA
            } elseif (!empty($result['associate'])) {
                $find_associate->execute(array($result['associate']));
                $result['associate'] = $find_associate->fetch();
            }

            // Listing not found
            if (empty($listing)) {
                $result['error'] = true;
                $listings[] = $result;
            } else {
                // Add to collection
                $listings[] = array_merge($listing, $result);
            }

        // Unexpected error
        } catch (Exception $e) {
            $result['error'] = true;
            $listings[] = $result;
            //throw $e;
        }
    }

// Database error
} catch (PDOException $e) {
    $errors[] = 'An error occurred while loading ' . $filter . ' listings. Please contact support.';

// Unexpected error
} catch (Exception $e) {
    $errors[] = 'An unexpected error has occurred. Please contact support.';
}
