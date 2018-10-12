<?php

/**
 * OAuth_Analytics is an abstract class used for pulling analytics data from third party analytics tools
 */

abstract class OAuth_Analytics
{

    /**
     * URL to Access Token
     * @var string
     */
    protected $access_token;

    /**
     * Calendar timezone
     * @var string
     */
    protected $timezone;

    /**
     * Page Object
     * @var object
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
    public function __construct(Page &$page, Auth &$authuser)
    {
        $this->page = $page;
        $this->authuser = $authuser;
        $this->validateToken();
    }

    /**
     * Request XML Data from Google
     * @param string $url
     * @param array $params
     * @throws Exception If Non-200 Status Code Returned
     * @return SimpleXMLElement
     */
    abstract protected function getXML($url, $params = array());

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
