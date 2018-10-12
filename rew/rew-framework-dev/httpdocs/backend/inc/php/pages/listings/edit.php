<?php

// App DB
$db = DB::get();

// Get Authorization Managers
$listingAuth = new REW\Backend\Auth\ListingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$listingAuth->canManageListings($authuser)) {
    if (!$listingAuth->canManageOwnListings($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to edit listings'
        );
    }
    /* Restrict to Agent's Data */
    $sql_agent = "AND `agent` = :agent";
}

/* Authorized to Delete? */
$can_delete = $listingAuth->canDeleteListings($authuser);

    // Success
    $success = array();

    // Errors
    $errors = array();

    // Listing ID
    $_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

    // Get Selected Row
    $params = ["id" => $_GET['id']];
    if (!empty($sql_agent)) {
        $params["agent"] = $authuser->info('id');
    }
    try {
        $listing = $db->fetch("SELECT * FROM `" . TABLE_LISTINGS . "` WHERE `id` = :id $sql_agent;", $params);
    } catch (PDOException $e) {}

/* Throw Missing Listing Exception */
if (empty($listing)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingListingException();
}

// Listing Agent's Teams
$teams = Backend_Team::getTeams($listing['agent']);

        // New Row Successful
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = 'Listing has successfully been created.';
}

// Price can only contain numbers
$_POST['price'] = preg_replace('/[^0-9]/', '', $_POST['price']);

        // Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required = array();
    $required[] = array('value' => 'title',   'title' => 'Listing Title');
    $required[] = array('value' => 'price',   'title' => 'Price');
    $required[] = array('value' => 'type',    'title' => 'Type');
    $required[] = array('value' => 'status',  'title' => 'Status');
    $required[] = array('value' => 'address', 'title' => 'Street Address');
    $required[] = array('value' => 'state',   'title' => 'State/Province');
    $required[] = array('value' => 'city',    'title' => 'City/Town');
    $required[] = array('value' => 'zip',     'title' => 'Zip/Postal');

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    // Require Valid Link
    $link = Format::slugify($_POST['link']);
    if (empty($link)) {
        $errors[] = 'Listing Link is a required field.';
    } else {
        // Check for Duplicates
        $params = ["id" => $listing['id'], "link" => $link];
        if (is_numeric($link)) {
            $link_id = "OR `id` = :link_id";
            $params["link_id"] = $link;
        }

        try {
            $duplicate = $db->fetch("SELECT `id` FROM `" . TABLE_LISTINGS . "` WHERE `id` != :id AND (`link` = :link $link_id);", $params);
            if (!empty($duplicate)) {
                $errors[] = 'A listing with this link already exists.';
            }
        } catch (PDOException $e) {}
    }

    // Check Errors
    if (empty($errors)) {
        // Sanitize Data
        $_POST['directions'] = htmlspecialchars($_POST['directions']);
        $_POST['garages']    = str_replace(',', '', $_POST['garages']);
        $_POST['features']   = is_array($_POST['features']) ? implode(',', $_POST['features']) : $_POST['features'];

        // Geocode
        if (empty($_POST['latitude']) && empty($_POST['longitude'])) {
            $address = $_POST['address'] . ' ' . $_POST['city'] . ' ' . $_POST['state'] . ' ' . $_POST['zip'];
            $geoinfo = Map::geocode($address);
            if (!empty($geoinfo)) {
                $_POST['latitude']  = $geoinfo['latitude'];
                $_POST['longitude'] = $geoinfo['longitude'];
            }
        }

        // Build INSERT Query
        $fields = [
            "team", "link", "title", "mls_number", "address", "city", "state", "zip", "price",
            "type", "status", "bedrooms", "bathrooms", "bathrooms_half", "squarefeet", "garages",
            "lotsize", "lotsize_unit", "yearbuilt", "stories", "subdivision", "school_district",
            "school_elementary", "school_middle", "school_high", "features", "description",
            "directions", "virtual_tour", "latitude", "longitude"
        ];
        $params = [];
        $query = "UPDATE `" . TABLE_LISTINGS . "` SET ";
        foreach($fields as $field) {
            $query .= "`$field` = :$field, ";
            if ($field == "team") {
                $params[$field] = (!empty($teams) && !empty($_POST['team']) ? $_POST['team'] : "NULL");
            } elseif ($field == "link") {
                $params[$field] = $link;
            } elseif (empty($_POST[$field])) {
                $params[$field] = "";
            } else {
                $params[$field] = $_POST[$field];
            }
        }
        $query .= "`timestamp_updated` = NOW() ";
        $query .= "WHERE `id` = :id;";
        $params["id"] = $listing['id'];

        try {
            // Execute Query
            $db->prepare($query)->execute($params);
            $success[] = 'Listing has successfully been updated.';

        // Query Error
        } catch (PDOException $e) {
            $errors[] = 'Listing could not be saved, please try again.';
        }
    }
}

