<?php

/**
 * Sends New Listing Email To Associated User Search.  Returns A Log Of The Activity
 *
 * Example Response
 * HTTP/1.1 200 OK
 * Content-Type: application/json
 *
 * {
 *     "data" : [
 *         {
 *             "type"    => "debug",
 *             "message" => "Skipping Email to david@realestatewebmasters.com.  No New Listings Found In Search 1234",
 *         }
 *     ]
 * }
 */

// Include IDX Configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

define('SAVEDSEARCH_DEBUG', false);

// Parse JSON POST
$listing_info = json_decode(file_get_contents("php://input"), true);
if (empty($listing_info) && json_last_error() !== JSON_ERROR_NONE) {
    throw new Exception_JSONParseError();
} else if (empty($listing_info)) {
    throw new UnexpectedValueException(
        "Missing listing information.  Expecting a JSON object similar to the following:" . PHP_EOL .
        json_encode(array(
            array(
                'search_ids' => array(1, 6, 90, 8705, 349494),
                'listing' => array(
                    'id' => '1234',
                    'AddressCity' => 'Orlando',
                    'ListingPrice' => '250000',
                    'ListingImage' => 'https://6fb2e921cc34e14c15f8-2af83b1a9e85a650dd64d056ed295658.ssl.cf5.rackcdn.com/s4835143-residential-1sweebn-m.jpg',
                    'ListingMLS' => 'V234950',
                    'NumberOfBedrooms' => 2,
                    'NumberOfBathrooms' => 2,
                    'ListingStatus' => 'Active',
                    'ListingRemarks' => 'Near Disney World, around 15-20 minutes drive. Charming Ranch/ 1-story home nestled behind mature oak trees providing shade by cul-de-sac street in a quiet neighborhood. Split floor plan and vaulted ceilings give roomy feel to the home. Wood floors carry through great room, dining room, and owners bedroom areas. Sliding doors and windows open to back patio and private yard. Enjoy cost efficient gas utilities. Very pretty façade.',
                    'ListingAgent' => 'Charles Simmons',
                    'ListingOffice' => 'RE/MAX Orlando'
                )
            )
        ), JSON_PRETTY_PRINT)
    );
}

// Set To Requested Feed
Settings::getInstance()->IDX_FEED = $feed;


// Fetch Instant Searches
$db = DB::get('users');

// Set Timezone
$timezone = $db->query("SELECT `t`.`TZ` FROM `agents` `a` LEFT JOIN `timezones` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`id` = 1 LIMIT 1;");
$timezone = $timezone->fetchColumn();
if (!empty($timezone)) {
    date_default_timezone_set($timezone);
    $db->query("SET `time_zone` = '" . $timezone . "';");
}


// Load IDX Settings
Util_IDX::loadSettings();


global $_COMPLIANCE;

$skin = Container::getInstance()->get(\REW\Core\Interfaces\SkinInterface::class);
$savedSearchEmailPath = $skin->getSavedSearchEmailPath();

