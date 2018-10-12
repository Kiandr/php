<?php

/**
 * Get Submission Message from POST
 *
 * @return string HTML Message
 */
function getFormVars()
{

    // Ignore Keys
    $ignore = array('submit', 'reset', 'vdaemonvalidators', 'phpsessid', 'ini_dest', '__utma', '__utmz', '__utmb', '__utmc', 'mi0moecs', 'onc5khko', 'sk5tyelo', 'send', 'step', 'approveform', 'buyersform', 'contactform','sellerform', 'cmaform', 'testimonialform', 'guaranteedsoldform');

    // Process $_POST Data
    $vars = $_POST;
    $honeypots = array();
    foreach ($vars as $key => $val) {
        // Empty (Ignore)
        if (empty($val)) {
            unset($vars[$key]);
            continue;
        }

        // Remove Honeypot Variables
        if (isset($vars['mi0moecs']) ||
            isset($vars['onc5khko']) ||
            isset($vars['sk5tyelo'])) {
            if ($key == 'email') {
                continue;
            }
            if ($key == 'first_name') {
                continue;
            }
            if ($key == 'last_name') {
                continue;
            }
            if ($key == 'onc5khko') {
                $honeypots['first_name'] = $val;
            }
            if ($key == 'sk5tyelo') {
                $honeypots['last_name']  = $val;
            }
            if ($key == 'mi0moecs') {
                $honeypots['email']      = $val;
            }
        }

        // Stringify Arrays
        if (is_array($vars[$key])) {
            $vars[$key] = implode(' / ', $vars[$key]);
        }

        // Remove Ignored Values
        foreach ($ignore as $x) {
            if (strtolower($key) == strtolower($x)) {
                unset($vars[$key]);
            }
        }
    }

    // Submission Details
    $form_ip    = !empty($_SERVER['REMOTE_ADDR'])  ? '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/?submit=true&search_ip=' . $_SERVER['REMOTE_ADDR'] . '">' . $_SERVER['REMOTE_ADDR'] . '</a>'  : 'Unknown';
    $form_refer = !empty($_SERVER['HTTP_REFERER']) ? '<a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '</a>' : 'Unknown';
    $form_time  = date('d F Y h:i A');

    // Generate Message
    $msg  = '<h2>Website Form Submission</h2>';
    $msg .= '<p>From IP ' . $form_ip . '<br><font color="red">This form was submitted from <b>' . $form_refer . '</b> at <b>' . $form_time . '</b></font></p>';

    // Generate Message from Submission Values
    $vars = array_merge($honeypots, $vars);
    if (!empty($vars)) {
        foreach ($vars as $field => $value) {
            if (stristr($value, 'Content-Type')) {
                die('Why ?? :(');
            } else {
                $field = ucfirst(htmlspecialchars(str_replace('_', ' ', $field)));

                // rename some fields
                $field = preg_replace('/Telephone/', 'Primary Number', $field);
                $field = preg_replace('/Fm-mobile/', 'Secondary Number', $field);

                // If value is a URL - make it a link
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $value = '<a href="' . $value . '">' . urldecode($value) . '</a>';
                } else {
                    $value = nl2br(htmlspecialchars($value));
                }
                // Append to message
                $msg .= '<b>' . $field . '</b><br>' . $value . '<br><br>' . "\n";
            }
        }
    }

    // Remove this Stuff
    $msg = preg_replace(array('/bcc\:/i', '/Content\-Type\:/i', '/cc\:/i', '/to\:/i'), '', $msg);

    // Return Message
    return $msg;
}

