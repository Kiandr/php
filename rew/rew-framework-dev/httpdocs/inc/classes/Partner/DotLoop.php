<?php

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\Exceptions\MissingId\MissingLeadException;

/**
 * Partner Integration: DotLoop
 */
class Partner_DotLoop
{

    /**
     * DotLoop Loops: Transaction Types
     * @var array TRANSACTION_TYPES
     */
    const TRANSACTION_TYPES = [
        'LEASE_OFFER',
        'LISTING_FOR_SALE',
        'LISTING_FOR_LEASE',
        // Removed "OTHER" Type - Caused too many API issues
//         'OTHER',
        'PURCHASE_OFFER',
        'REAL_ESTATE_OTHER',
    ];

    /**
     * DotLoop Loops: Statuses
     * @var array STATUSES
     */
    const STATUSES = [
        'LEASE_OFFER'       => ['LEASED', 'PRE_OFFER', 'UNDER_CONTRACT'],
        'LISTING_FOR_SALE'  => ['ACTIVE_LISTING', 'PRE_LISTING', 'PRIVATE_LISTING', 'SOLD', 'UNDER_CONTRACT'],
        'LISTING_FOR_LEASE' => ['ACTIVE_LISTING', 'LEASED', 'PRE_LISTING', 'PRIVATE_LISTING', 'UNDER_CONTRACT'],
        // Removed "OTHER" Type - Caused too many API issues
//         'OTHER'             => ['DONE', 'IN_PROGRESS', 'NEW'],
        'PURCHASE_OFFER'    => ['PRE_OFFER', 'SOLD', 'UNDER_CONTRACT'],
        'REAL_ESTATE_OTHER' => ['DONE', 'IN_PROGRESS', 'NEW'],
    ];

    /**
     * DotLoop Loops: Statuses That Mark a Loop as Deleted in DotLoop's System
     * @var array DELETION_STATUSES
     */
    const DELETION_STATUSES = ['ARCHIVED'];

    /**
     * DotLoop Loop: Participants Types
     * @var array PARTICIPANT_TYPES
     */
    const PARTICIPANT_TYPES = [
        'ADMIN', 'APPRAISER', 'ATTORNEY', 'BUYER_ATTORNEY', 'BUYER', 'BUYING_AGENT', 'BUYING_BROKER', 'ESCROW_TITLE_REP', 'HOME_IMPROVEMENT_SPECIALIST',
        'HOME_INSPECTOR', 'HOME_SECURITY_PROVIDER', 'HOME_WARRANTY_REP', 'INSPECTOR', 'INSURANCE_REP', 'LANDLORD', 'LISTING_AGENT', 'LISTING_BROKER',
        'LOAN_OFFICER', 'LOAN_PROCESSOR', 'MANAGING_BROKER', 'MORTGAGE', 'MOVING_STORAGE', 'OTHER_HOME_SERVICES', 'OTHER', 'PROPERTY_MANAGER', 'REALTOR',
        'RELOCATION_REP', 'SELLER_ATTORNEY', 'SELLER', 'TENANT', 'TRANSACTION_COORDINATOR', 'UTILITIES_PROVIDER'
    ];

    /**
     * REW's Application Key
     * @var string REW_APP_KEY
     */
    const REW_APP_KEY = 'dd04ac86-fac1-4b31-a1e3-63ca2eb931f6';

    /**
     * REW's Application Secret
     * @var string REW_SECRET_KEY
     */
    const REW_SECRET_KEY = '9cba65e7-4e4d-4ba2-978f-fd85a32865c9';

    /**
     * DotLoop API - Auth Endpoint
     * @var string API_URL_AUTH
     */
    const API_URL_AUTH = 'https://auth.dotloop.com/oauth/token';

    /**
     * DotLoop API - V2 Endpoint
     * @var string API_URL_V2
     */
    const API_URL_V2 = 'https://api-gateway.dotloop.com/public/v2';

    /**
     * DotLoop API Error Codes
     * @var array API_ERRORS
     */
    const API_ERRORS = [
        'RATE_LIMIT_EXCEEDED' => 429
    ];

    /**
     * DotLoop Tables - Accounts
     * @var string TABLE_ACCOUNTS
     */
    const TABLE_ACCOUNTS = 'partners_dotloop_accounts';

    /**
     * DotLoop Tables - Profiles
     * @var string TABLE_PROFILES
     */
    const TABLE_PROFILES = 'partners_dotloop_profiles';

    /**
     * DotLoop Tables - Loops
     * @var string TABLE_LOOPS
     */
    const TABLE_LOOPS = 'partners_dotloop_loops';

    /**
     * DotLoop Tables - Participants
     * @var string TABLE_PARTICIPANTS
     */
    const TABLE_PARTICIPANTS = 'partners_dotloop_participants';

    /**
     * DotLoop Tables - Lead Connections
     * @var string TABLE_CONNECTED_LEADS
     */
    const TABLE_CONNECTED_LEADS = 'partners_dotloop_connected_users';

    /**
     * DotLoop Tables - System
     * @var string TABLE_SYSTEM
     */
    const TABLE_SYSTEM = 'partners_dotloop_system';

    /**
     * DotLoop Tables - Delayed Syncs
     * @var string TABLE_DELAYED_SYNCS
     */
    const TABLE_DELAYED_SYNCS = 'partners_dotloop_delayed_syncs';

    /**
     * DotLoop Access Token (Expires: 12 Hours)
     * @var string
     */
    private $_access_token;

    /**
     * @var Backend_Agent
     */
    private $agent;

    /**
     * @var DBInterface
     */
    private $db;

    /**
     * Last API Error
     * @var string
     */
    private $_error;

    /**
     * Last API Response
     * @var array
     */
    private $_last_response;