// Build State/Province List
$locations = array();
try {
    foreach ($db->fetchAll("SELECT `country`, `state` FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `local` = '' ORDER BY `country` ASC, `state` ASC;") as $state) {
        if (!empty($state['state'])) {
            $locations[$state['country']][] = $state;
        }
    }
} catch (PDOException $e) {
    $errors[] = 'Error Occurred while loading States / Provinces.';
}

// Build City List
$cities = array();
$state = isset($_POST['state']) ? $_POST['state'] : $listing['state'];
try {
    foreach ($db->fetchAll("SELECT `local`, `user` FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `state` = :state AND `local` != '' ORDER BY `user` DESC, `local` ASC;", ["state" => $state]) as $city) {
        if (!empty($city['local'])) {
            $cities[] = $city;
        }
    }
} catch (PDOException $e) {
    $errors[] = 'Error Occurred while loading Cities.';
}

// AJAX Request
if (!empty($_POST['ajax'])) {
    // JSON Response
    $json = array();

    // Load JSON City List
    if (isset($_POST['loadCities'])) {
        $json['options'] = $cities;
    }

    // Send JSON
    header('Content-type: application/json');
    echo json_encode($json);
    exit;
}

// Listing Fields
$options = array();
$options['listing_types'] = array();
$options['listing_statuses'] = array();
$options['listing_features'] = array();
try {
    foreach ($db->fetchAll("SELECT `field`, `value`, IF(`user` = 'false', 1, 0) AS `required` FROM `" . TABLE_LISTING_FIELDS . "` ORDER BY `required` DESC, `value` ASC;") as $row) {
        if ($row['field'] == 'type') array_push($options['listing_types'], array('value' => $row['value'], 'title' => $row['value'], 'required' => (boolean)$row['required']));
        if ($row['field'] == 'status') array_push($options['listing_statuses'], array('value' => $row['value'], 'title' => $row['value'], 'required' => (boolean)$row['required']));
        if ($row['field'] == 'feature') array_push($options['listing_features'], array('value' => $row['value'], 'title' => $row['value'], 'required' => (boolean)$row['required']));
    }
} catch (PDOException $e) {}

// Use $_POST
foreach ($listing as $k => $v) {
    $listing[$k] = isset($_REQUEST[$k]) ? $_REQUEST[$k] : $v;
}

// Require Array
$listing['features'] = is_array($listing['features']) ? $listing['features'] : explode(',', $listing['features']);

        // Admin Mode
if ($listingAuth->canManageListings($authuser)) {
    // Listing Agent
    try {
        $agent = $db->fetch("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :id;", ["id" => $listing['agent']]);
    } catch (PDOException $e) {
        $errors[] = 'Error Occurred while loading Listing Agent.';
    }

}

// Listing Photos
$uploads = array();
try {
    $uploads = $db->fetchAll("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` = 'listing' AND `row` = :row ORDER BY `order` ASC;", ["row" => $listing['id']]);
} catch (PDOException $e) {}

// Cleanup Non-Numeric Values
$numeric = array('bedrooms', 'bathrooms', 'bathrooms_half', 'garages', 'latitude', 'longitude');
foreach ($numeric as $number) {
    if (isset($listing[$number]) && (!is_numeric($listing[$number]) || $listing[$number] == 0)) {
        unset($listing[$number]);
    }
}
