<?php


// Set ENV Variables
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['DOCUMENT_ROOT'] = $argv[1];
    $_SERVER['HTTP_HOST'] = $argv[2];
    $_SERVER['REQUEST_SCHEME'] = $argv[3];
}

// Include Config
$_GET['page'] = 'cron';
require_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';
@session_destroy();
$start = time();

// Restricted Access
if (is_null($argv)) {
    if (!Settings::isREW()) {
        die("Access Denied!" . PHP_EOL);
        // Running as REW
    } else {
        echo '<h1>Running as REW</h1>' . PHP_EOL;
        echo '<pre>';
        if (empty($_GET['email'])) {
            echo 'Email Required' . PHP_EOL;
            exit;
        }
    }
}

// Viewed Listings
$views = 20;

// Output
echo 'Creating Auto-Generated Saved Searches for Leads with ' . number_format($views) . '+ Viewed Listings:' . PHP_EOL;

// Search Leaders without Searches
$query = "SELECT"
       . " `u`.`id`,"
       . " `u`.`first_name`,"
       . " `u`.`last_name`,"
       . " `u`.`email`,"
       . " `u`.`num_listings`,"
       . " `u`.`agent`,"
       . " `u`.`opt_marketing`,"
       . " `u`.`verified`,"
       . " `u`.`email_alt`,"
       . " `u`.`email_alt_cc_searches`"
       . " FROM `" . TABLE_USERS . "` u"
       . " JOIN `agents` a ON `u`.`agent` = `a`.`id`"
       . " WHERE `a`.`auto_search` = 'true'"
       . " AND `opt_searches` = 'in'"
       . " AND `bounced` != 'true'"
       . " AND `fbl` != 'true'"
       . " AND `status` != 'pending'"
       . " AND `status` != 'rejected'"
       . " AND `u`.`auto_search` = 'N'"
       . " AND `num_listings` >= " . $views
       . " AND `u`.`id` NOT IN (SELECT `user_id` FROM `" . TABLE_SAVED_SEARCHES . "`)"
       . (!empty($_GET['email']) ? " AND `u`.`email` = '" . $db_users->cleanInput($_GET['email']) . "'" : '')
       . " ORDER BY `num_listings` DESC"
       . ";";
