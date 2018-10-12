<?php

use REW\Core\Interfaces\PageInterface;

/**
 * OAuth_Calendar is an abstract class used for pushing data to third party calendars
 */

abstract class OAuth_Calendar
{
    
    /**
     * URL to Access Token
     * @var string
     */
    protected $access_token;
    
    /**
     * URL to Access Token
     * @var string
     */
    protected $calendar_id;
    
    /**
     * Calendar timezone
     * @var string
     */
    protected $timezone;
    
    /**
     * Page Object
     * @var PageInterface
     */
    protected $page;
    
    /**
     * Auth Object
     * @var object
     */
    protected $authuser;
    
    /**
     * Set Access Token
     *
     * @return void
     */
    public function __construct(PageInterface $page, Auth $authuser)
    {
        $this->page = $page;
        $this->authuser = $authuser;
        $this->validateToken();
    }
    
    /**
     * Get Calendar ID
     * @param calendar_id string
     * @return boolean
     */
    abstract protected function getCalendar();
    
    /**
     * Get Calendar ID
     * @param object $event OAuth_Event
     * @param string $type push type ("INSERT", "UPDATE")
     * @return boolean
     */
    abstract protected function push(OAuth_Event $event, $type);
    
    /**
     * Update a calendar event
     * @param string $id Event ID
     * @return boolean
     */
    abstract public function deleteEvent($id);
    
    /**
     * Validates access token
     * @param object $page Page object
     * @param object $authuser Auth object
     * @return boolean
     */
    abstract public function validateToken();
    

    /**
     * Checks if the provided timestamp is past the current time
     * @param int $expire_time unix timestamp of token expiry time
     * @return boolean
     */
    protected function isExpired($expire_time)
    {
        return $expire_time <= time();
    }
}
