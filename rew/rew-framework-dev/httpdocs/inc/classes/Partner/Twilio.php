<?php

/**
 * Partner_Twilio
 */
class Partner_Twilio
{

    /**
     * Singleton Instance
     * @var self
     */
    private static $instance;

    /**
     * Account SID
     * @var string
     */
    private $_sid;

    /**
     * API Endpoint
     * @var string
     */
    private $_url_api_endpoint = 'http://twilio.rewhosting.com';

    /**
     * @var array
     */
    protected $numbers;

    /**
     * Error: Unexpected error occurred
     * @var string
     */
    const ERROR_UNEXPECTED = 'An unexpected error has occurred - please contact support';

    /**
     * Error: "To" number has unsubscribed
     * @var string
     */
    const ERROR_TO_OPTOUT = 'Message cannot be sent, number has previously replied with "STOP"';

    /**
     * Error: Invalid "To" number
     * @var string
     */
    const ERROR_TO_INVALID = 'To number must be a valid 10 digit phone number';

    /**
     * Error: Invalid "From" number
     * @var string
     */
    const ERROR_FROM_INVALID = 'From number must be a valid 10 digit phone number';

    /**
     * Error: Invalid "Body"
     * @var string
     */
    const ERROR_BODY_REQUIRED = 'Message is required';

    /**
     * Error: Maximum "Body" length is 1600
     * @var unknown
     */
    const ERROR_BODY_TOO_LONG = 'Message cannot be longer than 1600 characters';

    /**
     * Setup class instance
     */
    private function __construct($account_sid)
    {
        $this->_sid = $account_sid;
        $this->_db = DB::get();
    }

    /**
     * Get class instance
     * @return self|NULL
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            $account_sid = Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'];
            if (empty($account_sid)) {
                return null;
            }
            self::$instance = new self ($account_sid);
        }
        return self::$instance;
    }

    /**
     * Validate SMS Message
     * @param string $to
     * @param string $from
     * @param string $body
     * @param string|array $media
     * @throws Partner_Twilio_Exception
     * @return bool
     */
    public function validateSmsMessage(&$to, &$from, $body, $media = null)
    {

        // Require valid recipient number
        $to = $this->extractNumber($to);
        if (empty($to) || strlen($to) < 10) {
            throw new Partner_Twilio_Exception(self::ERROR_TO_INVALID);
        }

        // Require valid sender number
        $from = $this->extractNumber($from);
        if (empty($from) || strlen($from) < 10) {
            throw new Partner_Twilio_Exception(self::ERROR_FROM_INVALID);
        }

        // Require body or media
        $body = trim($body);
        if (empty($body) && empty($media)) {
            throw new Partner_Twilio_Exception(self::ERROR_BODY_REQUIRED);
        }

        // Success
        return true;
    }