    /**
     * Rate Limit Status Info
     * @var array
     */
    private $_rate_limit;

    /**
     * Create a new partner instance
     *
     * @param Backend_Agent $agent
     * @param DbInterface   $db
     *
     * ## Example Usage ##
     * $agent = Backend_Agent::load(123);
     * $api = new Partner_DotLoop($agent, $db);
     * if ($api->validateAPIAccess()) {
     *     // Start Making API Calls
     * } else {
     *     // Access token is either unavailable or expired
     *     // See: $api->requestAccessTokens() || $api->updateAccessTokens()
     *     // Use: REW\Core\Interfaces\AuthInterface::get()->info('partners.dotloop.refresh_token') to update expired access tokens
     *     // ^^ If refresh token is unavailable: Integration must be re-enabled
     * }
     */
    public function __construct(
        Backend_Agent $agent,
        DBInterface $db
    ) {
        $this->agent = $agent;
        $this->db = $db;
    }

    /**
     * Get the DotLoop Access Token
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->_access_token;
    }

    /**
     * Set the DotLoop Access Token
     *
     * @param string $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->_access_token = $access_token;
    }

    /**
     * Check the Validity of the Access Token + Set it Up
     *
     * @return bool
     */
    public function validateAPIAccess()
    {
        // Update Stored Tokens
        if ($agent_partners = json_decode($this->agent->info('partners'), true)) {
            if (!empty($agent_partners['dotloop']['access_token'])) {
                $this->setAccessToken($agent_partners['dotloop']['access_token']);
            }
        }
        if (empty($this->getProfiles())) {
            $this->_error = 'Failed to validate DotLoop API access.';
            return false;
        }
        return true;
    }

