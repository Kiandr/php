<?php

// @todo: cache to improve performance
// @todo: enforce strict password rules
// @todo: SQL pagination needed for searches/messages
// @todo: don't use stripTags: save as-is, escape on display
// @todo: track history events for preferences and opt status

// Must be logged in
if (!$user->isValid()) {
    $_SESSION['dashboard'] = true;
    $user->setRedirectUrl($_SERVER['REQUEST_URI']);
    $url_redirect = Settings::getInstance()->SETTINGS['URL_IDX_LOGIN'];
    if (isset($_GET['popup'])) {
        $url_redirect .= (stristr($url_redirect, '?') !== false ? '&' : '?') . 'popup';
    }
    header('Location: ' . $url_redirect);
    exit;
}

$settings = Settings::getInstance();

// Get Skin
$skin = Container::getInstance()->get(REW\Core\Interfaces\SkinInterface::class);

// DB connection
$db = DB::get();

// IDX instance
$idx = Util_IDX::getIDX();

// Load lead instance from ID
$user_id = $user->info('id');
$lead = Backend_Lead::load($user_id);

// Success notifications
$success = $user->info('success');
$success = is_array($success) ? $success : array();
$user->saveInfo('success', false);

// Error notifications
$errors = $user->info('errors');
$errors = is_array($errors) ? $errors : array();
$user->saveInfo('errors', false);

// Current page parameters
$query_params = array();
if (strpos($_SERVER['REQUEST_URI'], '?')) {
    parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_params);
    unset($query_params['delete']);
    unset($query_params['filter']);
    unset($query_params['thread']);
    unset($query_params['form']);
    unset($query_params['next']);
    unset($query_params['prev']);
}

// Dashboard views
$dashboard_views = array(
    'listings'      => array('title' => 'My Listings'),//, 'count' => 0)
    'searches'      => array('title' => 'My Searches'),//, 'count' => 0)
    'messages'      => array('title' => 'My Messages'),//, 'count' => 0)
    'preferences'   => array('title' => 'My Preferences'),//, 'count' => 0)
);

// Generate URL to dashboard views
foreach ($dashboard_views as $link => $view) {
    $dashboard_views[$link] = array_merge($view, array(
        'url'   => '?' . http_build_query(array_merge($query_params, array('view' => $link))),
        'link'  => $link
    ));
}

// Use current dashboard view or choose first from the list
$current_view = in_array($_GET['view'], array_keys($dashboard_views)) ? $dashboard_views[$_GET['view']] : false;
$current_view = $current_view ?: array_shift(array_values($dashboard_views));

// Page title from current view
$page_title = $current_view['title'];

// Remove MLS Disclaimer from views without IDX listings
if (!in_array($current_view['link'], array('listings'))) {
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->hideDisclaimer();
}

/**
 * Load listing activity
 */
