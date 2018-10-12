<?php

/**
 * Partner_WiseAgent
 *
 */
class Partner_WiseAgent
{

    /**
     * WiseAgent system group name
     * @var string
     */
    const GROUP_NAME = 'WiseAgent';

    /**
     * WiseAgent system group style
     * @var string
     */
    const GROUP_STYLE = 'q';

    /**
     * WiseAgent API Endpoint
     * @var string
     */
    const API_ENDPOINT_URL = 'https://sync.thewiseagent.com/http/webconnect.asp';

    /**
     * Default WiseAgent Category to Push Leads to
     * @var string
     */
    const DEFAULT_CATEGORY = 'REW Leads';

    /**
     * Pull Array of Wise Agent Team Member Names
     * NOTE: This is currently used specifically for API validation - though Wise Agent has expressed interest in adding team member assignment to their API in the future
     *
     * @var string $api_key
     * @return array
     * @throws Exception
     */
    public function getTeamMembers($api_key)
    {

        // Request Team Member List
        $response = self::executeRequest('getTeam', array('apiKey' => $api_key));
        $team = array();

        // Process Response
        if (!empty($response)) {
            $members = json_decode($response);

            if (!empty($members)) {
                foreach ($members as $member) {
                    if (!empty($member->Name)) {
                        $team[] = $member->Name;
                    }
                }
            }
        }

        return $team;
    }

    /**
     * Add a new contact to WiseAgent and return its details
     * @param string $api_key
     * @param string $email
     * @param string $category
     * @param bool $call_list
     * @param bool $notify
     * @return string|NULL WiseAgent clientID used for further updates.
     * @throws PDOException|Exception
     */
    public function addContact($api_key, $email, $category = false, $call_list = false)
    {

        // Pull Lead Info
        $db = DB::get();
        $sql = $db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1;");
        $sql->execute(array('email' => $email));
        if ($lead = $sql->fetch()) {
            // Settings
            $no_call_list = (!empty($call_list)) ? '0' : '1';

            // Lead Category
            $category = (!empty($category)) ? $category : self::DEFAULT_CATEGORY;

            // Build cURL Request
            $curlVars = array(
                // Push Settings
                'requestType'               => 'webcontact',
                'apikey'                    => $api_key,
                // User Data
                'CFirst'                    => $lead['first_name'],
                'CLast'                     => $lead['last_name'],
                'CEmail'                    => $lead['email'],
                'Phone'                     => $lead['phone'],
                'Fax'                       => $lead['phone_fax'],
                'Cell'                      => $lead['phone_cell'],
                'Work'                      => $lead['phone_work'],
                'Message'                   => $lead['notes'],
                'address'                   => $lead['address1'] . (!empty($lead['address2']) ? ' ' . $lead['address2'] : ''),
                'city'                      => $lead['city'],
                'state'                     => $lead['state'],
                'zip'                       => $lead['zip'],
                'Source'                    => $lead['referer'],
                // System Settings
                'Categories'                => $category,
                'noCallList'                => $no_call_list
            );

            $response = self::executeRequest('', $curlVars, $request_type = Util_Curl::REQUEST_TYPE_POST);
            // Flatten return object
            $response = preg_replace('/[\{|\}]/','', $response);
            $response = preg_replace('/\[(.*)\]/','{$1}', $response);
            $response = json_decode($response, true);

            return $response['ClientID'];
        } else {
            return null;
        }
    }

    /**
     * Update contact to WiseAgent and return its details
     * @param string $api_key
     * @param string $email
     * @param string $category
     * @param bool $call_list
     * @param bool $notify
     * @return void
     * @throws PDOException|Exception
     */
    public function updateContact($api_key, $email, $category = false, $call_list = false)
    {

        // Pull Lead Info
        $db = DB::get();
        $sql = $db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1;");
        $sql->execute(array('email' => $email));
        if ($lead = $sql->fetch()) {
            // Settings
            $no_call_list = (!empty($call_list)) ? '0' : '1';

            // Lead Category
            $category = (!empty($category)) ? $category : self::DEFAULT_CATEGORY;

            $address = $lead['address1'] . (!empty($lead['address2']) ? ' ' . $lead['address2'] : '');
            preg_match('/(\d+)(.*)/', $address, $match);
            // Grab first number as street number
            $street_number = $match[1];
            // Grab remainin text as street name
            $street_name = $match[2];

            // Build cURL Request
            $curlVars = array(
                // Push Settings
                'requestType'               => 'updateContact',
                'apikey'                    => $api_key,
                // User Data
                'clientID'                  => $lead['wiseagent_id'],
                'CFirst'                    => $lead['first_name'],
                'Clast'                     => $lead['last_name'],
                'CEmail'                    => $lead['email'],
                'HomePhone'                 => $lead['phone'] ?: 'Unavailable',
                'MobilePhone'               => $lead['phone_cell'] ?: 'Unavailable',
                'StreetNumber'              => $street_number ?: 'Unavailable',
                'StreetName'                => $street_name ?: 'Unavailable',
                'City'                      => $lead['city'] ?: 'Unavailable',
                'State'                     => $lead['state'] ?: 'Unavailable',
                'Zip'                       => $lead['zip'] ?: 'Unavailable',
                // System Settings
                'Categories'                => $category,
                'noCallList'                => $no_call_list
            );

            self::executeRequest('', $curlVars, $request_type = Util_Curl::REQUEST_TYPE_POST);
        }
    }

    /**
     * Execute a WiseAgent API request and return the response
     * @param string $endpoint
     * @param array $params
     * @param int $request_type
     * @return NULL|mixed
     * @throws Exception
     */
    public function executeRequest($endpoint, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST)
    {

        // Set API Endpoint
        Util_Curl::setBaseURL(self::API_ENDPOINT_URL);

        // Mrge Endpoint Into Parameters
        $params = array_merge(array('requestType' => $endpoint), $params);

        // cURL Request
        $response = Util_Curl::executeRequest('', $params, $request_type);

        // Return API Response
        return $response;
    }
}
