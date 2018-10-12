<?php

/**
 * Partner_Zillow
 *
 */
class Partner_Zillow
{

    /**
     * Zillow Partner User Agent
     * var string
     */
    const APP_USER = 'rewUser/1.0';

    /**
     * Zillow Parter Version Date
     * var string
     */
    const APP_VERSION = '2016-11-10';

    /**
     * Zillow group name
     * @var string
     */
    const GROUP_NAME = 'Zillow';

    /**
     * Zillow group style
     * @var string
     */
    const GROUP_STYLE = 'i';

    /**
     * Zillow Active Status
     * @var int
     */
    const ZILLOW_STATUS_ACTIVE = 1;

    /**
     * Zillow Drip Status
     * @var int
     */
    const ZILLOW_STATUS_ACTIVE_DRIP = 2;

    /**
     * Zillow Rejected Status
     * @var int
     */
    const ZILLOW_STATUS_REJECTED = 3;

    /**
     * Zillow Reassigned Status
     * @var int
     */
    const ZILLOW_STATUS_REASSIGNED = 4;

    /**
     * API Endpoint
     * @var string
     */
    protected $_url_api_endpoint = 'https://zillow.rewhosting.com/api/';

    /**
     * Partner API Key
     * @var string
     */
    protected $_api_key;

    /**
     * Partner API Secret
     * @var string
     */
    protected $_api_secret;

    /**
     * User Agent ID
     * @var string
     */
    protected $_user_id;

    /**
     * Zillow Group
     * @var string
     */
    protected $_zillow_group;

    /**
     * Last API Error
     * @var string
     */
    protected $_error;

    /**
     * Cached Contacts list
     * @var array
     */
    protected $_leads;

    /**
     * Create a new partner instance
     * @param array $options
     */
    public function __construct($options = [])
    {

        // Require array
        if (!is_array($options)) {
            throw new Exception('Invalid options specified in ' . __CLASS__ . ' constructor: array expected');
        }

        // Set options
        $this->setOptions($options);

        // Require API Key
        if (isset($this->_url_api_endpoint) && empty($this->_url_api_endpoint)) {
            throw new Exception('Failed to construct ' . __CLASS__ . ': API Endpoint not provided');
        }

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
     * Request User Authentication from Zillow API and obtain a Key & Secret
     * @param string $site_api_endpoint
     * @param string $site_api_key
     * @param string|null $agent_id
     * @param string|null $agent_name
     * @return NULL|mixed
     */
    public function requestAccount($site_api_endpoint, $site_api_key, $agent_id = null, $agent_name = null)
    {

        // Request options
        $options = array(
            'site_agent_id' => $agent_id,
            'site_agent_name' => $agent_name,
            'version' => Settings::getInstance()->getConfig()['app_version'],
            'group_id' => $this->getGroup()['id'],
            'url' => $site_api_endpoint,
            'key' => $site_api_key
        );

        // Generate key
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/', $options, Util_Curl::REQUEST_TYPE_POST))) {
            return null;
        }

        $this->_user_id = $response['id'];
        $this->_api_key = $response['auth']['key'];
        $this->_api_secret = $response['auth']['secret'];

        return $response;
    }

    /**
     * Get User Authentication from Zillow API
     * @return array
     */
    public function getAccount()
    {

        // Check for required fields
        $this->checkRequiredFields();

        // Generate key
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/'. $this->_user_id . '/', [], Util_Curl::REQUEST_TYPE_GET))) {
            return null;
        }

