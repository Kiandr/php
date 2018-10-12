<?php

/**
 * Partner_FollowUpBoss
 *
 */
class Partner_FollowUpBoss
{

    /**
     * Event API Notification types
     */
    const EVENT_TYPE_REGISTRATION       = 1;
    const EVENT_TYPE_INQUIRY_PROPERTY   = 2;
    const EVENT_TYPE_INQUIRY_GENERAL    = 3;
    const EVENT_TYPE_PROPERTY_VIEWED    = 4;
    const EVENT_TYPE_PROPERTY_SAVED     = 5;
    const EVENT_TYPE_WEBSITE_VISIT      = 6;
    const EVENT_TYPE_SEARCH_PERFORMED   = 7;
    const EVENT_TYPE_SEARCH_SAVED       = 8;
    const EVENT_TYPE_CALL_INCOMING      = 9;
    const EVENT_TYPE_CALL_MISSED        = 10;
    const EVENT_TYPE_CALL_OUTGOING      = 11;
    const EVENT_TYPE_SMS_INCOMING       = 12;
    const EVENT_TYPE_SMS_OUTGOING       = 13;

    /**
     * API Endpoint
     * @var string
     */
    private $_url_api_endpoint = 'https://api.followupboss.com/v1';

    /**
     * Follow Up Boss account API Key
     * @var string
     */
    private $_api_key;

    /**
     * Last API Error
     * @var string
     */
    private $_error;

    /**
     * Partner system instance
     * @var Partner_FollowUpBoss
     */
    protected static $_instance;

    /**
     * Get the shared system instance from the Super Admin's partner settings
     * @param DB $db
     * @return Partner_FollowUpBoss|NULL
     */
    public static function systemInstance($db = null)
    {

        // Return existing instance
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }

        $db = !empty($db) ? $db : DB::get('users');

        // Fetch Super Admin
        $admin_agent = $db->{'agents'}->getRow(1);

        // Require agent
        if (empty($admin_agent)) {
            return null;
        }

        // Require partner data
        if (empty($admin_agent['partners'])) {
            return null;
        }

        // Parse JSON
        $partners = json_decode($admin_agent['partners'], true);
        if (empty($partners)) {
            return null;
        }

        // Require FUB data
        if (!($api_key = $partners['followupboss']['api_key'])) {
            return null;
        }

        // Create instance
        $partner = new self(array(
            'api_key' => $api_key,
        ));

