<?php

namespace REW\Backend\CMS\SubdomainFactory;

use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Auth\ContentAuth;
use REW\Backend\Auth\DomainAuth;
use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

class DomainSettings extends SubdomainSettings
{
    /**
     * The type of subdomain that this represents. This is also the SQL field that will be used.
     * @const string
     */
    const TYPE = 'domain';

    /**
     * @var SubdomainFactoryInterface
     */
    private $subdomainFactory;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var DomainAuth
     */
    private $domainAuth;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * Subdomain Factory constructor.
     *
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param SettingsInterface $settings
     * @param DomainAuth $domainAuth
     * @param ContainerInterface $container
     * @param IDXFactoryInterface $idxFactory
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct(
        SubdomainFactoryInterface $subdomainFactory,
        SettingsInterface $settings,
        DomainAuth $domainAuth,
        ContainerInterface $container,
        IDXFactoryInterface $idxFactory,
        ServerRequestInterface $serverRequest
    ) {
        parent::__construct($subdomainFactory, $settings, $idxFactory, static::TYPE);
        $this->subdomainFactory = $subdomainFactory;
        $this->settings = $settings;
        $this->domainAuth = $domainAuth;
        $this->container = $container;
        $this->serverRequest = $serverRequest;
    }

    /**
     * Gets the Subdomains Link
     * @return string
     */
    public function getLink()
    {
        return $this->settings['URLS']['URL_DOMAIN'];
    }

    /**
     * Gets the Subdomain Title
     * @return string
     */
    public function getTitle()
    {
        return $this->settings['URLS']['URL_DOMAIN'];
    }

    /**
     * Configures these settings from the current request
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * Note that the purpose of this is to determine whether or not we're managing the main site.
     * @return bool True if we're managing the main site. False if we're managing a subdomain.
     */
    public function configureFromRequest($checkPermission = null)
    {

        $body = $this->serverRequest->getParsedBody();
        $query = $this->serverRequest->getQueryParams();

        $subdomains = array_keys($this->subdomainFactory->getSubdomainOptions());

        foreach ($subdomains as $type) {
            $id = isset($body[$type]) ? $body[$type] : (isset($query[$type]) ? $query[$type] : null);

            if (isset($id)) {
                return false;
            }
        }

        return !$checkPermission || $this->getAuth()->$checkPermission();
    }

    /**
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
     */
    public function configureDefault($checkPermission = null)
    {
        if ($checkPermission) {
            return $this->getAuth()->$checkPermission();
        }

        return true;
    }

    /**
     * Configures from the array
     * @param array $context
     * @return bool
     */
    public function configureFromArray(array $context)
    {
        foreach ($this->subdomainFactory->getTypes() as $type) {
            if (!is_null($context[$type]) && !($type === 'agent' && $context[$type] === '1')) {
                return false;
            }
        }

        return !$checkPermission || $this->getAuth()->$checkPermission();
    }

    /**
     * @return SubdomainAuthInterface
     */
    public function getAuth()
    {
        return $this->domainAuth;
    }

    /**
     * Get list of available subdomains
     * @return SubdomainInterface[]
     */
    public function getSubdomainList()
    {
        return [$this->container->make(SubdomainInterface::class, ['subdomainSettings' => $this])];
    }

    /**
     * Validates that this is adequately configured
     * @throws \REW\Backend\Exceptions\UnauthorizedPageException
     */
    public function validate()
    {
        return true;
    }

    /**
     * Get the default feed for this subdomain
     * @return string
     */
    public function getDefaultFeed()
    {
        return $this->settings['IDX_FEED'];
    }
}