    /**
     * [Request an Access Token || Refresh an Exsisting Token] to use in API Call Authentication
     *
     * @param string $key_or_token (User App Key for New Tokens || Refresh Token for Existing)
     * @param bool   $refresh
     * @return array
     */
    public function requestAccessTokens($key_or_token, $refresh = false)
    {
        // Refresh Existing Token
        if ($refresh) {
            $params = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $key_or_token,
            ];
        // Request a New set of Tokens
        } else {
            $params = [
                'grant_type' => 'authorization_code',
                'code' => $key_or_token,
            ];
        }
        // Response will contain 'access_token' and 'refresh_token'
        $tokens = $this->performAuthRequest($params);
        return $tokens;
    }

    /**
     * Attempt to refresh an Existing Access Token (Expiry time = 12 hours)
     *
     * @param string $refresh_token
     * @return void
     */
    public function updateAccessTokens($refresh_token)
    {
        $this->setAccessToken(null);
        $new_tokens = $this->requestAccessTokens($refresh_token, true);
        if (!empty($new_tokens)) {
            // Update Specific Partner Values
            if ($partners = json_decode($this->agent->info('partners'), true)) {
                $partners['dotloop']['access_token'] = $new_tokens['access_token'];
                $partners['dotloop']['refresh_token'] = $new_tokens['refresh_token'];
                $partners['dotloop']['token_updated'] = date('Y-m-d h:i:s', time());
                try {
                    $query = $this->db->prepare("UPDATE `agents` SET `partners` = :partners WHERE `id` = :id;");
                    if ($query->execute([
                        'partners' => json_encode($partners),
                        'id' => $this->agent->info('id'),
                    ])) {
                        $this->setAccessToken($new_tokens['access_token']);
                    }
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }
            } else {
                $this->_error = 'Failed to update access tokens, could not retrieve existing token.';
            }
        }
    }

    /**
     * Get the active account's basic info from DotLoop
     *
     * @return mixed null|array
     */
    public function getAccountInfo()
    {
        $response = $this->simpleAPIRequest(__FUNCTION__);
        if (!empty($response['data'])) {
            return $response['data'];
        } else if (!empty($response['errors'])) {
            $this->_error = sprintf('Failed to pull account info from DotLoop API: %s', $response['errors'][0]['detail']);
        } else if (!isset($response['data'])) {
            $this->_error = 'Failed to pull account info from DotLoop API.';
        }
        return null;
    }

    /**
     * Get the profiles from the DotLoop account
     *
     * @return mixed null|array
     */
    public function getProfiles()
    {
        $response = $this->simpleAPIRequest(__FUNCTION__);
        if (!empty($response['data'])) {
            return $response['data'];
        } else if (!empty($response['errors'])) {
            $this->_error = sprintf('Failed to pull profile list from DotLoop API: %s', $response['errors'][0]['detail']);
        } else if (!isset($response['data'])) {
            $this->_error = 'Failed to pull profile list from DotLoop API.';
        }
        return null;
    }

    /**
     * Get the Lead's DotLoop Data
     *
     * @param int $dotloop_contact_id
     * @return mixed null|array
     */
    public function getContact($dotloop_contact_id)
    {
        $dotloop_contact_id = ($dotloop_contact_id > 0) ? $dotloop_contact_id : null;
        if (!empty($dotloop_contact_id)) {
            $response = $this->simpleAPIRequest(__FUNCTION__, ['dotloop_contact_id' => $dotloop_contact_id]);
            // Check if lead has been previously connected to DotLoop
            if (!empty($response['data'])) {
                return $response['data'];
            } else if (!empty($response['errors'])) {
                $this->_error = sprintf('Failed to pull contact information from DotLoop API: %s', $response['errors'][0]['detail']);
            } else if (!isset($response['data'])) {
                $this->_error = 'Failed to pull contact information from DotLoop API.';
            }
        }
        return null;
    }

    /**
     * Push a Lead to DotLoop
     *
     * @param int $lead_id
     * @throws REW\Backend\Exceptions\MissingId\MissingLeadException
     * @return mixed int|null
     */
    public function pushContact($lead_id)
    {
        $lead_id = ($lead_id > 0) ? $lead_id : null;
        $response = $this->simpleAPIRequest(__FUNCTION__, ['lead_id' => $lead_id]);
        if (!empty($response['data'])) {
            try {
                if ($partners = json_decode($this->agent->info('partners'), true)) {
                    $query = $this->db->prepare(sprintf("REPLACE INTO `%s` "
                        . "SET `user_id` = :user_id, "
                        . "`dotloop_account_id` = :dotloop_account_id, "
                        . "`dotloop_contact_id` = :dotloop_contact_id "
                        . ";", self::TABLE_CONNECTED_LEADS));
                    if ($query->execute([
                        'user_id' => $lead_id,
                        'dotloop_account_id' => $partners['dotloop']['account_id'],
                        'dotloop_contact_id' => $response['data']['id']
                    ])) {
                        // Return Lead's DotLoop ID
                        return $response['data']['id'];
                    }
                } else {
                    $this->_error = 'Failed to push contact to DotLoop, could not retrieve DotLoop Integration settings.';
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        } else if (!empty($response['errors'])) {
            $this->_error = sprintf('Failed to push contact through DotLoop API: %s', $response['errors'][0]['detail']);
        } else {
            $this->_error = 'Failed to push contact through DotLoop API.';
        }
        return null;
    }

    /**
     * Get all Loops from the DotLoop Account/Profiles
     *
     * @param array $options
     * @return array
     */
    public function getProfilesLoops($options = [])
    {
        $profiles = $this->getProfiles();
        if (!empty($profiles) && is_array($profiles)) {
            foreach ($profiles as $profile) {
                $response = $this->simpleAPIRequest(__FUNCTION__, array_merge(['dotloop_profile_id' => $profile['id']], $options));
                if (!empty($response['data'])) {
                    $loops[] = [
                        'id' => $profile['id'],
                        'name' => $profile['name'],
                        'loops' => $response['data']
                    ];
                } else if (!empty($response['errors'])) {
                    $this->_error = sprintf('Failed to retrieve profile loops from DotLoop API: %s', $response['errors'][0]['detail']);
                } else if (!isset($response['data'])) {
                    $this->_error = 'Failed to retrieve profile loops from DotLoop API.';
                }
            }
        }
        return $loops ?: [];
    }

    /**
     * Get a Specific Loop by DotLoop ID
     *
     * @param int $dotloop_profile_id
     * @param int $dotloop_loop_id
     * @param string $force_function (optional)
     * @return mixed null|array
     */
    public function getLoop($dotloop_profile_id, $dotloop_loop_id, $force_endpoint_func = null)
    {
        $response = $this->simpleAPIRequest(($force_endpoint_func ?: __FUNCTION__), [
            'dotloop_profile_id' => $dotloop_profile_id,
            'dotloop_loop_id' => $dotloop_loop_id
        ]);
        if (!empty($response['data'])) {
            return $response['data'];
        } else if (!empty($response['errors'])) {
            $this->_error = sprintf('Failed to retrieve loop data from DotLoop API: %s', $response['errors'][0]['detail']);
        } else if (!isset($response['data'])) {
            $this->_error = 'Failed to retrieve loop data from DotLoop API.';
        }
        return null;
    }

    /**
     * Get a Loop's Advanced Details
     *
     * @param int $dotloop_profile_id
     * @param int $dotloop_loop_id
     * @return mixed null|array
     */
    public function getLoopDetails($dotloop_profile_id, $dotloop_loop_id)
    {
        return $this->getLoop($dotloop_profile_id, $dotloop_loop_id, __FUNCTION__);
    }

    /**
     * Get a Loop's Task Lists
     *
     * @param int $dotloop_profile_id
     * @param int $dotloop_loop_id
     * @return mixed null|array
     */
    public function getLoopTaskLists($dotloop_profile_id, $dotloop_loop_id)
    {
        return $this->getLoop($dotloop_profile_id, $dotloop_loop_id, __FUNCTION__);
    }

    /**
     * Get a Loop's Activity History
     *
     * @param int $dotloop_profile_id
     * @param int $dotloop_loop_id
     * @return mixed null|array
     */
    public function getLoopActivities($dotloop_profile_id, $dotloop_loop_id)
    {
        return $this->getLoop($dotloop_profile_id, $dotloop_loop_id, __FUNCTION__);
    }

    /**
     * Get all Templates from the DotLoop Account/Profiles
     *
     * @return array
     */
    public function getProfilesTemplates()
    {
        $profiles = $this->getProfiles();
        if (!empty($profiles) && is_array($profiles)) {
            foreach ($profiles as $profile) {
                $response = $this->simpleAPIRequest(__FUNCTION__, ['dotloop_profile_id' => $profile['id']]);
                if (!empty($response['data'])) {
                    $templates[] = [
                        'id' => $profile['id'],
                        'name' => $profile['name'],
                        'templates' => $response['data']
                    ];
                } else if (!empty($response['errors'])) {
                    $this->_error = sprintf('Failed to retrieve profile templates from DotLoop API: %s', $response['errors'][0]['detail']);
                } else if (!isset($response['data'])) {
                    $this->_error = 'Failed to retrieve profile templates from DotLoop API.';
                }
            }
        }
        return $templates ?: [];
    }

    /**
     * Push a New Loop to DotLoop
     *
     * @param int $dotloop_profile_id
     * @param string $loop_name
     * @param string $loop_transaction_type
     * @param string $loop_status
     * @param int $loop_template_id (optional)
     * @return int
     */
    public function pushLoop($dotloop_profile_id, $loop_name, $loop_transaction_type, $loop_status, $loop_template_id = null)
    {
        $response = $this->simpleAPIRequest(__FUNCTION__, [
            'dotloop_profile_id' => $dotloop_profile_id,
            'loop_name' => $loop_name,
            'loop_transaction_type' => $loop_transaction_type,
            'loop_status' => $loop_status,
            'loop_template' => $loop_template_id
        ]);
        if (!empty($response['data'])) {
            // Store Pushed Data in Local DotLoop DB
            try {
                $local_profile = $this->db->fetch(sprintf(
                    "SELECT `id` FROM `%s` WHERE `dotloop_profile_id` = :dotloop_profile_id LIMIT 1;",
                    self::TABLE_PROFILES
                ), [
                    'dotloop_profile_id' => $dotloop_profile_id
                ]);
                if (!empty($local_profile['id'])) {
                    // Store the loop as a local DB row
                    $new_loop = $this->updateLocalLoopRecord([
                        'id' => $response['data']['id'],
                        'name' => $response['data']['name'],
                        'transactionType' => $response['data']['transactionType'],
                        'status' => $response['data']['status'],
                        'created' => $response['data']['created'],
                        'updated' => $response['data']['updated']
                    ], $local_profile['id']);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
            // Flag for Delayed Sync if Local Record Creation Failed
            if (empty($new_loop)) {
                if ($agent_partners = json_decode($this->agent->info('partners'), true)) {
                    $this->flagDelayedLoopSync($agent_partners['dotloop']['account_id'], $dotloop_profile_id, $response['data']['id']);
                    $this->_error = 'Failed to store a local copy of the new loop. Changes made should be reflected in the CRM upon next scheduled (hourly) data sync.';
                } else {
                    $this->_error = 'Failed to store a local copy of the new loop.';
                }
            }
        } else if (!empty($response['errors'])) {
            $this->_error = sprintf('Failed to push new loop through DotLoop API: %s', $response['errors'][0]['detail']);
        } else {
            $this->_error = 'Failed to push new loop through DotLoop API.';
        }
        return (!empty($response['data']['id'])) ? intval($response['data']['id']) : 0;
    }

    /**
     * Assign Lead to an Existing Loop
     *
     * @param int $dotloop_profile_id
     * @param int $lead_id
     * @param string $contact_type
     * @param int $dotloop_loop_id
     * @throws REW\Backend\Exceptions\MissingId\MissingLeadException
     * @return int
     */
    public function pushLoopParticipant($dotloop_profile_id, $lead_id, $contact_type, $dotloop_loop_id)
    {
        $response = $this->simpleAPIRequest(__FUNCTION__, [
            'dotloop_profile_id' => $dotloop_profile_id,
            'lead_id' => $lead_id,
            'contact_type' => $contact_type,
            'dotloop_loop_id' => $dotloop_loop_id
        ]);
        if (!empty($response['data'])) {
            // Store Pushed Data in Local DotLoop DB
            try {
                $local_profile = $this->db->fetch(sprintf(
                    "SELECT `id` FROM `%s` WHERE `dotloop_profile_id` = :dotloop_profile_id LIMIT 1;",
                    self::TABLE_PROFILES
                ), [
                    'dotloop_profile_id' => $dotloop_profile_id
                ]);
                if (!empty($local_profile['id'])) {
                    // Attempt to retrieve loop info for immediate storage
                    $loop_details = [];
                    if (null !== ($loop_response = $this->getLoop($dotloop_profile_id, $dotloop_loop_id))) {
                        $loop_details = [
                            'name'               => $loop_response['name'],
                            'transactionType'    => $loop_response['transactionType'],
                            'status'             => $loop_response['status'],
                            'totalTaskCount'     => $loop_response['totalTaskCount'],
                            'completedTaskCount' => $loop_response['completedTaskCount'],
                            'created'            => $loop_response['created'],
                            'updated'            => $loop_response['updated'],
                        ];
                    }
                    // Make sure we have a local loop row - Doesn't need to contain data just needs to exist for FK restraints
                    $local_loop_id = $this->updateLocalLoopRecord(array_merge(['id' => $dotloop_loop_id], $loop_details), $local_profile['id']);
                    // Create the local participant record
                    if (!empty($local_loop_id)) {
                        $new_participant = $this->updateLocalParticipantRecord($response['data'], $local_loop_id);
                    }
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
            // Flag for Delayed Sync if Local Record Creation Failed
            if (empty($new_participant)) {
                if ($agent_partners = json_decode($this->agent->info('partners'), true)) {
                    $this->flagDelayedLoopSync($agent_partners['dotloop']['account_id'], $dotloop_profile_id, $dotloop_loop_id);
                    $this->_error = 'Failed to store a local record of the loop assignment. Changes made should be reflected in the CRM upon next scheduled (hourly) data sync.';
                } else {
                    $this->_error = 'Failed to store a local record of the loop assignment.';
                }
            }
        } else if (!empty($response['errors'])) {
            $this->_error = sprintf('Failed to push new loop participant through DotLoop API: %s', $response['errors'][0]['detail']);
        } else {
            $this->_error = 'Failed to push new loop participant through DotLoop API.';
        }
        return (!empty($response['data']['id'])) ? intval($response['data']['id']) : 0;
    }

    /**
     * Push a New Loop to DotLoop + Assign a Participant to it
     *
     * @param int    $dotloop_profile_id
     * @param int    $lead_id
     * @param string $contact_type
     * @param string $loop_name
     * @param string $loop_transaction_type
     * @param string $loop_status
     * @param int    $loop_template_id (optional)
     * @throws REW\Backend\Exceptions\MissingId\MissingLeadException
     * @return bool
     */
    public function pushLoopAndParticipant($dotloop_profile_id, $lead_id, $contact_type, $loop_name, $loop_transaction_type, $loop_status, $loop_template_id = null)
    {
        // Attempt to Push the Loop
        if (($dotloop_loop_id = $this->pushLoop($dotloop_profile_id, $loop_name, $loop_transaction_type, $loop_status, $loop_template_id)) > 0) {
            // Attempt to Push the Participant
            return ($this->pushLoopParticipant($dotloop_profile_id, $lead_id, $contact_type, $dotloop_loop_id) > 0);
        }
        return false;
    }

    /**
     * Insert loop record into local DB
     *
     * @var array $loop_data [
     *     int $id,
     *     (optional) string $name,
     *     (optional) string $transactionType,
     *     (optional) string $status,
     *     (optional) timestamp $created,
     *     (optional) timestamp updated,
     * ]
     * @var int $profile_id
     * @throws PDOException
     * @return int
     */
    public function updateLocalLoopRecord($loop_data, $local_profile_id)
    {
        // Set up query parameters
        $params = [
            'local_profile_id' => $local_profile_id,
            'dotloop_loop_id' => $loop_data['id']
        ];
        if (!empty($loop_data['name'])) {
            $params['name']                 = $loop_data['name'];
        }
        if (!empty($loop_data['transactionType'])) {
            $params['type']                 = $loop_data['transactionType'];
        }
        if (!empty($loop_data['status'])) {
            $params['status']               = $loop_data['status'];
        }
        if (!empty($loop_data['totalTaskCount'])) {
            $params['total_task_count']     = $loop_data['totalTaskCount'];
        }
        if (!empty($loop_data['completedTaskCount'])) {
            $params['completed_task_count'] = $loop_data['completedTaskCount'];
        }
        if (!empty($loop_data['created'])) {
            $params['created']              = date('Y-m-d h:i:s', strtotime($loop_data['created']));
        }
        if (!empty($loop_data['updated'])) {
            $params['updated']              = date('Y-m-d h:i:s', strtotime($loop_data['updated']));
        }
        // Insert or Update local loop record
        $insert = $this->db->prepare(sprintf(
            "INSERT INTO `%s` SET "
             . " `profile_id` = :local_profile_id, "
             . (!empty($params['name']) ? " `name` = :name, " : "")
             . (!empty($params['type']) ? " `transaction_type` = :type, " : "")
             . (!empty($params['status']) ? " `status` = :status, " : "")
             . (!empty($params['total_task_count']) ? " `total_task_count` = :total_task_count, " : "")
             . (!empty($params['completed_task_count']) ? " `completed_task_count` = :completed_task_count, " : "")
             . (!empty($params['created']) ? " `dotloop_created_timestamp` = :created, " : "")
             . (!empty($params['updated']) ? " `dotloop_updated_timestamp` = :updated, " : "")
             . " `dotloop_loop_id` = :dotloop_loop_id "
            . " ON DUPLICATE KEY UPDATE "
             . " `profile_id` = :local_profile_id, "
             . (!empty($params['name']) ? " `name` = :name, " : "")
             . (!empty($params['type']) ? " `transaction_type` = :type, " : "")
             . (!empty($params['status']) ? " `status` = :status, " : "")
             . (!empty($params['total_task_count']) ? " `total_task_count` = :total_task_count, " : "")
             . (!empty($params['completed_task_count']) ? " `completed_task_count` = :completed_task_count, " : "")
             . (!empty($params['created']) ? " `dotloop_created_timestamp` = :created, " : "")
             . (!empty($params['updated']) ? " `dotloop_updated_timestamp` = :updated, " : "")
             . " `dotloop_loop_id` = :dotloop_loop_id, "
             . " `timestamp_updated` = NOW() "
            . ";",
            self::TABLE_LOOPS
        ));
        if ($insert->execute($params)) {
            // Get ID of inserted/updated row
            $last_effected = $this->db->fetch(sprintf(
                "SELECT `id` FROM `%s` "
                . " WHERE `profile_id` = :local_profile_id "
                . " AND `dotloop_loop_id` = :dotloop_loop_id "
                . " LIMIT 1 "
                . ";",
                self::TABLE_LOOPS
            ), [
                'local_profile_id' => $local_profile_id,
                'dotloop_loop_id' => $loop_data['id']
            ]);
        }
        return $last_effected['id'] ?: 0;
    }

    /**
     * Insert loop participant record into local DB
     *
     * @var array $participant_data [
     *     int $id,
     *     string $fullName,
     *     string $email,
     *     string $role
     * ]
     * @var int $local_loop_id
     * @throws PDOException
     * @return int
     */
    public function updateLocalParticipantRecord($participant_data, $local_loop_id)
    {
        $params = [
            'local_loop_id' => $local_loop_id,
            'dotloop_participant_id' => $participant_data['id']
        ];
        if (!empty($participant_data['fullName'])) {
            $params['name']  = $participant_data['fullName'];
        }
        if (!empty($participant_data['email'])) {
            $params['email'] = $participant_data['email'];
        }
        if (!empty($participant_data['role'])) {
            $params['role']  = $participant_data['role'];
        }
        $insert = $this->db->prepare(sprintf(
            "INSERT INTO `%s` SET "
             . " `loop_id` = :local_loop_id, "
             . (!empty($params['name']) ? " `full_name` = :name, " : "")
             . (!empty($params['email']) ? " `email` = :email, " : "")
             . (!empty($params['role']) ? " `role` = :role, " : "")
             . " `dotloop_participant_id` = :dotloop_participant_id "
            . " ON DUPLICATE KEY UPDATE "
             . " `loop_id` = :local_loop_id, "
             . (!empty($params['name']) ? " `full_name` = :name, " : "")
             . (!empty($params['email']) ? " `email` = :email, " : "")
             . (!empty($params['role']) ? " `role` = :role, " : "")
             . " `dotloop_participant_id` = :dotloop_participant_id, "
             . " `removed_from_loop` = 'false', "
             . " `timestamp_updated` = NOW() "
            . ";",
            self::TABLE_PARTICIPANTS
        ));
        if ($insert->execute($params)) {
            // Get ID of inserted/updated row
            $last_effected = $this->db->fetch(sprintf(
                "SELECT `id` FROM `%s` "
                . " WHERE `loop_id` = :local_loop_id "
                . " AND `dotloop_participant_id` = :dotloop_participant_id "
                . " LIMIT 1 "
                . ";",
                self::TABLE_PARTICIPANTS
            ), [
                'local_loop_id' => $local_loop_id,
                'dotloop_participant_id' => $participant_data['id']
            ]);
        }
        return $last_effected['id'] ?: 0;
    }

    /**
     * Due to a bug on DotLoop's end - loops pushed through the API aren't tracked with proper timestamps, so the sync script won't
     * recognize and update them if we failed to store them during the push.
     * This flags the pushed loop to be force-updated on next sync.
     *
     * @param int $dotloop_account_id
     * @param int $dotloop_profile_id
     * @param int $dotloop_loop_id
     * @return void
     */
    protected function flagDelayedLoopSync($dotloop_account_id, $dotloop_profile_id, $dotloop_loop_id)
    {
        try {
            $delayed_sync = $this->db->prepare(sprintf(
                "INSERT IGNORE INTO `%s` SET "
                . " `dotloop_account_id` = :dotloop_account_id, "
                . " `dotloop_profile_id` = :dotloop_profile_id, "
                . " `dotloop_loop_id` = :dotloop_loop_id "
                . ";",
                self::TABLE_DELAYED_SYNCS
            ));
            $delayed_sync->execute([
                'dotloop_account_id' => $dotloop_account_id,
                'dotloop_profile_id' => $dotloop_profile_id,
                'dotloop_loop_id' => $dotloop_loop_id
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Get Participants from a Specific Loop
     *
     * @param int $dotloop_profile_id
     * @param int $dotloop_loop_id
     * @return mixed null|array
     */
    public function getLoopParticipants($dotloop_profile_id, $dotloop_loop_id)
    {
        $response = $this->simpleAPIRequest(__FUNCTION__, ['dotloop_profile_id' => $dotloop_profile_id, 'dotloop_loop_id' => $dotloop_loop_id]);
        return $response ?: null;
    }

    /**
     * Get the Specified Lead's DotLoop Connection Data from the Current Active Account
     *
     * @param int $lead_id
     * @return mixed int|null
     */
    public function getLeadConnectData($lead_id)
    {
        $lead_id = ($lead_id > 0) ? $lead_id : null;
        if (!empty($lead_id)) {
            if ($partners = json_decode($this->agent->info('partners'), true)) {
                $account_id = $partners['dotloop']['account_id'];
            }
            if (!empty($account_id)) {
                try {
                    $query = $this->db->prepare(sprintf("SELECT `user_id`, `dotloop_account_id`, `dotloop_contact_id`, `timestamp_connected` FROM `%s` "
                        . " WHERE `user_id` = :user_id "
                        . " AND `dotloop_account_id` = :dotloop_account_id "
                        . ";", self::TABLE_CONNECTED_LEADS));
                    if ($query->execute([
                        'user_id' => $lead_id,
                        'dotloop_account_id' => $account_id
                    ])) {
                        $user_dl_connect = $query->fetch();
                        if (!empty($user_dl_connect)) {
                            return $user_dl_connect;
                        } else {
                            $this->_errors = 'Failed to retrieve lead\'s DotLoop ID.';
                        }
                    }
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        } else {
            $this->_errors = 'Failed to retrieve lead\'s DotLoop ID - no lead ID provided.';
        }
        return null;
    }

    /**
     * Set the current rate limit status
     *
     * @param int $limit
     * @param int $remaining
     * @param int $reset_countdown
     * @return void
     */
    protected function setRateLimitStatus($limit, $remaining, $reset_countdown)
    {
        if (is_numeric($limit) && is_numeric($remaining) && is_numeric($reset_countdown)) {
            $this->_rate_limit = [
                'limit' => $limit,
                'remaining' => $remaining,
                'reset_countdown' => $reset_countdown,
                'current_ts' => time()
            ];
        } else {
            $this->_rate_limit = [];
        }
    }

    /**
     * Get the current rate limit status - based on the last API call's return headers
     *
     * @return mixed null|array {
     *   $limit (int),
     *   $remaining (int),
     *   $reset_countdown (int)[seconds],
     *   $current_ts (int)[unix timestamp]
     * }
     */
    public function getRateLimitStatus()
    {
        return (!empty($this->_rate_limit) && is_array($this->_rate_limit)) ? $this->_rate_limit : null;
    }

    /**
     * Get the Loops That the Lead is Assigned to in the DotLoop Account/Profile
     *
     * @param string $lead_email
     * @param int $dotloop_account_id
     * @param bool $show_archived
     * @return array
     */
    public function getLocalAssignedLoops($lead_email, $dotloop_account_id, $show_archived = false)
    {
        try {
            $query = $this->db->prepare(sprintf(
                "SELECT `l`.*, `pro`.`name` as `profile_name`, `par`.`role` AS `participant_role` FROM `%s` `l` "
                . " LEFT JOIN `%s` `par` ON `par`.`loop_id` = `l`.`id` "
                . " LEFT JOIN `%s` `pro` ON `pro`.`id` = `l`.`profile_id` "
                . " LEFT JOIN `%s` `a` ON `a`.`id` = `pro`.`account_id` "
                . " WHERE `par`.`email` = :email "
                . " AND `par`.`removed_from_loop` != 'true' "
                . " AND `a`.`dotloop_account_id` = :dotloop_account_id "
                . ((!$show_archived)
                    ? " AND `l`.`status` != 'ARCHIVED' "
                    : ""
                )
                . " ORDER BY `pro`.`name` ASC, `l`.`dotloop_updated_timestamp` DESC "
                . ";",
                self::TABLE_LOOPS,
                self::TABLE_PARTICIPANTS,
                self::TABLE_PROFILES,
                self::TABLE_ACCOUNTS
            ));
            $query->execute([
                'email' => $lead_email,
                'dotloop_account_id' => $dotloop_account_id
            ]);
            $profile_groups = [];
            $assigned_loops = $query->fetchAll();
            foreach ($assigned_loops as $assigned_loop) {
                $profile_groups[$assigned_loop['profile_name']][] = $assigned_loop;
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return (!empty($profile_groups)) ? $profile_groups : [];
    }

    /**
     * Pull Loop Info from the Local DB Records
     *
     * @param int $dotloop_account_id
     * @param int $local_loop_id
     * @param string $lead_email
     * @return array
     */
    public function getLocalLoopInfo($dotloop_account_id, $local_loop_id, $lead_email)
    {
        try {
            $get_loop = $this->db->prepare(sprintf(
                "SELECT `l`.*, `pro`.`dotloop_profile_id`, `pro`.`name` AS `profile_name` FROM `%s` `par` "
                . " LEFT JOIN `%s` `l` ON `l`.`id` = `par`.`loop_id` "
                . " LEFT JOIN `%s` `pro` ON `pro`.`id` = `l`.`profile_id` "
                . " LEFT JOIN `%s` `a` ON `a`.`id` = `pro`.`account_id` "
                . " WHERE `l`.`id` = :local_loop_id "
                . " AND `a`.`dotloop_account_id` = :dotloop_account_id "
                . " AND `par`.`email` = :email "
                . " ORDER BY `par`.`timestamp_updated` DESC "
                . " LIMIT 1 "
                . ";",
                self::TABLE_PARTICIPANTS,
                self::TABLE_LOOPS,
                self::TABLE_PROFILES,
                self::TABLE_ACCOUNTS
            ));
            $get_loop->execute([
                'local_loop_id' => $local_loop_id,
                'dotloop_account_id' => $dotloop_account_id,
                'email' => $lead_email
            ]);
            $loop = $get_loop->fetch();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return (!empty($loop) && is_array($loop)) ? $loop : [];
    }

    /**
     * Pull Loop Participants from the Local DB Records
     *
     * @param int $dotloop_account_id
     * @param int $local_loop_id
     * @return array
     */
    public function getLocalLoopParticipants($dotloop_account_id, $local_loop_id)
    {
        try {
            $get_participants = $this->db->prepare(sprintf(
                "SELECT `par`.* FROM `%s` `par` "
                . " LEFT JOIN `%s` `l` ON `l`.`id` = `par`.`loop_id` "
                . " LEFT JOIN `%s` `pro` ON `pro`.`id` = `l`.`profile_id` "
                . " LEFT JOIN `%s` `a` ON `a`.`id` = `pro`.`account_id` "
                . " WHERE `l`.`id` = :local_loop_id "
                . " AND `a`.`dotloop_account_id` = :dotloop_account_id "
                . " AND `par`.`removed_from_loop` != 'true' "
                . " ORDER BY `par`.`full_name` ASC "
                . ";",
                self::TABLE_PARTICIPANTS,
                self::TABLE_LOOPS,
                self::TABLE_PROFILES,
                self::TABLE_ACCOUNTS
            ));
            $get_participants->execute([
                'local_loop_id' => $local_loop_id,
                'dotloop_account_id' => $dotloop_account_id
            ]);
            $participants = $get_participants->fetchAll();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return (!empty($participants) && is_array($participants)) ? $participants : [];
    }

    /**
     * Build an API request based on the target endpoint and specified parameters
     *
     * @param string $api_method
     * @param array  $options
     * @throws REW\Backend\Exceptions\MissingId\MissingLeadException (If $options['lead_id'] is invalid)
     * @return array
     */
    protected function simpleAPIRequest($api_method, $options = [])
    {
        if (!empty($options['lead_id'])) {
            $lead = Backend_Lead::load($options['lead_id']);
            if (empty($lead)) {
                throw new MissingLeadException();
            }
        }
        $headers = [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getAccessToken(),
                'Content-Type: application/json',
            ]
        ];
        // Determine Endpoint/Params
        $params = [];
        switch ($api_method) {
            // Retrieve Account Info
            case 'getAccountInfo':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = '/account';
                break;
            // Retrieve a Contact
            case 'getContact':
                // If the Lead Hasn't Been Previously Connected, Don't Try to Pull Them - It Would Result in a "FORBIDDEN" API Error
                if (empty($options['dotloop_contact_id'])) {
                    return [];
                }
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/contact/%s', $options['dotloop_contact_id']);
                break;
            // Get Account Profiles
            case 'getProfiles':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = '/profile';
                break;
            // Check if a Loop is Attached to a Contact
            case 'getLoopParticipants':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop/%s/participant', $options['dotloop_profile_id'], $options['dotloop_loop_id']);
                break;
            // Get Account Loops
            case 'getProfilesLoops':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop%s', $options['dotloop_profile_id'], $options['filter']);
                break;
            // Get Specific Loop
            case 'getLoop':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop/%s', $options['dotloop_profile_id'], $options['dotloop_loop_id']);
                break;
            // Get Specific Loop
            case 'getLoopDetails':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop/%s/detail', $options['dotloop_profile_id'], $options['dotloop_loop_id']);
                break;
            // Get Specific Loop
            case 'getLoopTaskLists':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop/%s/tasklist', $options['dotloop_profile_id'], $options['dotloop_loop_id']);
                break;
            // Get Specific Loop
            case 'getLoopActivities':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop/%s/activity', $options['dotloop_profile_id'], $options['dotloop_loop_id']);
                break;
            // Create a Contact
            case 'pushContact':
                $request_type = Util_Curl::REQUEST_TYPE_POST;
                $endpoint = '/contact';
                $params = [
                    'firstName' => $lead->info('first_name'),
                    'lastName'  => $lead->info('last_name'),
                    'email'     => $lead->info('email'),
                    'home'      => $lead->info('phone'),
                    'office'    => $lead->info('phone_work'),
                    'fax'       => $lead->info('phone_fax'),
                    'address'   => $lead->info('address1') . $lead->info('address2') . $lead->info('address3'),
                    'city'      => $lead->info('city'),
                    'zipCode'   => $lead->info('zip'),
                    'state'     => $lead->info('state'),
                    'country'   => $lead->info('country')
                ];
                break;
            // Push a New Loop
            case 'pushLoop':
                $request_type = Util_Curl::REQUEST_TYPE_POST;
                $endpoint = sprintf('/loop-it?profile_id=%s', $options['dotloop_profile_id']);
                $params = [
                    'profile_id' => $options['dotloop_profile_id'],
                    'name' => $options['loop_name'],
                    'transactionType' => $options['loop_transaction_type'],
                    'status' => $options['loop_status'],
                ];
                if (!empty($options['loop_template'])) {
                    $params['templateId'] = $options['loop_template'];
                }
                break;
            // Assign Lead to Existing Loop
            case 'pushLoopParticipant':
                $request_type = Util_Curl::REQUEST_TYPE_POST;
                $endpoint = sprintf('/profile/%s/loop/%s/participant', $options['dotloop_profile_id'], $options['dotloop_loop_id']);
                $params = [
                    'fullName' => sprintf('%s %s', $lead->info('first_name'), $lead->info('last_name')),
                    'email' => $lead->info('email'),
                    'role' => $options['contact_type']
                ];
                break;
            // Get Account Templates
            case 'getProfilesTemplates':
                $request_type = Util_Curl::REQUEST_TYPE_GET;
                $endpoint = sprintf('/profile/%s/loop-template', $options['dotloop_profile_id']);
                break;
        }
        $return_val = json_decode($this->performAPIRequest(self::API_URL_V2 . $endpoint, $params, $headers, $request_type), true) ?: [];
        if (!empty($return_val['errors'])) {
            Log::error('DotLoop API Error: [' . __FUNCTION__ . ']' . $return_val['errors'][0]['detail']);
        }
        return $return_val;
    }

    /**
     * Perform a Request to the DotLoop API
     *
     * @param string $full_endpoint_url
     * @param array  $params         (optional)
     * @param array  $custom_headers (optional)
     * @param int    $request_type   (optional)
     * @return array [ JSON-Decoded API Response ]
     */
    protected function performAPIRequest($full_endpoint_url, $params = [], $custom_headers = [], $request_type = Util_Curl::REQUEST_TYPE_POST)
    {
        $header_responses = [];
        $custom_headers += [
            CURLOPT_HEADERFUNCTION => (function ($curl, $header_line) use (&$header_responses) {
                $response = array_map('trim', explode(':', $header_line));
                $header_responses[$response[0]] = $response[1];
                return strlen($header_line);
            })
        ];
        $response = Util_Curl::executeRequest($full_endpoint_url, $params, $request_type, $custom_headers);
        $info = Util_Curl::info();
        if (!empty($header_responses)) {
            $this->setRateLimitStatus(
                $header_responses['X-RateLimit-Limit'],
                $header_responses['X-RateLimit-Remaining'],
                $header_responses['X-RateLimit-Reset']
            );
        }
        if (strpos($info['http_code'], '2') !== 0) {
            Log::error('DotLoop API Call - Bad Response Code: [CODE : ' . $info['http_code'] . '][ENDPOINT : ' . $full_endpoint_url . ']');
        }
        $this->_last_response = $response;
        return $response;
    }

    /**
     * Perform a Request to the DotLoop Authentication API
     *
     * @param array $params
     * @return array [ JSON-Decoded API Response ]
     */
    protected function performAuthRequest($params = [])
    {
        $headers = [
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode(self::REW_APP_KEY . ':' . self::REW_SECRET_KEY)
            ]
        ];
        return json_decode($this->performAPIRequest(self::API_URL_AUTH, $params, $headers), true) ?: [];
    }

    /**
     * Build the link to the API Application Access Approval Page
     *
     * @param int  $lead_id (optional)
     * @param bool $popup   (will the link load in a popup?)
     * @return string
     */
    public static function generateApprovalLink($lead_id = null, $popup = false)
    {
        $url = 'https://auth.dotloop.com/oauth/authorize'
            . '?response_type=code'
            . (!$popup ? '&redirect_on_deny=true' : '')
            . '&client_id=' . self::REW_APP_KEY
            . '&redirect_uri=' . Settings::getInstance()->URLS['URL_BACKEND'] . 'settings/partners/dotloop/?setup';

        // If a Lead was Specified, Redirect to Lead Summary Page After APP Approval
        $lead = ($lead_id) ? Backend_Lead::load($lead_id) : null;
        if (!empty($lead)) {
            $url .= '=' . $lead->getId();
        }
        return $url;
    }

    /**
     * Get the last occurred error
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->_error;
    }

    /**
     * Get the last API response
     *
     * @return string (json encoded)
     */
    public function getLastAPIResponse()
    {
        return (!empty($this->_last_response)) ? $this->_last_response : [];
    }

    /**
     * Get the last API Error ID
     *
     * @return mixed null|int
     */
    public function getLastAPIErrorID()
    {
        $last_response = json_decode($this->_last_response, true);
        return (!empty($last_response['status'])) ? intval($last_response['status']) : null;
    }
}
