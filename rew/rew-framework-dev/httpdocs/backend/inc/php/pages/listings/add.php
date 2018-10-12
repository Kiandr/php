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
    $sql_agent = "`agent` = '" . $authuser->info('id') . "'";
}

    // Agent's Teams
    $teams = Backend_Team::getTeams($authuser->info('id'));

    // Success
    $success = array();

    // Errors
    $errors = array();

    // Show Form
    $show_form = true;

    // Use $_POST['status'] or $_GET['status']
    $_POST['status'] = isset($_POST['status']) ? $_POST['status'] : $_GET['status'];

    // Process Submit
if (isset($_GET['submit'])) {
    // Price can only contain numbers
    $_POST['price'] = preg_replace('/[^0-9]/', '', $_POST['price']);

    // Required Fields
    $required   = array();
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
        $params = ["link" => $link];
        if (is_numeric($link)) {
            $link_id = "OR `id` = :link_id";
            $params["link_id"] = $link;
        }

        try {
            $duplicate = $db->fetch("SELECT `id` FROM `" . TABLE_LISTINGS . "` WHERE (`link` = :link $link_id);", $params);
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
                "agent", "team", "link", "title", "mls_number", "address", "city", "state", "zip", "price",
                "type", "status", "bedrooms", "bathrooms", "bathrooms_half", "squarefeet", "garages",
                "lotsize", "lotsize_unit", "yearbuilt", "stories", "subdivision", "school_district",
                "school_elementary", "school_middle", "school_high", "features", "description",
                "directions", "virtual_tour", "latitude", "longitude"
        ];
        $params = [];
        $query = "INSERT INTO `" . TABLE_LISTINGS . "` SET ";
        foreach($fields as $field) {
            $query .= "`$field` = :$field, ";
            if ($field == "agent") {
                $params[$field] = $authuser->info('id');
            } elseif ($field == "team") {
                $params[$field] = (!empty($teams) && !empty($_POST['team']) ? $_POST['team'] : "NULL");
            } elseif ($field == "link") {
                $params[$field] = $link;
            } elseif (empty($_POST[$field])) {
                $params[$field] = "";
            } else {
                $params[$field] = $_POST[$field];
            }
        }
        $query .= "`timestamp_created` = NOW();";

        try {
            // Execute Query
            $db->prepare($query)->execute($params);

            // Insert ID
            $insert_id = $db->lastInsertId();

            // Assign Uploads to Listing
            if (!empty($_POST['uploads'])) {
                foreach ($_POST['uploads'] as $upload) {
                    try {
                        $db->prepare("UPDATE `" . Settings::getInstance()->TABLES['UPLOADS'] . "` SET `row` = :row WHERE `id` = :id;")->execute(["row" => $insert_id, "id" => $upload]);
                    } catch (PDOException $e) {}
                }
            }

        // Redirect to Edit Form
        header('Location: ../edit/?id=' . $insert_id . '&success=add');
        exit;

        // Query Error
        } catch (PDOException $e) {
            $errors[] = 'Listing could not be saved, please try again.';

        }

    }

}

// Default Status is Active
$_POST['status'] = isset($_POST['status']) ? $_POST['status'] : 'Active';

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
if (!empty($_POST['state'])) {
    try {
        foreach ($db->fetchAll("SELECT `local`, `user` FROM `" . TABLE_LISTING_LOCATIONS . "` WHERE `state` = :state AND `local` != '' ORDER BY `user` DESC, `local` ASC;", ["state" => $_POST['state']]) as $city) {
            if (!empty($city['local'])) {
                $cities[] = $city;
            }
        }
    } catch (PDOException $e) {
        $errors[] = 'Error Occurred while loading Cities.';
    }
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

// Listing Photos
$uploads = array();
if (!empty($_POST['uploads'])) {
    foreach ($_POST['uploads'] as $upload) {
        try {
            $uploads[] = $db->fetch("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `id` = :id ORDER BY `order` ASC;", ["id" => $upload]);
        } catch (PDOException $e) {}
    }
}