    /**
     * Send SMS Message
     * @param string $to
     * @param string $from
     * @param string $body
     * @param string|array $media
     * @throws Partner_Twilio_Exception
     * @return bool
     */
    public function sendSmsMessage($to, $from, $body, $media = null)
    {
        try {
            // Validate SMS Message
            $this->validateSmsMessage($to, $from, $body, $media);

            // Handle API request
            $status = self::executeRequest('/sms', array(
                'From'      => $from,   // From a Twilio number
                'To'        => $to,     // Send to this number
                'Body'      => $body,   // Message body (if no media)
                'MediaUrl'  => $media   // A string or an array of MediaUrls
            ));

            // Return status
            return !is_null($status);

        // Error occurred
        } catch (Partner_Twilio_Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available phone numbers
     * @param bool $reload
     * @throws Partner_Twilio_Exception
     * @return array
     */
    public function getTwilioNumbers($reload = false)
    {
        try {
            if (!$this->numbers || $reload) {
                $this->numbers = self::executeRequest('/incoming-numbers', array(), Util_Curl::REQUEST_TYPE_GET);
            }
            return $this->numbers;
        } catch (Partner_Twilio_Exception $e) {
            throw $e;
        }
    }

    /**
     * Get phone number details
     * @param string $number_sid
     * @throws Partner_Twilio_Exception
     * @return array
     */
    public function getTwilioNumber($number_sid)
    {
        try {
            return self::executeRequest('/incoming-numbers/' . $number_sid, array(), Util_Curl::REQUEST_TYPE_GET);
        } catch (Partner_Twilio_Exception $e) {
            throw $e;
        }
    }

    /**
     * Update phone number details
     * @param string $number_sid
     * @param array $data
     * @throws Partner_Twilio_Exception
     * @return array
     */
    public function updateTwilioNumber($number_sid, $data)
    {
        try {
            return self::executeRequest('/incoming-numbers/' . $number_sid, $data, Util_Curl::REQUEST_TYPE_PUT);
        } catch (Partner_Twilio_Exception $e) {
            throw $e;
        }
    }


    /**
     * Execute an REW Twilio API request and return the response
     * @param string $uri
     * @param array $params
     * @param int $request_type
     * @throws Partner_Twilio_Exception
     * @return NULL|mixed
     */
    private function executeRequest($uri, $params = array(), $request_type = Util_Curl::REQUEST_TYPE_POST, $opt_override = array())
    {

        // Set API Endpoint
        Util_Curl::setBaseURL($this->_url_api_endpoint);

        // Execute cURL request using X-REW-Twilio-Sid header
        $response = Util_Curl::executeRequest($uri, $params, $request_type, $opt_override + array(
            CURLOPT_HTTPHEADER => array(
                'X-REW-Twilio-Sid: ' . $this->_sid,
                'Content-Type: application/json'
            )
        ));

        // 204: No Content
        $info = Util_Curl::info();
        if ($info['http_code'] === 204) {
            return null;
        }

        // Require JSON response
        $json = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Partner_Twilio_Exception(self::ERROR_UNEXPECTED);
        }

        // An error occurred, throw it back
        $error = $json['error'];
        if (!empty($error)) {
            $error = $this->errorMessage($error['message'], $error['code']);
            throw new Partner_Twilio_Exception($error);
        }

        // API response
        return $json;
    }

    /**
     * Return user-friendly error message
     * @param string $error
     * @param int|NULL $code
     * @return string
     */
    private function errorMessage($error, $code = null)
    {
        $code = (int) $code;
        switch ($code) {
            # 21212: Invalid 'From' Phone Number
            # 21603: 'From' phone number is required to send a Message
            case 21212:
            case 21603:
                $error = self::ERROR_FROM_INVALID;
                break;
            # 21211: 'Invalid 'To' Phone Number
            # 21604: 'To' phone number is required to send a Message
            case 21211:
            case 21604:
                $error = self::ERROR_TO_INVALID;
                break;
            # 21602: 'Body' OR MediaURL is required to send a Message
            case 21602:
                $error = self::ERROR_BODY_REQUIRED;
                break;
            # 21605: Maximum body length is 1600 characters
            # 21617: The concatenated message body exceeds the 1600 character limit
            case 21605:
            case 21617:
                $error = self::ERROR_BODY_TOO_LONG;
                break;
            # 21610: Message cannot be sent to the 'To' number because the customer has replied with STOP
            case 21610:
                $error = self::ERROR_TO_OPTOUT;
                break;
            default:
                $error = self::ERROR_UNEXPECTED;
                # 10001: Account is not active
                # 20429: 429 Too Many Requests
                # 21601: Phone number is not a valid SMS-capable/MMS-capable inbound phone number
                # 21606: The 'From' phone number provided is not a valid, message-capable Twilio phone number.
                # 21607: The 'From' number is not a valid, SMS-capable Twilio number
                # 21608: This number can send messages only to verified numbers
                # 21609: Invalid StatusCallback url
                # 21611: This 'From' number has exceeded the maximum number of queued messages
                # 21612: The 'To' phone number is not currently reachable via SMS
                # 21613: PhoneNumber Requires an Address
                # 21615: PhoneNumber Requires a Local Address
                # 21614: 'To' number is not a valid mobile number
                # 21616: The 'From' number matches multiple numbers for your account
                # 21618: The message body cannot be sent
                # 21619: A text message body or media urls must be specified
                # 21620: Invalid media URL(s)
                # 21623: Number of media files exceeds allowed limit
                # 21621: The 'From' number has not been enabled for picture messaging
                # 21622: MMS has not been enabled for your account
                break;
        }
        return $error;
    }

    /**
     * Extract all numbers from a string
     * @param string $phone
     * @return string
     */
    private function extractNumber($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
