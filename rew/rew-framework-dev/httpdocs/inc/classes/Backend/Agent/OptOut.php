<?php

/**
 * Backend_Agent_OptOut
 * @package Backend
 */
class Backend_Agent_OptOut
{

    /**
     * Timeframe (in Minutes)
     * @var int
     */
    protected $timeframe = 240; // 4 Hours

    /**
     * Actions to Perform
     * @var array
     */
    protected $actions = array();

    /**
     * Enable Out-Opt Feature
     * @var boolean
     */
    protected $enabled = false;

    /**
     * Agent Database
     * @var DB
     */
    protected $db;

    /**
     * Debug Mode (Don't use on a live website, debug output will break design)
     * @var int
     */
    protected static $debug = 0;

    /**
     * Agent Events
     * @var array
     */
    public static $events = array(
        // Manually Create a New Lead
        '00' => array('title' => 'Add Lead', 'classes' => array(
            'History_Event_Create_Lead'
        )),
        // Update an Existing Lead
        '01' => array('title' => 'Update Lead', 'classes' => array(
            'History_Event_Update_Status',
            'History_Event_Update_Rejected',
            'History_Event_Update_Lead'
        )),
        // Assign or Un-Assign a Lead from a Group
        '02' => array('title' => 'Assign Group', 'classes' => array(
            'History_Event_Update_GroupRemove',
            'History_Event_Update_GroupAdd'
        )),
        // Track a Phone Call
        '03' => array('title' => 'Track Phone Call', 'classes' => array(
            'History_Event_Phone'
        )),
        // Send an Email to Lead
        '04' => array('title' => 'Send Email', 'classes' => array(
            'History_Event_Email_Sent',
            'History_Event_Email_Delayed',
            'History_Event_Email_AutoResponder'
        )),
        // Add a Lead Note
        '05' => array('title' => 'Add Lead Note', 'classes' => array(
            'History_Event_Create_LeadNote'
        )),
        // Add a Lead Reminder
        '06' => array('title' => 'Add Lead Reminder', 'classes' => array(
            'History_Event_Create_LeadReminder'
        )),
        // Recommend an MLS Listing
        '07' => array('title' => 'Recommend Listing', 'classes' => array(
            'History_Event_Action_SavedListing'
        )),
        // Suggest a Saved Search
        '08' => array('title' => 'Suggest Search', 'classes' => array(
            'History_Event_Action_SavedSearch'
        ))
    );

    /**
     * Setup Agent Opt-Out Feature (Load Settings from Database)
     */
    public function __construct()
    {

        try {
            // Agent DB
            $this->db = DB::get('users');

            // Load Settings from DB
            $settings = $this->db->fetch("SELECT `auto_optout`, `auto_optout_days`, `auto_optout_hours`, `auto_optout_time`, `auto_optout_actions` FROM `default_info` WHERE `agent` = 1;");
            if (!empty($settings)) {
                // Enabled
                $this->enabled = ($settings['auto_optout'] === 'true');

                // Timeframe
                $this->timeframe = $settings['auto_optout_time'];

                // Timeframe
                if (!empty($settings['auto_optout_actions'])) {
                    $this->actions = unserialize($settings['auto_optout_actions']);
                }

                // Check Days in Affect
                if (empty($settings['auto_optout_days']) || !in_array(date('w'), explode(',', $settings['auto_optout_days']))) {
                    if (self::$debug) {
                        echo "\t" . date('w (l)') . ' is not in selected days (' . $settings['auto_optout_days'] . ')' . PHP_EOL;
                    }
                    $this->enabled = false;

                // Check Hours in Affect
                } elseif (!in_array(date('G'), explode(',', $settings['auto_optout_hours']))) {
                    if (self::$debug) {
                        echo "\t" . date('G:i') . ' is not in selected hours (' . $settings['auto_optout_hours'] . ')' . PHP_EOL;
                    }
                    $this->enabled = false;
                }

                // Output
                if (self::$debug) {
                    echo PHP_EOL . 'Agent Opt-Out Feature: ' . ($this->enabled ? 'On' : 'Off') . PHP_EOL;
                    if ($this->enabled) {
                        echo PHP_EOL . "\t" . 'Out-Out Timeframe: ' . ($this->timeframe / 60) . ' Hours' . PHP_EOL;
                        if ($this->actions) {
                            echo "\t" . 'Actions to Perform:' . PHP_EOL . PHP_EOL . "\t - " . implode(PHP_EOL . "\t - ", array_map(function ($action) {
                                $class = __CLASS__;
                                return $class::$events[$action]['title'];
                            }, $this->actions)) . PHP_EOL;
                        }
                    }
                }
            }

        // DB Error
        } catch (PDOException $e) {
            Log::error($e);
            throw $e;
        }
    }

