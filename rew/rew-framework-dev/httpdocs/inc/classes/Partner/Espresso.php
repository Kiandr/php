<?php

/**
 * Partner_Espresso
 *
 */
class Partner_Espresso
{

    /**
     * API Key For Dialer Requests
     * @var string
     */
    private $contactAPIKey;

    /**
     * API Key For Account Creation / Deletion
     * @var string
     */
    private $accountAPIKey;

    /**
     * Sub Group ID For REW Espresso Account
     * @var string
     */
    private $subGroupID;

    /**
     * Dialer API URL
     * @var string
     */
    private $dialerURL;

    /**
     * Requested Contacts
     * @var array
     */
    private $contacts;

    /**
     * Last API Error
     * @var string
     */
    private $_error;

    /**
     * Create a new partner instance
     */
    public function __construct()
    {

        // These Keys Are Used For Every REW Client
        $this->contactAPIKey = '005df3a7-1927-45b2-a15f-7134f0f106ce'; // For Interacting With Espresso's Dialer API
        $this->accountAPIKey = '11aa8202-0c12-48e4-a807-df4c07f43d96'; // For Interacting With Espresso's Account Management API
        $this->subGroupID = '146'; // REW Master Account's ID

        $this->dialerURL = 'www.rewdialer.com';

        $this->contacts = array();
        $this->_error = null;
    }

    /**
     * Return Contact API Key
     * @return string
     */
    public function getContactAPIKey()
    {
        return $this->contactAPIKey;
    }

    /**
     * Return Account API Key
     * @return string
     */
    public function getAccountAPIKey()
    {
        return $this->accountAPIKey;
    }

    /**
     * Return Account Sub Group ID
     * @return string
     */
    public function getSubGroupID()
    {
        return $this->subGroupID;
    }

    /**
     * Get the Last Occurred Error
     * @return string
     */
    public function getLastError()
    {
        $error = $this->_error;
        $this->_error = null;
        return $error;
    }

    /**
     * Return Selected Leads
     * @return array
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Return Dialer URL
     * @return string
     */
    public function getDialerURL()
    {
        return $this->dialerURL;
    }

    /**
     * Return TPID
     * @return string
     */
    public function generateTPID($http_host, $line_number)
    {
        if (empty($http_host) || empty($line_number)) {
            return false;
        }
        $tpid = preg_replace('/[^a-zA-Z0-9.]/', '.', $http_host) . '.' . $line_number;
        return $tpid;
    }

    /**
     * Return Password
     * @return string
     */
    public function generatePassword($tpid)
    {
        if (empty($tpid)) {
            return false;
        }
        $password = substr(md5(preg_replace('/[^a-zA-Z0-9]/', '', $tpid)), 0, 20);
        return $password;
    }

    /**
     * Set Error
     * @param string $error
     */
    public function setError($error)
    {
        $this->_error = $error;
        return true;
    }

    /**
     * Set Requested Leads
     * @param array $ids        Array of lead ids
     * @param string $tpid      Backend User's TPID - md5() of Their Email
     * @return bool
     */
    public function setContacts($ids, $tpid)
    {

        // Reset Contact Array
        $this->contacts = array();

        // Grab Database Object
        $db = DB::get('cms');

        // Add Leads to Array
        $ids = (is_array($ids)) ? $ids : array($ids);
        foreach ($ids as $id) {
            // Check the Hash That Was Sent to Espresso for This Lead and Grab the Lead ID
            $id_and_hash = explode('-', $id);

            // Hash Doesn't Match, Don't Serve the Lead's Information
            if ($id_and_hash[1] != $tpid) {
                continue;
            }

            // Grab the Lead's Information
            $sql = "SELECT CONCAT(`id`, '-" . $tpid . "') AS `id`, `first_name`, `last_name`, `phone`, `phone_cell`, `email` FROM `users` WHERE `id` = :id LIMIT 1;";
            $contact = $db->fetch($sql, array('id' => $id_and_hash[0]));

            if (!empty($contact)) {
                // Try Cell # if Home # is empty
                $contact['phone'] = (!empty($contact['phone'])) ? $contact['phone'] : $contact['phone_cell'];
                $this->contacts[] = $contact;
            }
        }

        // Confirm Contacts Have Been Set
        if (empty($this->contacts)) {
            $this->setError('Requested contacts could not be verified.');
            return false;
        }

        return true;
    }

    /**
     * Return Backend User Info Based On TPID
     * @param array User's Espresso TPID
     */
    public function userHistoryInfo($user_info)
    {

        if (!empty($user_info) && is_array($user_info)) {
            // Backend User Table and History Class
            switch ($user_info[1]) {
                case 'associate':
                    $user_class = 'History_User_Associate';
                    break;
                default:
                    $user_class = 'History_User_Agent';
                    break;
            }

            if ($history_user = new $user_class($user_info[0])) {
                return $history_user;
            } else {
                $this->setError('Backend user history could not be retrieved.');
            }
        }

        return false;
    }

