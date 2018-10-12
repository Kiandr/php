<?php

/**
 * History_User_Agent
 */
class History_User_Agent extends History_User
{

    /**
     * User Type
     * @var string
     */
    protected $type = History_User::TYPE_AGENT;

    /**
     * @var Backend_Agent_OptOut
     */
    private $optout;

    /**
     * History_User_Agent constructor.
     * @param null $user
     * @param \REW\Core\Interfaces\DBInterface|null $db
     */
    public function __construct($user = null, \REW\Core\Interfaces\DBInterface $db = null)
    {
        parent::__construct($user, $db);

        // Agent Opt-Out Feature
        if ($this->optout === null) {
            $this->optout = new Backend_Agent_OptOut;
        }
    }

    /**
     * Load Agent Row from Database
     *
     * @return null|array
     * @throws PDOException
     */
    function getUserRow()
    {
        try {
            // Set User Data
            $this->data = $this->db->fetch("SELECT * FROM `" . Settings::getInstance()->TABLES['LM_AGENTS'] . "` WHERE `id` = '" . $this->user . "';");

            // Return User Data
            return $this->data;

        // Query Error
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Display Link to Agent Summary
     *
     * @return HTML Anchor Link
     */
    public function displayLink()
    {
        if ($this->getUserData('id') < 1) {
            return '(Unknown Agent)';
        }
        return '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=' . $this->getUserData('id') . '">' . $this->getUserData('first_name') . ' ' . $this->getUserData('last_name') . '</a>';
    }

    /**
     * @see History_User::save()
     */
    public function save($event)
    {
        if (parent::save($event)) {
            // Update Agent's Timestamp
            $this->optout->update($this->getUserData('id'), $event);
        }
    }
}
