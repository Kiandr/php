<?php

// DB Connection
$db = DB::get('directory');

// Directory categories
$categories = array();
$result = $db->prepare("SELECT * FROM `directory_categories` WHERE `parent` = :parent ORDER BY `order` ASC, `title` ASC;");
$result->execute(array('parent' => ''));
while ($category = $result->fetch()) {
    $category['subcategories'] = array();
    $subcategories = $db->prepare("SELECT * FROM `directory_categories` WHERE `parent` = :parent ORDER BY `order` ASC, `title` ASC;");
    $subcategories->execute(array('parent' => $category['link']));
    while ($subcategory = $subcategories->fetch()) {
        $subcategory['subcategories'] = array();
        $subsubcategories = $db->prepare("SELECT * FROM `directory_categories` WHERE `parent` = :parent ORDER BY `order` ASC, `title` ASC;");
        $subsubcategories->execute(array('parent' => $subcategory['link']));
        while ($subsubcategory = $subsubcategories->fetch()) {
            $subcategory['subcategories'][] = $subsubcategory;
        }
        $category['subcategories'][] = $subcategory;
    }
    $categories[] = $category;
}

// Listing Categories
$_POST['listing_category'] = isset($_POST['listing_category']) ? $_POST['listing_category'] : $_GET['listing_category'];
$_POST['listing_category'] = is_array($_POST['listing_category']) ? $_POST['listing_category'] : explode(',', $_POST['listing_category']);