/**
 * Collect data from contact forms and add the user to the database
 *
 * $fieldList is an array of all the values known about the user
 *
 * array (
 *        'first_name'      => 'Bob',
 *        'last_name'       => 'Smith',
 *        'password'        => 'pass',
 *        'email'           => 'test@test.com',
 *        'address1'        => '123 test street',
 *        'address2'        => '#3',
 *        'address3'        => 'Atten: Owner',
 *        'city'            => 'Nanaimo',
 *        'country'         => 'Canada',
 *        'state'           => 'BC',
 *        'zip'             => '12345',
 *        'phone'           => '123-4567',
 *        'comments'        => 'user submitted comments',
 *        'subject'         => 'subject for email msg to agent',
 *        'message'         => 'email msg to agent',
 *        'phone_work'      => '123-4567',
 *        'phone_fax'       => '123-4567',
 *        'agent'           => '3',
 *        'forms'           => 'Contact Form',
 *        'opt_marketing'   => 'in',
 *        'opt_searches'    => 'out',
 *        'opt_texts'       => 'out'
 * )
 *
 * $ar_id is the Auto Responder ID to user, 0 to disable
 *
 * @param array $fieldList
 * @param int $ar_id Auto-Responder ID
 * @return int Lead ID
 */
function collectContactData($fieldList, $ar_id)
{

    // Start timing
    $_timer = Profile::timer()->stopwatch(__FUNCTION__)->start();

    // Global Variables
    global $userTrackID;

    // Track if its a new lead or not
    $newlead = true;

    // Set Variables
    foreach ($fieldList as $field => $form_field) {
        $form_field = !is_array($form_field) ? trim($form_field) : $form_field;
        if (!empty($form_field)) {
            $$field = $form_field;
        }
    }

    // DB connection
    $db = DB::get();

    // Find existing lead by email address
    $result = $db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1;");
    $result->execute(array('email' => $email));
    $contact = $result->fetch();
    if (!empty($contact)) {
        $newlead = false;

        // Prepare UPDATE Query
        $result = $db->prepare("UPDATE `users` SET "
            . "`first_name`		= IFNULL(:first_name, `first_name`),"
            . "`last_name`		= IFNULL(:last_name, `last_name`),"
            . "`email`			= IFNULL(:email, `email`),"
            . "`password`		= IFNULL(:password, `password`),"
            . "`address1`		= IFNULL(:address1, `address1`),"
            . "`address2`		= IFNULL(:address2, `address2`),"
            . "`address3`		= IFNULL(:address3, `address3`),"
            . "`city`			= IFNULL(:city, `city`),"
            . "`country`		= IFNULL(:country, `country`),"
            . "`state`			= IFNULL(:state, `state`),"
            . "`zip`			= IFNULL(:zip, `zip`),"
            . "`phone`			= IFNULL(:phone, `phone`),"
            . "`comments`		= IF(:comments IS NULL, `comments`, CONCAT(:comments, '\\n\\n', `comments`)),"
            . "`phone_cell`		= IFNULL(:phone_cell, `phone_cell`),"
            . "`phone_work`		= IFNULL(:phone_work, `phone_work`),"
            . "`phone_fax`		= IFNULL(:phone_fax, `phone_fax`),"
            . "`forms`			= IF(:forms IS NULL, `forms`, CONCAT_WS(',', `forms`, :forms)),"
            . "`contact_method`	= IFNULL(:contact_method, `contact_method`),"
            . "`opt_marketing`	= IFNULL(:opt_marketing, `opt_marketing`),"
            . "`opt_searches`	= IFNULL(:opt_searches, `opt_searches`),"
            . "`opt_texts`      = IFNULL(:opt_texts, `opt_texts`),"
            . "`timestamp_active` = NOW()"
            . " WHERE `id` = :id"
        . ";");

        // Query Parameters
        $params = array_map(function ($val) {
            return empty($val) ? null : $val;
        }, array(
            'id'            => $contact['id'],
            'first_name'    => Format::ucnames($first_name),
            'last_name'     => Format::ucnames($last_name),
            'email'         => $email,
            'password'      => $password,
            'address1'      => $address1,
            'address2'      => $address2,
            'address3'      => $address3,
            'city'          => $city,
            'country'       => $country,
            'state'         => $state,
            'zip'           => $zip,
            'phone'         => $phone,
            'comments'      => $comments,
            'phone_cell'    => $phone_cell,
            'phone_work'    => $phone_work,
            'phone_fax'     => $phone_fax,
            'forms'         => $forms,
            'contact_method'=> $contact_method,
            'opt_marketing' => $opt_marketing,
            'opt_searches'  => $opt_searches,
            'opt_texts'     => $opt_texts
        ));

        // Execute UPDATE Query
        $result->execute($params);

        // Lead ID
        $user_id = $contact['id'];
    } else {
        // Get Site Settings
        $settings = $db->fetch("SELECT `auto_assign`, `auto_assign_lenders` FROM `default_info` WHERE `agent` = 1;");

        // Agent or Team Sub-Domain
        if (Settings::getInstance()->SETTINGS['agent'] != 1) {
            // Team subdomain
            if (Settings::getInstance()->SETTINGS['team'] && !empty(Settings::getInstance()->MODULES['REW_TEAMS'])) {
                $team = Backend_Team::load(Settings::getInstance()->SETTINGS['team']);
                $userSite = Settings::getInstance()->SETTINGS['team'];
                $userSiteType = 'team';
            // Agent subdomain
            } else {
                $agent = Settings::getInstance()->SETTINGS['agent'];
                $userSite = Settings::getInstance()->SETTINGS['agent'];
                $userSiteType = 'agent';
            }
        } else {
            $userSite = 1;
            $userSiteType = 'domain';
        }

        // Agent Assigned (Don't Rotate Lead)
        if (!empty($agent)) {
            $auto_rotate = 'false';

        // Team Assigned (Team Rotate Lead)
        } else if (!empty($team) && $team instanceof Backend_Team) {
            $teamAgentsCollection = $team->getAgentCollection()->filterByGrantedPermissions([Backend_Team::PERM_ASSIGN]);
            $agent = $teamAgentsCollection->getNextAgent();
            if (!empty($agent)) {
                $result = $db->prepare("UPDATE `team_agents` SET `auto_assign_time` = NOW() WHERE `team_id` = :team_id AND `agent_id` = :agent_id;");
                $result->execute(['team_id' => $team->getId(), 'agent_id' => $agent]);
                $auto_rotate_team = $team->getId();
            }
        // Lead Auto-Assign
        } elseif ($settings['auto_assign'] == 'true') {
            $agent = $db->fetch("SELECT `id` FROM `agents` WHERE `auto_assign_admin` = 'true' AND `auto_assign_agent` = 'true' ORDER BY `auto_assign_time` ASC LIMIT 1;");
            if (!empty($agent)) {
                $result = $db->prepare("UPDATE `agents` SET `auto_assign_time` = NOW() WHERE `id` = :agent;");
                $result->execute(array('agent' => $agent['id']));
                $agent = $agent['id'];
            }
        }

        // Lender Auto-Assignment
        if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
            if ($settings['auto_assign_lenders'] == 'true' && empty($lender)) {
                $lender = $db->fetch("SELECT `id` FROM `lenders` WHERE `auto_assign_admin` = 'true' AND `auto_assign_optin` = 'true' ORDER BY `auto_assign_time` ASC LIMIT 1;");
                if (!empty($lender)) {
                    $result = $db->prepare("UPDATE `lenders` SET `auto_assign_time` = NOW() WHERE `id` = :lender;");
                    $result->execute(array('lender' => $lender['id']));
                    $lender = $lender['id'];
                }
            }
        }

        // Prepare INSERT Query
        $result = $db->prepare("INSERT INTO `users` SET "
            . "`first_name`			= IFNULL(:first_name, `first_name`),"
            . "`last_name`			= IFNULL(:last_name, `last_name`),"
            . "`email`				= IFNULL(:email, `email`),"
            . "`password`			= IFNULL(:password, `password`),"
            . "`address1`			= IFNULL(:address1, `address1`),"
            . "`address2`			= IFNULL(:address2, `address2`),"
            . "`address3`			= IFNULL(:address3, `address3`),"
            . "`city`				= IFNULL(:city, `city`),"
            . "`country`			= IFNULL(:country, `country`),"
            . "`state`				= IFNULL(:state, `state`),"
            . "`zip`				= IFNULL(:zip, `zip`),"
            . "`phone`				= IFNULL(:phone, `phone`),"
            . "`comments`			= IF(:comments IS NULL, `comments`, CONCAT(:comments, '\\n\\n', `comments`)),"
            . "`phone_cell`			= IFNULL(:phone_cell, `phone_cell`),"
            . "`phone_work`			= IFNULL(:phone_work, `phone_work`),"
            . "`phone_fax`			= IFNULL(:phone_fax, `phone_fax`),"
            . "`forms`				= IF(:forms IS NULL, `forms`, CONCAT_WS(',', `forms`, :forms)),"
            . "`contact_method`		= IFNULL(:contact_method, `contact_method`),"
            . "`opt_marketing`		= IFNULL(:opt_marketing, `opt_marketing`),"
            . "`opt_searches`		= IFNULL(:opt_searches, `opt_searches`),"
            . "`opt_texts`          = IFNULL(:opt_texts, `opt_texts`),"
            . "`auto_rotate`		= IFNULL(:auto_rotate, `auto_rotate`),"
            . "`auto_rotate_team`	= IFNULL(:auto_rotate_team, `auto_rotate_team`),"
            . "`site`               = IFNULL(:site, `site`),"
            . "`site_type`          = IFNULL(:site_type, `site_type`),"
            . "`timestamp_assigned`	= NOW(),"
            . "`timestamp_active`	= NOW(),"
            . "`timestamp`			= NOW()"
        . ";");

        // Query Parameters
        $params = array_map(function ($val) {
            return empty($val) ? null : $val;
        }, array(
            'first_name'        => Format::ucnames($first_name),
            'last_name'         => Format::ucnames($last_name),
            'email'             => $email,
            'password'          => $password,
            'address1'          => $address1,
            'address2'          => $address2,
            'address3'          => $address3,
            'city'              => $city,
            'country'           => $country,
            'state'             => $state,
            'zip'               => $zip,
            'phone'             => $phone,
            'comments'          => $comments,
            'phone_cell'        => $phone_cell,
            'phone_work'        => $phone_work,
            'phone_fax'         => $phone_fax,
            'forms'             => $forms,
            'contact_method'    => $contact_method,
            'opt_marketing'     => $opt_marketing,
            'opt_searches'      => $opt_searches,
            'opt_texts'         => $opt_texts,
            'auto_rotate'       => $auto_rotate,
            'auto_rotate_team'  => $auto_rotate_team,
            'site'              => $userSite,
            'site_type'         => $userSiteType
        ));

        // Execute INSERT Query
        $result->execute($params);

        // Lead ID
        $user_id = $db->lastInsertId();

        // Log Event: New Lead Created
        $event = new History_Event_Create_Lead(array(
            'lead_id' => $user_id
        ), array (
            new History_User_Lead($user_id)
        ));

        // Save to DB
        $event->save();

        // Add Viewed Listings
        $user = User_Session::get();
        $viewed_data = $user->info('viewed_data');
        if (!empty($viewed_data) && is_array($viewed_data)) {
            foreach ($viewed_data as $listing_data) {
                if (!isset($listing_data['ListingMLS']) || empty($listing_data['ListingMLS'])) {
                    continue;
                }

                // Track Viewed Listing
                $sql = "INSERT INTO `users_viewed_listings` SET "
                    . "`user_id`     = '" . $user_id . "', "
                    . "`mls_number`  = " . $db->quote($listing_data['ListingMLS']) . ", "
                    . "`table`       = " . $db->quote($listing_data['table']) . ", "
                    . "`idx`         = " . $db->quote($listing_data['idx']) . ", "
                    . "`type`        = " . $db->quote($listing_data['ListingType']) . ", "
                    . "`city`        = " . $db->quote($listing_data['AddressCity']) . ", "
                    . "`subdivision` = " . $db->quote($listing_data['AddressSubdivision']) . ", "
                    . "`bedrooms`    = " . $db->quote($listing_data['NumberOfBedrooms']) . ", "
                    . "`bathrooms`   = " . $db->quote($listing_data['NumberOfBathrooms']) . ", "
                    . "`sqft`        = " . $db->quote($listing_data['NumberOfSqFt']) . ", "
                    . "`price`       = " . $db->quote($listing_data['ListingPrice']) . ", "
                    . "`timestamp`   = '".$listing_data['timestamp']."';";
                try {
                    $db->query($sql);
                } catch (Exception $e) {
                    if (Settings::isREW()) {
                        echo $e->getMessage() . PHP_EOL;
                        echo $sql . PHP_EOL;
                    }
                    trigger_error("Track Viewed Listing Failed", E_USER_ERROR);
                }

                // Increment Viewed Listings
                $db->query("UPDATE `users` SET `num_listings` = `num_listings` + 1 WHERE `id` = '" . $user_id . "';");

                // Log Event: Viewed MLS Listing
                $event = new History_Event_Action_ViewedListing(array(
                    'listing' => $listing_data
                ), array(
                    new History_User_Lead($user_id)
                ));
                $event->save();
            }

            $user->info('viewed_data', array()); // empty viewed_data
            unset($viewed_data, $listing_data, $event);
        }

        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->trackRegisterEvent($agent);
    }

    // Select Contact
    $phone_cell = !empty($contact['phone_cell']) ? $contact['phone_cell'] : $phone_cell;
    $result = $db->prepare("SELECT * FROM `users` WHERE `id` = :user_id LIMIT 1;");
    $result->execute(array('user_id' => $user_id));
    $contact = $result->fetch();
    $contact['phone_cell'] = $phone_cell;

    // Form Submissions
    if (!empty($forms)) {
        foreach (explode(',', $forms) as $form) {
            // Run hook (and modify $_POST data)
            $data = Hooks::hook(Hooks::HOOK_LEAD_FORM_SUBMISSION)->run($_POST, $form, $contact, $listing);

            // Save Listing Data as part of History Events & Form Data
            if (!empty($listing) && is_array($listing)) {
                if (!isset($data['ListingMLS']) && isset($listing['ListingMLS'])) {
                    $data['ListingMLS'] = $listing['ListingMLS'];
                }
                if (!isset($data['ListingType']) && isset($listing['ListingType'])) {
                    $data['ListingType'] = $listing['ListingType'];
                }
                if (!isset($data['ListingFeed']) && isset($listing['ListingFeed'])) {
                    $data['ListingFeed'] = $listing['ListingFeed'];
                }
            }

            // Skip IDX / RT Registration as Form
            if ($form == 'IDX Registration' || $form == 'RT Registration') {
                continue;
            }

            // Log Event: Form Submission
            $event = new History_Event_Action_FormSubmission(array(
                'form' => $form,
                'page' => $_SERVER['HTTP_REFERER'],
                'data' => $data
            ), array(
                new History_User_Lead($contact['id'])
            ));

            // Save to DB
            $event->save();

            // Insert form into database
            $db->prepare("INSERT INTO `users_forms` SET "
                . "`user_id` = :user_id, "
                . "`form`    = :form, "
                . "`data`    = :data, "
                . "`page`    = :page, "
                . "`timestamp` = NOW()"
            . ";")->execute(array(
                'user_id' => $contact['id'],
                'form'    => $form,
                'data'    => serialize($data),
                'page'    => $_SERVER['HTTP_REFERER']
            ));

            // Increment Form Count
            $contact['num_forms']++;
        }
    }

    // Load Lead
    $lead = new Backend_Lead($contact);

    // Load Lead Agent
    $agent = (!empty($agent) ? $agent : (!empty($lead['agent']) ? $lead['agent'] : 1));
    $agent = Backend_Agent::load($agent);

    // Assign Lead to Agent
    if ($agent->getId() > 1 && !empty($newlead)) {
        if ($lead->info('agent') == 1) {
            $lead->info('agent', 0);
        }
        $lead->assign($agent);
    }

    // Assign Lead to Lender
    if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE']) && !empty($lender)) {
        $lender = Backend_Lender::load($lender);
        if (!empty($lender)) {
            $lender->assign(array($lead));
        }
    }

    // Update Lead Score
    $contact['score'] = $lead->updateScore();

    // Send Form Submission to Agent
    if (!empty($message)) {
        // Link to Summary (Backend)
        $url_backend = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'];

        // Lead's phone number
        $phone = ($lead['phone'] ?: $lead['phone_cell'] ?: '');

        // Send Form Submission
        $mailer = new Backend_Mailer_SMS(array(
            'subject' => htmlspecialchars_decode($forms . (!empty($subject) ? ' - ' . $subject : '')),
            'message' => $message . '<br><strong>Lead Summary:</strong> <a href="' . $url_backend . '">' . $url_backend . '</a>',
            'sms_message' => '*' . $forms . ' Submission*'
                . PHP_EOL . $lead->getNameOrEmail()
                . (!empty($phone) ? PHP_EOL . $phone : '')
                . PHP_EOL . $url_backend
        ));

        // Set Lead as Reply-To
        $mailer->setReplyTo($lead['email'], $lead->getName());

        // Check Incoming Notification Settings
        $check = $agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LEAD_INQUIRED);

        // Send Email
        if (!empty($check)) {
            $mailer->Send();
        }
    }

    // Require Email Verification
    if ((!empty(Settings::getInstance()->SETTINGS['registration_verify']) && !Validate::verifyWhitelisted($lead->info('email'))) || Validate::verifyRequired($lead->info('email'))) {
        // Require Email Verification
        if ($newlead && $lead->info('verified') != 'yes') {
            // Setup Verification Mailer
            $mailer = new Backend_Mailer_Verification(array(
                'lead'  => $lead
            ));

            // Send Email
            $mailer->Send();
        }
    }

    // Locate Auto-Responder
    $result = $db->prepare("SELECT * FROM `auto_responders` WHERE `active` = 'Y' AND `id` = :ar_id;");
    $result->execute(array('ar_id' => $ar_id));
    $autoresponder = $result->fetch();

    // Require Auto-Responder
    if (!empty($autoresponder)) {
        // Setup Mailer
        $mailer = new Backend_Mailer_FormAutoResponder($autoresponder);

        // Set Recipient
        $mailer->setRecipient($contact['email'], Format::trim($contact['first_name'] . ' ' . $contact['last_name']));

        // Send Email
        $mailer->Send(array(
            'id'          => $lead->getId(),
            'guid' => Format::toGuid($lead->info('guid')),
            'first_name'  => $lead->info('first_name'),
            'last_name'   => $lead->info('last_name'),
            'email'       => $lead->info('email'),
            'agent'       => $agent->getId(),
            'verify'      => Settings::getInstance()->SETTINGS['URL_IDX'] . 'verify.html?verify=' . Format::toGuid($lead->info('guid')),
        ));
    }

    if ($newlead) {
        Hooks::hook(Hooks::HOOK_LEAD_CREATED)->run($lead);
    }

    // Set $userTrackID
    $userTrackID = $contact['id'];

    // Password is not Required, Automatically Validate User Session for IDX
    if (empty(Settings::getInstance()->SETTINGS['registration_password'])) {
        $user = User_Session::get();
        if (is_object($user) && is_a($user, 'User_Session')) {
            if (!empty($userTrackID)) {
                $user->setUserId($userTrackID);
                $user->setValid(false);
                $user->validate();
            }
        }
    }

    // Stop timer
    $_timer->stop();

    // Return ID
    return $userTrackID;
}

