<?php

/**
 * Partner_HappyGrasshopper
 *
 */
class Partner_HappyGrasshopper
{

    /**
     * Happy Grasshopper system group name
     * @var string
     */
    const GROUP_NAME = 'Happy Grasshopper';

    /**
     * Happy Grasshopoper system group style
     * @var string
     */
    const GROUP_STYLE = 'lime';

    /**
     * API Endpoint
     * @var string
     */
    protected $_url_api_endpoint = 'https://go.happygrasshopper.com/api';

    /**
     * Partner API Key
     * @var string
     */
    protected $_api_key;

    /**
     * HGH issued User Key
     * @var string
     */
    protected $_user_key;

    /**
     * HGH issued User Code
     * @var string
     */
    protected $_user_code;

    /**
     * Last API Error
     * @var string
     */
    protected $_error;

    /**
     * Cached Contacts list
     * @var array
     */
    protected $_contacts;

    /**
     * Create a new partner instance
     * @param array $options
     */
    public function __construct($options)
    {

        // Require array
        if (!is_array($options)) {
            throw new Exception('Invalid options specified in ' . __CLASS__ . ' constructor: array expected');
        }

        // Set options
        $this->setOptions($options);

        // Require API Key
        if (empty($this->_api_key)) {
            throw new Exception('Failed to construct ' . __CLASS__ . ': API Key not provided');
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
     * Request User Authentication from Happy Grasshopper and obtain a User Key + User Code
     * @param string $username
     * @param string $password
     * @param string $user_key
     * @param string $user_code
     * @return NULL|mixed
     */
    public function requestAuthentication($username, $password, &$user_key, &$user_code)
    {

        // Request options
        $options = array(
            'api_key' => $this->_api_key,
            'username' => $username,
            'password' => $password,
        );

        // Generate key
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/authentication', $options))) {
            return null;
        }

        // Set response vars
        $user_key = $response['User_Key'];
        $user_code = $response['User_Code'];

        return $response;
    }

    /**
     * Get User Signature
     * @param array $details Array to store signature data into
     * @return NULL|mixed
     */
    public function getUserSignature(&$details)
    {

        // Request options
        $options = array(
            'api_key' => $this->_api_key,
            'user_key' => $this->_user_key,
            'user_code' => $this->_user_code,
        );

        // Get signature
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/signatures', $options))) {
            return null;
        }

        // Set response vars
        $details = $response;

        return $response;
    }

    /**
     * Update the user signature
     * @param array $data Signature fields and values
     * @return boolean
     */
    public function updateUserSignature($data)
    {

        // Request options
        $options = array(
            'api_key' => $this->_api_key,
            'user_key' => $this->_user_key,
            'user_code' => $this->_user_code,
        );

        // Merge signature data
        if (!empty($data) && is_array($data)) {
            $options = array_merge($options, $data);
        }

        // Update signature
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/signatures', $options, Util_Curl::REQUEST_TYPE_POST))) {
            return false;
        }

        return true;
    }

    /**
     * Get the account's contacts
     * @param array $contacts
     * @param string $email Filter contacts that have an e-mail address beginning with this string
     * @return NULL|array
     */
    public function getContacts(&$contacts, $email = null)
    {

        // Request list
        if (is_null($this->_contacts)) {
            // Request options
            $options = array(
                'api_key' => $this->_api_key,
                'user_key' => $this->_user_key,
                'user_code' => $this->_user_code,
            );

            // Get contacts
            $this->_error = null;
            if (is_null($response = $this->executeRequest($this->_error, '/contacts', $options))) {
                return null;
            }

            // Set response vars
            $contacts = $response;
            $this->_contacts = $contacts;
        } else {
            $response = $this->_contacts;
        }

        // Email filter
        if (is_array($this->_contacts) && !empty($email)) {
            $contacts_filtered = array();
            foreach ($this->_contacts as $contact) {
                if (stripos($contact['Email'], $email) === 0) {
                    $contacts_filtered[] = $contact;
                }
            }
            $contacts = $contacts_filtered;
        }

        return $response;
    }

    /**
     * Get a contact by its e-mail address
     * @param string $email
     * @return NULL|array
     */
    public function getContact($email)
    {

        // Contacts request
        if (!($contacts_response = $this->getContacts($contacts, $email))) {
            return null;
        }

        // Return user
        return !empty($contacts) ? $contacts[0] : null;
    }

    /**
     * Add a new contact
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param array $tags
     * @param pass-by-reference $response
     * @return boolean
     */
    public function addContact($first_name, $last_name, $email, $tags = array(), &$response = null)
    {

        // Request options
        $options = array(
            'api_key' => $this->_api_key,
            'user_key' => $this->_user_key,
            'user_code' => $this->_user_code,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'tags' => $tags

        );

        // Request options
        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS  => json_encode($options),
        );

        // Add contact
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/contacts', array(), Util_Curl::REQUEST_TYPE_GET, $options))) {
            return false;
        }

        return true;
    }

    /**
     * Update an existing contact
     * @param string $data_id HG Contact ID
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param string[] $tags
     * @return boolean
     */
    public function updateContact($data_id, $first_name, $last_name, $email, $tags = null)
    {

        // Request options
        $options = array(
            'request_type' => 'update_contact',
            'api_key' => $this->_api_key,
            'user_key' => $this->_user_key,
            'user_code' => $this->_user_code,
            'data_id' => $data_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
        );

        // Update tags
        if (is_array($tags)) {
            $options['tags'] = !empty($tags) ? $tags : '';
        }

        // Update contact
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/contacts', $options, Util_Curl::REQUEST_TYPE_POST))) {
            return false;
        }

        return true;
    }

    /**
     * Add a new tag
     * @param string $data_id HG Contact ID
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param string[] $tags
     * @return boolean
     */
    public function addTag ($data_id, $tag) {

        // Request options
        $options = array(
            'request_type' => 'put_tag',
            'api_key' => $this->_api_key,
            'user_key' => $this->_user_key,
            'user_code' => $this->_user_code,
            'data_id' => $data_id,
            'tag' => $tag
        );

        // Update contact
        $this->_error = null;
        if (!($response = $this->executeRequest($this->_error, '/contacts', $options, Util_Curl::REQUEST_TYPE_PUT))) {
            return false;
        }

        return true;
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

        // cURL Request
        $response = Util_Curl::executeRequest($uri, $params, $request_type, $opt_override);
        $json = json_decode($response, true);

        // Require response
        if (empty($json)) {
            $error = 'An unexpected response was received from Happy Grasshopper&reg;';
            return null;
        }

        // Check for API error
        if ($json['error'] !== '0') {
            $error = $json['error_detail'];
            return null;
        }

        // API Response
        return $json['response'];
    }
}
