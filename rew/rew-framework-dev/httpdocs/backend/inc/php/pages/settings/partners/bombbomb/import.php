<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageBombomb($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage bombomb integrations')
    );
}

// API integration status
$logins_valid = false;

// Partner instance
$api = new Partner_BombBomb();

// Configure Provider
$api_key = $authuser->info('partners.bombbomb.api_key');
$list_id = $authuser->info('partners.bombbomb.list_id');

// Required parameters
if (!empty($api_key) && !empty($list_id)) {
    // Set options
    $api->setOptions(array(
        'api_key' => $api_key,
    ));

    // Test validity
    $lists = $api->getLists();
    if (!($err = $api->getLastError())) {
        $logins_valid = true;
    }
}

// Require configured Partner
if (empty($logins_valid)) {
    header('Location: ../');
    exit;
}

// Current Import status
$import_status = $api->getListProcessingStatus($list_id);
$importing = $import_status['status'] === '1';

// App DBstat
$db = DB::get();

// Groups
$groups = array();

// Select Groups
$query = "SELECT g.`id`, g.`name`, COUNT(DISTINCT `ug`.`user_id`) AS `leads`"
    . " FROM `" . LM_TABLE_GROUPS . "` `g`"
    . " LEFT JOIN `" . LM_TABLE_USER_GROUPS . "` `ug` ON `g`.`id` = `ug`.`group_id`"
    . " LEFT JOIN `" . LM_TABLE_LEADS . "` `u` ON `ug`.`user_id` = `u`.`id`"
    . " WHERE u.`agent` = '" . mysql_real_escape_string($authuser->info('id')) . "'"
    . " GROUP BY g.`id`"
    . " ORDER BY g.`name`"
. ";";

// Build Collection
if ($result = mysql_query($query)) {
    while ($group = mysql_fetch_assoc($result)) {
        // Add Group
        $groups[] = $group;
    }
}

// Form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SQL WHERE
    $sql_where = array();
    $sql_where[] = "u.`agent` = '" . mysql_real_escape_string($authuser->info('id')) . "'";

    // Search group
    if (!empty($_POST['group_id'])) {
        $sql_where[] = "u.`id` IN (SELECT `user_id` FROM `" . LM_TABLE_USER_GROUPS . "` WHERE `group_id` = '" . mysql_real_escape_string($_POST['group_id']) . "')";
    }

    // Leads query
    $sql = "SELECT `first_name`, `last_name`, `email` `phone`, `address1`, `address2`, `city`, `state`, `country`, `zip` "
            . "FROM `" . LM_TABLE_LEADS . "` u "
            . (!empty($sql_where) ? 'WHERE ' . implode(' AND ', $sql_where) . ' ' : '')
            . ";";

    // Execute
    if ($leads_result = mysql_query($sql)) {
        // Require rows
        if (mysql_num_rows($leads_result)) {
            // Generate CSV filename
            $filename = $_SERVER['DOCUMENT_ROOT'] . '/inc/cache/tmp/' . uniqid(null, true) . '.csv.php';
            $fp = fopen($filename, 'w');
            if ($fp) {
                // Put contents
                while ($lead_row = mysql_fetch_assoc($leads_result)) {
                    fputcsv($fp, array_values($lead_row));
                }

                // Close file handle
                fclose($fp);

                // BombBomb Import
                if (!($import = $api->importCSVToList($list_id, array('first_name', 'last_name', 'email', 'phone_number', 'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code'), $filename))) {
                    $errors[] = __('BombBomb returned an error: %s', $api->getLastError());
                    $errors[] = __('Make sure you have configured a Destination List before importing leads');
                } else {
                    $success[] = __('Your import has been successfully queued for processing');
                }

                // Clean up file
                unlink($filename);

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect
                header('Location: ?');
                exit;
            }
        }
    }
} else if (isset($_GET['refresh']) && isset($import_status['status'])) {
    $importStatuses = [
        '0' => [
            'messageType' => 'success',
            'message' => 'Leads Imported'
        ],
        '1' => [
            'messageType' => 'warnings',
            'message' => 'Importing Leads...'
        ]
    ];
    $status = $importStatuses[$import_status['status']];
    if (!empty($status)) {
        ${$status['messageType']}[] = __($status['message']);
    }
}
