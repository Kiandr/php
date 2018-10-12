<?php

/**
 * Partner_REW
 *
 */
class Partner_REW
{

    /**
     * API Endpoint
     * @var string
     */
    private $_url_api_endpoint;

    /**
     * REW Application API Key
     * @var string
     */
    private $_api_key;

    /**
     * Last API Error
     * @var string
     */
    private $_error;

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
     * Create a new lead
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param array $create_data
     * @return mixed|NULL
     */
    public function createLead($email, $first_name, $last_name, $create_data = array())
    {

        // POST data
        $create_data['email'] = $email;
        $create_data['first_name'] = $first_name;
        $create_data['last_name'] = $last_name;

        // Execute request
        return $this->executeAPIRequest('leads', $create_data);
    }

    /**
     * Get a single lead record
     * @param string $email
     * @return mixed|NULL
     */
    public function getLead($email)
    {
        return $this->executeAPIRequest('leads/' . $email, array(), Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Update a single lead record
     * @param string $email
     * @param array $update_data The fields and values to override
     * @return mixed|NULL
     */
    public function updateLead($email, $update_data)
    {

        // POST data
        $params = is_array($update_data) ? $update_data : array();
        return $this->executeAPIRequest('leads/' . $email, $params);
    }

    /**
     * Update a lead record or create it automatically if it doesn't exist
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param array $data
     */
    public function createOrUpdateLead($email, $first_name, $last_name, $data = array())
    {
        if ($this->getLead($email)) {
            $data['first_name'] = $first_name;
            $data['last_name'] = $last_name;
            // Merge groups as we likely don't want to overwrite
            if (!empty($data['groups']) && !empty($lead['groups'])) {
                $data['groups'] = array_unique(array_merge($data['groups'], array_map(function ($group) {
                    return $group['id'];
                }, $lead['groups'])));
            }
            $this->updateLead($email, $data);
        } else {
            $this->createLead($email, $first_name, $last_name, $data);
        }
    }

    /**
     * Create a new favorite for a lead
     * @param string $email
     * @param string $mls_number
     * @param string $type
     * @param string $feed
     * @param string $source
     * @return mixed|NULL
     */
    public function createFavorite($email, $mls_number, $type, $feed, $source)
    {

        // POST data
        $params = array(
            'mls_number' => $mls_number,
            'type' => $type,
            'feed' => $feed,
            'source' => $source,
        );

        // Execute request
        return $this->executeAPIRequest('leads/' . $email . '/favorites', $params);
    }

    /**
     * Get a single favorite record
     * @param string $email
     * @param integer $id
     * @return mixed|NULL
     */
    public function getFavorite($email, $id)
    {
        return $this->executeAPIRequest('leads/' . $email . '/favorites/' . $id, array(), Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Delete a single favorite record
     * @param string $email
     * @param integer $id
     * @return mixed|NULL
     */
    public function deleteFavorite($email, $id)
    {
        return $this->executeAPIRequest('leads/' . $email . '/favorites/' . $id, array(), 'DELETE');
    }

    /**
     * Get all favorites for a lead
     * @param string $email
     * @param string $feed
     * @param string $mls_number
     * @param string $type
     * @return mixed|NULL
     */
    public function getFavorites($email, $feed = null, $mls_number = null, $type = null)
    {

        // GET params
        $params = array();
        if (!is_null($feed)) {
            $params['feed'] = $feed;
        }
        if (!is_null($mls_number)) {
            $params['mls_number'] = $mls_number;
        }
        if (!is_null($type)) {
            $params['type'] = $type;
        }

        return $this->executeAPIRequest('leads/' . $email . '/favorites', $params, Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Create a saved search for a lead
     * @param string $email
     * @param string $title
     * @param array $criteria
     * @param string $feed
     * @param string $source
     * @param string $frequency
     * @param boolean $suppressed Set to true to prevent the destination from sending email alerts
     * @return mixed|NULL
     */
    public function createSavedSearch($email, $title, $criteria, $feed, $source, $frequency = null, $suppressed = null)
    {

        // POST params
        $params = array(
            'title' => $title,
            'criteria' => $criteria,
            'feed' => $feed,
            'source' => $source,
        );

        // Optional fields
        if (!is_null($frequency)) {
            $params['frequency'] = $frequency;
        }
        if (!is_null($suppressed) && $suppressed === true) {
            $params['_suppress_alerts'] = true;
        }

        // Execute request
        return $this->executeAPIRequest('leads/' . $email . '/searches', $params);
    }

    /**
     * Get a single saved search
     * @param string $email
     * @param integer $id
     * @return mixed|NULL
     */
    public function getSavedSearch($email, $id)
    {
        return $this->executeAPIRequest('leads/' . $email . '/searches/' . $id, array(), Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Update a single saved search
     * @param string $email
     * @param integer $id
     * @param array $update_data
     * @return mixed|NULL
     */
    public function updateSavedSearch($email, $id, $update_data)
    {

        // POST data
        $params = is_array($update_data) ? $update_data : array();
        return $this->executeAPIRequest('leads/' . $email . '/searches/' . $id, $params);
    }

    /**
     * Delete a single saved search
     * @param string $email
     * @param integer $id
     * @return mixed|NULL
     */
    public function deleteSavedSearch($email, $id)
    {
        return $this->executeAPIRequest('leads/' . $email . '/searches/' . $id, array(), 'DELETE');
    }

    /**
     * Get all saved searches for a lead
     * @param string $email
     * @param string $feed
     * @param string $title
     * @param array $criteria
     * @return mixed|NULL
     */
    public function getSavedSearches($email, $feed = null, $title = null, $criteria = null)
    {

        // GET params
        $params = array();
        if (!is_null($feed)) {
            $params['feed'] = $feed;
        }
        if (!is_null($title)) {
            $params['title'] = $title;
        }
        if (!is_null($criteria)) {
            $params['criteria'] = $criteria;
        }

        return $this->executeAPIRequest('leads/' . $email . '/searches', $params, Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Get all agents
     * @return mixed|NULL
     */
    public function getAgents()
    {
        return $this->executeAPIRequest('agents', array(), Util_Curl::REQUEST_TYPE_GET);
    }

    /**
     * Create a History Event record
     * @param string $email
     * @param string $type
     * @param string $subtype
     * @param array $details
     * @return mixed|NULL
     */
    public function createHistoryEvent($email, $type, $subtype, $details)
    {

        // POST params
        $params = array(
            'type' => $type,
            'subtype' => $subtype,
            'details' => $details,
        );

        // Execute request
        return $this->executeAPIRequest('leads/' . $email . '/events', $params);
    }

    /**
     * Execute an authenticated REW API request to a specific resource
     * @param string $resource
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    private function executeAPIRequest($resource, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST)
    {

        // Clear error
        $this->_error = null;

        // User agent
        $user_agent = 'rewCRM/' . Settings::getInstance()->APP_VERSION . (!empty(Settings::getInstance()->APP_BUILD) ? '-' . Settings::getInstance()->APP_BUILD : '');

        // API Authentication
        $options = array(
            CURLOPT_HTTPHEADER => array(
                'X-REW-API-Key: ' . $this->_api_key,
            ),
            CURLOPT_USERAGENT => $user_agent,
        );

        // Special request types
        if (in_array($request_type, array('DELETE'))) {
            $options[CURLOPT_CUSTOMREQUEST] = $request_type;
        }

        // Execute request
        return $this->executeRequest($this->_error, '/' . $resource, $params, $request_type, $options);
    }

    /**
     * Execute an REW API request and return the response
     * @param string $error
     * @param string $uri
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    private function executeRequest(&$error, $uri, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST, $opt_override = array())
    {

        // Set API Endpoint
        Util_Curl::setBaseURL($this->_url_api_endpoint);

        // cURL Request
        $response = Util_Curl::executeRequest($uri, $params, $request_type, $opt_override);
        $json = json_decode($response, true);

        // Require response
        if (empty($json)) {
            $error = 'An unexpected response was received from REW API';
            return null;
        }

        // Check for API error
        if (!empty($json['error'])) {
            $error = $json['error']['message'];
            return null;
        }

        // API Response
        return $json['data'];
    }
}