if ($current_view['link'] === 'listings') {
    try {
        // Load user's saved favorites
        $bookmarked = $user->getSavedListings($idx);

        // Load user's dismissed listings
        $dismissed = $user->getDismissedListings($idx);

    // Silent failure
    } catch (Exception $e) {
    }

    // Listing filters
    $listing_filters = array(
        'saved'         => array('title' => 'My ' . Locale::spell('Favorite') . ' Listings'),//, 'count' => 0),
        'recommended'   => array('title' => 'Recommended Listings'),//, 'count' => 0),
        'dismissed'     => array('title' => 'Dismissed Listings'),//, 'count' => 0),
        'viewed'        => array('title' => 'Recently Viewed'),//, 'count' => 0)
    );

    // Hide dismissed filter
    if (!Skin::hasFeature(Skin::DASHBOARD_SHOW_DISMISSED)) {
        unset($listing_filters['dismissed']);
    }

    // Generate URL to listing filters
    foreach ($listing_filters as $link => $filter) {
        $listing_filters[$link] = array_merge($filter, array(
            'url'   => '?' . http_build_query(array_merge($query_params, array('filter' => $link))),
            'link'  => $link
        ));
    }

    // Use current listing view or choose first from the list
    $current_filter = in_array($_GET['filter'], array_keys($listing_filters)) ? $listing_filters[$_GET['filter']] : false;
    $current_filter = $current_filter ?: array_shift(array_values($listing_filters));

    try {
        // Load saved listings
        if ($current_filter['link'] === 'saved') {
            $query = $db->prepare("SELECT `mls_number`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings` WHERE `idx` = :idx AND `user_id` = :user_id AND `agent_id` IS NULL AND `associate` IS NULL;");
        // Load recommended listings
        } elseif ($current_filter['link'] === 'recommended') {
            $query = $db->prepare("SELECT `mls_number`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings` WHERE `idx` = :idx AND `user_id` = :user_id"
                . " AND `mls_number` NOT IN ('" . implode("', '", array_merge($bookmarked, $dismissed)) . "')"
                . " AND (`agent_id` IS NOT NULL OR `associate` IS NOT NULL)"
            . ";");

        // Load recently viewed listings (last 20)
        } elseif ($current_filter['link'] === 'viewed') {
            $query = $db->prepare("SELECT `mls_number` FROM `users_viewed_listings` WHERE `idx` = :idx AND `user_id` = :user_id"
                //. " AND `mls_number` NOT IN ('" . implode("', '", array_merge($bookmarked, $dismissed)) . "')"
            . " ORDER BY `timestamp` DESC LIMIT 20;");
        }

        if ($current_filter['link'] === 'dismissed') {
            $mls_numbers = $dismissed;
        } else {
            // Execute database query
            $query->execute(array('user_id' => $user_id, 'idx' => $idx->getName()));
            $mls_numbers = $query->fetchAll(PDO::FETCH_COLUMN);
        }

        // SQL query
        $limit = $page->getSkin()->config('dashboard.results.limit');
        $sql_where = "`" . $idx->field('ListingMLS') . "` IN ('" . implode("', '", $mls_numbers) . "')";
        $sql_order = " ORDER BY `" . $idx->field('ListingMLS') . "` DESC";
        $sql_limit = " LIMIT " . ($limit + 1);
        $next_mls = $_GET['next'];
        $prev_mls = $_GET['prev'];
        $dir = null;

        // SQL pagination
        $pagination = array();
        if (!empty($next_mls)) {
            $sql_where .= " AND `" . $idx->field('ListingMLS') . "` < '" . $db_idx->cleanInput($next_mls) . "'";
            $dir = 'next';
        } else if ($prev_mls) {
            $sql_where .= " AND `" . $idx->field('ListingMLS') . "` > '" . $db_idx->cleanInput($prev_mls) . "'";
            $sql_order = " ORDER BY `" . $idx->field('ListingMLS') . "` ASC";
            $dir = 'prev';
        }

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Search IDX listings
        $query = $db_idx->query("SELECT SQL_CACHE "
            . $idx->selectColumns()
            . " FROM `" . $idx->getTable() . "`"
            . "WHERE " . $sql_where . $sql_order . $sql_limit);

        // Load available listings
        $count = 0;
        $listings = array();
        while ($listing = $db_idx->fetchArray($query)) {
            if (++$count > $num_limit) {
                $listings[] = Util_IDX::parseListing($idx, $db_idx, $listing);
            }
        }

        // Pagination details
        $count = count($listings);
        unset($prev, $next);

        // More listings found
        if ($count > $limit) {
            array_pop($listings);
            if ($dir === 'prev') {
                $next = $listings[0];
                $prev = $listings[$count - 2];
            } elseif ($dir === 'next') {
                $next = $listings[$count - 2];
                $prev = $listings[0];
            } else {
                $next = $listings[$count - 2];
            }

        // Next page of results
        } elseif ($dir === 'prev') {
            $next = $listings[0];

        // Prev page of results
        } elseif ($dir === 'next') {
            $prev = $listings[0];
        }

        // Reverse resultset
        if ($dir === 'prev') {
            $listings = array_reverse($listings);
        }
        reset($listings);

        // Link to next page
        if (!empty($next)) {
            $pagination['next'] = '?' . http_build_query(array_merge($query_params, array(
                'filter' => $current_filter['link'],
                'next' => $next['ListingMLS']
            )));
        }

        // Link to prev page
        if (!empty($prev)) {
            $pagination['prev'] = '?' . http_build_query(array_merge($query_params, array(
                'filter' => $current_filter['link'],
                'prev' => $prev['ListingMLS']
            )));
        }

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred while loading ' . $current_filter['link'] . ' listings.';
        //$errors[] = $e->getMessage();
    }

/**
 * Load search activity
 */
} elseif ($current_view['link'] === 'searches') {
    // Search filters
    $search_filters = array(
        'saved'     => array('title' => 'My Saved Searches'),//, 'count' => 0),
        'suggested' => array('title' => 'Suggested Searches'),//, 'count' => 0),
        'viewed'    => array('title' => 'Recent Searches'),//, 'count' => 0)
    );

    // Generate URL to search filters
    foreach ($search_filters as $link => $filter) {
        $search_filters[$link] = array_merge($filter, array(
            'url'   => '?' . http_build_query(array_merge($query_params, array('filter' => $link))),
            'link'  => $link
        ));
    }

    // Use current listing view or choose first from the list
    $current_filter = in_array($_GET['filter'], array_keys($search_filters)) ? $search_filters[$_GET['filter']] : false;
    $current_filter = $current_filter ?: array_shift(array_values($search_filters));

    // Handle request to delete search
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['delete'])) {
        try {
            // Search table name
            $table_name = false;
            if ($current_filter['link'] == 'saved') {
                $table_name = 'users_searches';
            }
            if ($current_filter['link'] == 'suggested') {
                $table_name = 'users_viewed_searches';
            }
            if ($current_filter['link'] == 'viewed') {
                $table_name = 'users_viewed_searches';
            }

            // Locate search record
            $select = $db->prepare("SELECT * FROM `" . $table_name . "` WHERE `user_id` = :user_id AND `id` = :id LIMIT 1;");
            $select->execute(array('id' => $_POST['delete'], 'user_id' => $user_id));
            $search = $select->fetch();

            // Search not found
            if (empty($search)) {
                throw new UnexpectedValueException('The selected ' . $current_filter['link'] . ' search could not be found.');
            }

            // Delete search record
            $delete = $db->prepare("DELETE FROM `" . $table_name . "` WHERE `user_id` = :user_id AND `id` = :id;");
            $delete->execute(array('id' => $search['id'], 'user_id' => $search['user_id']));

                        //Track Event
                        $event = new History_Event_Delete_SavedSearch(array(
                            'search' => $search
                        ), array(
                            new History_User_Lead($user_id)
                        ));

                        // Save to DB
                        $event->save();

                        // Decrease Search Count
                        $decrease = $db->prepare("UPDATE `users` SET `num_saved` = IF(`num_saved` > 0, `num_saved` - 1, 0) WHERE `id` = :id;");
                        $decrease->execute(array(
                               'id' => $user_id
                        ));

            // Success
            $success[] = 'The selected ' . $current_filter['link'] . ' search has successfully been removed.';

            // Trigger hook for deleting saved search
            if ($current_filter['link'] == 'saved') {
                Hooks::hook(Hooks::HOOK_LEAD_SEARCH_REMOVED)->run($lead->getRow(), $search);
            }

        // Validation error
        } catch (UnexpectedValueException $e) {
            $errors[] = $e->getMessage();

        // Database error
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while removing ' . $current_filter['link'] . ' search.';
            //$errors[] = $e->getMessage();
        }

        // Save notices
        $user->saveInfo('errors', $errors);
        $user->saveInfo('success', $success);

        // Redirect back to searches
        $url_redirect = '?' . http_build_query(array_merge($query_params, array('filter' => $current_filter['link'])));
        header('Location: ' . $url_redirect);
        exit;
    }

    try {
        // SQL limit
        $limit = 25;
        $sql_limit = " LIMIT " . $limit;

        // Load saved searches
        if ($current_filter['link'] == 'saved') {
            $query = $db->prepare("SELECT *, UNIX_TIMESTAMP(`timestamp_sent`) AS `timestamp_sent`, UNIX_TIMESTAMP(`timestamp_created`) AS `timestamp` FROM `users_searches` WHERE `idx` = :idx AND `user_id` = :user_id ORDER BY `timestamp_updated` DESC" . $sql_limit . ";");

        // Load suggested searches
        } elseif ($current_filter['link'] == 'suggested') {
            $query = $db->prepare("SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_viewed_searches` WHERE `idx` = :idx AND `user_id` = :user_id AND (`agent_id` IS NOT NULL OR `associate` IS NOT NULL) ORDER BY `timestamp` DESC" . $sql_limit . ";");

        // Load recently viewed searches (last 10 searches)
        } elseif ($current_filter['link'] == 'viewed') {
            $query = $db->prepare("SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_viewed_searches` WHERE `idx` = :idx AND `user_id` = :user_id AND `agent_id` IS NULL AND `associate` IS NULL ORDER BY `timestamp` DESC LIMIT 10;");
        }

        // Execute database query
        $query->execute(array(
            'user_id' => $user_id,
            'idx' => $idx->getName()
        ));

        // Load searches
        $searches = array();
        foreach ($query->fetchAll() as $search) {
            try {
                // IDX resources
                $idx = Util_IDX::getIDX($search['idx']);
                $db_idx = Util_IDX::getDatabase($search['idx']);

                // Saved search
                if ($current_filter['link'] == 'saved') {
                    // Saved search URL
                    $search['url'] = sprintf(Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'], $search['id']);

                    // Saved search criteria
                    $search_criteria = unserialize($search['criteria']);
                    if (!empty($search_criteria) && is_array($search_criteria)) {
                        if ($search_criteria['search_by'] === 'map') {
                            $search['url'] = Settings::getInstance()->URLS['URL_IDX_MAP'] . '?saved_search_id=' . $search['id'];
                        }
                    }
                } else {
                    // URL to view search
                    $search['url'] = Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'] . '?' . $search['url'];
                }

                // URL to delete search record
                $search['url_delete'] = '?' . http_build_query(array_merge($query_params, array('delete' => $search['id'])));

                // Add search to collection
                $searches[] = $search;

            // Unexpected error
            } catch (Exception $e) {
                //$errors[] = $e->getMessage();
                //throw $e;
            }
        }

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred while loading ' . $current_filter['link'] . ' searches.';
        //$errors[] = $e->getMessage();
    }

/**
 * View preferences
 */
} elseif ($current_view['link'] === 'preferences') {
    try {
        // Load lead's preferences
        $preferences = $lead->getRow();

        // Choose current form to display
        $form_values = array('preferences', 'password');
        $current_form = in_array($_GET['form'], $form_values) ? $_GET['form'] : false;
        $current_form = $current_form ?: array_shift($form_values);

        // Opt-in messages
        $opt_text = array();
        $opt_text['opt_marketing'] = Settings::get('anti_spam.consent_text') ?: $skin->getDefaultConsentMessage() ;
        if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
            $opt_text['opt_texts'] = Settings::get('anti_spam_sms.consent_text');
        }

        // Reset bounced when email changes or opting in to marketing
        if ($preferences['bounced'] === 'true') {
            if (
                (strtoupper($_POST['email']) !== strtoupper($preferences['email'])) ||
                ($_POST['opt_marketing'] === 'in')
            ) {
                $preferences['bounced'] = 'false';
            }
        }

        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Update preferences
            if ($_POST['form'] === 'preferences') {

                // Alternate email opt in settings
                if (empty($_POST['email_alt']) && !empty($_POST['email_alt_cc_searches'])) {
                    $errors[] = 'Failed to opt-in alternate email for CC emails: A valid alternate email is required.';
                }
                $email_alt_cc_searches = (!empty($_POST['email_alt_cc_searches']) ? 'true' : 'false');

                try {
                    // POST data
                    $data = array(
                        'user_id'       => $user_id,
                        'first_name'    => Format::trim($_POST['first_name']),
                        'last_name'     => Format::trim($_POST['last_name']),
                        'email'         => Format::trim($_POST['email']),
                        'phone'         => Format::trim($_POST['phone']),
                        'phone_cell'    => Format::trim($_POST['phone_cell']),
                        'phone_work'    => Format::trim($_POST['phone_work']),
                        'phone_fax'     => Format::trim($_POST['phone_fax']),
                        'address1'      => Format::trim($_POST['address1']),
                        'city'          => Format::trim($_POST['city']),
                        'state'         => Format::trim($_POST['state']),
                        'zip'           => Format::trim($_POST['zip']),
                        'country'       => Format::trim($_POST['country']),
                        'opt_searches'  => ($_POST['opt_searches'] === 'in' ? 'in' : 'out'),
                        'opt_marketing' => ($_POST['opt_marketing'] === 'in' ? 'in' : 'out'),
                        'opt_texts'     => $preferences['opt_texts'],
                        'verified'      => $preferences['verified'],    // email flag: verified
                        'bounced'       => $preferences['bounced'],     // email flag: bounced
                        'email_alt'     => Format::trim($_POST['email_alt']),
                        'email_alt_cc_searches' => $email_alt_cc_searches
                    );

                    // Check For Potentially Malicious Submission Data
                    $check_fields = array(
                        'onc5khko' => $_POST['onc5khko'],
                        'sk5tyelo' => $_POST['sk5tyelo'],
                        'address1' => $_POST['address1'],
                        'city' => $_POST['city'],
                        'state' => $_POST['state'],
                        'zip' => $_POST['zip'],
                        'country' => $_POST['country']
                    );
                    list($not_allowed, $bad_fields) = Validate::formFields($check_fields, $_POST);
                    if (!empty($bad_fields)) {
                        throw new UnexpectedValueException('We are sorry.  We are unable to process your submission as the following fields: ' . implode(', ', $bad_fields) . ' contains at least one of the following characters: ' . implode(', ', Format::htmlspecialchars($not_allowed)));
                    }

                    // Opt-in/Opt-out of text messages
                    if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) {
                        $data['opt_texts'] = $_POST['opt_texts'] === 'in' ? 'in' : 'out';
                    }

                    // Strip HTML from POST data
                    $data = array_map(function ($val) {
                        return Format::stripTags($val);
                    }, $data);

                    // Require valid email
                    $email_value = $data['email'];
                    if (!Validate::email($email_value)) {
                        throw new UnexpectedValueException('The provided email address is not valid.');

                    // Email changed - check for duplicate
                    } elseif ($email_value != $preferences['email']) {
                        $check_email = $db->prepare("SELECT `id` FROM `users` WHERE `email` = ? AND `id` != ? LIMIT 1;");
                        $check_email->execute(array($email_value, $user_id));
                        if ($check_email->fetchColumn > 0) {
                            throw new UnexpectedValueException('The provided email address is already being used.');
                        }
                        // Set email flags to false
                        $data['verified'] = 'false';
                        $data['bounced'] = 'false';
                    }

                    // Require valid phone
                    foreach (array(
                        'phone'         => 'primary phone',
                        'phone_cell'    => 'secondary phone',
                        'phone_work'    => 'work phone',
                        'phone_fax'     => 'fax'
                    ) as $phone_input => $phone_name) {
                        $phone_value = $data[$phone_input];
                        if (empty($phone_value)) {
                            continue;
                        }
                        if (!Validate::phone($phone_value)) {
                            throw new UnexpectedValueException('The provided ' . $phone_name . ' number is not valid.');
                        }
                    }

                    // Phone number is required
                    if (!empty(Settings::getInstance()->SETTINGS['registration_phone'])) {
                        if (!Validate::phone($data['phone'])) {
                            throw new UnexpectedValueException('You must provide a valid primary phone number.');
                        }
                    }

                    // Check for change in subscription
                    $unsubscribe = array();
                    if ($preferences['opt_searches'] != $data['opt_searches'] && $data['opt_searches'] == 'out') {
                        $unsubscribe[] = 'opt_searches';
                    }
                    if ($preferences['opt_marketing'] != $data['opt_marketing'] && $data['opt_marketing'] == 'out') {
                        $unsubscribe[] = 'opt_marketing';
                    }
                    if (!empty($unsubscribe)) {
                        // Log Event: Lead Unsubscribed
                        $event = new History_Event_Action_Unsubscribe(array(
                            'unsubscribe' => $unsubscribe
                        ), array(
                            new History_User_Lead($data['user_id'])
                        ));

                        // Save to DB
                        $event->save($db);
                    }

                    // Update user preferences
                    $db->prepare("UPDATE `users` SET "
                        . "`first_name`		= :first_name,"
                        . "`last_name`		= :last_name,"
                        . "`email`			= :email,"
                        . "`phone`			= :phone,"
                        . "`phone_cell`		= :phone_cell,"
                        . "`phone_work`		= :phone_work,"
                        . "`phone_fax`		= :phone_fax,"
                        . "`address1`		= :address1,"
                        . "`city`			= :city,"
                        . "`state`			= :state,"
                        . "`zip`			= :zip,"
                        . "`country`		= :country,"
                        . "`opt_searches`	= :opt_searches,"
                        . "`opt_marketing`	= :opt_marketing,"
                        . "`opt_texts`		= :opt_texts,"
                        . "`verified`		= :verified,"
                        . "`bounced`		= :bounced,"
                        . "`email_alt`		= :email_alt,"
                        . "`email_alt_cc_searches`	= :email_alt_cc_searches"
                        . " WHERE `id` = :user_id"
                    . ";")->execute($data);

                    // Success
                    $success[] = 'Your preferences have been successfully been updated.';

                // Validation error
                } catch (UnexpectedValueException $e) {
                    $errors[] = $e->getMessage();

                // Database error
                } catch (PDOException $e) {
                    $errors[] = 'An error occurred while attempting to update your preferences.';
                    $errors[] = $e->getMessage();

                // Unexpected error
                } catch (Exception $e) {
                    $errors[] = 'An unexpected error occurred while updating your preferences.';
                    $errors[] = $e->getMessage();
                }
            }

            // Change password
            if ($_POST['form'] === 'password') {
                try {
                    // Provided password values
                    $current_password = $_POST['current_password'];
                    $confirm_password = $_POST['confirm_password'];
                    $new_password = $_POST['new_password'];

                    // Check current password
                    if (!empty($preferences['password'])) {
                        if (!$user->authenticate($preferences['email'], $current_password)) {
                            throw new UnexpectedValueException('The current password you provided is incorrect.');
                        }
                    }

                    // Require new password
                    if (!Validate::stringRequired($new_password)) {
                        throw new UnexpectedValueException('You must provide a new password to use.');
                    }

                    // New password is the same as current
                    if ($new_password == $current_password) {
                        throw new UnexpectedValueException('The new password you provided is the same as your current password.');
                    }

                    // Confirm new password
                    if ($new_password != $confirm_password) {
                        throw new UnexpectedValueException('The two passwords you provided did not match.');
                    }

                    // Validate new password
                    if (!empty($new_password)) {
                        try {
                            Validate::password($new_password);
                        } catch (Exception $e) {
                            throw new UnexpectedValueException($e->getMessage());
                        }
                    }

                    $new_password = $user->encryptPassword($new_password);

                    // Update database record
                    $query = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?;");
                    $query->execute(array($new_password, $user_id));

                    // Update session password
                    $user->setPassword($new_password);

                    // Success
                    $success[] = 'Your password has successfully been changed.';
                    unset($_POST['form']);

                // Validation error
                } catch (UnexpectedValueException $e) {
                    $errors[] = $e->getMessage();

                // Database error
                } catch (PDOException $e) {
                    $errors[] = 'An error occurred while attempting to change your password.';
                    //$errors[] = $e->getMessage();

                // Unexpected error
                } catch (Exception $e) {
                    $errors[] = 'An unexpected error occurred while changing your password.';
                    //$errors[] = $e->getMessage();
                }
            }

            // Save notices
            $user->saveInfo('errors', $errors);
            $user->saveInfo('success', $success);

            // Redirect back to preferences
            $url_redirect = '?' . http_build_query(array_merge($query_params, array('form' => $_POST['form'])));
            header('Location: ' . $url_redirect);
            exit;
        }

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred while loading your account preferences.';
        //$errors[] = $e->getMessage();
    }

