<?php

/**
 * Http_Cookie
 * Object-oriented representation of a Cookie to be sent in an HTTP response
 *
 */
class Http_Cookie
{

    /**
     * Cookie Name
     * @var string
     */
    protected $name;

    /**
     * Cookie Value
     * @var string
     */
    protected $value;

    /**
     * Cookie Expiry Time (UNIX timestamp)
     * @var int
     */
    protected $expires;

    /**
     * Cookie Path
     * @var string
     */
    protected $path;

    /**
     * Cookie Domain
     * @var string
     */
    protected $domain;

    /**
     * Secure Cookie (Only Transmit Over HTTPS)
     * @var bool
     */
    protected $secure;

    /**
     * HTTP Only (Only Transmit Over HTTP)
     * @var bool
     */
    protected $httponly;

    /**
     * Create New Cookie
     *
     * @param   string  $name       The cookie name
     * @param   string  $value      The cookie value
     * @param   mixed   $time       The duration of the cookie;
     *                              If integer, should be a UNIX timestamp;
     *                              If string, converted to UNIX timestamp with `strtotime`;
     * @param   string  $path       The path on the server in which the cookie will be available on
     * @param   string  $domain     The domain that the cookie is available to
     * @param   bool    $secure     Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client
     * @param   bool    $httponly   When TRUE the cookie will be made accessible only through the HTTP protocol
     * @return  void
     */
    public function __construct($name, $value = null, $expires = 0, $path = null, $domain = null, $secure = false, $httponly = false)
    {

        // Set Details
        $this->setName($name);
        $this->setValue($value);
        $this->setExpires($expires);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure($secure);
        $this->setHttpOnly($httponly);

        // Set Cookie
        //setcookie($this->getName(), $this->getValue(), $this->getExpires(), $this->getPath(), $this->getDomain(), $this->getSecure(), $this->getHttpOnly());
    }

    /**
     * Get cookie name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cookie name
     *
     * @param   string $name
     * @return  void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get cookie value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set cookie value
     *
     * @param   string $value
     * @return  void
     */
    public function setValue($value)
    {
        $this->value = (string) $value;
    }

    /**
     * Get cookie expiration time
     *
     * @return int UNIX timestamp
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set cookie expiration time
     *
     * @param   string|int Cookie expiration time
     * @return  void
     */
    public function setExpires($time)
    {
        $this->expires = is_string($time) ? strtotime($time) : (int) $time;
    }

    /**
     * Get cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set cookie path
     *
     * @param   string $path
     * @return  void
     */
    public function setPath($path)
    {
        $this->path = (string) $path;
    }

    /**
     * Get cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set cookie domain
     *
     * @param   string $domain
     * @return  void
     */
    public function setDomain($domain)
    {
        $this->domain = (string) $domain;
    }

    /**
     * Is cookie sent only if SSL/HTTPS is used?
     *
     * @return bool
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Set whether cookie is sent only if SSL/HTTPS is used
     *
     * @param   bool $secure
     * @return  void
     */
    public function setSecure($secure)
    {
        $this->secure = (bool) $secure;
    }

    /**
     * Is cookie sent with HTTP protocol only?
     *
     * @return bool
     */
    public function getHttpOnly()
    {
        return $this->httponly;
    }

    /**
     * Set whether cookie is sent with HTTP protocol only
     *
     * @param   bool $httponly
     * @return  void
     */
    public function setHttpOnly($httponly)
    {
        $this->httponly = (bool) $httponly;
    }
}
