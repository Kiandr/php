<?php

/**
 * Backend_Agent_Notifications
 * @package Backend
 */
class Backend_Agent_Notifications
{

    /**
     * Receive Notifications When Leads Enter the Shark Tank
     * @const string
     */
    const INCOMING_SHARK_TANK_LEADS     = 'lead-shark-tank';

    /**
     * Incoming Lead Assigned to Agent
     * @const string
     */
    const INCOMING_LEAD_ASSIGNED        = 'lead-assigned';

    /**
     * Incoming Lead Inquiries & Form Submissions
     * @const string
     */
    const INCOMING_LEAD_INQUIRED        = 'lead-inquired';

    /**
     * Incoming Saved Search Notices
     * @const string
     */
    const INCOMING_SEARCH_SAVED         = 'search-saved';

    /**
     * Incoming Saved Listing Notices
     * @const string
     */
    const INCOMING_LISTING_SAVED        = 'listing-saved';

    /**
     * Outgoing Search Updates
     * @const string
     */
    const OUTGOING_SEARCH_UPDATES       = 'search-updates';

    /**
     * Outgoing Search Suggestions
     * @const string
     */
    const OUTGOING_SEARCH_SUGGEST       = 'search-suggest';

    /**
     * Outgoing Listing Recommendations
     * @const string
     */
    const OUTGOING_LISTING_RECOMMEND    = 'listing-recommend';

    /**
     * Incoming notifications are emails that are sent to an agent when a lead completes an action
     * - Key is required and used to retrieve settings
     * - 'title' is description used to describe the type of notification
     * - 'email' allows for toggle of email notifications (if false, toggle is disabled)
     * - 'sms' allows for toggle of sms notifications (if false, toggle is not displayed)
     * @var array
     */
    protected $incoming;

    /**
     * Outgoing notifications are emails that are sent to leads
     *  - Key is required and used to retrieve settings
     *  - 'title' is description used to describe type of notification
     * @var array
     */
    protected $outgoing;

    /**
     * Incoming Settings
     *  - Key is required and used to retrieve settings.
     *  - Value must be an array, containing:
     *  - 'cc' can be set to an email address to include in notification
     *  - 'email' is a boolean to control if an email notification should be sent
     *  - 'sms' is a boolean to control if an SMS notification should be sent
     * @var array
     */
    protected $incomingSettings = array(
        self::INCOMING_SHARK_TANK_LEADS => array('cc' => false, 'email' => true, 'sms' => false),
        self::INCOMING_LEAD_ASSIGNED    => array('cc' => false, 'email' => true, 'sms' => false),
        self::INCOMING_LEAD_INQUIRED    => array('cc' => false, 'email' => true, 'sms' => false),
        self::INCOMING_SEARCH_SAVED     => array('cc' => false, 'email' => true),
        self::INCOMING_LISTING_SAVED    => array('cc' => false, 'email' => true),
    );

    /**
     * Outgoing Settings
     *  - Key is required and used to retrieve settings.
     *  - Value can be one of these: false, 'cc', 'bcc'
     * @var array
     */
    protected $outgoingSettings = array(
        self::OUTGOING_SEARCH_UPDATES       => false,
        self::OUTGOING_SEARCH_SUGGEST       => false,
        self::OUTGOING_LISTING_RECOMMEND    => false,
    );

    /**
     * Notification Email
     * @var string
     */
    protected $email;

    /**
     * Setup Agent Notifications
     * @param mixed $settings
     */
    public function __construct($settings = array())
    {
        // Load Settings
        if (!empty($settings)) {
            $this->loadSettings($settings);
        }

        $this->incoming = array(
            self::INCOMING_SHARK_TANK_LEADS => array('title' => __('New leads enter the Shark Tank'),                   'email' => true,    'sms' => true,  'cc' => false),
            self::INCOMING_LEAD_ASSIGNED    => array('title' => __('New leads are assigned'),                           'email' => false,   'sms' => true,  'cc' => true),
            self::INCOMING_LEAD_INQUIRED    => array('title' => __('New inquiries and form submissions are received'),  'email' => false,   'sms' => true,  'cc' => true),
            self::INCOMING_SEARCH_SAVED     => array('title' => __('An assigned lead saves a new search'),              'email' => true,    'sms' => false, 'cc' => true),
            self::INCOMING_LISTING_SAVED    => array('title' => __('An assigned lead adds a favorite listing'),         'email' => true,    'sms' => false, 'cc' => true),
        );

        $this->outgoing = array(
            self::OUTGOING_SEARCH_UPDATES       => array('title' => __('Saved Search Updates'),     'tip' => __('These notifications are sent out when new listings that match a lead\'s saved search criteria are found.')),
            self::OUTGOING_SEARCH_SUGGEST       => array('title' => __('Saved Search Suggestions'), 'tip' => __('These notifications are sent out when an agent suggests that a lead creates a saved search.')),
            self::OUTGOING_LISTING_RECOMMEND    => array('title' => __('Listing Recommendations'),  'tip' => __('These notifications are sent out when a listing is recommended by an agent.')),
        );

    }

