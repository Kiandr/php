<?php

// Include IDX common files
require $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

// Fetch requested lead
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
    $errors[] = 'The specified lead could not be found';
    return;
}

// Required parameters
$required = array('mls_number', 'type', 'feed', 'source');

// Check POST
foreach ($required as $field) {
    if (!isset($_POST[$field])) {
        $errors[] = 'Required parameter is missing: \'' . $field . '\'';
    }
}

// Require no errors
if (!empty($errors)) {
    return;
}

// Check existing record
$where = array(
    '$eq' => array(
        'user_id' => $lead['id'],
        'idx' => $_POST['feed'],
        'mls_number' => $_POST['mls_number'],
        'type' => $_POST['type'],
    )
);

if ($existing = $db->{'users_listings'}->search($where)->fetch()) {
    $app->response->status(409);
    $errors[] = 'This listing is already a ' . Locale::spell('favorite') . ' for this user';
    return;
}

// Validate feed
try {
    $idx = Util_IDX::getIdx($_POST['feed']);
    $db_idx = Util_IDX::getDatabase($_POST['feed']);
} catch (Exception $ex) {
    $errors[] = 'An error occurred while retrieving the specified feed: ' . $ex->getMessage();
    return;
}

// Validate listing
$listing = null;

// Build query
$sql = "SELECT " . $idx->selectColumns(). " FROM `" .  $db_idx->cleanInput($_POST['source']) . "` WHERE "
            . "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($_POST['mls_number']) . "' AND "
            . "`" . $idx->field('ListingType') . "` = '" . $db_idx->cleanInput($_POST['type']) . "';";

// Execute
if (!($listing = $db_idx->fetchQuery($sql))) {
    $errors[] = 'The specified listing could not be found';
    return;
}

// Record data
$data = array(
    'user_id'       => $lead['id'],
    'mls_number'    => $listing['ListingMLS'],
    'table'         => $_POST['source'],
    'idx'           => $_POST['feed'],
    'type'          => $listing['ListingType'],
    'city'          => $listing['AddressCity'],
    'subdivision'   => $listing['AddressSubdivision'],
    'bedrooms'      => $listing['NumberOfBedrooms'],
    'bathrooms'     => $listing['NumberOfBathrooms'],
    'sqft'          => $listing['NumberOfSqFt'],
    'price'         => $listing['ListingPrice'],
);

// Insert record
try {
    $row = $db->{'users_listings'}->insert($data);

    // Update score
    Backend_Lead::load($lead['id'])->updateScore();

    // Track history event
    (new History_Event_Action_SavedListing(array(
        'listing' => $listing
    ), array(
        new History_User_Lead($lead['id'])
    )))->save($db);

    // Run hook
    Hooks::hook(Hooks::HOOK_LEAD_LISTING_SAVED)->run($lead, $idx, $listing);

    // Notify agent on save
    if ($lead['notify_favs'] == 'yes') {
        // Load assigned agent
        $agent = Backend_Agent::load($lead['agent']);

        // Send notification to assigned agent
        $mailer = new Backend_Mailer_SavedListing(array(
            'listing' => $listing,
            'lead'  => $lead
        ));

        // Check incoming notification settings
        if ($agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LISTING_SAVED)) {
            $mailer->Send();
        }
    }

    // API object
    $object = new API_Object_Lead_Favorite($db, $row);

    // Data subset
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'The ' . Locale::spell('favorite') . ' could not be created: ' . $ex->getMessage();
}
