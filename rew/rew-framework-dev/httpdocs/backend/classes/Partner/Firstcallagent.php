<?php

namespace REW\Backend\Partner;

use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\User\SessionInterface;
use REW\Core\Interfaces\LogInterface;
use Util_Curl;
use Backend_Lead;
use History_Event_LegacyNote_LegacyHistory;
use History_User_Lead;
use \PDOException;
use REW\Core\Interfaces\CacheInterface;

/**
 * FirstCallAgent
 *
 */
class Firstcallagent
{


    /**
     * @var array INQUIRY_TYPES
     */
    const INQUIRY_TYPES = ['IDX Inquiry','Quick Showing','Quick Inquire'];

    /**
     * API Endpoint
     * @var string
     */
    protected static $url_api_endpoint = 'http://www.insidesalesagents.com/api/crm/v1/fca';
    
    /**
     * FCA API Key
     * @var string
     */
    protected $api_key;

    /**
     * Last API Error
     * @var string
     */
    protected $error;

    /**
     * Settings
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * DB Connection
     * @var DBInterface
     */
    protected $db;

    /**
     * User_Session
     * @var SessionInterface
     */
    protected $user;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * FCA Sending enabled
     * @var bool
     */
    protected $sending = false;

    /**
     * Array of agent IDs excluded from FCA sending
     * @var array
     */
    protected $exclude_agents = array();

    /**
     * Create a new instance
     */
    public function __construct(
        SettingsInterface $settings,
        DBInterface $db,
        SessionInterface $user,
        LogInterface $log,
        CacheInterface $cache
    ) {
    

        $this->settings = $settings;
        $this->db = $db;
        $this->user = $user;
        $this->log = $log;
        $this->cache = $cache;

        // Set API Endpoint
        Util_Curl::setBaseURL(static::$url_api_endpoint);

        // Get FCA Settings
        try {
            if ($settings = $this->db->fetch("SELECT `sending`, `exclude_agents`, `api_key` FROM `partners_firstcallagent` WHERE `agent_id` = 1 LIMIT 1;")) {
                // Require API Key
                if (!empty($settings['api_key'])) {
                    $this->api_key = $settings['api_key'];

                    $this->sending = ($settings['sending'] == 'true');
                    $this->exclude_agents = !empty($settings['exclude_agents']) ? explode(',', $settings['exclude_agents']) : array();
                }
            }
        } catch (PDOException $e) {
            $this->log->error($e);
        }
    }

    /**
     * Send lead to FCA system
     * @param Backend_Lead $lead
     * @param bool $manual If lead was manually sent (not from registration)
     * @return string
     */
    public function sendLead(Backend_Lead $lead, $manual = false)
    {

        // Clear Error
        $this->error = null;

        // Ensure FCA sending is enabled
        if ($this->sending === false) {
            $this->error = 'FCA Sending is disabled.';
            return false;
        }

        if ($this->agentExcluded($lead->info('agent'))) {
            $this->error = 'The assigned agent is excluded from FCA sending.';
            return false;
        }

        // Require agent - get assigned agent info
        try {
            $agent = $this->db->fetch("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name`, `email`, `cell_phone` AS `phone` "
                . "FROM `agents` WHERE `id` = :id", array('id' => $lead->info('agent')));
        
            if (empty($agent)) {
                $this->error = 'Unable to get assigned agent info.';
                return false;
            }
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }

        // Require phone number
        $lead['phone'] = trim($lead['phone']);
        $lead['phone_cell'] = trim($lead['phone_cell']);
        $lead['phone_work'] = trim($lead['phone_work']);
        if (empty($lead['phone']) && empty($lead['phone_cell']) && empty($lead['phone_work'])) {
            $this->error = 'No phone number provided.';
            return false;
        }

        // Get Search criteria from last search url
        // Attempt to get last search from User_Session object
        $criteria = $this->getCriteria();

        // Format criteria
        $criteria_formatted = array();
        if (!empty($criteria)) {
            $criteria_formatted = $this->formatCriteria($criteria);
        }

        // fetch any inquiry made
        $inquiry = $this->getInquiry($lead);

        // Build request parameters
        $params = array(
            'first_name'     => $lead['first_name'],
            'last_name'      => $lead['last_name'],
            'email'          => $lead['email'],
            'phone'          => $lead['phone'],
            'phone_cell'     => $lead['phone_cell'],
            'phone_work'     => $lead['phone_work'],
            'agent_name'     => $agent['name'],
            'agent_email'    => $agent['email'],
            'agent_phone'    => $agent['phone'],
            'criteria'       => (!empty($criteria_formatted) ? $criteria_formatted : array()),
            'inquiry'        => (!empty($inquiry) ? $inquiry : array()),
            'origin'         => $lead['referer'],
            'manual'         => $manual ? 'yes' : 'no',
            'source_user_id' => $lead->getId()
        );

        // cURL Request
        $response = $this->executeCurlRequest('', $params);
        $response = json_decode($response, true);

        // Check response
        if (!empty($response['data']['id'])) {
            try {
                // Mark lead as sent
                $sent = $this->db->prepare("INSERT INTO `partners_firstcallagent_users` SET `sent` = 'true', `user_id` = :user_id;");
                $sent->execute(array('user_id' => $lead['id']));
            } catch (PDOException $e) {
                    $this->log->error($e->getMessage());
            }
            
            // Use a legacy history event to track when the lead was sent.
            $event = new History_Event_LegacyNote_LegacyHistory(
                array('type' => 'FCA', 'details' => 'Lead sent to FCA System'),
                array(new History_User_Lead($lead['id']))
            );
            $event->save();

            // Lead successfully sent! The lead record ID in FCA system is returned
            return $response['data']['id'];
        } else if (!empty($response['error'])) {
            // API Request error
            $this->error = $response['error']['message'];
            return null;
        } else {
            // Generic error
            $this->error = 'An unexpected response was received from the FCA system';
            return null;
        }
    }

