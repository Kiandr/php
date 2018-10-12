<?php

// Don't Execute Through Apache
if (isset($_SERVER['HTTP_HOST'])) {
    // Not Authorized
    die('Not Authorized');
}

$_SERVER['DOCUMENT_ROOT'] = $argv[1];
$_SERVER['HTTP_HOST'] = $argv[2];
$_SERVER['REQUEST_SCHEME'] = ($argv[3] === 'https' ? $argv[3] : 'http');

define('SAVEDSEARCH_DEBUG', ($argv[4] === 'debug'));


// Include Config
$_GET['page'] = 'cron';
require_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';
@session_destroy();

$worker= new GearmanWorker();
// Worker Dies After A Minute Of Inactivity.
$worker->setTimeout(60000);
$worker->addServer();

// Logs Messages For Saved Search Script
$logger = Log_Message::getInstance(DB::get(), 'saved_search_logger');

// App DB
$db = DB::get();


$worker->addFunction("process_saved_search", function (GearmanJob $job) use ($logger, $db) {

    try {
        // Get Params
        try {
            $params = unserialize($job->workload());

            $search = $params['search'];

            $logger->setGroupID($search['id']);

            $agent = new Backend_Agent($params['agent']);

            $settings = $params["settings"];

            $saved_search_message = $settings['savedsearches_message'];

            $last_updated = $settings['last_updated'];

            $uuid = $params['uuid'];



            if ($settings["savedsearches_responsive"] === "true") {

                // Check if responsive template exists
                $indexTemplate = $params['indexTemplate'];

                $view = Container::getInstance()->get(\REW\Backend\View\Interfaces\FactoryInterface::class);

                if(!$view->exists($indexTemplate)) {
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

            // Use the agent as the sender
            if (!in_array($settings["params"]["sender"]["from"], ['admin', 'custom'])) {
                $sender = $agent;
                $sender["name"] = $sender["first_name"] . " " . $sender["last_name"];
            }
        } catch (Exception $e) {
            throw new Exception("Unable to get saved search params. Here's why: " . PHP_EOL . $e->getMessage());
        }

        try {
            // IDX Objects
            $idx = Util_IDX::getIdx($search['idx']);
            $db_idx = Util_IDX::getDatabase($search['idx']);

            // Error Occurred
        } catch (Exception $e) {
            throw new Exception('Failed to load IDX ' . $search['idx'] . '! Skipping to next search.');
        }

        // Determine Timestamp Offset
        switch ($search['frequency']) {
            case 'daily':
                $diff = '1 day';
                break;
            case 'monthly':
                $diff = '1 month';
                break;
            case 'weekly':
            default:
                $diff = '1 week';
                break;
        }

        // Set Base Timestamp For Saved Searches That Have Not Yet Sent Out An Update
        if (empty($search['timestamp_idx'])) {
            $search['timestamp_idx'] = strtotime('-' . $diff, $last_updated);
        }

        // Search Specify Date Range
        $search_date = array();
        $search_date[] = "`t1`.`timestamp_created` > '" . date('Y-m-d H:i:s', $search['timestamp_idx']) . "'";
        $search_date[] = "`t1`.`timestamp_created` <= '" . date('Y-m-d H:i:s', $last_updated) . "'";
        $search_date = '(' . implode(' AND ', $search_date) . ')';

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

        // $_REQUEST Array
        $_REQUEST = array();

        // Search Criteria
        if (!empty($search['criteria'])) {
            // Searchable Fields
            $search_fields = search_fields($idx);
            $search_fields = array_keys($search_fields);

            // Search Criteria
            $criteria = unserialize($search['criteria']);
            if (!empty($criteria) && is_array($criteria)) {
                // Set $_REQUEST
                foreach ($criteria as $key => $val) {
                    if (in_array($key, array_merge(array('map', 'view', 'search_location'), $search_fields))) {
                        if (!isset($_REQUEST[$key])) {
                            $_REQUEST[$key] = $val;
                        }
                    }
                }
            }

            // Snippet, Over-Ride Feed Defaults
            $_REQUEST['search_city'] = isset($_REQUEST['search_city']) ? $_REQUEST['search_city'] : false;
            $_REQUEST['search_type'] = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : false;
        }

        //Latitude / Longitude Columns
        $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
        $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

        // Build Query
        $search_vars        = $idx->buildWhere($idx, $db_idx, 't1');
        $search_where       = $search_vars['search_where'];
        $search_title       = $search_vars['search_title'];
        $search_criteria    = $search_vars['search_criteria'];

        // Query Collection
        $search_where = !empty($search_where) ? array($search_where) : array();

        /**
         * Map Queries
        */

        // HAVING Queries
        $search_having = array();

        // Search Group
        $search_group = array();

        // Latitude / Longitude Columns
        $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
        $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

        // Search In Bounds
        if (!empty($_REQUEST['map']['bounds']) && Settings::getInstance()->IDX_FEED != 'cms') {
            $bounds = $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'], $search_group, $col_latitude, $col_longitude);
        }

        // Search In Radiuses
        $radiuses = $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);

        // Search In Polygons
        $polygons = $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
        if (!empty($polygons)) {
            $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
        }

        // Add To Search Criteria
        if (!empty($search_group)) {
            $search_where[] = '(' . implode(' OR ', $search_group) . ')';
        }

        // Query String (WHERE & HAVING)
        $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

        $select_columns = array (
            $idx->field('Address'),
            $idx->field('AddressCity'),
            $idx->field('ListingPrice'),
            $idx->field('ListingImage'),
            $idx->field('ListingMLS'),
            $idx->field('NumberOfBedrooms'),
            $idx->field('NumberOfBathrooms'),
            $idx->field('ListingStatus'),
            $idx->field('ListingRemarks'),
            $idx->field('ListingAgent'),
            $idx->field('ListingOffice'),
            $idx->field('NumberOfSqFt'),
        );

        // New Listings Query
        $query = "SELECT " . $idx->selectColumns("`t1`.", $select_columns);
        $geo_join = '';
        if (!empty($polygons)) {
            $query .= ", `t2`.`Point`";
            $geo_join = " JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`." . $idx->field('ListingMLS') . "=`t2`.`ListingMLS`"
                . " AND `t1`.`" . $idx->field('ListingType') . "`=`t2`.`ListingType`";
        }
        $query .= " FROM `" . $idx->getTable() . "` `t1`" . $geo_join
            . " WHERE " . $search_date . (!empty($search_where) ? ' AND ' . $search_where : '')
            . " ORDER BY `t1`.`timestamp_created` DESC";

        //Query count results
        $count = 0;
        if($count_query = $db_idx->query("SELECT COUNT(*) as count from ($query) ttl;")) {
            $count_query = $db_idx->fetchArray($count_query);
            if(!empty($count_query["count"])) {
                $count = $count_query["count"];
            }
        }

        $limit = 10;
        // Get Limit from Responder params
        if (!empty($settings["params"]["listings"]["num_rows"])) {
            $limit = $settings["params"]["listings"]["num_rows"] * 2;
        }
        if (!empty($settings["params"]["listings"]["hero"])) {
            $limit += 1;
        }

        // Execute Query
        if ($listings = $db_idx->query($query." LIMIT $limit")) {
            global $_COMPLIANCE;


            // Select Most Recent New Listings Matching Search
            while ($listing = $db_idx->fetchArray($listings)) {
                // Parse Listing
                $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                // Replace Original URL With Agent Site URL If Assigned Agent Has Subdomain
                if ($agent['cms'] == 'true' && !empty($agent['cms_link'])) {
                    $listing['url_details'] = str_replace($url, $url_agent, $listing['url_details']);
                }

                // Append UID to Link
                $listing['url_details'] .= '?uid=' . Format::toGuid($search['guid']);

                // Add To Collection
                $results[] = $listing;
            }

            // No Listings, End The Job
            if (empty($results)) {
                $logger->log("Skipping Email to " . $search['email'] . ".  No New Listings Found In Search " . $search['id']);
                return;
            }

            // Setup Mailer
            $mailer = new Backend_Mailer(array(
                'subject' => date('M j') . ' - Your search ' . $search['title'] . '  has some new listings!'
            ));

            // Set Sender
            $mailer->setSender($sender['email'], $sender['name']);

            // Set Recipient
            $mailer->setRecipient($search['email'], $search['first_name'] . ' ' . $search['last_name']);

            // CC Alternate Email if Opted-in
            if (!empty($search['email_alt_cc_searches']) && $search['email_alt_cc_searches'] === 'true') {
                $mailer->addCC($search['email_alt']);
            }

            $mailer = $agent->checkOutgoingNotifications($mailer, Backend_Agent_Notifications::OUTGOING_SEARCH_UPDATES);

            // Legacy Saved Searches e-mail
            if($settings["savedsearches_responsive"] === "false") {
                // Generate Results
                if (!empty($results) && is_array($results)) {
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
                }

                // Email Tags
                $tags = array(
                    'first_name'	=> $search['first_name'],
                    'last_name'		=> $search['last_name'],
                    'email'			=> $search['email'],
                    'signature'		=> $agent['signature'],
                    'search_url'	=> sprintf($URL_IDX_SAVED_SEARCH, $search['id']) . '?uid=' . Format::toGuid($search['guid']),
                    'search_title'	=> $search['title'],
                    'results'		=> $results,
                    'unsubscribe'	=> $URL . 'unsubscribe/' . Format::toGuid($search['guid']) . '/',
                    'url'			=> $URL . '?uid=' . sha1(strtoupper($search['email'])),
                    'domain'		=> $URL
                );
                if ($settings["savedsearches_responsive"] === "true" ) {
                    $tags['result_count'] = $count;
                    $tags['result_count'] = $count;
                }
            // Using Responsive Template
            } else {

                // Agent Name
                $agent['name'] = $agent['first_name'] . ' ' . $agent['last_name'];

                // Agent Link
                if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) && $agent['display'] == 'Y') {
                    $agent['link'] = Settings::getInstance()->SETTINGS['URL'] . 'agents/' . Format::slugify($agent['name']) . '/';
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

                $saved_search_message = $view->render($indexTemplate, [
                    "search" => $search,
                    "permalink" => $URL . "email/" . $uuid,
                    "site" => $site,
                    "user" => $user,
                    "listings" => $results,
                    "agent" => $agent,
                    "social_media" => $social_media,
                    "office" => $office,
                    "unsubscribe" => $URL . 'unsubscribe.php',
                    "sub_preferences" => $URL . 'idx/dashboard.html?view=preferences&uid=' . sha1(strtoupper($search['email'])),
                    "message" => $settings["params"]['message']['body'],
                    "params" => $settings["params"]

                ]);
                $tags = ['results' => ''];
            }

            if ($settings["savedsearches_responsive"] === "true") {
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

            // Set Message
            $mailer->setMessage($saved_search_message);

            // Output
            $logger->log('Sending Email [' . $uuid . '] To ' . $search['email'] . ' For Search ' . $search['id']);

            // Send Email
            if (SAVEDSEARCH_DEBUG || $mailer->Send($tags)) {
                $logger->log('Successfully Sent Email To ' . $search['email'] . ' For Search ' . $search['id']);

                // Log Event: Listings Update Sent To Lead
                $event = new History_Event_Email_Listings(array(
                    'subject'   => $mailer->getSubject(),
                    'message'   => $mailer->getMessage(),
                    'tags'      => $mailer->getTags()
                ), array(
                    new History_User_Lead($search['user_id'])
                ));

                // Save To DB
                $event_id = $event->save();

                $db_users = DB::get();

                //Tie email uuid to event
                $db_users->prepare("INSERT INTO  `users_emails` SET `guid` = GuidToBinary(:uuid), `event_id` = :event_id;")->execute(["uuid" => $uuid, "event_id" => $event_id]);

                // Update Saved Search Sent Timestamp
                $db_users->query("UPDATE `" . TABLE_SAVED_SEARCHES . "` SET `sent` = `sent` + 1, `timestamp_sent` = NOW(), `timestamp_idx` = '" . date('Y-m-d H:i:s', $last_updated) . "' WHERE `id` = '" . $search['id'] . "';");
            } else {
                // Mailer Error
                throw new Exception('Failed To Send Email To ' . $search['email'] . ' For Search ' . $search['id']);
            }
        }
    } catch (GearmanException $e) {
        $logger->error($e->getMessage());
    } catch (Exception $e) {
        $logger->error($e->getMessage());
    }
});

while ($worker->work()) {
}