//Add Saved Search Message To Feed Info
$settings = $db->fetch("SELECT idxs.`savedsearches_responsive`, 
                                      idxs.`savedsearches_message`,
                                      idxs.`savedsearches_responsive_params`,
                                      idxs.`force_savedsearches_responsive`
                               FROM `" . TABLE_IDX_SYSTEM . "` idxs
                               WHERE idx in('" . Settings::getInstance()->IDX_FEED . "', '') ORDER BY idx DESC LIMIT 1;");
if ($setting["force_savedsearches_responsive"] == 'true') {
    $setting["savedsearches_responsive"] = 'true';
}
if ($settings["savedsearches_responsive"] == 'true') {
    // Unserialize template settings
    $settings["params"] = unserialize($settings["savedsearches_responsive_params"]);

    // Check if responsive template exists

    $view_path = $savedSearchEmailPath . "index.php";
    $view = Container::getInstance()->get(\REW\Backend\View\Interfaces\FactoryInterface::class);

    if(!$view->exists($view_path)) {
        // Force using legacy template if there is no responsive partials for the skin
        $settings["savedsearches_responsive"] = "false";
    } else {
        switch ($settings["params"]["sender"]["from"]) {
            case "admin":
                $sender = Backend_Agent::load(1);
                $sender["name"] = $sender["first_name"] . " " . $sender["last_name"];
                break;
            case "custom":
                $sender["name"] = $settings["params"]["sender"]["name"];
                $sender["email"] = $settings["params"]["sender"]["email"];
                break;
        }
    }
}

if(empty($settings['savedsearches_message'])) {
    // Generate Email Body (HTML Only)
    ob_start();
    include Page::locateTemplate('idx', 'emails', 'saved_searches');
    $settings['savedsearches_message'] = ob_get_contents();
    ob_end_clean();
}

$savedsearches_message = $settings['savedsearches_message'];

$limit = 10;
// Get Limit from Responder params
if (!empty($settings["params"]["listings"]["num_rows"])) {
    $limit = $settings["params"]["listings"]["num_rows"] * 2;
}


// Populate Searches Array (Unbundling Packaged Payload)
$searches = array();
foreach ($listing_info as $listing) {
    // Convert Each ID To An Integer
    $listing['search_ids'] = array_map('intval', $listing['search_ids']);
    // Bind Listing's Search IDs To Searches Array
    $searches = array_merge($searches, $listing['search_ids']);
}
// Remove Duplicate Values
array_unique($searches);
// Remove Zero Valued Elements
$searches = array_filter($searches);

if (empty($searches)) {
    throw new UnexpectedValueException("No valid searches have been provided.");
}

$query = "SELECT
    `t1`.`id`,
    `t1`.`user_id`,
    `t1`.`title`,
    `t2`.`first_name`,
    `t2`.`last_name`,
    `t2`.`email`,
    `t2`.`agent`,
    `t2`.`verified`,
    `t2`.`guid`,
    `t2`.`email_alt`,
    `t2`.`email_alt_cc_searches`
FROM `users_searches` `t1` LEFT JOIN `users` `t2` ON `t1`.`user_id` = `t2`.`id` AND `t2`.`id` IS NOT NULL
WHERE `t1`.`id` IN (" . implode(',', $searches) . ")
AND `t1`.`frequency` = 'immediately'
AND `t2`.`opt_searches` = 'in'
AND `t2`.`bounced` != 'true'
AND `t2`.`fbl` != 'true'
AND `t1`.`source_app_id` IS NULL
ORDER BY `t2`.`agent` ASC, `t1`.`id` ASC";

if ($searches = $db->query($query)) {
    while ($search = $searches->fetch(PDO::FETCH_ASSOC)) {
        //Generate message uuid
        $uuid = $db->fetch("SELECT UUID() UUID;")["UUID"];

        // Check If E-Mail Host Is Blocked
        if (Validate::verifyWhitelisted($search['email'])) {
            $json[] = array(
                "type"    => 'debug',
                "message" => $search['email'] . '\'s e-mail provider is on the server block list - skipping automated e-mail'
            );
            continue;
        }

        // Check If E-Mail Host Requires Verification
        if (Validate::verifyRequired($search['email']) || !empty(Settings::getInstance()->SETTINGS['registration_verify'])) {
            if ($search['verified'] != 'yes') {
                $json[] = array(
                    "type"    => 'debug',
                    "message" => $search['email'] . '\'s e-mail provider is set to require e-mail verification on this server, but the account has not been verified yet - skipping automated e-mail'
                );
                continue;
            }
        }

        // Only Load Agent If Needed.
        if (empty($agent) || $agent->getId() != $search['agent']) {
            $agent = Backend_Agent::load($search['agent']);
        }

        // Agent CMS
        if ($agent['cms'] == 'true' && !empty($agent['cms_link'])) {
            $url = Settings::getInstance()->SETTINGS['URL'];
            $url_agent = sprintf(Settings::getInstance()->SETTINGS['URL_AGENT_SITE'], $agent['cms_link']);
            $URL = $url_agent;
            $URL_IDX_SAVED_SEARCH = str_replace($url, $url_agent, Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH']);
        } else {
            $URL_IDX_SAVED_SEARCH = Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'];
            $URL = Settings::getInstance()->SETTINGS['URL'];
        }

        $results = array();

        // Format Listing Information For Display
        foreach ($listing_info as $listing) {
            // If The Listing's Info Does Not Contain The Search's ID, Continue To The Next Listing
            if (!in_array($search['id'], $listing['search_ids'])) {
                continue;
            }

            // Parse Listing
            $listing = Util_IDX::parseListing($idx, $db_idx, $listing['listing']);

            // Replace Original URL With Agent Site URL If Assigned Agent Has Subdomain
            if ($agent['cms'] == 'true' && !empty($agent['cms_link'])) {
                $listing['url_details'] = str_replace($url, $url_agent, $listing['url_details']);
            }

            // Append UID To Link
            $listing['url_details'] .= '?uid=' . Format::toGuid($search['guid']);

            // Add To Collection
            $results[] = $listing;
        }


        // No Listings, Skip To The Next Search
        if (empty($results)) {
            $json[] = array(
                "type"    => "debug",
                "message" => "Skipping Email to " . $search['email'] . ".  No New Listings Found In Search " . $search['id']
            );
            continue;
        }


        // Setup Mailer
        $mailer = new Backend_Mailer(array(
            'subject' => date('M j') . ' - Your search ' . $search['title'] . '  has some new listings!'
        ));

        // Use the agent as the sender
        if (!in_array($settings["params"]["sender"]["from"], ['admin', 'custom'])) {
            $sender = $agent;
            $sender["name"] = $sender["first_name"] . " " . $sender["last_name"];
        }

        // Set Sender
        $mailer->setSender($sender['email'], $sender['name']);

        // Set Recipient
        $mailer->setRecipient($search['email'], $search['first_name'] . ' ' . $search['last_name']);

        // CC Alternate Email if Opted-in
        if (!empty($search['email_alt_cc_searches']) && $search['email_alt_cc_searches'] === 'true') {
            $mailer->addCC($search['email_alt']);
        }
        
        // Update agent on changes in leads' saved searches
        $mailer = $agent->checkOutgoingNotifications($mailer, Backend_Agent_Notifications::OUTGOING_SEARCH_UPDATES);

        // Legacy Saved Searches e-mail
        if($settings["savedsearches_responsive"] === "false") {
        // Generate Results
            ob_start();
            foreach ($results as $result) {
                ?>
<!-- MLS Listing -->
<table align="center" width="625" cellpadding="5" cellspacing="0" style="border: 1px solid #ccc; background-color: #fff;">
<tr>
    <td width="200">
        <a href="<?=$result['url_details']; ?>"><img src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="" width="200" height="150" style="width: 200px; height: 150px" border="0" /></a>
    </td>
    <td valign="top">
        <div style="padding: 20px; font-size: 14px;">
            <?php if (!empty($_COMPLIANCE['results']['show_icon'])) {
                echo '<span style="float: right;">' . $_COMPLIANCE['results']['show_icon'] . '</span>';
            } ?>
            <i style="color: #777; font-size: 12px;"><?=Lang::write('MLS_NUMBER'); ?><?=$result['ListingMLS']; ?></i><br />
            <b>$<?=Format::number($result['ListingPrice']); ?></b><br />
            <?=$result['NumberOfBedrooms']; ?> Bedrooms, <?=$result['NumberOfBathrooms']; ?> Bathrooms<br />
            <?=ucwords(strtolower($result['AddressCity'])); ?>, <?=ucwords(strtolower($result['AddressState'])); ?>
            <?=!empty($_COMPLIANCE['results']['show_status']) ? '<br />Status: ' . $listing['ListingStatus'] : ''; ?>
            <p style="font-size: 12px;"><?=substr(ucwords(strtolower($result['ListingRemarks'])), 0, 100); ?>...</p>
            <div style="background: #ddd; padding: 3px 8px; margin-top: 15px;">
                <a href="<?=$result['url_details']; ?>" style="color: #333; font-family: georgia; font-style: italic; display: block;">Read More &raquo;</a>
            </div>
            <?php if (!empty($_COMPLIANCE['results']['lang']['provider_bold'])) { ?>
                <?php if (!empty($_COMPLIANCE['results']['show_agent']) || !empty($_COMPLIANCE['results']['show_office'])) { ?>
                    <p style="font-size: 12px; font-weight: bold;"><?=!empty($_COMPLIANCE['results']['lang']['provider']) ? $_COMPLIANCE['results']['lang']['provider'] : 'Provided by:';?> <?=!empty($_COMPLIANCE['results']['show_agent']) && !empty($result['ListingAgent']) ? $result['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['results']['show_office']) && !empty($result['ListingOffice']) ? (!empty($_COMPLIANCE['results']['show_agent']) ? ', ' : '') . $result['ListingOffice'] : ''; ?></p>
                <?php } ?>
            <?php } else { ?>
                <?php if (!empty($_COMPLIANCE['results']['show_agent']) || !empty($_COMPLIANCE['results']['show_office'])) { ?>
                    <p style="font-size: 10px;"><?=!empty($_COMPLIANCE['results']['lang']['provider']) ? $_COMPLIANCE['results']['lang']['provider'] : 'Listing courtesy of';?> <?=!empty($_COMPLIANCE['results']['show_agent']) && !empty($result['ListingAgent']) ? $result['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['results']['show_office']) && !empty($result['ListingOffice']) ? (!empty($_COMPLIANCE['results']['show_agent']) && !empty($result['ListingAgent']) ? ', ' : '') . $result['ListingOffice'] : ''; ?></p>
                <?php } ?>
            <?php } ?>
            </div>
        </td>
    </tr>
</table>
                    <?php
            }
            \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true, $idx->getLink());
            $results = ob_get_clean();

            // Email Tags
            $tags = array(
                'first_name'    => $search['first_name'],
                'last_name'     => $search['last_name'],
                'email'         => $search['email'],
                'signature'     => $agent['signature'],
                'search_url'    => sprintf($URL_IDX_SAVED_SEARCH, $search['id']) . '?uid=' . Format::toGuid($search['guid']),
                'search_title'  => $search['title'],
                'results'       => $results,
                'unsubscribe'   => $URL . 'unsubscribe/' . Format::toGuid($search['guid']) . '/',
                'url'           => $URL . '?uid=' . sha1(strtoupper($search['email'])),
                'domain'        => $URL,
            );
            if ($settings["savedsearches_responsive"] === "true" ) {
                $tags['result_count'] = count($results);
            }
        // Using Responsive Template
        } else {
            $count = count($results);

            // Agent Name
            $agent['name'] = $agent['first_name'] . ' ' . $agent['last_name'];

            // Agent Link
            if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) && $agent['display'] == 'Y') {
                $agent['link'] = $url . 'agents/' . Format::slugify($agent['name']) . '/';
            }

            array_walk($results, function($i) {
                $i['ListingImage'] =  Format::thumbUrl($i['ListingImage'], '540x400');
            });

            //Get Mailling address
            if (!empty($settings["params"]["mailing_address"]["from"])) {
                switch ($settings["params"]["mailing_address"]["from"]) {
                    case "agent":
                        $office_id = $agent["office"];
                        break;
                    case "admin":
                        $result = $db->fetch("SELECT office
                                                  FROM `agents`
                                                  WHERE id = :id;", ["id" => 1]);
                        $office_id = $result["office"];
                        break;
                    default:
                        $office_id = null;
                }

                // Use default office
                if (empty($office_id)) {
                    $office_id = $settings["params"]["mailing_address"]["office_id"];
                }

                if (!empty($office_id)) {
                    $office = $db->fetch("SELECT title, address, city, state, zip
                                              FROM `featured_offices`
                                              WHERE id = :id;", ["id" => $office_id]);
                }
            }

            //Get Social media links
            $social_media = [];
            if(!empty($settings["params"]["social_media"]["from"])) {
                $social_media_agent = Backend_Agent::load($settings["params"]["social_media"]["from"] == "agent" ? $agent["id"] : 1);
                $social_media = $social_media_agent->getSocialNetworks();
            }

            // Add search result count and search url
            $search["count"] = $count;
            $search["url"] = sprintf($URL_IDX_SAVED_SEARCH, $search['id']) . '?uid=' . Format::toGuid($search['guid']);

            // Get User Info
            $user = [
                'first_name'	=> $search['first_name'],
                'last_name'		=> $search['last_name'],
                'email'			=> $search['email']
            ];

            // Gather site info
            $site = [
                "url"  => $URL,
                "name" => $_SERVER['HTTP_HOST']
            ];

            $view = Container::getInstance()->get(\REW\Backend\View\Interfaces\FactoryInterface::class);

            $savedsearches_message = $view->render($savedSearchEmailPath . "index.php", [
                "search" => $search,
                "permalink" => $URL . "email/" . $uuid,
                "site" => $site,
                "user" => $user,
                "listings" => array_slice($results, 0, $limit),
                "agent" => $agent,
                "social_media" => $social_media,
                "office" => $office,
                "unsubscribe" => $URL . 'unsubscribe.php',
                "sub_preferences" => $URL . 'idx/dashboard.html?view=preferences',
                "message" => $settings["params"]['message']['body'],
                "params" => $settings["params"]

            ]);
            $tags = ['results' => ''];

            //Replace tags from Subject
            $sub_tags = [
                'date'         => date('M j'),
                'search_title' => $search['title'],
                'first_name'   => $search['first_name'],
                'last_name'    => $search['last_name'],
                'site_name'    => $_SERVER['HTTP_HOST'],

            ];
            $mailer->setSubject($mailer->replaceTags($settings["params"]['message']['subject'], $sub_tags));
        }

        // Set Instant Search Base Email Message
        $mailer->setMessage($savedsearches_message);

        // Output
        $json[] = array(
            "type"    => 'debug',
            "message" => 'Sending Email to ' . $search['email'] . ' for search ' . $search['id']
        );

        // Send Email
        if (SAVEDSEARCH_DEBUG || $mailer->Send($tags)) {
            $json[] = array(
                "type"    => 'debug',
                "message" => 'Successfully sent email to ' . $search['email'] . ' for search ' . $search['id']
            );

            // Log Event: Listings Update sent to Lead
            $event = new History_Event_Email_Listings(array(
                'subject'   => $mailer->getSubject(),
                'message'   => $mailer->getMessage(),
                'tags'      => $mailer->getTags()
            ), array(
                new History_User_Lead($search['user_id'])
            ));

            // Save to DB
            $event_id = $event->save();

            //Tie email uuid to event
            $db->prepare("INSERT INTO  `users_emails` SET `guid` = GuidToBinary(:uuid), `event_id` = :event_id;")->execute(["uuid" => $uuid, "event_id" => $event_id]);

            // Update Saved Search Sent Timestamp
            $db->query("UPDATE `" . TABLE_SAVED_SEARCHES . "` SET `sent` = `sent` + 1, `timestamp_sent` = NOW() WHERE `id` = '" . $search['id'] . "';");
        } else {
            // Mailer Error
            $json[] = array(
                "type"    => 'error',
                "message" => 'Unable to send email to ' . $search['email'] . ' for search ' . $search['id']
            );
        }
    }
}