// Process Submission
if (isset($_GET['submit'])) {
    // Errors
    $errors = array();

    // Required Fields
    if (empty($_POST['business_name'])) {
        $errors[] = 'Business Name is a required field.';
    }
    if (empty($_POST['contact_name'])) {
        $errors[] = 'Contact Name is a required field.';
    }

    // Remove City from Address
    if (!empty($_POST['city']) && preg_match('|\, ' . $_POST['city'] . '|', $_POST['address'])) {
        $_POST['address'] = preg_replace('|\, ' . $_POST['city'] . '|', '', $_POST['address']);
    }

    // Check Business Required Fields
    if (empty($_POST['address']) && empty($_POST['phone']) && empty($_POST['website'])) {
        $errors[] = 'You must supply either a Business Address, Primary Phone Number, or Website URL.';
    } else {
        if (!empty($_POST['phone']) && !Validate::phone($_POST['phone'], true)) {
            $errors[] = 'Primary phone number must be entered as ###-###-###.';
        }
    }

    // Check Phone Numberss
    if (!empty($_POST['alt_phone']) && !Validate::phone($_POST['alt_phone'], true)) {
        $errors[] = 'Secondary phone number must be entered as ###-###-###.';
    }
    if (!empty($_POST['toll_free']) && !Validate::phone($_POST['toll_free'], true)) {
        $errors[] = 'Toll Free number must be entered as ###-###-###.';
    }
    if (!empty($_POST['fax']) && !Validate::phone($_POST['fax'], true)) {
        $errors[] = 'Fax number must be entered as ###-###-###.';
    }

    // Require Category
    if (empty($_POST['listing_category']) && empty($_POST['other_category'])) {
        $errors[] = 'You must select a category or suggest a new category for your business.';
    }

    // Require Contact Info
    if (empty($_POST['contact_phone']) && empty($_POST['contact_email'])) {
        $errors[] = 'You must supply either a Contact Phone Number or Contact Email.';
    } else {
        if (!empty($_POST['contact_email']) && !Validate::email($_POST['contact_email'])) {
            $errors[] = 'You must provide a valid Contact Email.';
        }
    }

    // Slugify link from business name
    $_POST['link'] = Format::slugify($_POST['business_name']);

    // Check Duplicates
    $result = $db->prepare("SELECT `id` FROM `directory_listings` WHERE `link` = :link;");
    $result->execute(array('link' => $_POST['link']));
    $check = $result->fetchColumn();
    if (!empty($check)) {
        $errors[] = 'Sorry but a business by that name already exists.';
    }

    // Check Errors
    if (empty($errors)) {
        // Check Phone Number
        if (preg_match('|\)([0-9]+)|', $_POST['phone'])) {
            $_POST['phone'] = preg_replace('|\)|', '-', preg_replace('|\(|', '', $_POST['phone']));
        } elseif (preg_match('|\)([ ]+)|', $_POST['phone'])) {
            $_POST['phone'] = preg_replace('|\) |', '-', preg_replace('|\(|', '', $_POST['phone']));
        } else {
            $_POST['phone'] = preg_replace('|\)|', '', preg_replace('|\(|', '', $_POST['phone']));
        }

        // Check Contact Phone #
        if (preg_match('|\)([0-9]+)|', $_POST['contact_phone'])) {
            $_POST['contact_phone'] = preg_replace('|\)|', '-', preg_replace('|\(|', '', $_POST['contact_phone']));
        } elseif (preg_match('|\)([ ]+)|', $_POST['contact_phone'])) {
            $_POST['contact_phone'] = preg_replace('|\) |', '-', preg_replace('|\(|', '', $_POST['contact_phone']));
        } else {
            $_POST['contact_phone'] = preg_replace('|\)|', '', preg_replace('|\(|', '', $_POST['contact_phone']));
        }

        // Geocode Address
        if (!empty($_POST['address'])) {
            $address = $_POST['address'] . ' ' . $_POST['city'] . ' ' . $_POST['state'] . ' ' . $_POST['zip'];
            $geoinfo = Map::geocode($address);
            if (!empty($geoinfo)) {
                $_POST['latitude']  = $geoinfo['latitude'];
                $_POST['longitude'] = $geoinfo['longitude'];
            }
        }

        try {
            // Prepare INSERT Query
            $insert = $db->prepare("INSERT INTO `directory_listings` SET "
                . "`business_name`		= IFNULL(:business_name, ''),"
                . "`page_title`			= IFNULL(:page_title, ''),"
                . "`link`				= IFNULL(:link, ''),"
                . "`address`			= IFNULL(:address, ''),"
                . "`city`				= IFNULL(:city, ''),"
                . "`state`				= IFNULL(:state, ''),"
                . "`zip`				= IFNULL(:zip, ''),"
                . "`phone`				= IFNULL(:phone, ''),"
                . "`alt_phone`			= IFNULL(:alt_phone, ''),"
                . "`toll_free`			= IFNULL(:toll_free, ''),"
                . "`fax`				= IFNULL(:fax, ''),"
                . "`website`			= IFNULL(:website, ''),"
                . "`description`		= IFNULL(:description, ''),"
                . "`categories`			= IFNULL(:categories, ''),"
                . "`other_category`		= IFNULL(:other_category, ''),"
                . "`contact_name`		= IFNULL(:contact_name, ''),"
                . "`contact_phone`		= IFNULL(:contact_phone, ''),"
                . "`contact_email`		= IFNULL(:contact_email, ''),"
                . "`latitude`			= IFNULL(:latitude, ''),"
                . "`longitude`			= IFNULL(:longitude, ''),"
                . "`preview`			= IFNULL(:preview, ''),"
                . "`session_id`			= IFNULL(:session_id, ''),"
                . "`website_link`		= 'Y',"
                . "`timestamp_created`	= NOW()"
            . ";");

            // Execute INSERT Query
            $insert->execute(array(
                'business_name'     => $_POST['business_name'],
                'page_title'        => $_POST['business_name'],
                'link'              => $_POST['link'],
                'address'           => $_POST['address'],
                'city'              => $_POST['city'],
                'state'             => $_POST['state'],
                'zip'               => $_POST['zip'],
                'phone'             => $_POST['phone'],
                'alt_phone'         => $_POST['alt_phone'],
                'toll_free'         => $_POST['toll_free'],
                'fax'               => $_POST['fax'],
                'website'           => $_POST['website'],
                'description'       => $_POST['description'],
                'categories'        => (is_array($_POST['listing_category'])  ? implode(',', $_POST['listing_category']) : $_POST['listing_category']),
                'other_category'    => $_POST['other_category'],
                'contact_name'      => $_POST['contact_name'],
                'contact_phone'     => $_POST['contact_phone'],
                'contact_email'     => $_POST['contact_email'],
                'latitude'          => $_POST['latitude'],
                'longitude'         => $_POST['longitude'],
                'preview'           => ($_POST['preview'] == 'Y' ? 'Y' : 'N'),
                'session_id'        => $_COOKIE['PHPSESSID']
            ));

            // Listing ID
            $listing_id = $db->lastInsertId();

            // Assign Uploads to Listing
            if (!empty($_POST['uploads'])) {
                $update = $db->prepare("UPDATE `" . Settings::getInstance()->TABLES['UPLOADS']  . "` SET `row` = :listing WHERE `id` = :upload;");
                foreach ($_POST['uploads'] as $upload) {
                    $update->execute(array('listing' => $listing_id, 'upload' => $upload));
                }
            }

            // Show Preview
            if ($_POST['preview'] == 'Y') {
                header('Location: ' . URL_DIRECTORY . $listing_id . '/edit/');
                exit;
            } else {
                // Success
                $success = 'Your business has successfully been submitted and now awaits approval to be displayed.';
                unset($_POST);

                // Get Super Admin
                $admin = $db->fetch("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name`, `email` FROM `agents` WHERE `id` = :agent;", array('agent' => 1));
                if (!empty($admin)) {
                    // Create Mailer
                    $mailer = new \PHPMailer\RewMailer();
                    $mailer->IsHTML(true);

                    // Configure Sender
                    $mailer->FromName = 'Business Directory';
                    $mailer->From = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];

                    // Add Email Recipient
                    $mailer->AddAddress($admin['email'], $admin['name']);

                    // Email Subject
                    $mailer->Subject = 'New Business Submission';

                    // Backend URL
                    $url_backend = Settings::getInstance()->URLS['URL_BACKEND'] . 'directory/listings/?pending=show';

                    // Email Message (HTML)
                    $mailer->Body    = '';
                    $mailer->Body   .= '<p>You have a new business directory submission.</p>';
                    $mailer->Body   .= '<p>You can manage your business directory from <a href="' . $url_backend . '" target="_blank">' . $url_backend . '</a>.</p>';
                    $mailer->Body   .= '<p>';
                    $mailer->Body   .= '<strong>Business Name:</strong> ' . Format::htmlspecialchars($_POST['business_name']) . '<br />';
                    $mailer->Body   .= '<strong>Contact Name:</strong> ' . Format::htmlspecialchars($_POST['contact_name']) . '<br />';
                    $mailer->Body   .= '</p>';

                    // Email Message (Pain Text)
                    $mailer->AltBody  = '';
                    $mailer->AltBody .= 'You have a new business directory submission.' . "\n\n";
                    $mailer->AltBody .= 'You can manage your business directory from ' . $url_backend . "\n\n";
                    $mailer->AltBody .= 'Business Name: ' . Format::htmlspecialchars($_POST['business_name']) . "\n";
                    $mailer->AltBody .= 'Contact Name: ' . Format::htmlspecialchars($_POST['contact_name']) . "\n\n";

                    // Send Email
                    $mailer->Send();
                }
            }

        // Database error
        } catch (PDOException $e) {
            $errors[] = 'An error occurred while attempting to submit your business. Please try again.';
            Log::error($e);
        }
    }

    // Listing Photos
    $uploads = array();
    $logo_uploads = array();
    if (!empty($_POST['uploads'])) {
        $result = $db->prepare("SELECT `id`, `file`, `type` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `id` = :upload ORDER BY `order` ASC;");
        foreach ($_POST['uploads'] as $upload) {
            $result->execute(array('upload' => $upload));
            $upload = $result->fetch();
            if (!empty($upload)) {
                if ($upload['type'] === 'directory_logo') {
                    $logo_uploads[] = $upload;
                } else {
                    $uploads[] = $upload;
                }
            }
        }
    }
}