/**
 * Load message thread
 */
} elseif ($current_view['link'] === 'messages' && !empty($_GET['thread'])) {
    try {
        try {
            // Find message thread in database
            $query = $db->prepare("SELECT `m`.*"
                . ", CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent`"
                . ", `a`.`image` AS `agent_photo`"
                . ", UNIX_TIMESTAMP(`m`.`timestamp`) AS `timestamp`"
                . " FROM `users_messages` `m`"
                . " LEFT JOIN `agents` `a` ON `m`.`agent_id` = `a`.`id`"
                . " WHERE `m`.`user_id` = ? AND `m`.`id` = ? AND `m`.`reply` = 'N'"
                . " LIMIT 1"
            . ";");
            $query->execute(array($user_id, $_GET['thread']));
            if (!$thread = $query->fetch()) {
                throw new UnexpectedValueException('The selected message could not be found.');
            }

        // Catch thrown exceptions
        } catch (UnexpectedValueException $e) {
            throw new Exception($e->getMessage());
        } catch (PDOException $e) {
            throw new Exception('An error occurred while locating the selected message.');
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while locating the selected message.');
        }

    // Redirect on error
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        $user->saveInfo('errors', $errors);
        header('Location: ' . '?' . http_build_query($query_params));
        exit;
    }

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Delete specified reply
        $delete = $_POST['delete'];
        if (!empty($delete)) {
            try {
                // Flag reply as deleted
                $query = $db->prepare("UPDATE `users_messages` SET `user_del` = 'Y' WHERE `user_id` = ? AND `id` = ?;");
                $query->execute(array($user_id, $delete));

                // Success
                $success[] = 'The selected message has successfully been removed.';

            // Database error
            } catch (PDOException $e) {
                $errors[] = 'An error occurred while removing the selected message.';
                //$errors[] = $e->getMessage();
            }

        // Send reply to agent
        } else if ($_POST['form'] === 'reply') {
            try {
                // Format message
                $message = Format::stripTags($_POST['message']);
                $message = Format::trim($message);

                // Require message
                if (empty($message)) {
                    throw new UnexpectedValueException('You must provide a message to send.');
                }

                // Find agent who sent message
                $agent_id = $thread['agent_id'];
                $agent = Backend_Agent::load($agent_id);

                // Insert reply into database
                $db->prepare("INSERT INTO `users_messages` SET "
                    . "`sent_from`	= 'lead',"
                    . "`category`	= :category,"
                    . "`subject`	= :subject,"
                    . "`message`	= :message,"
                    . "`agent_id`	= :agent_id,"
                    . "`user_id`	= :user_id,"
                    . "`user_read`	= 'Y',"
                    . "`reply`		= 'Y',"
                    . "`timestamp`	= NOW()"
                . ";")->execute(array(
                    'user_id'   => $user_id,
                    'agent_id'  => $agent->getId(),
                    'category'  => $thread['category'],
                    'subject'   => $thread['subject'],
                    'message'   => $message
                ));

                // Success
                $success[] = 'Your reply has successfully been sent.';

                // Send notification email to agent
                $mailer = new Backend_Mailer_MessageReply(array(
                    'user_id'   => $user_id,
                    'agent_id'  => $thread['agent_id'],
                    'subject'   => $thread['subject'],
                    'message'   => $message
                ));

                // Check notification settings & send
                if ($agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LEAD_INQUIRED)) {
                    $mailer->Send();
                }

            // Validation error
            } catch (UnexpectedValueException $e) {
                $errors[] = $e->getMessage();

            // Database error
            } catch (PDOException $e) {
                $errors[] = 'An error occurred while attempting to send your response.';
                //$errors[] = $e->getMessage();

            // Unexpected error
            } catch (Exception $e) {
                $errors[] = 'An unexpected error occurred while sending your response.';
                //$errors[] = $e->getMessage();
            }
        }

        // Save notices
        $user->saveInfo('errors', $errors);
        $user->saveInfo('success', $success);

        // Redirect back to message thread
        $url_redirect = '?' . http_build_query(array_merge($query_params, array('thread' => $thread['id'])));
        header('Location: ' . $url_redirect);
        exit;
    }

    try {
        // Lead message formatting
        if ($thread['sent_from'] == 'lead') {
            $thread['message'] = Format::htmlspecialchars($thread['message']);
            $thread['message'] = nl2br($thread['message']);
        }

        // Load replies
        $replies = array();
        $query = $db->prepare("SELECT `m`.*"
            . ", CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent`"
            . ", `a`.`image` AS `agent_photo`"
            . ", UNIX_TIMESTAMP(`m`.`timestamp`) AS `timestamp`"
            . " FROM `users_messages` `m`"
            . " LEFT JOIN `agents` `a` ON `m`.`agent_id` = `a`.`id`"
            . " WHERE `m`.`user_id` = ? AND `m`.`category` = ? AND `m`.`reply` = 'Y' AND `m`.`user_del` = 'N'"
            . " ORDER BY `m`.`timestamp` ASC"
        . ";");
        $query->execute(array($user_id, $thread['id']));
        foreach ($query->fetchAll() as $reply) {
            // Lead message formatting
            if ($reply['sent_from'] == 'lead') {
                $reply['message'] = Format::htmlspecialchars($reply['message']);
                $reply['message'] = nl2br($reply['message']);
            }

            // Add to collection
            $replies[] = $reply;
        }

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred loading replies to this message.';
        //$errors[] = $e->getMessage();
    }

    try {
        // Mark entire thread as read
        $query = $db->prepare("UPDATE `users_messages` SET `user_read` = 'Y' WHERE `category` = ?;");
        $query->execute(array($thread['id']));
    } catch (PDOException $e) {
    }

    // Viewing message thread
    $current_view['link'] = 'message';

