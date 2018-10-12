<?php

/**
 * Partner_BombBomb
 *
 */
class Partner_BombBomb
{

    /**
     * BombBomb system group name
     * @var string
     */
    const GROUP_NAME = 'BombBomb';

    /**
     * BombBomb system group style
     * @var string
     */
    const GROUP_STYLE = 'q';

    /**
     * BombBomb List name for REW leads
     * @var string
     */
    const LIST_NAME = 'REW Leads';

    /**
     * API Endpoint
     * @var string
     */
    private $_url_api_endpoint = 'https://app.bombbomb.com/app/api/api.php';

    /**
     * BombBomb account API Key
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
     * Validate BombBomb credentials
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function isValidLogin($email, $password)
    {

        // Request options
        $options = array(
            'email' => $email,
            'pw' => $password,
        );

        // Validate login
        if (!($response = $this->executeAPIRequest('IsValidLogin', $options))) {
            return false;
        }

        return true;
    }

    /**
     * Get a list of all Lists in the BombBomb account
     * @return array|NULL
     */
    public function getLists()
    {

        // Get lists
        if (!($response = $this->executeAPIRequest('GetLists'))) {
            return null;
        }

        return $response;
    }

    /**
     * Create a new list in the BombBomb account and return its details
     * @param string $name The name of the list
     * @return array|NULL
     */
    public function createList($name)
    {

        // Create list
        if (!($response = $this->executeAPIRequest('CreateList', array('name' => $name)))) {
            return null;
        }

        return $response;
    }

    /**
     * Get a list of contacts within a BombBomb List
     * @param string $list_id
     * @return array|NULL
     */
    public function getListContacts($list_id)
    {

        // Get contacts
        $response = $this->executeAPIRequest('GetListContacts', array('list_id' => $list_id));
        if (!is_array($response)) {
            return null;
        }

        return $response;
    }

    /**
     * Add a new contact to BombBomb and return its details
     * @param string $email
     * @param array $data The contact's fields & values
     * @param array|string $list_id A List ID or array of List IDs to which to assign the contact
     * @return array|NULL
     */
    public function addContact($email, $data = array(), $list_id = null)
    {

        // Contact data
        $data = array_merge(array('eml' => $email), $data);
        if (!empty($list_id)) {
            $data['listlist'] = is_array($list_id) ? implode(';', $list_id) : $list_id;
        }
        if (!($response = $this->executeAPIRequest('AddContact', $data))) {
            return null;
        }

        return $response;
    }

    /**
     * Update an existing contact in BombBomb and return its details
     * @param string $email
     * @param array $data The contact's fields & values
     * @param array|string $list_id A List ID or array of List IDs to which to assign the contact
     * @return array|NULL
     */
    public function updateContact($email, $data = array(), $list_id = null)
    {

        // Contact data
        $data = array_merge(array('eml' => $email), $data);
        if (!empty($list_id)) {
            $data['listlist'] = is_array($list_id) ? implode(';', $list_id) : $list_id;
        }
        if (!($response = $this->executeAPIRequest('UpdateContact', $data))) {
            return null;
        }

        return $response;
    }

    /**
     * Add an existing BombBomb contact to a List
     * @param string $email
     * @param string $list_id
     * @return NULL|boolean
     */
    public function addEmailToList($email, $list_id)
    {

        // Add to list
        if (!($response = $this->executeAPIRequest('AddEmailToList', array('new_email_address' => $email, 'list_id' => $list_id)))) {
            return null;
        }

        return true;
    }

    /**
     * Remove a BombBomb contact from a List
     * @param string $email
     * @param string $list_id
     * @return NULL|boolean
     */
    public function removeEmailFromList($email, $list_id)
    {

        // Remove from list
        if (!($response = $this->executeAPIRequest('RemoveEmailFromList', array('new_email_address' => $email, 'list_id' => $list_id)))) {
            return null;
        }

        return true;
    }

    /**
     * Get a list of all emails in the BombBomb account
     * @return array|NULL
     */
    public function getEmails()
    {

        // Get emails
        if (!($response = $this->executeAPIRequest('GetEmails'))) {
            return null;
        }

        return $response;
    }

    /**
     * Get a list of standard and custom fields available for contacts
     * @return NULL|array
     */
    public function getContactFields()
    {

        // Get fields
        if (!($response = $this->executeAPIRequest('GetContactFields'))) {
            return null;
        }

        return $response;
    }

    /**
     * Import many contacts from a CSV file into a specified list.
     * This method will return immediately, while the actual import will happen in another process.
     * You can use the getListProcessingStatus method to monitor import progress.
     *
     * @param string $list_id The List ID
     * @param array $fields Array of field_names in the CSV file. These can be retrieved via the getContactFields method
     * @param string $path_csv Full path to the CSV file containing the contacts
     */
    public function importCSVToList($list_id, $fields, $path_csv)
    {

        // Options
        $options = array(
            'list_id' => $list_id,
            'fields' => implode(',', $fields),
            'csv' => new CURLFile($path_csv),
        );

        // Import CSV
        if (!($response = $this->executeAPIRequest('importCsvToList', $options, Util_Curl::REQUEST_TYPE_POST_MULTIPART))) {
            return null;
        }

        return $response;
    }

    /**
     * Get the status of a list import operation. There will be two properties, status and message. Status will be either 1 for importing, or 0 for not importing.
     * @param string $list_id The List ID
     * @return NULL|array
     */
    public function getListProcessingStatus($list_id)
    {

        // Get processing status
        if (!($response = $this->executeAPIRequest('GetListProcessingStatus', array('list_id' => $list_id)))) {
            return null;
        }

        return $response;
    }

    /**
     * Execute an authenticated BombBomb API method request and return the response
     * @param string $method The API method to execute
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    private function executeAPIRequest($method, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST)
    {

        // Clear error
        $this->_error = null;

        // Request options
        $options = array_merge(array(
            'method' => $method,
            'api_key' => (!empty($params['api_key']) ? $params['api_key'] : $this->_api_key),
        ), $params);

        // Execute request
        return $this->executeRequest($this->_error, '', $options, $request_type);
    }

    /**
     * Execute a BombBomb API request and return the response
     * @param string $error
     * @param string $uri
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     */
    private function executeRequest(&$error, $uri, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST)
    {

        // cURL Request
        $response = Util_Curl::executeRequest($uri, $params, $request_type);
        $json = json_decode($response, true);

        // Require response
        if (empty($json)) {
            $error = 'An unexpected response was received from BombBomb&reg;';
            return null;
        }

        // Check for API error
        if ($json['status'] === 'failure') {
            $error = $json['info'] ?: 'An expected error occurred.';
            return null;
        }

        // API Response
        return $json['info'];
    }
}