    /**
     * Run a cURL Request
     * @param string
     * @return string output | bool(false)
     */
    public function CURLHandler($url)
    {

        if (!empty($url)) {
            // Grab Output From Their Page - Response Codes are Output Directly in Content
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $output = curl_exec($ch);
            curl_close($ch);

            return $output;
        }

        return false;
    }

    /**
     * Validate Espresso Backend User Account
     * @param string Auth
     * @return bool
     */
    public function validateAccount($tpid)
    {

        // Grab Response From Validation Page
        $validationURL = 'https://' . $this->getDialerURL() . '/pb-api.php?a=hasdialer'
            . '&apiKey='    . urlencode($this->getAccountAPIKey())
            . '&tpid='      . urlencode($tpid);

        $response = $this->CURLHandler($validationURL);

        // This Will Start With "OK" or "ERROR"
        if (strpos($response, 'OK') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Create (or Re-enable) Espresso Backend User Account
     * @param string $site
     * @param string $tpid
     * @return bool
     */
    public function enableAccount($site, $tpid)
    {

        // Check URL + Dialer #
        $dialer = explode('.', $tpid);
        $line = $dialer[count($dialer) - 1];

        // First Check if the Account Already Exists
        if ($this->validateAccount($tpid)) {
            $this->setError('REW Dialer Line #' . $line . ' is Already Active For ' . $site);
        } else {
            // Check Whether Account is Inactive or Doesn't Exist Yet
            $url = 'http://' . $this->getDialerURL() . '/pb-api-vulcan7.php?a=account_status'
                . '&subgroupid='    . urlencode($this->getSubGroupID())
                . '&apiKey='        . urlencode($this->getAccountAPIKey())
                . '&tpid='          . urlencode($tpid);

            $account_status = $this->CURLHandler($url);

            // Account is Inactive
            if (strpos($account_status, 'CANCEL') !== false) {
                // Send a New Dialer Account Request
                $url = 'http://' . $this->getDialerURL() . '/pb-api-vulcan7.php?a=reactivate'
                    . '&subgroupid='    . urlencode($this->getSubGroupID())
                    . '&apiKey='        . urlencode($this->getAccountAPIKey())
                    . '&tpid='          . urlencode($tpid);

                $response = $this->CURLHandler($url);

            // Account Doesn't Exist Yet
            } else if (strpos($account_status, 'ERROR|User not found') !== false) {
                // Send a New Dialer Account Request
                $url = 'http://' . $this->getDialerURL() . '/pb-api-vulcan7.php?a=setup'
                    . '&subgroupid='    . urlencode($this->getSubGroupID())
                    . '&apiKey='        . urlencode($this->getAccountAPIKey())
                    . '&tpid='          . urlencode($tpid)
                    . '&idnumber='      . urlencode($tpid) // Site URL
                    . '&password='      . urlencode($this->generatePassword($tpid))
                    . '&firstname='     . urlencode($site)
                    . '&lastname='      . urlencode('Line #' . $line)
                    . '&email='         . urlencode('none')
                    . '&address='       . urlencode('none')
                    . '&city='          . urlencode('none')
                    . '&state='         . urlencode('none')
                    . '&phone='         . urlencode('none');

                $response = $this->CURLHandler($url);
            }

            // This Will Start With "OK" or "ERROR"
            if (strpos($response, 'OK') !== false) {
                return true;
            } else {
                $this->setError($response);
            }
        }

        return false;
    }

    /**
     * Disable Espresso Backend User Account
     * @param string
     */
    public function disableAccount($tpid)
    {

        // Make sure backend user exists
        if ($this->validateAccount($tpid)) {
            // Grab Account API Key
            $api_key = $this->getAccountAPIKey();

            // Send a New Dialer Account Request
            $url = 'http://' . $this->getDialerURL() . '/pb-api-vulcan7.php?a=cancel'
                . '&subgroupid='    . urlencode('146')
                . '&apiKey='        . urlencode($this->getAccountAPIKey())
                . '&tpid='          . urlencode($tpid);

            $response = $this->CURLHandler($url);

            // This Will Start With "OK" or "ERROR"
            if (strpos($response, 'OK') !== false) {
                return true;
            } else {
                $this->setError($response);
            }
        } else {
            $this->setError('Invalid Dialer Account.');
        }

        return false;
    }

    /**
     * Synchronize Dialer Lines
     * @param int   line#
     */
    public function synchronizeAccounts($site, $line)
    {

        // Line ID
        $tpid = $this->generateTPID($site, $line);

        $next_line = $line + 1;

        // Validate Line
        if ($this->validateAccount($tpid)) {
            // Disable Line if it Exceeds Dialer Limit
            if ($line > Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) {
                $this->disableAccount($tpid);
            }

        // Line Didn't Validate
        } else {
            if (isset(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) && !empty(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'])
                && is_int(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) &&
                $line <= Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']
            ) {
                // Enable Line (Limit has Likely Changed Recently)
                $this->enableAccount($site, $tpid);
            } else {
                // Lines Synchronized Successfully
                return true;
            }
        }

        // Validate Next Line
        $this->synchronizeAccounts($site, $next_line);

        // Done
        return true;
    }
}
