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

        // Save data to session (to dismiss after login/register)
        $_SESSION['dismissListing'] = $listing_mls;
        $_SESSION['dismissFeed'] = $listing_feed;
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

        // Require valid IDX listing
        $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $sql_where . " LIMIT 1");
        if (empty($listing)) {
            throw new UnexpectedValueException('The selected listing could not be found');
        }
        $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

        // Lead's ID
        $user_id = $user->user_id();

        // DB connection
        $db = DB::get();

        // Check is listing is already dismissed
        $query = $db->prepare("SELECT * FROM `users_listings_dismissed` WHERE `user_id` = :user_id AND `mls_number` = :mls_number LIMIT 1;");
        $query->execute(array('user_id' => $user_id, 'mls_number' => $listing_mls));
        $dismissed = $query->fetch();

        // Already dismissed, return success
        if (!empty($dismissed) && !empty($_POST['force'])) {
            // JSON response
            $json['added'] = true;

        // Remove from dismissed listings
        } elseif (!empty($dismissed)) {
            // Remove record from database
            $db->prepare("DELETE FROM `users_listings_dismissed` WHERE `id` = ?;")->execute(array($dismissed['id']));

            // JSON response
            $json['removed'] = true;

            // Track history event
            (new History_Event_Delete_DismissListing(array(
                'listing' => $listing
            ), array(
                new History_User_Lead($user_id)
            )))->save($db);

            // Trigger hook
            Hooks::hook(Hooks::HOOK_LEAD_LISTING_UNDISMISSED)->run($user->getRow(), $dismissed);

        // Add to dismissed listings
        } elseif (empty($dismissed)) {
            // Check if max has been reached (for performance reasons)
            $max = 1000;
            $query = $db->prepare("SELECT COUNT(`id`) FROM `users_listings_dismissed` WHERE `idx` = :idx AND `user_id` = :user_id;");
            $query->execute(array('user_id' => $user_id, 'idx' => $idx->getName()));
            $count = $query->fetchColumn();
            if ($count > $max) {
                throw new UnexpectedValueException('You\'ve reached the maximum number of ' . (int) $max . ' dismissed listings.');
            }

            // Insert record into dismissed listings
            $db->prepare("INSERT INTO `users_listings_dismissed` SET "
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
            (new History_Event_Action_DismissListing(array(
                'listing' => $listing
            ), array(
                new History_User_Lead($user_id)
            )))->save($db);

            // Trigger hook
            Hooks::hook(Hooks::HOOK_LEAD_LISTING_DISMISSED)->run($user->getRow(), $idx, $listing);
        }

        //remove dismissed listing from bokmarked
        if ($json['added']) {
            // Remove record from database
            $query = $db->prepare('DELETE FROM `users_listings` WHERE `user_id` = :user_id AND `mls_number` = :mls_number');
            $query->execute(array('user_id' => $user_id, 'mls_number' => $listing_mls));
        }
    }


// Validation error
} catch (UnexpectedValueException $e) {
    $json['error'] = $e->getMessage();

// Database error
} catch (PDOException $e) {
    $json['error'] = 'An error occurred while processing request';
    //$json['error'] = $e->getMessage();

// Unexpected error
} catch (Exception $e) {
    $json['error'] = 'An unexpected error has occurred';
    //$json['error'] = $e->getMessage();
}

// Return JSON Response
header('Content-Type: application/json');
echo json_encode($json);
exit;