/**
 * Load messages
 */
} elseif ($current_view['link'] === 'messages') {
    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Delete specified message
        $delete = $_POST['delete'];
        if (!empty($delete)) {
            try {
                // Flag thread as deleted
                $query = $db->prepare("UPDATE `users_messages` SET `user_del` = 'Y' WHERE `user_id` = ? AND `category` = ?;");
                $query->execute(array($user_id, $delete));

                // Success
                $success[] = 'The selected message has successfully been removed.';

            // Database error
            } catch (PDOException $e) {
                $errors[] = 'An error occurred while removing the selected message.';
                //$errors[] = $e->getMessage();
            }

        // Send new message to agent
        } else if ($_POST['form'] === 'compose') {
            try {
                // POST data
                $data = array(
                    'subject' => Format::trim($_POST['subject']),
                    'message' => Format::trim($_POST['message']),
                );

                // Strip HTML from user input
                $data['subject'] = Format::stripTags($data['subject']);
                $data['message'] = Format::stripTags($data['message']);

                // Require subject and message
                if (empty($data['subject']) || empty($data['message'])) {
                    throw new UnexpectedValueException('You must provide both a subject and message.');
                }

                // Find assigned agent
                $agent_id = $lead->info('agent');
                $agent = Backend_Agent::load($agent_id);

                // Insert message into database
                $db->prepare("INSERT INTO `users_messages` SET "
                    . "`sent_from`	= 'lead',"
                    . "`user_read`	= 'Y',"
                    . "`user_id`	= :user_id,"
                    . "`agent_id`	= :agent_id,"
                    . "`subject`	= :subject,"
                    . "`message`	= :message,"
                    . "`timestamp`	= NOW()"
                . "")->execute(array(
                    'user_id'   => $user_id,
                    'agent_id'  => $agent->getId(),
                    'subject'   => $data['subject'],
                    'message'   => $data['message']
                ));

                // Update message category
                $message_id = $db->lastInsertId();
                $query = $db->prepare("UPDATE `users_messages` SET `category` = :id WHERE `id` = :id;");
                $query->execute(array('id' => $message_id));

                // Send notification email to agent
                $mailer = new Backend_Mailer_MessageSent(array(
                    'user_id'   => $user_id,
                    'agent_id'  => $agent->getId(),
                    'subject'   => $data['subject'],
                    'message'   => $data['message']
                ));

                // Check notification settings & send
                if ($agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LEAD_INQUIRED)) {
                    $mailer->Send();
                }

                // Success
                $success[] = 'Your message has successfully been sent.';
                unset($_POST['form']);

            // Validation error
            } catch (UnexpectedValueException $e) {
                $errors[] = $e->getMessage();

            // Database error
            } catch (PDOException $e) {
                $errors[] = 'An error occurred while attempting to send your message.';
                //$errors[] = $e->getMessage();

            // Unexpected error
            } catch (Exception $e) {
                $errors[] = 'An unexpected error occurred while sending your message.';
                //$errors[] = $e->getMessage();
            }
        }

        // Save notices
        $user->saveInfo('errors', $errors);
        $user->saveInfo('success', $success);

        // Redirect back to searches
        $url_redirect = '?' . http_build_query(array_merge($query_params, array('form' => $_POST['form'])));
        header('Location: ' . $url_redirect);
        exit;
    }

    try {
        // Active threads
        $threads = array();
        $query = $db->prepare("SELECT "
            . "`m`.`category` AS `id`"
            . ", `m`.`subject`"
            . ", SUM(`user_read` = 'N') AS `unread`"
            . ", SUM(`m`.`id` != `m`.`category`) AS `replies`"
            . ", CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent`"
            . ", UNIX_TIMESTAMP(MAX(`m`.`timestamp`)) AS `timestamp`"
            . " FROM `users_messages` `m`"
            . " LEFT JOIN `agents` `a` ON `m`.`agent_id` = `a`.`id`"
            . " WHERE EXISTS (SELECT `m2`.`id` FROM `users_messages` `m2` WHERE `m2`.`id` = `m`.`category` AND `m2`.`user_del` = 'N' LIMIT 1)"
            . " AND `m`.`user_id` = ?"
            . " GROUP BY `m`.`category`"
            . " ORDER BY `m`.`timestamp` DESC"
        . ";");
        $query->execute(array($user_id));
        while ($thread = $query->fetch()) {
            // URL to view messages
            $thread['url'] = '?' . http_build_query(array_merge($query_params, array('thread' => $thread['id'])));

            // Add to collection
            $threads[] = $thread;
        }

    // Database error
    } catch (PDOException $e) {
        $errors[] = 'An error occurred while loading your messages.';
        //$errors[] = $e->getMessage();
    }
}