    /**
     * Get Timeframe (in Minutes)
     * @return int
     */
    public function getTimeframe()
    {
        return $this->timeframe;
    }

    /**
     * Get Actions
     * @return array
     */
    public function getActions()
    {
        return !empty($this->actions) ? $this->actions : array_keys(self::$events);
    }

    /**
     * Check If Enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return !empty($this->enabled);
    }

    /**
     * Automatically Opt-Out Agents who haven't performed certain actions within timeframe.
     * @return void
     */
    public function execute()
    {
        try {
            // Turned Off (Don't do anyting)
            if (!$this->isEnabled()) {
                return;
            }

            // Output
            if (self::$debug) {
                echo PHP_EOL . 'Running...' . PHP_EOL;
            }

            // Time to Compare
            $time = ($this->timeframe * 60);    // Convert from Minutes to Seconds
            $time = time() - $time;             // Subtract from Current Time

            // Select Agents
            $agents = $this->db->fetchAll("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name`, `auto_assign_agent`, UNIX_TIMESTAMP(`auto_optout_time`) AS `time` FROM `agents` WHERE `auto_optout` = 'true';");
            foreach ($agents as $agent) {
                // Output
                if (self::$debug) {
                    echo PHP_EOL . "\t" . '#' . $agent['id'] . ' - ' . $agent['name'] . ':' . PHP_EOL;
                }

                // Is Opted Out
                if ($agent['auto_assign_agent'] === 'false') {
                    if (self::$debug) {
                        echo "\t\t" . 'Is Opted Out' . PHP_EOL;
                    }

                // Compare Time
                } else if ($agent['time'] <= $time) {
                    // Automatic Opt-Out
                    $this->db->query("UPDATE `agents` SET `auto_assign_agent` = 'false' WHERE `id` = '" . $agent['id'] . "';");

                    // Log Event: Agent has been opted out
                    $event = new History_Event_Update_OptOut(array(
                        'auto'      => true,
                        'inactive'  => (time() - $agent['time']) / 60,
                    ), array(
                        new History_User_Agent($agent['id'])
                    ));

                    // Save to Database
                    $event->save($this->db);

                    // Output
                    if (self::$debug) {
                        echo "\t\t" . 'Has Been Opted Out' . PHP_EOL;
                    }
                } else {
                    // Output
                    if (self::$debug) {
                        echo "\t\t" . 'Meets Requirements' . PHP_EOL;
                    }
                }

                // Output
                if (self::$debug) {
                    echo "\t\t" . 'Last Active: ' . (!empty($agent['time']) ? Format::dateRelative($agent['time']) : 'Never') . PHP_EOL;
                }
            }

        // DB Error
        } catch (PDOException $e) {
            Log::error($e);
            throw $e;
        }
    }

    /**
     * Update Agent's Timestamp
     * @param int $agent
     * @param History_Event $event
     * @return boolean
     */
    public function update($agent_id, History_Event $event = null)
    {
        // Check Event
        if (is_null($event) || $this->checkEvent($event)) {
            try {
                // Update Timestamp
                $this->db->query("UPDATE `agents` SET `auto_optout_time` = NOW() WHERE `id` = '" . $agent_id . "';");

                // Automatic Opt-In
                if ($this->isEnabled()) {
                    $this->db->query("UPDATE `agents` SET `auto_assign_agent` = 'true' WHERE `id` = '" . $agent_id . "' AND `auto_optout` = 'true';");
                }

                // Success
                return true;

            // DB Error
            } catch (PDOException $e) {
                Log::error($e);
                throw $e;
            }
        }
    }

    /**
     * Check Event Instance
     * @param History_Event $event
     * @return boolean
     */
    public function checkEvent(History_Event $event)
    {
        foreach (self::$events as $value => $events) {
            if (empty($this->actions) || in_array($value, $this->actions)) {
                foreach ($events['classes'] as $class) {
                    if ($event instanceof $class) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Toggle Debug Mode
     * @param boolean $debug
     */
    public static function setDebug($debug)
    {
        self::$debug = $debug;
    }
}
