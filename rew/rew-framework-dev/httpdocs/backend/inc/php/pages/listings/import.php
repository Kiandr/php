
<?php

// Get Authorization Managers
$listingAuth = new REW\Backend\Auth\ListingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$listingAuth->canImportListings($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to import listings')
    );
}
    // App DB
    $db = DB::get();

    // Success
    $success = array();

    // Errors
    $errors = array();

    // Show Form
    $show_form = true;

    // IDX Listings
    $listings = array();

    // Not Yet Imported Listings
    $new_listings = [];

    // IDX Object
    $idx = Util_IDX::getIdx();

    // IDX Database
    $db_idx = Util_IDX::getDatabase();

    // Locations
    $locations = Location::getLocations();

    $settings = Settings::getInstance();

    // Import IDX Listings by MLS Number
if (is_array($_GET['listings']) && !empty($_GET['listings'])) {
    try {
        // Close session for AJAX requests
        if (!empty($_GET['ajax'])) {
            @session_write_close();
        }

        // CMS Feed
        $cms = Util_IDX::getIdx('cms');

        // Available Columns
        $fields = $cms->getFields();
        $columns = array();
        foreach ($idx->getFields() as $field => $column) {
            if ($field == 'ListingMLS') {
                $field = 'ListingMLSNumber';
            }
            if (!empty($column) && !empty($fields[$field])) {
                $columns[$column] = $fields[$field];
            }
        }

        // Imported
        $imported = 0;

        // Import Listings
        foreach ($_GET['listings'] as $mls) {
            // Skip Empty
            if (!Validate::stringRequired($mls)) {
                continue;
            }

            $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($mls) . "'";

            // Any global criteria
            $idx->executeSearchWhereCallback($search_where);

            // Select IDX Listing
            $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $search_where . ";");
            if (!empty($listing)) {
                // Parse IDX Listing
                $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                // Check for Already Imported Listing
                try {
                    $exists = $db->fetch("SELECT `id` FROM `" . TABLE_LISTINGS . "` WHERE `imported` = 'true' AND `mls_number` = :mls_number;", ["mls_number" => $listing['ListingMLS']]);
                    if (!empty($exists)) {
                        $errors[] = 'MLS&reg; #' . $listing['ListingMLS'] . ' has already been imported.';
                        continue;
                    }
                } catch (PDOException $e) {};


                // Generate Title
                $title = implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState']))) . ' ' . $listing['AddressZipCode'] . ' (MLS&reg; #' . $listing['ListingMLS'] . ')';

                // Generate Link
                $link = Format::slugify(implode(' ', array('mls', $listing['ListingMLS'], $listing['Address'], $listing['AddressCity'], $listing['AddressState'], $listing['AddressZipCode'])));

                // Detect Location
                $location = array('country' => 'United States', 'state' => $listing['AddressState'], 'local' => $listing['AddressCity']);
                foreach ($locations as $country => $states) {
                    foreach ($states as $abbr => $state) {
                        if ($listing['AddressState'] === $abbr || $listing['AddressState'] === $state) {
                            $listing['AddressState'] = $state;
                            $location['country'] = $country;
                            $location['state'] = $state;
                            break;
                        }
                    }
                }

                // If Status is Empty, Force as "Active" Listing
                $listing['ListingStatus'] = !empty($listing['ListingStatus']) ? $listing['ListingStatus'] : __('Active');

                // Import Listing
                try {

                    // Generate INSERT Query
                    $query = "INSERT INTO `" . TABLE_LISTINGS . "` SET ";
                    $values = [];
                    foreach ($listing as $field => $value) {
                        $value = trim($value);
                        if (empty($value)) continue;
                        $column = $columns[$field];
                        if (!empty($column) && !in_array($column, array('id', 'imported', 'agent', 'title', 'link', 'timestamp_created'))) {
                            $query .= "`" . $column . "` = :$column, ";
                            $values[$column] = $value;
                        }
                    }
                    $query .= "`imported` = 'true',";
                    $query .= "`agent` = :agent,";
                    $query .= "`title` = :title,";
                    $query .= "`link`  = :link,";
                    $query .= "`timestamp_created` = NOW();";

                    $values["agent"] = $authuser->info('id');
                    $values["title"] = $title;
                    $values["link"] = $link;

                    $db->prepare($query)->execute($values);

                    // Success
                    if (!empty($_GET['ajax'])) {
                        $success[] = __('Successfully imported listing MLS&reg; #%s.', $listing['ListingMLS']);
                    }

                    // Listing ID
                    $insert_id = $db->lastInsertId();

                    // Import Location
                    if (!empty($location)) {
                        $cols = array();
                        $keys = array_keys($location);
                        for ($i = 0; $i < count($keys); $i++) {
                            $key = $keys[$i];
                            $sql[] = "`" . $key . "` = :$key";
                            try {
                                $count = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `_listing_locations` WHERE " . implode(' AND ', $sql) . ";", $location);
                                if (empty($count['total'])) {
                                    try {
                                        $db->prepare("INSERT INTO `_listing_locations` SET `user` = 'Y', " . implode(', ', $sql) . ";")->execute($location);
                                    } catch (PDOException $e) {};
                                }
                            } catch (PDOException $e) {}
                        }
                    }

                    // Import Listing Type
                    try {
                        $type = $db->fetch("SELECT `id` FROM `_listing_fields` WHERE `field` = 'type' AND `value` = :value LIMIT 1;", ["value" => $listing['ListingType']]);
                        if (empty($type)) {
                            try {
                                $db->prepare("INSERT INTO `_listing_fields` SET `user` = 'true', `field` = 'type', `value` = :value;")->execute(["value" => $listing['ListingType']]);
                            } catch (PDOException $e) {
                                $errors[] = __('Error occurred while importing listing type: %s', $listing['ListingType']);
                            }
                        }
                    } catch (PDOException $e) {
                        $errors[] = __('Error occurred while checking listing type: %s', $listing['ListingType']);
                    }

                    // Import Listing Status
                    try {
                        $type = $db->fetch("SELECT `id` FROM `_listing_fields` WHERE `field` = 'status' AND `value` = :value LIMIT 1;", ["value" => $listing['ListingStatus']]);
                        if (empty($type)) {
                            try {
                                $db->prepare("INSERT INTO `_listing_fields` SET `user` = 'true', `field` = 'status', `value` = :value;")->execute(["value" => $listing['ListingStatus']]);
                            } catch (PDOException $e) {
                                $errors[] = __('Error occurred while importing listing status: %s', $listing['ListingStatus']);
                            }
                        }
                    } catch (PDOException $e) {
                        $errors[] = __('Error occurred while checking listing status: %s', $listing['ListingStatus']);
                    }

                    // Import Listing Images
                    $i = 0;
                    $images = images($idx, $db_idx, $listing, true);
                    foreach ($images as $image) {
                        // Download Image
                        $data = Util_Curl::executeRequest($image);
                        // Response Info
                        $info = Util_Curl::info();
                        // Require 200 Response
                        if ($info['http_code'] == 200 && !empty($data)) {
                            $size = strlen($data);
                            $ext = '.jpg';
                            $filename = $link . '.' . $i;
                            $file = $filename . $ext;
                            // Ensure Unique Filename
                            while (file_exists(Settings::getInstance()->DIRS['UPLOADS'] . $file)) {
                                $filename .= rand(10, 99);
                                $file = $filename . $ext;
                            }
                            // Save to File
                            if (file_put_contents(Settings::getInstance()->DIRS['UPLOADS'] . $file, $data)) {
                                // Save to Database
                                try {
                                    $db->prepare("INSERT INTO `cms_uploads` SET "
                                        . "`type`  = 'listing', "
                                        . "`row`   = :row, "
                                        . "`file`  = :file, "
                                        . "`size`  = :size, "
                                        . "`order` = :order, "
                                        . "`timestamp` = NOW();")->execute([
                                        "row" => $insert_id,
                                        "file" => $file,
                                        "size" => $size,
                                        "order" => $i
                                    ]);

                                    $i++;
                                } catch (PDOException $e) {
                                    $errors[] = __('An error occurred while importing photo #%s for MLS&reg; #%s.', ($i + 1), $listing['ListingMLS']);
                                    break;
                                }
                            } else {
                                $errors[] = __('An error occurred while saving photo #%s for MLS&reg; #%s.', ($i + 1), $listing['ListingMLS']);
                                break;
                            }
                        } else {
                            $errors[] = __('An error occurred while downloading photo #%s for MLS&reg; #%s', ($i + 1), $listing['ListingMLS']);
                            break;
                        }
                    }

                    // Success
                    $imported++;

                // Query Error
                } catch (PDOException $e) {
                    $errors[] = __('An error occurred while importing MLS&reg; #%s.', $listing['ListingMLS']);

                }

                // Listing not Found
            } else {
                $errors[] = __('Could not locate listing: MLS&reg; #%s', $mls);
            }
        }
    } catch (Exception $e) {
        $errors[] = __('An error occurred while attempting to import your MLS&reg; listings.');
    }

    // AJAX Request
    if (!empty($_GET['ajax'])) {
        $json = array();
        if (!empty($errors)) {
            $json['errors'] = $errors;
        }
        if (!empty($success)) {
            $json['success'] = $success;
        }
        die(json_encode($json));
    } else {
        // Success
        if (!empty($imported)) {
            $success[] = n__('Successfully imported %s MLS&reg; Listing', 'Successfully imported %s MLS&reg; Listings.', Format::number($imported), Format::number($imported) );
        }
    }
}

    // Find Listings by Office ID