        // Cache instance & return
        self::$_instance = $partner;
        return self::$_instance;
    }

    /**
     * Create a new partner instance
     * @param array $options
     */
    public function __construct($options = array())
    {

        // Require array
        if (!is_array($options)) {
            throw new Exception('Invalid options specified in ' . __CLASS__ . ' constructor: array expected');
        }

        // Set options
        $this->setOptions($options);

        // Set API Endpoint
        Util_Curl::setBaseURL($this->_url_api_endpoint);
    }

    /**
     * Get the last occurred error
     * @return string
     */
    public function getLastError()
    {
        return $this->_error;
    }

    /**
     * Set a collection of options
     * @param array $options
     */
    public function setOptions($options = array())
    {
        foreach ($options as $key => $value) {
            $propname = '_' . $key;
            if (!property_exists(__CLASS__, $propname)) {
                continue;
            }

            // Set property
            $this->$propname = $value;
        }
    }

    /**
     * Post a New Registration event notification
     * @param string|array $lead
     * @param array $listing
     * @return mixed|NULL
     */
    public function notifyRegistration($lead, $listing = array())
    {
        $event_data = array();
        if (!empty($listing)) {
            $property = $this->formattedPropertyForListing($listing);
            $event_data['property'] = $property;
        }

        // Execute request
        return $this->postEvent(self::EVENT_TYPE_REGISTRATION, $lead, 'New IDX Registration', $event_data);
    }

    /**
     * Post a Property Inquiry event notification
     * @param string|array $lead
     * @param array $listing
     * @param string $inquire_type
     * @param string $inquire_message
     * @return mixed|NULL
     */
    public function notifyPropertyInquiry($lead, $listing, $inquire_type, $inquire_message)
    {
        $property = $this->formattedPropertyForListing($listing);

        // Execute request
        return $this->postEvent(self::EVENT_TYPE_INQUIRY_PROPERTY, $lead, $inquire_type, array('property' => $property), $inquire_message);
    }

    /**
     * Post a General Inquiry event notification
     * @param string|array $lead
     * @param string $subject
     * @param string $message
     * @return mixed|NULL
     */
    public function notifyGeneralInquiry($lead, $subject, $message)
    {
        return $this->postEvent(self::EVENT_TYPE_INQUIRY_GENERAL, $lead, $subject, array(), $message);
    }

    /**
     * Post a property viewing event notification
     * @param string|array $lead
     * @param array $listing
     * @return mixed|NULL
     */
    public function notifyPropertyViewed($lead, $listing)
    {
        $property = $this->formattedPropertyForListing($listing);

        // Execute request
        return $this->postEvent(self::EVENT_TYPE_PROPERTY_VIEWED, $lead, null, array('property' => $property));
    }

    /**
     * Post a property favorite event notification
     * @param string|array $lead
     * @param array $listing
     * @return mixed|NULL
     */
    public function notifyPropertySaved($lead, $listing)
    {
        $property = $this->formattedPropertyForListing($listing);

        // Execute request
        return $this->postEvent(self::EVENT_TYPE_PROPERTY_SAVED, $lead, null, array('property' => $property));
    }

    /**
     * Post a website visit event notification
     * @param string|array $lead
     * @param string $referer
     * @param string $keywords
     * @param string $domain
     * @return mixed|NULL
     */
    public function notifyWebsiteVisit($lead, $referer = null, $keywords = null, $domain = null)
    {

        // FUB Campaign
        $campaign = array();
        if (!empty($referer) && !empty($keywords)) {
            $source = $this->formattedSourceForReferer($referer);
            $medium = $this->formattedMediumForReferer($referer);

            // Build campaign data
            $campaign = array(
                'campaign' => array(
                    'source' => $source,
                    'medium' => $medium,
                    'term' => $keywords,
                )
            );
        }

        return $this->postEvent(self::EVENT_TYPE_WEBSITE_VISIT, $lead, null, $campaign, null, $domain);
    }

    /**
     * Post a property search event notification
     * @param string|array $lead
     * @param array $request
     * @param string $title
     * @return mixed|NULL
     */
    public function notifyPropertySearch($lead, $request, $title = null)
    {
        $search = $this->formattedSearchForCriteria($request);

        // Execute request
        return $this->postEvent(self::EVENT_TYPE_SEARCH_PERFORMED, $lead, $title, array('propertySearch' => $search));
    }

    /**
     * Post a property saved search event notification
     * @param string|array $lead
     * @param array $request
     * @param string $title
     * @return mixed|NULL
     */
    public function notifyPropertySearchSaved($lead, $request, $title = null)
    {
        $search = $this->formattedSearchForCriteria($request);

        // Execute request
        return $this->postEvent(self::EVENT_TYPE_SEARCH_SAVED, $lead, $title, array('propertySearch' => $search));
    }

    /**
     * Post an outgoing call event notification
     * @param string|array $lead
     * @param string $outcome
     * @param string $details
     * @return mixed|NULL
     */
    public function notifyOutgoingCall($lead, $outcome, $details)
    {
        $outcome = $this->formattedOutcomeForString($outcome);

        return $this->postEvent(self::EVENT_TYPE_CALL_OUTGOING, $lead, $outcome, array(), $details);
    }

    /**
     * Post a notification to the Events API
     * @param int $type
     * @param string $message
     * @param string|array $lead Lead data row or ID
     * @param array $data
     * @param string $description
     * @param string $source
     * @return mixed|NULL
     */
    public function postEvent($type, $lead, $message = null, $data = array(), $description = null, $source = null)
    {

        // Defaults
        $source = !empty($source)? $source : Http_Host::getDomain();
        $type_name = $this->eventTypeToString($type);
        $lead = is_array($lead) ? $lead : Backend_Lead::load($lead);
        $db = DB::get('users');

        // Require parameters
        if (empty($source) || empty($type_name) || empty($lead)) {
            $this->_error = 'Invalid parameters provided for notification';
            return null;
        }

        // Notification Person
        $person = array(
            'firstName' => $lead['first_name'],
            'lastName' => $lead['last_name'],
            'emails' => array(
                array('value' => $lead['email'], 'type' => 'home'),
            ),
        );

        // Person phones
        $phones = array();
        if (!empty($lead['phone'])) {
            $phones[] = array('value' => $lead['phone'], 'type' => 'home');
        }
        if (!empty($lead['phone_cell'])) {
            $phones[] = array('value' => $lead['phone_cell'], 'type' => 'mobile');
        }
        if (!empty($lead['phone_work'])) {
            $phones[] = array('value' => $lead['phone_work'], 'type' => 'work');
        }
        if (!empty($phones)) {
            $person['phones'] = $phones;
        }

        // Person tags
        $tags = array();
        $sql = "SELECT `g`.* FROM `users_groups` ug "
                . "LEFT JOIN `groups` g ON g.`id` = ug.`group_id` "
                . "WHERE `user_id` = " . $db->quote($lead['id']) . ";";

        // Execute
        if ($lead_groups = $db->fetchAll($sql)) {
            foreach ($lead_groups as $group_row) {
                if (in_array($group_row['name'], $tags)) {
                    continue;
                }
                $tags[] = $group_row['name'];
            }
        }

        // Set person tags
        if (!empty($tags)) {
            $person['tags'] = implode(', ', $tags);
        }

        // Notification payload
        $notification = array(
            'source' => $source,
            'type' => $type_name,
            'person' => $person,
        );

        // Message & Description
        if (!empty($message)) {
            $notification['message'] = $message;
        }
        if (!empty($description)) {
            $notification['description'] = $description;
        }

        // Event data
        if (!empty($data) && is_array($data)) {
            $notification = array_merge($notification, $data);
        }

        // Execute request
        return $this->executeAPIRequest('events', $notification);
    }

    /**
     * Get the account's tasks
     * @return array
     */
    public function getTasks()
    {
        return $this->executeAPIRequest('tasks', array(), Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Execute an authenticated Follow Up Boss API request to a specific resource
     * @param string $resource
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    private function executeAPIRequest($resource, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST)
    {

        // Clear error
        $this->_error = null;

        // API Authentication
        $options = array(
            CURLOPT_HTTPAUTH    => CURLAUTH_BASIC,
            CURLOPT_USERPWD     => $this->_api_key . ':',
        );

        // Encode POST fields
        if ($request_type === Util_Curl::REQUEST_TYPE_POST) {
            $options[CURLOPT_HTTPHEADER] = array('Content-Type: application/json');
            $options[CURLOPT_POSTFIELDS] = json_encode($params);
        }

        // Clear params
        $params = array();

        // Execute request
        return $this->executeRequest($this->_error, '/' . $resource, $params, $request_type, $options);
    }

    /**
     * Execute a Follow Up Boss API request and return the response
     * @param string $error
     * @param string $uri
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    private function executeRequest(&$error, $uri, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST, $opt_override = array())
    {

        // cURL Request
        $response = Util_Curl::executeRequest($uri, $params, $request_type, $opt_override);
        $json = json_decode($response, true);

        // Require response
        if (empty($json)) {
            $error = 'An unexpected response was received from Follow Up Boss&reg;';
            return null;
        }

        // Check for API error
        if (!empty($json['errorMessage'])) {
            $error = $json['errorMessage'];
            return null;
        }

        // API Response
        return $json;
    }

    /**
     * Convert an event type to a string that the API expects
     * @param int $event_type
     * @return string|NULL
     */
    private function eventTypeToString($event_type)
    {
        switch ($event_type) {
            case self::EVENT_TYPE_REGISTRATION:
                return 'Registration';
            case self::EVENT_TYPE_INQUIRY_PROPERTY:
                return 'Property Inquiry';
            case self::EVENT_TYPE_INQUIRY_GENERAL:
                return 'General Inquiry';
            case self::EVENT_TYPE_PROPERTY_VIEWED:
                return 'Viewed Property';
            case self::EVENT_TYPE_PROPERTY_SAVED:
                return 'Saved Property';
            case self::EVENT_TYPE_WEBSITE_VISIT:
                return 'Visited Website';
            case self::EVENT_TYPE_SEARCH_PERFORMED:
                return 'Property Search';
            case self::EVENT_TYPE_SEARCH_SAVED:
                return 'Saved Property Search';
            case self::EVENT_TYPE_CALL_INCOMING:
                return 'Incoming Call';
            case self::EVENT_TYPE_CALL_MISSED:
                return 'Missed Call';
            case self::EVENT_TYPE_CALL_OUTGOING:
                return 'Outgoing Call';
            case self::EVENT_TYPE_SMS_INCOMING:
                return 'Incoming SMS';
            case self::EVENT_TYPE_SMS_OUTGOING:
                return 'Outgoing SMS';
        }

        // Invalid
        return null;
    }

    /**
     * Format a parsed IDX listing into a FUB-accepted object
     * @param array $listing
     * @return array
     */
    private function formattedPropertyForListing($listing)
    {

        // Formatted property object
        $property = array();

        // Follow Up Boss => IDX field map
        $field_map = array(
            array('fub_field' => 'street',      'idx_field' => 'Address'),
            array('fub_field' => 'city',        'idx_field' => 'AddressCity'),
            array('fub_field' => 'state',       'idx_field' => 'AddressState'),
            array('fub_field' => 'code',        'idx_field' => 'AddressZipCode'),
            array('fub_field' => 'mlsNumber',   'idx_field' => 'ListingMLS'),
            array('fub_field' => 'price',       'idx_field' => 'ListingPrice'),
            array('fub_field' => 'url',         'idx_field' => 'url_details'),
            array('fub_field' => 'type',        'idx_field' => 'ListingType'),
            array('fub_field' => 'bedrooms',    'idx_field' => 'NumberOfBedrooms'),
            array('fub_field' => 'bathrooms',   'idx_field' => 'NumberOfBathrooms'),
            array('fub_field' => 'area',        'idx_field' => 'NumberOfSqft'),
            array('fub_field' => 'lot',         'idx_field' => 'NumberOfAcres'),
            array('fub_field' => 'forRent',     'value' => (
                stristr($listing['ListingType'], 'rental') ||
                stristr($listing['ListingType'], 'lease') ||
                stristr($listing['ListingSubType'], 'rental') ||
                stristr($listing['ListingSubType'], 'lease')
            )),
        );

        // Map fields
        foreach ($field_map as $fld) {
            $fub_field = $fld['fub_field'];
            $idx_field = $fld['idx_field'];
            if (isset($fld['value'])) {
                $property[$fub_field] = $fld['value'];
            } else if (!empty($listing[$idx_field])) {
                $property[$fub_field] = $listing[$idx_field];
            }
        }

        // Return object
        return $property;
    }

    /**
     * Format search criteria into a FUB-accepted object
     * @param array
     * @return array
     */
    private function formattedSearchForCriteria($request)
    {

        // Formatted search object
        $search = array();

        // Follow Up Boss => REW criteria field map
        $field_map = array(
            array('fub_field' => 'type',            'criteria_fields' => array('search_type')),
            array('fub_field' => 'neighborhood',    'criteria_fields' => array('search_subdivision', 'search_area')),
            array('fub_field' => 'city',            'criteria_fields' => array('search_city')),
            array('fub_field' => 'state',           'criteria_fields' => array('search_state')),
            array('fub_field' => 'code',            'criteria_fields' => array('search_zip')),
            array('fub_field' => 'minPrice',        'criteria_fields' => array('minimum_price', 'minimum_rent')),
            array('fub_field' => 'maxPrice',        'criteria_fields' => array('maximum_price', 'maximum_rent')),
            array('fub_field' => 'minBedrooms',     'criteria_fields' => array('minimum_bedrooms', 'minimum_beds')),
            array('fub_field' => 'maxBedrooms',     'criteria_fields' => array('maximum_bedrooms', 'maximum_beds')),
            array('fub_field' => 'minBathrooms',    'criteria_fields' => array('minimum_bathrooms', 'minimum_baths')),
            array('fub_field' => 'maxBathrooms',    'criteria_fields' => array('maximum_bathrooms', 'maximum_baths')),
            array('fub_field' => 'minArea',         'criteria_fields' => array('minimum_sqft')),
            array('fub_field' => 'maxArea',         'criteria_fields' => array('maximum_sqft')),
            array('fub_field' => 'forRent',         'value' => (
                (is_array($request['search_type']) && (stristr(implode(',', $request['search_type']), 'rental') || stristr(implode(',', $request['search_type']), 'lease'))) ||
                (is_string($request['search_type']) && (stristr($request['search_type'], 'rental') || stristr($request['search_type'], 'lease'))) ||
                (is_array($request['search_subtype']) && (stristr(implode(',', $request['search_subtype']), 'rental') || stristr(implode(',', $request['search_subtype']), 'lease'))) ||
                (is_string($request['search_subtype']) && (stristr($request['search_subtype'], 'rental') || stristr($request['search_subtype'], 'lease')))
            ))
        );

        // Map fields
        foreach ($field_map as $fld) {
            $fub_field = $fld['fub_field'];
            $criteria_fields = $fld['criteria_fields'];
            if (isset($fld['value'])) {
                $search[$fub_field] = $fld['value'];
            } else {
                $fub_field_values = array();
                foreach ($criteria_fields as $form_field) {
                    if (empty($request[$form_field])) {
                        continue;
                    }
                    $fub_field_values[] = is_array($request[$form_field]) ? implode(', ', $request[$form_field]) : $request[$form_field];
                }
                if (!empty($fub_field_values)) {
                    $search[$fub_field] = implode(', ', $fub_field_values);
                }
            }
        }

        // Return object
        return $search;
    }

    /**
     * Format a recognized short outcome string into a human-readable version
     * @param string $outcome
     * @return string
     */
    private function formattedOutcomeForString($outcome)
    {
        switch ($outcome) {
            case 'call':
                return 'Talked to Lead';
            case 'attempt':
                return 'Attempted';
            case 'voicemail':
                return 'Voicemail';
            case 'invalid':
                return 'Wrong Number';
        }
        return $outcome;
    }

    /**
     * Format a parsed referer Source into a FUB campaign source
     * @param string $referer
     * @return string
     */
    private function formattedSourceForReferer($referer)
    {
        if (stristr($referer, 'google')) {
            return 'google';
        }
        switch ($referer) {
            case 'Yahoo!':
                return 'yahoo';
            case 'Live search':
                return 'live';
            case 'Ask.com':
                return 'ask';
        }
        return strtolower(str_replace(' ', '_', $referer));
    }

    /**
     * Format a parsed referer Source into a FUB campaign medium
     * @param string $referer
     * @return string
     */
    private function formattedMediumForReferer($referer)
    {
        if (stristr($referer, 'ppc')) {
            return 'cpc';
        }
        return 'organic';
    }
}
