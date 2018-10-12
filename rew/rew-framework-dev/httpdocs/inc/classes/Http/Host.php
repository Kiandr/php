<?php

use REW\Core\Interfaces\Http\HostInterface;

/**
 * Http_Host
 * Parses HTTP_HOST
 *
 */
class Http_Host implements HostInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * Parts
     * @var array
     */
    protected $parts;

    /**
     * Host
     * @var string
     */
    protected $host;

    /**
     * Domain
     * @var string
     */
    protected $domain;

    /**
     * Top Level Domain
     * @var string
     */
    protected $tld;

    /**
     * Dev Location
     * @var string
     */
    protected $dev;

    /**
     * Subdomain
     * @var string
     */
    protected $subdomain;

    /**
     * List of REW Development Domains
     * @var array
     */
    protected $dev_domains = array('rewdev', 'rewlec', 'rewtemplates', 'rewpert', 'rewsites', 'rewdemo');

    /**
     * Get Host
     *
     * @param   bool    $reload
     * @return  string
     */
    public function getHost($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        if ($reload || is_null($this->host)) {
            $this->host = (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
        }
        return $this->host;
    }
    /**
     * Get Parts
     *
     * @param   bool    $reload
     * @return  string
     */
    public function getParts($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        if ($reload || is_null($this->parts)) {
            $this->parts = explode('.', $this->getHost($reload));
        }
        return $this->parts;
    }

    /**
     * Get Domain
     *
     * @param   bool    $reload
     * @return  string
     */
    public function getDomain($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        if ($reload || is_null($this->domain)) {
            $parts = $this->getParts();

            // Domain Name
            $this->domain = implode('.', array_slice($parts, -2));

            // Dev Location
            if ($this->getDev()) {
                if ($parts[count($parts) - 4] === 'dev') {
                    $this->domain = implode('.', array_slice($parts, -4));
                } else {
                    $this->domain = implode('.', array_slice($parts, -3));
                }
            }
        }
        return $this->domain;
    }

    /**
     * Get Domain URL
     */
    public function getDomainUrl()
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        return Http_Uri::getScheme() . '://' . ($this->getDev() ? '' : 'www.') . $this->getDomain() . '/';
    }

    /**
     * Get Top Level Domain
     *
     * @param   bool    $reload
     * @return  string
     */
    public function getTld($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        if ($reload || is_null($this->tld)) {
            $parts = $this->getParts();
            $this->tld = implode('.', array_slice($parts, -1));
        }
        return $this->tld;
    }

    /**
     * Get Dev Location
     *
     * @param   bool    $reload
     * @return  string
     */
    public function getDev($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        if ($reload || is_null($this->dev)) {
            $parts = $this->getParts($reload);
            $tld = array_pop($parts);
            $dmn = array_pop($parts);
            $subdomain = array_pop($parts);
            if (in_array($dmn, $this->dev_domains) || $subdomain == 'dev') {
                $this->dev = $subdomain;
            }
        }
        return $this->dev;
    }

    /**
     * Is Dev Location
     *
     * @param bool $reload
     * @return bool
     */
    public function isDev($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        $this->dev = $this->getDev($reload);
        return !empty($this->dev) ? true : false;
    }

    /**
     * Get Subdomain
     *
     * @param   bool    $reload
     * @return  string
     */
    public function getSubdomain($reload = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        if ($reload || is_null($this->subdomain)) {
            $host = $this->getHost($reload);
            $domain = $this->getDomain($reload);
            $subdomain = str_replace($domain, '', $host);
            $this->subdomain = rtrim($subdomain, '.');
        }
        return $this->subdomain;
    }

    /**
     * Get top level domain to use for cookies
     * @return string
     */
    public function getCookieDomain()
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HostInterface::class, __FUNCTION__, func_get_args());
        }

        $domain = $this->getDomain();
        if ($this->isDev()) {
            $domain = preg_replace('/^dev\./', '', $domain);
        }
        return '.' . $domain;
    }
}