if (Validate::stringRequired($_GET['office_id']) || Validate::stringRequired($_GET['agent_id'])) {

    // Search by Office ID
    if (Validate::stringRequired($_GET['office_id'])) {
        $office_id = trim($_GET['office_id']);
        $searching = 'Office ID "' . htmlspecialchars($office_id) . '"';
        $sql_where = "`" . $idx->field('ListingOfficeID') . "` = '" . $db_idx->cleanInput($office_id) . "'";

    // Search by Agent ID
    } elseif (Validate::stringRequired($_GET['agent_id'])) {
        $agent_id = trim($_GET['agent_id']);
        $searching = 'Agent ID "' . htmlspecialchars($agent_id) . '"';
        $sql_where = "`" . $idx->field('ListingAgentID') . "` = '" . $db_idx->cleanInput($agent_id) . "'";

    }

    try {
        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Count Listings
        $query = "SELECT COUNT(*) AS `total` FROM `" . $idx->getTable() . "` WHERE " . $sql_where . ";";
        if ($result = $db_idx->query($query)) {
            $count = $db_idx->fetchArray($result);
            if (!empty($count['total'])) {
                // Page Limit
                $page_limit = 250;

                // SQL Limit
                if ($count['total'] > $page_limit) {
                    $limitvalue = (($_GET['p'] - 1) * $page_limit);
                    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
                    $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
                }

                // Pagination
                $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit);

                // Find Listings by Office ID
                $query = "SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $sql_where . " ORDER BY `" . $idx->field('ListingPrice') . "` DESC" . $sql_limit . ";";
                if ($result = $db_idx->query($query)) {
                    // Order by Price, Already Imported Last
                    $i = 0;
                    $order_price = array();
                    $order_import = array();

                    // Hide Form
                    $show_form = false;

                    // Process Results
                    while ($listing = $db_idx->fetchArray($result)) {
                        // Parse IDX Listing
                        $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                        // Check If Listing is Already Imported
                        try {
                            $exists = $db->fetch("SELECT `id` FROM `" . TABLE_LISTINGS . "` WHERE `imported` = 'true' AND `mls_number` = :mls_number;", ["mls_number" => $listing['ListingMLS']]);

                            if (!empty($exists)) {
                                $listing['imported'] = true;
                            } else {
                                $new_listings[] = $listing['ListingMLS'];
                            }
                        } catch (PDOException $e) {}

                        // Add to Listings
                        $listings[$i] = $listing;
                        $order_price[$i] = $listing['ListingPrice'];
                        $order_import[$i] = empty($listing['imported']);

                        // Increment
                        $i++;
                    }

                    // Re-Order Listings
                    array_multisort($order_import, SORT_DESC, $order_price, SORT_DESC, $listings);

                    // Query Error
                } else {
                    $errors[] = __('An error occurred while searching for MLS&reg; listings to import.');
                }

                // Query Error
            } else {
                $errors[] = __('An error occurred while finding MLS&reg; listings to import.');
            }

            // No Results
        } else {
            $errors[] = __('No MLS&reg; listings using this Office ID could be found in the database.');
        }

        // Exception Caught
    } catch (Exception $e) {
        $errors[] = __('An error occurred while attempting to search your MLS&reg; listings.');
    }
}
