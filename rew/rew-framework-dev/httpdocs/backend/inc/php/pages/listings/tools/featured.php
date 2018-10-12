<?php

// App DB
$db = DB::get();

// Full Width
$body_class = 'full';

// Get Authorization Managers
$listingAuth = new REW\Backend\Auth\ListingsAuth(Settings::getInstance());

$feeds = (Settings::getInstance()->IDX_FEEDS) ? Settings::getInstance()->IDX_FEEDS : [Settings::getInstance()->IDX_FEED => ['title' => Settings::getInstance()->IDX_FEED]];

// Authorized to manage directories
if (!$listingAuth->canFeatureListings($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to manage featured listings'
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Delete Featured Listing
if (!empty($_GET['delete'])) {
    // Require Record from Database
    try {
        $delete = $db->fetch("SELECT * FROM `" . TABLE_FEATURED_LISTINGS . "` WHERE `id` = :id;", ["id" => $_GET['delete']]);
    } catch (PDOException $e) {}

    if (!empty($delete)) {
        // Delete Record
        try {
            $db->prepare("DELETE FROM `" . TABLE_FEATURED_LISTINGS . "` WHERE `id` = :id;")->execute(["id" => $_GET['delete']]);

            // Success
            $success[] = 'The selected featured listing has successfully been removed.';

            // Trigger hook after featured listing is removed
            Hooks::hook(Hooks::HOOK_FEATURED_LISTING_DELETE)->run($delete['idx'], $delete['mls_number']);

        // Query Error
        } catch (PDOException $e) {
            $errors[] = 'The selected featured listing could not be removed.';
        }

    // Unknown Record
    } else {
        $errors[] = 'The selected featured listing could not be found.';
    }

    // Save Notices
    $authuser->setNotices($success, $errors);

    // Redirect Back to List
    header('Location: ?delete');
    exit;
}

// Record ID
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];

// Edit Featured Listing
if (!empty($_GET['edit'])) {
    // Check if Module is Installed
    if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS_OVERRIDE'])) {
        // Require Record from Database
        try {
            $edit_row = $db->fetch("SELECT * FROM `" . TABLE_FEATURED_LISTINGS . "` WHERE `id` = :id;", ["id" => $_GET['edit']]);
        } catch (PDOException $e) {}

        if (!empty($edit_row)) {
            try {
                // IDX Object
                $idx = Util_IDX::getIdx($edit_row['idx']);

                // IDX Database
                $db_idx = Util_IDX::getDatabase($edit_row['idx']);

                $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($edit_row['mls_number']) . "'";

                // Any global criteria
                $idx->executeSearchWhereCallback($search_where);

                // Select Listing Details
                $query = "SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $search_where . ";";
                $featured = $db_idx->fetchQuery($query);

                // Parse Listing
                $featured = Util_IDX::parseListing($idx, $db_idx, $featured);

                // Featured Listing Details
                $edit_row['city']        = !empty($edit_row['city'])      ? $edit_row['city']      : $featured['AddressCity'];
                $edit_row['price']       = !empty($edit_row['price'])     ? $edit_row['price']     : $featured['ListingPrice'];
                $edit_row['bedrooms']    = !empty($edit_row['bedrooms'])  ? $edit_row['bedrooms']  : $featured['NumberOfBedrooms'];
                $edit_row['bathrooms']   = !empty($edit_row['bathrooms']) ? $edit_row['bathrooms'] : $featured['NumberOfBathrooms'];
                $edit_row['remarks']     = !empty($edit_row['remarks'])   ? $edit_row['remarks']   : $featured['ListingRemarks'];
                $edit_row['address']     = $featured['Address'];
                $edit_row['url_details'] = $featured['url_details'];

                // Listing Details
                $edit_row['listing'] = $featured;

                // Process Form Submission
                if (isset($_GET['submit'])) {
                    // Raise Memory Limit
                    ini_set('memory_limit', (64 * 1024 * 1024));

                    // Upload Feature Listing Photo
                    if (isset($_FILES) && count($_FILES) > 0 && is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
                        try {
                            // Read Uploaded Image
                            $uploadedImage = new Image;
                            $uploadedImage->readFile($_FILES['featured_image']['tmp_name']);

                            // Attempt Resize
                            $width     = $uploadedImage->getWidth();
                            $height    = $uploadedImage->getHeight();
                            $newWidth  = 0;
                            $newHeight = 0;
                            $uploadedImage->calculateNewDimensions(600, 400, $newWidth, $newHeight);
                            $canResize = $uploadedImage->predict($newWidth, $newHeight);
                            if ($canResize) {
                                // Generate Filename
                                $imageName = $_FILES['featured_image']['name'];
                                $imageExt  = substr(strrchr($imageName, '.'), 1);
                                $imageName = mt_rand() . '.' . $imageExt;
                                $uploadedImage->setName($imageName);

                                // Resize Image
                                $resizedImage = $uploadedImage->resample($newWidth, $newHeight);

                                // Save Thumbnail
                                $resizedImage->write(DIR_FEATURED_IMAGES);

                                // Delete Previous Image
                                if (!empty($edit_row['image'])) {
                                    if (file_exists(DIR_FEATURED_IMAGES . $edit_row['image'])) {
                                        unlink(DIR_FEATURED_IMAGES . $edit_row['image']);
                                    }
                                }

                                // Save Image
                                $_POST['featured_image'] = $uploadedImage->getName();

                            // Resize Error
                            } else {
                                $errors[] = "Featured listing image received, but it's too big for the server to process.";
                            }

                        // Error Occurred
                        } catch (Exception $e) {
                            //$errors[] = 'Featured listing image upload failed during communication with the server.';
                            $errors[] = $e->getMessage();
                        }
                    }

                    // Check Errors
                    if (empty($errors)) {
                        // Delete Image
                        if (!empty($_POST['delete_image'])) {
                            @unlink(DIR_FEATURED_IMAGES . $_POST['featured_image']);
                            unset($_POST['featured_image']);
                        }


                        try {
                            // Execute Query
                            $db->prepare("UPDATE `" . TABLE_FEATURED_LISTINGS . "` SET "
                                   . " `city`      = :city,"
                                   . " `price`     = :price,"
                                   . " `bedrooms`  = :bedrooms,"
                                   . " `bathrooms` = :bathrooms,"
                                   . " `remarks`   = :remarks,"
                                   . " `image`     = :image"
                                   . " WHERE `id` = :id;")->execute([
                                        "city" =>       $_POST['featured_city'],
                                        "price" =>      $_POST['featured_price'],
                                        "bedrooms" =>   $_POST['featured_bedrooms'],
                                        "bathrooms" =>  $_POST['featured_bathrooms'],
                                        "remarks" =>    $_POST['featured_remarks'],
                                        "image" =>      $_POST['featured_image'],
                                        "id" =>         $edit_row['id']
                            ]);

                            // Success
                            $success[] = 'Featured Listing has successfully been updated.';

                            // Trigger hook after featured listing has been updated
                            Hooks::hook(Hooks::HOOK_FEATURED_LISTING_UPDATE)->run($edit_row['idx'], $edit_row['mls_number']);

                            // Save Notices & Redirect Back to List
                            $authuser->setNotices($success, $errors);
                            header('Location: ?success');
                            exit;

                        // Query Error
                        } catch (PDOException $e) {
                            $errors[] = 'The selected featured listing could not be updated.';
                        }
                    }
                }

            // Error Occurred
            } catch (Exception $e) {
                Log::error($e);
            }

        // Record not Found
        } else {
            $errors[] = 'The selected featured listing could not be found.';
        }
    }
} else {
    // Row ID
    $_GET['add'] = isset($_POST['add']) ? $_POST['add'] : $_GET['add'];

    // Add Featured Listing
    if (isset($_GET['submit'])) {
        // Trim MLS Number
        $_POST['featured_mls'] = trim($_POST['featured_mls']);

        // Require MLS Number
        if (empty($_POST['featured_mls'])) {
            $errors[] = 'Please supply a valid MLS&reg; Number of the listing you\'d like to feature.';
        } else {
            // IDX Object
            $idx = Util_IDX::getIdx($featured['idx']);

            // IDX Database
            $db_idx = Util_IDX::getDatabase($featured['idx']);

            // Split by Comma, Allowing Mulitples
            $mls_numbers = explode(',', $_POST['featured_mls']);
            foreach ($mls_numbers as $mls_number) {
                $mls_number = trim($mls_number);
                if (empty($mls_number)) {
                    continue;
                }

                // Require Valid IDX Object
                if (!($idx instanceof IDX)) {
                    $errors[] = 'Listing could not be found for MLS&reg; #' . htmlspecialchars($mls_number);
                } else {
                    $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($mls_number) . "'";

                    // Any global criteria
                    $idx->executeSearchWhereCallback($search_where);

                    // Locate IDX Listing
                    $query = "SELECT COUNT(*) AS `total` FROM `" . $idx->getTable(). "` WHERE " . $search_where . ";";
                    $checkExistance = $db_idx->fetchQuery($query);
                    if ($checkExistance['total'] < 1) {
                        $errors[] = 'Listing could not be found for MLS&reg; #' . htmlspecialchars($mls_number);
                    } else {
                        // Already Featured
                        try {
                            $checkDuplicate = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_FEATURED_LISTINGS . "` WHERE `mls_number` = :mls_number;", ["mls_number" => $mls_number]);
                        } catch (PDOException $e) {}

                        if ($checkDuplicate['total'] > 0) {
                            $errors[] = 'This listing is already featured: MLS&reg; #' . htmlspecialchars($mls_number);

                        // Add Featured
                        } else {
                            // Compliance - Office restriction
                            if (!empty($_COMPLIANCE['featured']['office_id'])) {
                                $office_ids = !is_array($_COMPLIANCE['featured']['office_id']) ? explode(',', $_COMPLIANCE['featured']['office_id']) : $_COMPLIANCE['featured']['office_id'];

                                $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($mls_number) . "' AND "
                                    . "`" . $idx->field('ListingOfficeID') . "` IN ('" . implode("', '", $office_ids) . "')";

                                // Any global criteria
                                $idx->executeSearchWhereCallback($search_where);


                                $query = "SELECT COUNT(*) AS `total` FROM `" . $idx->getTable(). "` WHERE "
                                    . $search_where . ";";
                                $checkExistance = $db_idx->fetchQuery($query);
                                if ($checkExistance['total'] == 0) {
                                    $errors[] = 'Your MLS Board restricts you from featuring properties that aren\'t listed by your office.';
                                }
                            }

                            // Require no errors
                            if (empty($errors)) {
                                // IDX feed name
                                $idx_feed = Settings::getInstance()->IDX_FEED;

                                try{
                                    // Execute Insert Query
                                    $db->prepare("INSERT INTO `" . TABLE_FEATURED_LISTINGS . "` SET "
                                           . "`mls_number` = :mls_number,"
                                           . "`city`       = :city,"
                                           . "`price`      = :price,"
                                           . "`bedrooms`   = :bedrooms,"
                                           . "`bathrooms`  = :bathrooms,"
                                           . "`remarks`    = :remarks,"
                                           . "`table`      = :table,"
                                           . "`idx`        = :idx;")->execute([
                                                "mls_number" => $mls_number,
                                                "city" => !empty($_POST['featured_city']) ? $_POST['featured_city'] : "",
                                                "price" => !empty($_POST['featured_price']) ? $_POST['featured_price'] : "",
                                                "bedrooms" => !empty($_POST['featured_bedrooms']) ? $_POST['featured_bedrooms'] : "",
                                                "bathrooms" => !empty($_POST['featured_bathrooms']) ? $_POST['featured_bathrooms'] : "",
                                                "remarks" => !empty($_POST['featured_remarks']) ? $_POST['featured_remarks'] : "",
                                                "table" => $idx->getTable(),
                                                "idx" => $idx_feed
                                    ]);

                                    // Success
                                    $success[] = 'Featured Listing has successfully been added: MLS&reg; #' . htmlspecialchars($mls_number);

                                    // Trigger hook after featured listing has been added
                                    Hooks::hook(Hooks::HOOK_FEATURED_LISTING_CREATE)->run($idx_feed, $mls_number);

                                    // Save Notices & Redirect Back to List
                                    $authuser->setNotices($success, $errors);
                                    header('Location: ?success' . (Settings::getInstance()->IDX_FEED_DEFAULT && Settings::getInstance()->IDX_FEED_DEFAULT !== $idx_feed ? '&feed=' . $idx_feed : ''));
                                    exit;

                                // Query Error
                                } catch (PDOException $e) {
                                    $errors[] = 'Featured Listing could not be added: MLS&reg; #' . htmlspecialchars($mls_number);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// Featured Listings
$featured_listings = array();
try {
    foreach ($db->fetchAll("SELECT * FROM `" . TABLE_FEATURED_LISTINGS . "`;") as $featured) {
        try {
            // IDX Object
            $idx = Util_IDX::getIdx($featured['idx']);

            // IDX Database
            $db_idx = Util_IDX::getDatabase($featured['idx']);

            $search_where = "`" . $idx->field('ListingMLS') . "` = '" . $featured['mls_number'] . "'";

            // Any global criteria
            $idx->executeSearchWhereCallback($search_where);

            // IDX Listing Row
            $row = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $search_where);
            if (!empty($row)) {
                // Parse Listing
                $featured_listing = Util_IDX::parseListing($idx, $db_idx, $row);

                // Set Information
                $featured_listing['id'] = $featured['id'];
                $featured_listing['heading'] = $featured['heading'];

                // Check if Module is Installed
                if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS_OVERRIDE'])) {
                        $featured_listing['AddressCity']       = !empty($featured['city'])      ? $featured['city']       : $featured_listing['AddressCity'];
                        $featured_listing['ListingPrice']      = !empty($featured['price'])     ? $featured['price']      : $featured_listing['ListingPrice'];
                        $featured_listing['NumberOfBedrooms']  = !empty($featured['bedrooms'])  ? $featured['bedrooms']   : $featured_listing['NumberOfBedrooms'];
                        $featured_listing['NumberOfBathrooms'] = !empty($featured['bathrooms']) ? $featured['bathrooms']  : $featured_listing['NumberOfBathrooms'];
                        $featured_listing['ListingRemarks']    = !empty($featured['remarks'])   ? $featured['remarks']    : $featured_listing['ListingRemarks'];
                    if (!empty($featured['image']) && file_exists(DIR_FEATURED_IMAGES . $featured['image'])) {
                        $featured_listing['ListingImage'] = '/thumbs/60x60/uploads/featured/' . $featured['image'];
                    }
                }

                // Add Featured Listing
                $featured_listings[] = $featured_listing;
            } else {
                // Add Featured Listing
                $featured_listings[] = $featured;
            }

        // Error Occurred...
        } catch (Exception $e) {
            Log::error($e);
        }
    }

// Query Error
} catch (PDOException $e) {
    $errors[] = 'Error Occurred while loading Featured Listings.';
}