    /**
     * Check Incoming Settings
     * @param string $notice
     * @return array
     */
    public function checkIncoming($notice)
    {
        return $this->check('incoming', $notice);
    }

    /**
     * Check Outgoing Settings
     * @param string $notice
     * @return array
     */
    public function checkOutgoing($notice)
    {
        $check = $this->check('outgoing', $notice);
        if (!empty($check)) {
            return array($check => $this->getEmail());
        }
        return $check;
    }

    /**
     * Check Notification Setting
     * @param string $type
     * @param string $notice
     * @return mixed
     */
    public function check($type, $notice)
    {
        if ($type == 'incoming') {
            $settings = $this->getIncomingSettings();
        }
        if ($type == 'outgoing') {
            $settings = $this->getOutgoingSettings();
        }
        return isset($settings) ? $settings[$notice] : null;
    }

    /**
     * Get Incoming Notifications
     * @return array Incoming Notification
     */
    public function getIncoming()
    {
        return $this->incoming;
    }

    /**
     * Get Outgoing Notifications
     * @return array Outgoing Notification
     */
    public function getOutgoing()
    {
        return $this->outgoing;
    }

    /**
     * Get Incoming Settings
     * @return array Incoming Settings
     */
    public function getIncomingSettings()
    {
        return $this->incomingSettings;
    }

    /**
     * Get Outgoing Settings
     * @return array Outgoing Settings
     */
    public function getOutgoingSettings()
    {
        return $this->outgoingSettings;
    }

    /**
     * Get Notification Email
     * @return string Email Address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get Notification Settings
     * @return array Settings
     */
    public function getSettings()
    {
        return array(
            'email'    => $this->getEmail(),
            'incoming' => $this->getIncomingSettings(),
            'outgoing' => $this->getOutgoingSettings()
        );
    }

    /**
     * Load Notication Settings
     * @param string|array $settings If string, will be parsed as JSON.
     * @param array $errors If present, error report will be appended to collection
     */
    public function loadSettings($settings = array(), &$errors = null)
    {

        // Parse Settings from JSON
        if (is_string($settings)) {
            $settings = json_decode($settings, true);
        }
        $settings = is_array($settings) ? $settings : array();

        // Require Array of Settings
        if (is_array($settings)) {
            // Load Incoming Settings
            $incomingSettings = $settings['incoming'];
            foreach ($this->incomingSettings as $k => $incoming) {
                foreach ($incoming as $prop => $value) {
                    // Over-Ride Settings
                    $value = $incomingSettings[$k][$prop];
                    if (in_array($prop, array('email', 'sms')) && (isset($errors) || isset($incomingSettings[$k][$prop]))) {
                        $this->incomingSettings[$k][$prop] = !empty($value) ? true : false;
                    }
                    // Require Valid CC Email
                    if ($prop == 'cc') {
                        $this->incomingSettings[$k][$prop] = !empty($value) ? $value : false;
                        if (!empty($value) && !Validate::email($value, true)) {
                            if ($errors) {
                                $errors[] = __('Please supply a valid CC Email Address: ') . htmlspecialchars($value);
                            }
                        }
                    }
                }
            }

            // CC/BCC Email Required
            $email_required = false;

            // Load Outgoing Settings
            $outgoingSettings = $settings['outgoing'];
            foreach ($this->outgoingSettings as $k => $incoming) {
                if (isset($outgoingSettings[$k])) {
                    if (in_array($outgoingSettings[$k], array('cc', 'bcc'))) {
                        $this->outgoingSettings[$k] = $outgoingSettings[$k];
                        $email_required = true;
                    } else {
                        $this->outgoingSettings[$k] = false;
                    }
                }
            }

            // Validate Valid Notification Email
            if (!empty($email_required) || !empty($settings['email'])) {
                $this->email = $settings['email'];
                if (!Validate::email($settings['email'], true)) {
                    if (isset($errors)) {
                        $errors[] = __('Please supply a valid Email Address to send notifications: ') . htmlspecialchars($settings['email']);
                    }
                }
            }
        }
    }
}