if ($leads = $db_users->query($query)) {
    while ($lead = $db_users->fetchArray($leads)) {
        // Output
        echo PHP_EOL  . str_repeat('-', 50) . PHP_EOL;
        echo '<strong>' . $lead['first_name'] . ' ' . $lead['last_name'] . ' (' . $lead['email'] . ')</strong>' . PHP_EOL . PHP_EOL;
        echo 'Viewed Listings: ' . number_format($lead['num_listings']) . PHP_EOL;

        // Only send from Canadian website if lead is opt-in
        if (Settings::getInstance()->LANG == 'en-CA' && $lead['opt_marketing'] != 'in') {
            echo 'Email not sent to ' . $lead['email'] . '. This lead has opted out from email notifications.' . PHP_EOL;
            continue;
        }

        // Check if e-mail host is blocked
        if (Validate::verifyWhitelisted($lead['email'])) {
            // Output
            echo $lead['email'] . '\'s e-mail provider is on the server block list - skipping automated e-mail' . PHP_EOL;

            // Skip to next lead
            continue;
        }

        // Check if e-mail host requires verification
        if (Validate::verifyRequired($lead['email']) || !empty(Settings::getInstance()->SETTINGS['registration_verify'])) {
            // User still not verified?
            if ($lead['verified'] != 'yes') {
                // Output
                echo $lead['email'] . '\'s e-mail provider is set to require e-mail verification on this server, but the account has not been verified yet - skipping automated e-mail' . PHP_EOL;

                // Skip to next search
                continue;
            }
        }

        // Reset IDX Objects
        unset($idx, $db_idx);

        // Find Viewed Listings by IDX Feed
        $query = "SELECT `idx`, COUNT(`id`) AS `total` FROM `" . TABLE_VIEWED_LISTINGS . "` WHERE `user_id` = '" . $lead['id'] . "' GROUP BY `idx` HAVING `total` >= " . $views . " ORDER BY `total` DESC;";
        if ($listings = $db_users->query($query)) {
            echo PHP_EOL;
            while ($listing = $db_users->fetchArray($listings)) {
                echo "\tLoading " . $listing['idx'] . ': ' . number_format($listing['total']) . PHP_EOL;

                // Switch Feed
                try {
                    $idx = Util_IDX::getIdx($listing['idx']);
                    $db_idx = Util_IDX::getDatabase($listing['idx']);

                    // Feed Is Configured; Break
                    break;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }

        // Query Error
        } else {
            echo 'MySQL Error: ' . $db_users->error() . PHP_EOL;
            echo 'MySQL Query: ' . $query . PHP_EOL;
            exit;
        }

        // If No IDX Then Move On To The Next Lead
        if (empty($idx) || empty($db_idx)) {
            echo PHP_EOL . "This lead does not meet the " . $views . " listing views requirement for an active IDX.  Skipping to the next lead" . PHP_EOL;
            continue;
        }

        // Require IDX Feed
        echo PHP_EOL . 'Primary Feed: ' . (!empty($idx) ? $idx->getLink() : 'Unknown') . PHP_EOL;

        // Search Data
        $count = 0;
        $data = array(
            'cities'    => array(),
            'types'     => array(),
            'prices'    => array(),
            'bedrooms'  => array(),
            'bathrooms' => array(),
            'sqft'      => array()
        );

        // Locate Viewed Listings in IDX Feed
        $query = "SELECT * FROM `" . TABLE_VIEWED_LISTINGS . "` WHERE `user_id` = '" . $lead['id'] . "' AND `idx` = '" . $db_users->cleanInput($idx->getLink()) . "' ORDER BY `timestamp` DESC;";
        if ($listings = $db_users->query($query)) {
            echo PHP_EOL;
            while ($listing = $db_users->fetchArray($listings)) {
                echo "\t" . Lang::write('MLS_NUMBER') . $listing['mls_number'] . ': ' . $listing['city'] . ': ' . $listing['type'] . ': ' . $listing['price'] . ': ' . $listing['bedrooms'] . ': ' . $listing['bathrooms'] . ': ' . $listing['sqft'];
                if (empty($listing['city']) && empty($listing['type']) && $listing['price'] <= 0 && $listing['bedrooms'] <= 0 && $listing['bathrooms'] <= 0 && $listing['sqft'] <= 0) {
                    echo ' - Skipping' . PHP_EOL;
                    continue;
                }
                $count++;
                echo ' - Included' . PHP_EOL;
                // Search Criteria
                if (!empty($listing['city'])) {
                    $data['cities'][] = $listing['city'];
                }
                if (!empty($listing['type'])) {
                    $data['types'][] = $listing['type'];
                }
                if ($listing['price'] > 0) {
                    $data['prices'][] = $listing['price'];
                }
                if ($listing['bedrooms'] > 0) {
                    $data['bedrooms'][] = $listing['bedrooms'];
                }
                if ($listing['bathrooms'] > 0) {
                    $data['bathrooms'][] = $listing['bathrooms'];
                }
                if ($listing['sqft'] > 0) {
                    $data['sqft'][] = $listing['sqft'];
                }
            }
        // Query Error
        } else {
            echo 'MySQL Error: ' . $db_users->error() . PHP_EOL;
            echo 'MySQL Query: ' . $query . PHP_EOL;
            exit;
        }

        echo PHP_EOL;

        // Require min count
        if ($count < $views) {
            echo 'Active Listings in Feed: ' . number_format($count) . PHP_EOL;
            echo 'WE REQUIRE ' . number_format($views) . ' LISTINGS, SKIPPING!' . PHP_EOL;
            continue;
        }

        // Search Criteria
        $criteria = array(
            'auto_search' => true,
            'idx'         => $idx->getLink()
        );

        // Search Types (Use Types Viewed >= 3 Times)
        $criteria['search_type'] = array();
        foreach (array_count_values($data['types']) as $value => $count) {
            if ($count >= 3) {
                $criteria['search_type'][] = $value;
            }
        }

        // Search Cities (Use Cities Viewed >= 3 Times)
        $criteria['search_city'] = array();
        foreach (array_count_values($data['cities']) as $value => $count) {
            if ($count >= 3) {
                $criteria['search_city'][] = $value;
            }
        }

        // Search Price Ranges
        $price_count = count($data['prices']);
        if ($price_count > 0) {
            $position   = ($price_count % 2 == 0) ? 1 : 2;
            // Sort Prices
            sort($data['prices']);
            $median     = ($position == 2) ? $data['prices'][($price_count/2)] : (($data['prices'][($price_count / 2) - 1]) + ($data['prices'][($price_count / 2)])) / 2;
            // Available Price Options
            $idx_prices = IDX_Panel::get('Price');
            $idx_prices = $idx_prices->getPriceOptions();
            foreach ($idx_prices as $key => $value) {
                if ($median >= $idx_prices[0]['value']) {
                    if ($value['value'] >= $median) {
                        $criteria['maximum_price'] = $value['value'];
                        $criteria['minimum_price'] = $idx_prices[$key-1]['value'];
                        break;
                    } else if ($median >= $idx_prices[(count($idx_prices) - 1)]['value']) {
                        $criteria['minimum_price'] = $idx_prices[(count($idx_prices) - 1)]['value'];
                        break;
                    }
                }
            }
        }

        // Search Bedrooms
        if (count($data['bedrooms']) > 0) {
            $avg_beds = round(array_sum($data['bedrooms']) / count($data['bedrooms']));
            // Available Bedroom Options
            $idx_bedrooms = IDX_Panel::get('Bedrooms');
            $idx_bedrooms = $idx_bedrooms->getOptions();
            foreach ($idx_bedrooms as $key => $value) {
                if ($value['value'] > $avg_beds) {
                    $criteria['minimum_bedrooms'] = $idx_bedrooms[$key-1]['value'];
                    break;
                } else if ($value['value'] == $avg_beds) {
                    $criteria['minimum_bedrooms'] = $idx_bedrooms[$key]['value'];
                    break;
                } else if ($avg_beds >= $idx_bedrooms[(count($idx_bedrooms) - 1)]['value']) {
                    $criteria['minimum_bedrooms'] = $idx_bedrooms[(count($idx_bedrooms) - 1)]['value'];
                    break;
                }
            }
        }

        // Search Bathrooms
        if (count($data['bathrooms']) > 0) {
            $avg_baths = round(array_sum($data['bathrooms']) / count($data['bathrooms']));
            // Available Bathroom Options
            $idx_bathrooms = IDX_Panel::get('Bathrooms');
            $idx_bathrooms = $idx_bathrooms->getOptions();
            foreach ($idx_bathrooms as $key => $value) {
                if ($value['value'] > $avg_baths) {
                    $criteria['minimum_bathrooms'] = $idx_bathrooms[$key-1]['value'];
                    break;
                } else if ($value['value'] == $avg_baths) {
                    $criteria['minimum_bathrooms'] = $idx_bathrooms[$key]['value'];
                    break;
                } else if ($avg_baths >= $idx_bathrooms[(count($idx_bathrooms) - 1)]['value']) {
                    $criteria['minimum_bathrooms'] = $idx_bathrooms[(count($idx_bathrooms) - 1)]['value'];
                    break;
                }
            }
        }

        // Search Square Feet
        if (count($data['sqft']) > 0) {
            $avg_sqft = round(array_sum($data['sqft']) / count($data['sqft']));
            // Available Sq. Ft Options
            $idx_sqft = IDX_Panel::get('Sqft');
            $idx_sqft = $idx_sqft->getOptions();
            foreach ($idx_sqft as $key => $value) {
                if ($value['value'] >= $avg_sqft) {
                    $criteria['minimum_sqft'] = $idx_sqft[$key-1]['value'];
                    break;
                } else if ($avg_sqft >= $idx_sqft[(count($idx_sqft) - 1)]['value']) {
                    $criteria['minimum_sqft'] = $idx_sqft[(count($idx_sqft) - 1)]['value'];
                    break;
                }
            }
        }

        // Searchable Fields
        $search_fields = search_fields($idx, 'AddressCity,ListingType,ListingPrice,NumberOfBedrooms,NumberOfBathrooms,NumberOfSqFt');

        // Build Criteria
        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $field = $search_fields[$field];
                if (empty($field)) {
                    continue;
                }
                if (in_array($field['form_field'], array('minimum_price', 'maximum_price', 'minimum_rent', 'maximum_rent'))) {
                    $value = '$' . Format::number($value);
                }
                if (in_array($field['field_form'], array('minimum_sqft', 'maximum_sqft'))) {
                    $value = Format::number($value);
                }
            }
        }

        // Generate Saved Search
        $search = array(
            'user_id'   => $lead['id'],
            'title'     => 'Auto Generated Search',
            'frequency' => 'weekly',
            'table'     => $idx->getTable(),
            'idx'       => $idx->getName(),
            'criteria' => serialize($criteria)
        );

        // Build INSERT Query
        $query = "INSERT INTO `" . TABLE_SAVED_SEARCHES . "` SET ";
        foreach ($search as $k => $v) {
            $query .= "`" . $k . "` = '" . $db_users->cleanInput($v) . "', ";
        }
        $query .= "`timestamp_created` = NOW();";

        // Execute Query
        if ($db_users->query($query)) {
            // Insert ID
            $insert_id = $db_users->insert_id();

            // Set `auto_search` to 'Y' to Prevent Duplicate Auto-Suggested Searches
            $db_users->query("UPDATE `" . TABLE_USERS . "` SET `auto_search` = 'Y' WHERE `id` = '" . $lead['id'] . "';");

            // Select Saved Search
            $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $insert_id . "';");

            // Log Event: Auto Generated Saved Search
            $event = new History_Event_Action_SavedSearch(array(
                'auto'   => true,
                'search' => $search
            ), array(
                new History_User_Lead($lead['id'])
            ));

            // Save Event
            $event->save();

            // Increase Search Count
            $update = $db_users->query("UPDATE `" . TABLE_USERS . "` SET `num_saved` = `num_saved` + 1 WHERE `id` = '" . $db_users->cleanInput($lead['id']) . "';");

            // Output
            echo 'Auto Generated Search Generated' . PHP_EOL;

            // Load Assigned Agent
            $agent = Backend_Agent::load($lead['agent']);

            // Setup Mailer
            $mailer = new Backend_Mailer_AutoSavedSearch(array(
                'url'       => ($agent['cms'] == 'true' && !empty($agent['cms_link']) ? sprintf(Settings::getInstance()->SETTINGS['URL_AGENT_SITE'], $agent['cms_link']) : Settings::getInstance()->SETTINGS['URL']),
                'lead'      => $lead,
                'search_id' => $insert_id,
                'criteria'  => $criteria,
                'signature' => $agent['signature'],         // Signature
                'append'    => ($agent['add_sig'] == 'Y')   // Append Signature
            ));

            // Check Outgoing Notification Settings
            $mailer = $agent->checkOutgoingNotifications($mailer, Backend_Agent_Notifications::OUTGOING_SEARCH_SUGGEST);

            // Set Sender
            $mailer->setSender($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);

            // Set Recipient
            $mailer->setRecipient($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

            // CC Alternate Email if Opted-in
            if (is_array($search['email_alt_cc_searches']) && in_array('saved_searches', $search['email_alt_cc_searches'])) {
                $mailer->addCC($search['email_alt']);
            }

            // Email Tags
            $tags = array();

            // Send Email
            if ($mailer->Send($tags)) {
                // Success
                echo 'Auto Generated Search Email Sent' . PHP_EOL;

                // Log Event: Email Sent to Lead
                $event = new History_Event_Email_Sent(array(
                    'subject'   => $mailer->getSubject(),
                    'message'   => $mailer->getMessage(),
                    'tags'      => $mailer->getTags()
                ), array(
                    new History_User_Lead($lead['id'])
                ));

                // Save to DB
                $event->save();

            // Mailer Error
            } else {
                echo 'ERROR: Email not sent to ' . $lead['email'] . PHP_EOL;
                echo $mailer->ErrorInfo . PHP_EOL;
            }
        } else {
            // Query Error
            echo 'MySQL Error: ' . $db_users->error() . PHP_EOL;
            echo 'MySQL Query: ' . $query . PHP_EOL;
            exit;
        }
    }

// Query Error
} else {
    echo 'MySQL Error: ' . $db_users->error() . PHP_EOL;
    echo 'MySQL Query: ' . $query . PHP_EOL;
    exit;
}

// Calculate script execution time
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

// Output
echo "\n\n" . "Running time: " . $hours . " hrs, " . $minutes . " mins, " . $seconds . " secs\n";
