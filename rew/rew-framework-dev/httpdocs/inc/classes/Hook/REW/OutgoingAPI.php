<?php

/**
 * Hook_REW_OutgoingAPI
 * Forwards hook events to configured destinations
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI extends Hook
{

    const DESTINATION_TYPE_REW = 1;
    const DESTINATION_TYPE_CUSTOM = 2;

    /**
     * Cached outgoing API configuration
     * @var array
     */
    private static $_outgoing_config;

    /**
     * Get the supported event types for a given destination type, as they should be displayed in a UI
     * @param integer $destination_type
     * @return array
     */
    public static function getSupportedEventsForDestination($destination_type)
    {
        if ($destination_type == self::DESTINATION_TYPE_REW) {
            return array(
                array('value' => Hooks::HOOK_LEAD_FORM_SUBMISSION,  'title' => 'Form Submissions & IDX Registrations'),
                array('value' => Hooks::HOOK_LEAD_SEARCH_SAVED,     'title' => 'Saved Searches Created'),
                array('value' => Hooks::HOOK_LEAD_SEARCH_REMOVED,   'title' => 'Saved Searches Deleted'),
                array('value' => Hooks::HOOK_LEAD_LISTING_SAVED,    'title' => 'Listing ' . Locale::spell('Favorites') . ' Created'),
                array('value' => Hooks::HOOK_LEAD_LISTING_REMOVED,  'title' => 'Listing ' . Locale::spell('Favorites') . ' Deleted'),
                array('value' => Hooks::HOOK_LEAD_VISIT,            'title' => 'Lead Visits'),
            );
        } else if ($destination_type == self::DESTINATION_TYPE_CUSTOM) {
            return array(
                array('value' => Hooks::HOOK_LEAD_FORM_SUBMISSION,  'title' => 'Form Submissions & IDX Registrations',                  'placeholder' => '/form.php'),
                array('value' => Hooks::HOOK_LEAD_SEARCH_SAVED,     'title' => 'Saved Searches Created',                                'placeholder' => '/search_saved.php'),
                array('value' => Hooks::HOOK_LEAD_SEARCH_REMOVED,   'title' => 'Saved Searches Deleted',                                'placeholder' => '/search_deleted.php'),
                array('value' => Hooks::HOOK_LEAD_SEARCH_PERFORMED, 'title' => 'IDX Searches Performed',                                'placeholder' => '/search_performed.php'),
                array('value' => Hooks::HOOK_LEAD_LISTING_SAVED,    'title' => 'Listing ' . Locale::spell('Favorites') . ' Created',    'placeholder' => '/favorite_saved.php'),
                array('value' => Hooks::HOOK_LEAD_LISTING_REMOVED,  'title' => 'Listing ' . Locale::spell('Favorites') . ' Deleted',    'placeholder' => '/favorite_deleted.php'),
                array('value' => Hooks::HOOK_LEAD_LISTING_VIEWED,   'title' => 'Listing Views',                                         'placeholder' => '/listing_viewed.php'),
                array('value' => Hooks::HOOK_LEAD_VISIT,            'title' => 'Lead Visits',                                           'placeholder' => '/visit.php'),
            );
        }

        return array();
    }

    /**
     * Get the configured outgoing API destinations
     * @return array|NULL
     */
    protected function getDestinations()
    {

        // Require outgoing config
        if (!($outgoing = $this->getOutgoingConfiguration())) {
            return null;
        }
        return is_array($outgoing['destinations']) ? $outgoing['destinations'] : array();
    }

    /**
     * Get the current system configuration for the outgoing API
     * @return array|NULL
     */
    protected function getOutgoingConfiguration()
    {

        // Fetch
        if (is_null(self::$_outgoing_config)) {
            $db = DB::get('users');

            // Fetch Super Admin
            $admin_agent = $db->{'agents'}->getRow(1);

            // Require agent
            if (empty($admin_agent)) {
                self::$_outgoing_config = false;
                return false;
            }

            // Require partner data
            if (empty($admin_agent['partners'])) {
                self::$_outgoing_config = false;
                return false;
            }

            // Parse JSON
            $partners = json_decode($admin_agent['partners'], true);
            if (empty($partners)) {
                self::$_outgoing_config = false;
                return false;
            }

            // Require Outgoing API data
            if (!($outgoing_config = $partners['outgoing_api'])) {
                self::$_outgoing_config = false;
                return false;
            }

            // Cache config
            self::$_outgoing_config = $outgoing_config;
        }

        return self::$_outgoing_config;
    }

    /**
     * Handle forwarding hook event to configured destinations
     * @param string $hook_type The hook name
     * @param array $params The hook's invoke paramters
     */
    protected function sendOutgoingEvent($hook_type, $params)
    {

        // Require destinations
        if (!($destinations = $this->getDestinations())) {
            return;
        }

        // Check destinations
        foreach ($destinations as $destination) {
            // Destination supported?
            if (!$this->destinationTypeSupported($destination['type'])) {
                continue;
            }
            if (empty($destination['events'])) {
                continue;
            }
            if ($destination['enabled'] !== 'Y') {
                continue;
            }

            // REW domain destination
            if ($destination['type'] === self::DESTINATION_TYPE_REW) {
                // Current hook is on the event list?
                if (!in_array($hook_type, $destination['events'])) {
                    continue;
                }

                // Require URL & API Key
                if (empty($destination['url']) || empty($destination['api_key'])) {
                    continue;
                }

                // Parse URL
                if (strpos($destination['url'], 'http') !== 0) {
                    $destination['url'] = 'http://' . $destination['url'];
                }
                $host = parse_url($destination['url'], PHP_URL_HOST);
                if (empty($host)) {
                    continue;
                }
                $endpoint = 'http://' . $host . '/api/crm/v1';

                // Create partner instance
                $api = new Partner_REW(array(
                    'api_key'           => $destination['api_key'],
                    'url_api_endpoint'  => $endpoint,
                ));

                // Hook type
                switch ($hook_type) {
                    case Hooks::HOOK_LEAD_FORM_SUBMISSION:
                        // Lead data
                        $lead = $params['lead'];

                        // API Request - Create or update lead record
                        $api->createOrUpdateLead($lead['email'], $lead['first_name'], $lead['last_name'], array(
                            'password'          => $lead['password'],
                            'address'           => $lead['address1'],
                            'city'              => $lead['city'],
                            'state'             => $lead['state'],
                            'zip'               => $lead['zip'],
                            'phone'             => $lead['phone'],
                            'comments'          => $lead['comments'],
                            'origin'            => $lead['referer'],
                            'opt_marketing'     => $lead['opt_marketing'],
                            'opt_searches'      => $lead['opt_searches'],
                            'auto_rotate'       => 'true',
                            'source_user_id'    => $lead['id'],
                        ));

                        // Form name
                        $form_name = $params['form_name'];

                        // Skip registration forms
                        if ($form_name == 'IDX Registration') {
                            break;
                        }

                        // Forward form submission
                        $api->createHistoryEvent($lead['email'], 'Action', 'FormSubmission', array(
                            'page' => $_SERVER['HTTP_REFERER'],
                            'form' => $form_name,
                            'data' => $params['post'],
                        ));

                        break;
                    case Hooks::HOOK_LEAD_LISTING_SAVED:
                        // Lead data
                        $lead = $params['lead'];
                        $idx = $params['idx'];
                        $listing = $params['listing'];

                        // API Request - Favorite listing
                        $api->createFavorite($lead['email'], $listing['ListingMLS'], $listing['ListingType'], $idx->getName(), $idx->getTable());

                        break;
                    case Hooks::HOOK_LEAD_LISTING_REMOVED:
                        // Lead data
                        $lead = $params['lead'];
                        $row = $params['row'];

                        // Find equivalent remote record
                        if ($favorites = $api->getFavorites($lead['email'], null, $row['mls_number'], $row['type'])) {
                            foreach ($favorites as $remote_favorite) {
                                $api->deleteFavorite($lead['email'], $remote_favorite['id']);
                            }
                        }

                        break;
                    case Hooks::HOOK_LEAD_LISTING_VIEWED:
                        break;
                    case Hooks::HOOK_LEAD_SEARCH_PERFORMED:
                        break;
                    case Hooks::HOOK_LEAD_SEARCH_SAVED:
                        // Lead data
                        $lead = $params['lead'];
                        $idx = $params['idx'];
                        $criteria = $params['criteria'];
                        $title = $params['title'];
                        $frequency = $params['frequency'];

                        // API Request - Save search
                        $api->createSavedSearch($lead['email'], $title, $criteria, $idx->getName(), $idx->getTable(), $frequency, true);

                        break;
                    case Hooks::HOOK_LEAD_SEARCH_REMOVED:
                        // Lead data
                        $lead = $params['lead'];
                        $row = $params['row'];
                        $criteria = @unserialize($row['criteria']);

                        // Find equivalent remote record
                        if ($searches = $api->getSavedSearches($lead['email'], null, $row['title'], $criteria)) {
                            foreach ($searches as $remote_search) {
                                $api->deleteSavedSearch($lead['email'], $remote_search['id']);
                            }
                        }

                        break;
                    case Hooks::HOOK_LEAD_VISIT:
                        // Lead data
                        $lead_id = $params['lead_id'];
                        $referer = $params['referer'];
                        $keywords = $params['keywords'];
                        $lead = Backend_Lead::load($lead_id);

                        // API Request - get remote record
                        if ($remote_lead = $api->getLead($lead['email'])) {
                            // Update data
                            $update_data = array(
                                'num_visits' => $remote_lead['num_visits'] + 1,
                            );

                            // Append extra data
                            if (!empty($referer)) {
                                $update_data['origin'] = $referer;
                            }
                            if (!empty($keywords)) {
                                $update_data['keywords'] = $keywords;
                            }

                            // API Request - update remote record
                            $api->updateLead($remote_lead['email'], $update_data);
                        }

                        break;
                }
            } else if ($destination['type'] === self::DESTINATION_TYPE_CUSTOM) { // Custom Destination

                // Require URL
                if (empty($destination['url'])) {
                    continue;
                }

                // Parse URL
                if (strpos($destination['url'], 'http') !== 0) {
                    $destination['url'] = 'http://' . $destination['url'];
                }
                $host = parse_url($destination['url'], PHP_URL_HOST);
                if (empty($host)) {
                    continue;
                }
                $endpoint = 'http://' . $host;

                // Current hook is on the event list & is enabled?
                if (!array_key_exists($hook_type, $destination['events'])) {
                    continue;
                }
                if ($destination['events'][$hook_type]['enabled'] !== 'Y') {
                    continue;
                }

                // Event data
                $event = $destination['events'][$hook_type];

                // User agent
                $user_agent = 'rewCRM/' . Settings::getInstance()->APP_VERSION . (!empty(Settings::getInstance()->APP_BUILD) ? '-' . Settings::getInstance()->APP_BUILD : '');

                // API Authentication
                $curl_options = array(
                    CURLOPT_USERAGENT => $user_agent,
                );

                // Set Base URL
                Util_Curl::setBaseURL($destination['url']);

                // Lead data
                $db = DB::get('users');
                $lead = $params['lead'];
                $lead = !empty($lead) ? $lead : array();
                $lead_data = new API_Object_Lead($db, $lead);

                // Hook type
                switch ($hook_type) {
                    case Hooks::HOOK_LEAD_FORM_SUBMISSION:
                        // Form submission event
                        $history_event = new History_Event_Action_FormSubmission(array(
                            'form' => $params['form_name'],
                            'page' => $_SERVER['HTTP_REFERER'],
                            'data' => $params['post'],
                        ), array(
                            new History_User_Lead($lead['id'])
                        ));

                        $event_data = new API_Object_Event($db, $history_event);

                        // Execute request
                        Util_Curl::executeRequest($event['url'], array(
                            'lead'  => $lead_data->getData(),
                            'event' => $event_data->getData(),
                        ), Util_Curl::REQUEST_TYPE_POST, $curl_options);

                        break;
                    case Hooks::HOOK_LEAD_LISTING_SAVED:
                        // Hook parameters
                        $listing = $params['listing'];

                        // Get the saved row in the DB
                        $where = array(
                            '$eq' => array(
                                'user_id'       => $lead['id'],
                                'idx'           => $params['idx']->getName(),
                                'mls_number'    => $listing['ListingMLS'],
                                'type'          => $listing['ListingType'],
                            )
                        );

                        // Execute request
                        if ($existing = $db->{'users_listings'}->search($where)->fetch()) {
                            $favorite_data = new API_Object_Lead_Favorite($db, $existing);
                            Util_Curl::executeRequest($event['url'], array(
                                'lead'      => $lead_data->getData(),
                                'favorite'  => $favorite_data->getData(),
                            ), Util_Curl::REQUEST_TYPE_POST, $curl_options);
                        }

                        break;
                    case Hooks::HOOK_LEAD_LISTING_REMOVED:
                        // Hook parameters
                        $row = $params['row'];
                        $favorite_data = new API_Object_Lead_Favorite($db, $row);

                        // Execute request
                        Util_Curl::executeRequest($event['url'], array(
                            'lead'      => $lead_data->getData(),
                            'favorite'  => $favorite_data->getData(),
                        ), Util_Curl::REQUEST_TYPE_POST, $curl_options);

                        break;
                    case Hooks::HOOK_LEAD_LISTING_VIEWED:
                        // Hook parameters
                        $listing = $params['listing'];

                        // Get the saved row in the DB
                        $where = array(
                            '$eq' => array(
                                'user_id'       => $lead['id'],
                                'idx'           => $params['idx']->getName(),
                                'mls_number'    => $listing['ListingMLS'],
                                'type'          => $listing['ListingType'],
                            )
                        );

                        // Execute request
                        if ($existing = $db->{'users_viewed_listings'}->search($where)->fetch()) {
                            $viewed_data = new API_Object_Lead_Favorite($db, $existing);
                            Util_Curl::executeRequest($event['url'], array(
                                'lead'      => $lead_data->getData(),
                                'favorite'  => $viewed_data->getData(),
                            ), Util_Curl::REQUEST_TYPE_POST, $curl_options);
                        }

                        break;
                    case Hooks::HOOK_LEAD_SEARCH_PERFORMED:
                        // Get the saved row in the DB
                        $where = array(
                            '$eq' => array(
                                'user_id'       => $lead['id'],
                                'idx'           => $params['idx']->getName(),
                                'title'         => htmlspecialchars($params['title']),
                                'criteria'      => serialize($params['criteria']),
                            )
                        );

                        // Execute request
                        if ($existing = $db->{'users_viewed_searches'}->search($where)->fetch()) {
                            $search_data = new API_Object_Lead_Search($db, $existing);
                            Util_Curl::executeRequest($event['url'], array(
                                'lead'      => $lead_data->getData(),
                                'search'    => $search_data->getData(),
                            ), Util_Curl::REQUEST_TYPE_POST, $curl_options);
                        }

                        break;
                    case Hooks::HOOK_LEAD_SEARCH_SAVED:
                        // Get the saved row in the DB
                        $where = array(
                            '$eq' => array(
                                'user_id'       => $lead['id'],
                                'idx'           => $params['idx']->getName(),
                                'title'         => $params['title'],
                                'criteria'      => serialize($params['criteria']),
                                'suggested'     => (!empty($params['suggested']) ? 'true' : 'false'),
                                'frequency'     => $params['frequency'],
                            )
                        );

                        // Execute request
                        if ($existing = $db->{'users_searches'}->search($where)->fetch()) {
                            $search_data = new API_Object_Lead_Search($db, $existing);
                            Util_Curl::executeRequest($event['url'], array(
                                'lead'      => $lead_data->getData(),
                                'search'    => $search_data->getData(),
                            ), Util_Curl::REQUEST_TYPE_POST, $curl_options);
                        }

                        break;
                    case Hooks::HOOK_LEAD_SEARCH_REMOVED:
                        // Hook parameters
                        $row = $params['row'];
                        $search_data = new API_Object_Lead_Search($db, $row);

                        // Execute request
                        Util_Curl::executeRequest($event['url'], array(
                            'lead'      => $lead_data->getData(),
                            'search'    => $search_data->getData(),
                        ), Util_Curl::REQUEST_TYPE_POST, $curl_options);

                        break;
                    case Hooks::HOOK_LEAD_VISIT:
                        // Hook parameters
                        $lead = Backend_Lead::load($params['lead_id']);
                        $lead_data = new API_Object_Lead($db, $lead->getRow());

                        // Execute request
                        Util_Curl::executeRequest($event['url'], array(
                            'lead' => $lead_data->getData(),
                        ), Util_Curl::REQUEST_TYPE_POST, $curl_options);

                        break;
                }
            }
        }
    }

    /**
     * Check if a configured destination type is currently supported
     * @param integer $type
     * @return boolean
     */
    protected function destinationTypeSupported($type)
    {
        if (empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
            return false;
        }
        if (empty(Settings::getInstance()->MODULES['REW_CRM_API_OUTGOING'])) {
            return false;
        }
        return in_array($type, array(self::DESTINATION_TYPE_REW, self::DESTINATION_TYPE_CUSTOM));
    }
}