/**
 * Collect Data from Form
 *
 * @param integer $ar_id
 * @param string $form
 * @param boolean $test_addr
 * @param boolean $test_phone
 * @return string Response Message
 */
function contactForm($ar_id, $form, $test_addr = false, $test_phone = false, $test_name = true)
{

    // Check For Potentially Malicious Submission Data
    $check_fields = array(
        'onc5khko' => $_POST['onc5khko'],
        'sk5tyelo' => $_POST['sk5tyelo'],
        'subject' => $_POST['subject'],
        'fm-addr' => $_POST['fm-addr'],
        'fm-town' => $_POST['fm-town'],
        'fm-state' => $_POST['fm-state'],
        'fm-postcode' => $_POST['fm-postcode']
    );
    list($not_allowed, $bad_fields) = Validate::formFields($check_fields);
    foreach ($bad_fields as $bad_field) {
        $errors[] = 'We are sorry.  We are unable to process your submission as ' . $bad_field . ' contains at least one of the following characters: ' . implode(', ', Format::htmlspecialchars($not_allowed));
    }

    // Clean $_POST
    $post = Format::htmlspecialchars($_POST);

    // Un-Obfiscate Honeypot Variables
    $email      = trim($post['mi0moecs']);
    $first_name = trim($post['onc5khko']);
    $last_name  = trim($post['sk5tyelo']);
    $subject    = trim($post['subject']);

    // Test Honeypot Variable
    $fake = !empty($post['registration_type']);

    // Require Input
    $email_test      = Validate::email($email, true);
    if ($test_name) {
        $first_name_test = Validate::stringRequired($first_name);
    }
    if ($test_name) {
        $last_name_test  = Validate::stringRequired($last_name);
    }

    // Require Address
    if ($test_addr) {
        $address_test = Validate::stringRequired($post['fm-addr']);
        $city_test    = Validate::stringRequired($post['fm-town']);
    }

    // Require Phone Number
    if ($test_phone) {
        // Test $post['telephone']
        $phone_test = Validate::phone($post['telephone']);
        if (empty($phone_test)) {
            // Test $post['phone_cell']
            $phone_test = Validate::phone($post['phone_cell']);
        }
    }

    // Check Requirements
    if (empty($email_test)) {
        $errors[] = 'You must supply a valid email address';
    }
    if (!empty($test_name) && empty($first_name_test)) {
        $errors[] = 'You must supply your first name.';
    }
    if (!empty($test_name) && empty($last_name_test)) {
        $errors[] = 'You must supply your last name.';
    }
    if (!empty($test_phone) && empty($phone_test)) {
        $errors[] = 'You must supply your phone number.';
    }
    if (!empty($test_addr) && empty($address_test)) {
        $errors[] = 'You must supply your address.';
    }
    if (!empty($test_addr) && empty($city_test)) {
        $errors[] = 'You must supply your city.';
    }

    // Spam Check
    require_once dirname(__FILE__) . '/../routine.spam-stop.php';
    $spam = checkForSpam($package);

    // Spam Detected
    if ($spam || !$package['is_browser'] || $fake) {
        $errors[] = 'Oops, It appears that you are being detected as SPAM!';
    }

    // Error Occurred
    if (!empty($errors)) {
        return array('errors' => $errors);

    // Success
    } else {
        // If single phone # provided - default as primary(home) #
        $phone_home = $post['phone'] ?: $post['telephone'];
        $phone_cell = $post['phone_cell'];
        if (empty($phone_home) && !empty($phone_cell)) {
            $phone_home = $phone_cell;
            $phone_cell = null;
        }

        // Get Listing
        $listing = (isset($post['mls_number']) && isset($post['listing_type']) && isset($post['listing_feed']))
            ? ['ListingMLS'  => $post['mls_number'],
            'ListingType' => $post['listing_type'],
            'ListingFeed' => $post['listing_feed']]
            : null;

        // Collect Contact Data
        collectContactData(array(
            'first_name' => isset($post['onc5khko']) ? $post['onc5khko'] : null,
            'last_name'  => isset($post['sk5tyelo']) ? $post['sk5tyelo'] : null,
            'email'      => isset($post['mi0moecs']) ? $post['mi0moecs'] : null,
            'agent'      => isset($post['agent'])  ? $post['agent']  : null,
            'address1'   => isset($post['fm-addr'])  ? $post['fm-addr']  : null,
            'city'       => isset($post['fm-town'])  ? $post['fm-town']  : null,
            'zip'        => isset($post['fm-postcode']) ? $post['fm-postcode'] : null,
            'state'      => isset($post['fm-state'])    ? $post['fm-state']    : null,
            'phone'      => $phone_home ?: null,
            'phone_cell' => $phone_cell ?: null,
            'comments'   => isset($post['comments'])    ? $post['comments']    : null,
            'contact_method'=> isset($post['contact_method']) ? $post['contact_method'] : null,
            'opt_marketing' => isset($post['opt_marketing']) ? $post['opt_marketing'] : null,
            'opt_texts'  => isset($post['opt_texts']) ? $post['opt_texts'] : null,
            'message'    => getFormVars(),
            'subject'    => $subject,
            'forms'      => $form,
            'listing'    => isset($listing) ? $listing : null,
        ), $ar_id);

        // Success
        return array('success' => true);
    }
}
