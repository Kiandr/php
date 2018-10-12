<?php

// Delete favorite
$db = DB::get('users');

// Search where
$where = array(
    '$eq' => array(
        'email' => $email,
    ),
);

// Fetch lead row
if (!($lead = $db->{'users'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified Lead could not be found';
    return;
}

// Search where
$where = array(
    '$eq' => array(
        'user_id' => $lead['id'],
        'id' => $id,
    ),
);

// Fetch favorite row
if (!($row = $db->{'users_listings'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified ' . Locale::spell('Favorite') . ' could not be found';
    return;
}

// API Object
$object = new API_Object_Lead_Favorite($db, $row);

// Delete
try {
    $db->{'users_listings'}->delete($where);

    // Update score
    Backend_Lead::load($lead['id'])->updateScore();

    try {
        // Find listing record in database
        $idx = Util_IDX::getIdx($_POST['feed']);
        $db_idx = Util_IDX::getDatabase($_POST['feed']);
        $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns(). " FROM `" .  $db_idx->cleanInput($row['table']) . "` WHERE "
            . "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($row['mls_number']) . "' AND "
            . "`" . $idx->field('ListingType') . "` = '" . $db_idx->cleanInput($row['type']) . "';");

    // Silent failure
    } catch (Exception $e) {
    }

    // Listing not found - use cached information
    if (empty($listing)) {
        $listing = array(
            'ListingMLS'        => $row['mls_number'],
            'ListingType'       => $row['type'],
            'AddressCity'       => $row['city'],
            'AddressSubdivision'=> $row['subdivision'],
            'NumberOfBedrooms'  => $row['bedrooms'],
            'NumberOfBathrooms' => $row['bathrooms'],
            'NumberOfSqFt'      => $row['sqft'],
            'ListingPrice'      => $row['price']
        );
    }

    // Track history event
    (new History_Event_Delete_SavedListing(array(
        'listing' => $listing
    ), array(
        new History_User_Lead($lead['id'])
    )))->save($db);

    // Trigger hook
    Hooks::hook(Hooks::HOOK_LEAD_LISTING_REMOVED)->run($lead, $row);

    // Data subset
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'The specified ' . Locale::spell('Favorite') . ' could not be deleted';
    return;
}
