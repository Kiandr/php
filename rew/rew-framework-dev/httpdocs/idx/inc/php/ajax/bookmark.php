<?php

try {
    // JSON response data
    $json = array();

    // Require MLS # and IDX feed name
    $listing_mls = $_POST['mls_number'];
    $listing_feed = $_POST['feed'];
    if (empty($listing_mls) || empty($listing_feed)) {
        throw new UnexpectedValueException('Must specify an MLS # and IDX feed');
    }

    // Require IDX resources
    include_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

    // Must be logged in
    if (!$user->isValid()) {
        // Redirect to registration form
        $json['register'] = Settings::getInstance()->SETTINGS['URL_IDX_REGISTER'];

        // Save data to session (to bookmark after login/register)
        $_SESSION['bookmarkListing'] = $listing_mls;
        $_SESSION['bookmarkFeed'] = $listing_feed;
    } else {
        // Close session
        @session_write_close();

        // IDX resources
        $idx = Util_IDX::getIDX($listing_feed);
        $db_idx = Util_IDX::getDatabase($listing_feed);

        // Require valid IDX listing
        $sql_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($listing_mls) . "'";

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $sql_where . " LIMIT 1");
        if (empty($listing)) {
            throw new UnexpectedValueException('The selected listing could not be found');
        }
        $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

        // Lead's ID
        $user_id = $user->user_id();
        $user_row = $user->getRow();

        // DB connection
        $db = DB::get();

        // Check is listing is already saved
        $query = $db->prepare("SELECT * FROM `users_listings` WHERE `user_id` = :user_id AND `mls_number` = :mls_number LIMIT 1;");
        $query->execute(array('user_id' => $user_id, 'mls_number' => $listing_mls));
        $saved = $query->fetch();

        // Already saved, return success
        if (!empty($saved) && !empty($_POST['force'])) {
            // JSON response
            $json['added'] = true;

        // Remove from saved listings
        } elseif (!empty($saved)) {
            // Remove record from database
            $db->prepare("DELETE FROM `users_listings` WHERE `id` = ?;")->execute(array($saved['id']));

            // JSON response
            $json['removed'] = true;

            // Track history event
            (new History_Event_Delete_SavedListing(array(
                'listing' => $listing
            ), array(
                new History_User_Lead($user_id)
            )))->save($db);

            // Trigger hook
            Hooks::hook(Hooks::HOOK_LEAD_LISTING_REMOVED)->run($user_row, $saved);

        // Add to saved listings
        } elseif (empty($saved)) {
            // Insert record into saved listings
            $db->prepare("INSERT INTO `users_listings` SET "
                . "`user_id`	= :user_id,"
                . "`mls_number`	= :mls_number,"
                . "`table`		= :table,"
                . "`idx`		= :idx,"
                . "`type`		= IFNULL(:type, `type`),"
                . "`city`		= IFNULL(:city, `city`),"
                . "`subdivision`= IFNULL(:subdivision, `subdivision`),"
                . "`bedrooms`	= IFNULL(:bedrooms, `bedrooms`),"
                . "`bathrooms`	= IFNULL(:bathrooms, `bathrooms`),"
                . "`sqft`		= IFNULL(:sqft, `sqft`),"
                . "`price`		= IFNULL(:price, `price`),"
                . "`timestamp`	= NOW()"
            . ";")->execute(array(
                'user_id'       => $user_id,
                'mls_number'    => $listing['ListingMLS'],
                'price'         => $listing['ListingPrice'],
                'type'          => $listing['ListingType'],
                'city'          => $listing['AddressCity'],
                'subdivision'   => $listing['AddressSubdivision'],
                'bedrooms'      => $listing['NumberOfBedrooms'],
                'bathrooms'     => $listing['NumberOfBathrooms'],
                'sqft'          => $listing['NumberOfSqFt'],
                'table'         => $idx->getTable(),
                'idx'           => $idx->getName()
            ));

            // JSON response
            $json['added'] = true;

            // Track history event
            (new History_Event_Action_SavedListing(array(
                'listing' => $listing
            ), array(
                new History_User_Lead($user_id)
            )))->save($db);

            // Notify agent on save
            if ($user_row['notify_favs'] == 'yes') {
                // Load assigned agent
                $agent = Backend_Agent::load($user_row['agent']);

                // Send notification to assigned agent
                $mailer = new Backend_Mailer_SavedListing(array(
                    'listing' => $listing,
                    'lead'  => $user_row
                ));

                // Check incoming notification settings
                if ($agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LISTING_SAVED)) {
                    $mailer->Send();
                }
            }

            // Run hook
            Hooks::hook(Hooks::HOOK_LEAD_LISTING_SAVED)->run($user_row, $idx, $listing);
        }

        //remove bokmarked listing from dismissed
        if ($json['added']) {
            // Remove record from database
            $query = $db->prepare('DELETE FROM `users_listings_dismissed` WHERE `user_id` = :user_id AND `mls_number` = :mls_number');
            $query->execute(array('user_id' => $user_id, 'mls_number' => $listing_mls));
        }
    }


// Validation error
} catch (UnexpectedValueException $e) {
    $json['error'] = $e->getMessage();

// Database error
} catch (PDOException $e) {
    $json['error'] = 'An error occurred while processing request';

// Unexpected error
} catch (Exception $e) {
    $json['error'] = 'An unexpected error has occurred';
}

// Return JSON Response
header('Content-Type: application/json');
echo json_encode($json);
exit;