    /**
     * Send request to FCA system to update lead info
     * @param Backend_Lead $lead
     * @param array $values key value pair array of fields to update
     * @return bool
     */
    public function updateLead(Backend_Lead $lead, $values = array())
    {

        if (!empty($values) && is_array($values)) {
            // Build request parameters
            $params = array(
                'email'     => $lead->info('email'),
                'values'    => $values
            );

            // cURL Request
            $response = $this->executeCurlRequest('/update', $params);
            $response = json_decode($response, true);

            if (!empty($response['error'])) {
                // API Request error
                $this->error = $response['error']['message'];
                return null;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Send request to FCA system to stop calling/close lead
     * @param Backend_Lead $lead
     * @return bool
     */
    public function closeLead(Backend_Lead $lead)
    {

        // Build request parameters
        $params = array(
            'email'     => $lead->info('email'),
        );

        // cURL Request
        $response = $this->executeCurlRequest('/close', $params);
        $response = json_decode($response, true);

        if (!empty($response['error'])) {
            // API Request error
            $this->error = $response['error']['message'];
            return null;
        } else {
            try {
                // Set no calling flag
                $sent = $this->db->prepare("UPDATE `partners_firstcallagent_users` SET `no_call` = 'true' WHERE `user_id` = :user_id;");
                $sent->execute(array('user_id' => $lead->getId()));
            } catch (PDOException $e) {
                $this->log->error($e->getMessage());
            }
            return true;
        }
    }

    /**
     * Retrieve lead limit data From FCA system
     * @return array
     */
    public function leadLimit()
    {

        // Load lead limit data from cache
        $cacheIndex = 'fca.lead.limit.message';
        $cachedCode = $this->cache->getCache($cacheIndex);
        if (!is_null($cachedCode)) {
            return($cachedCode);
        }

        // Build request parameters
        $params = [
            'brokerage'     => null, // brokerage id (will look up with API key at end point if empty)
        ];

        // cURL Request
        $response = $this->executeCurlRequest('/limit', $params);
        $response = json_decode($response, true);

        // format limit message
        if (empty($response['error'])) {
            $response['data']['message'] = $this->formatLeadLimit($response['data']);
        }

        // Save limit data to cache, 5min duration
        $this->cache->setCache($cacheIndex, $response['data'], false, 300);

        return($response['data']);
    }

    /**
     * Retrieve lead limit data From FCA system
     * @return string
     */
    private function formatLeadLimit($response)
    {
        $message = '';
        $uc = !empty($response['users_count']) ? $response['users_count'] : 0;
        $la = !empty($response['limit_amount']) ? $response['limit_amount'] : 0;
        $la = $response['limit_amount'];
        $perc = 100 - (($uc / $la) * 100);
        if ($uc >= $la) {
            $message = __('The FCA lead limit has been reached (%d/%d)', $response['users_count'], $response['limit_amount']);
        } else if ($perc > 0 && $perc <= 20) { // 20% or less remaining on limit?
            $message = __('Approaching the FCA lead limit  (%d/%d)', $response['users_count'], $response['limit_amount']);
        }
        return $message;
    }

    /**
     * Get the last occurred error
     * @return string $this->error
     */
    public function getLastError()
    {

        return $this->error;
    }

    /**
     * @return array $settings
     */
    public function getSettings()
    {
        try {
            $settings = $this->db->fetch('SELECT * FROM `partners_firstcallagent` WHERE `agent_id` = :agent_id;', ['agent_id' => 1]);
            return $settings;
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->settings->getInstance()->MODULES['REW_PARTNERS_FIRSTCALLAGENT'];
    }

    /**
     * @return bool
     */
    public function hasAPIKey()
    {
        return !empty($this->api_key);
    }

    /**
     * @param Backend_Lead $lead
     * @return array $settings
     */
    public function getLeadSettings(Backend_Lead $lead)
    {
        try {
            $settings = $this->db->fetch('SELECT * FROM `partners_firstcallagent_users` WHERE `user_id` = :user_id;', ['user_id' => $lead->getId()]);
            return $settings;
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
    }

    /**
     * @param string $agent_id
     * @return bool
     */
    public function agentExcluded($agent_id)
    {
        // Ensure assigned agent is not an excluded agent
        if (in_array($agent_id, $this->exclude_agents)) {
            return true;
        }
        return false;
    }

    /**
     * @return array $criteria
     */
    protected function getCriteria()
    {
        $criteria = array();

        $user = $this->user->get();

        if (!empty($user)) {
            $last_search = $user->url_search();
            $last_search = parse_url($last_search);
            parse_str($last_search['query'], $criteria);
        } else {
            // If unable to get URL of last search, attempt to get criteria from preferences, or last viewed search

            // Search criteria info
            if (!empty($lead['search_type'])) {
                $criteria['search_type']          = $lead['search_type'];
            }
            if (!empty($lead['search_city'])) {
                $criteria['search_city']          = $lead['search_city'];
            }
            if (!empty($lead['search_subdivision'])) {
                $criteria['search_subdivision']   = $lead['search_subdivision'];
            }
            if (!empty($lead['search_minimum_price'])) {
                $criteria['search_minimum_price'] = $lead['search_minimum_price'];
            }
            if (!empty($lead['search_maximum_price'])) {
                $criteria['search_maximum_price'] = $lead['search_maximum_price'];
            }

            // Attempt to get criteria from last viewed search
            if (empty($criteria)) {
                try {
                    $viewed_search = $this->db->fetch("SELECT `criteria` FROM `users_viewed_searches` WHERE `user_id` = :user_id ORDER BY `timestamp` DESC LIMIT 1;", array('user_id' => $lead->getId()));
                    if (!empty($viewed_search['criteria'])) {
                        $criteria = unserialize($viewed_search['criteria']);
                    }
                } catch (PDOException $e) {
                    $this->log->error($e->getMessage());
                }
            }
        }

        return $criteria;
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getInquiry(Backend_Lead $lead)
    {
        try {
            // Attempt to get property inquiry info
            $inquiry_types  = str_repeat('?,', count(self::INQUIRY_TYPES) - 1) . '?';
            $sql = "SELECT `data`, `timestamp`, `form` FROM `users_forms` WHERE `user_id` = ? AND `form` IN ($inquiry_types) ORDER BY `timestamp` DESC LIMIT 1;";
            $inquiry_query = $this->db->prepare($sql);
            $params = array_merge([$lead->getId()], self::INQUIRY_TYPES);
            $inquiry_query->execute($params);
            $inquiry_data = $inquiry_query->fetch();
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }

        if (!empty($inquiry_data)) {
            // Get inquiry comments
            $data = unserialize($inquiry_data['data']);
            if ($data['form'] == 'IDX Inquiry') {
                $comments = $data['comments'];
            } else if ($data['form'] == 'Quick Showing') {
                $comments = $data['showing']['comments'];
            } else if ($data['form'] == 'Quick Inquire') {
                $comments = $data['inquire']['comments'];
            } else {
                $comments = '';
            }

            return array(
                'comments' => $comments,
                'timestamp' => $data['timestamp']
            );
        }
    }

    /**
     * @param array $data
     * @return array $return_data
     */
    protected function formatCriteria($data)
    {
        $return_data = array();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $key = trim($key);
                $value = is_array($value) ? implode(', ', $value) : $value;
                $return_data[$key] = trim($value);
            }
        }
        return $return_data;
    }

    /**
     * @param string $url
     * @param array $params
     * @param int $type
     * @return string
     */
    protected function executeCurlRequest($url, $params, $type = Util_Curl::REQUEST_TYPE_POST)
    {
        return Util_Curl::executeRequest($url, $params, $type, [
            CURLOPT_HTTPHEADER => [sprintf('X-REW-API-Key: %s', $this->api_key)]
        ]);
    }
}