        return $response;
    }

    /**
     * Delete Zillow API Integration
     * @return array
     */
    public function deleteAccount()
    {

        // Check for required fields
        $this->checkRequiredFields();

        // Generate key
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/'. $this->_user_id . '/', [], Util_Curl::REQUEST_TYPE_DELETE))) {
            return null;
        }
        return $response;
    }

    /**
     * Get the account's leads
     * @return NULL|array
     */
    public function getLeads()
    {

        // Check for required fields
        $this->checkRequiredFields();

        // Generate key
        $this->_error = null;
        $response = $this->executeRequest($this->_error, '/'. $this->_user_id . '/leads/', [], Util_Curl::REQUEST_TYPE_GET);
        return $response;
    }

    /**
     * Update Zillow API Integration statuses
     * @param string $email
     * @param int $status
     * @return array
     */
    public function updateLeads($email, $status)
    {

        // Check for required fields
        $this->checkRequiredFields();

        // Request options
        $options = array(
            'email' => $email,
            'status' => $status
        );

        // Update Leads
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/'. $this->_user_id . '/leads/lead/', $options, Util_Curl::REQUEST_TYPE_POST))) {
            return null;
        }
        return $response;
    }

    /**
     * Check for requried fields
     */
    protected function checkRequiredFields()
    {

        // Require API User Id
        if (empty($this->_user_id)) {
            throw new Exception('Failed to get Account: API User Id not provided');
        }

        // Require API Key
        if (empty($this->_api_key)) {
            throw new Exception('Failed to get Account: API Key not provided');
        }

        // Require API Secret
        if (empty($this->_api_secret)) {
            throw new Exception('Failed to get Account: API Secret not provided');
        }
    }

    /**
     * Create or Load Zillow Group
     * @return int
     */
    public function getGroup()
    {

        if (empty($this->_zillow_group)) {
            // Load Existing Zillow Group
            $db = DB::get();
            $query = $db->prepare("SELECT `id`, `name` FROM `groups` WHERE `name` = :name AND `agent_id` IS NULL AND `user` = 'false';");
            $query->execute(array(
                'name' => self::GROUP_NAME,
            ));
            $this->_zillow_group = $query->fetch();

            // Create New Zillow Group
            if (empty($this->_zillow_group)) {
                $zillow_query = $db->prepare("INSERT INTO `groups` SET "
                    . "`name`			= :name, "
                    . "`description`	= 'Leads in this group will be synced with Zillow', "
                    . "`style`			= :style, "
                    . "`user`			= 'false', "
                    . "`agent_id`		= NULL;");
                $zillow_query->execute(['name' => self::GROUP_NAME, 'style' => self::GROUP_STYLE]);
                $this->_zillow_group = ['id' => $db->lastInsertId(), 'name' => self::GROUP_NAME];
            }
        }
        return $this->_zillow_group;
    }

    /**
     * Execute an HG API request and return the response
     * @param string $error
     * @param string $uri
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    protected function executeRequest(&$error, $uri, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_GET, $opt_override = array())
    {

        // Required Authentication Headers
        $headers = [
            'User-Agent: ' . self::APP_USER,
            'X-REW-ORIGIN: ' . URL,
            'X-REW-PATH-LINE: ' . (__FILE__ . ":" . __LINE__),
            'X-REW-VERSION: application/json',
            'X-REW-Version: ' . self::APP_VERSION,
            'Content-Type: application/json'
        ];
        if (isset($this->_api_key)) {
            $headers[] = 'X-REW-API-Key: ' . $this->_api_key;
        }
        if (isset($this->_api_secret)) {
            $nonce = time();
            $headers[] = 'X-REW-Nonce: ' . $nonce;
            $headers[] = 'X-REW-Nonce-Token: ' . hash('sha256', $this->_api_secret.$nonce);
        }

        // cURL Request
        $response = Util_Curl::executeRequest($uri, $params, $request_type, $opt_override + array(
            CURLOPT_HTTPHEADER => $headers
        ));

        if ($request_type == Util_Curl::REQUEST_TYPE_DELETE) {
            return Util_Curl::info()['http_code'] == 204;
        } else {
            $json = json_decode($response, true);

            // Require response
            if (empty($json)) {
                $error = 'An unexpected response was received from Zillow&reg;';
                return null;
            }

            // Check for API error
            if (!empty($json['errors'])) {
                $error = $json['errors'];
                return null;
            }

            // API Response
            return $json['data'];
        }
    }
}
