<?php

namespace REW\Backend\CMS;

use Psr\Http\Message\ServerRequestInterface;

use REW\Backend\CMS\SubdomainFactory\DomainSettings;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactory\SubdomainSettingsInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DBInterface;

class SubdomainFactory implements SubdomainFactoryInterface
{
    /**
     * Maximum subdomains per type (ie agent, team, domain). This affects sorting.
     * @const int
     */
    const MAX_SUBDOMAINS_PER_TYPE = 10000000;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var DBInterface
     */
    protected $dbFactory;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Subdomain Factory constructor.
     *
     * @param SettingsInterface $settings
     * @param DBInterface $db
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     */
    public function __construct(
        SettingsInterface $settings,
        DBInterface $db,
        ServerRequestInterface $request,
        ContainerInterface $container
    ) {
            $this->settings = $settings;
            $this->db       = $db;
            $this->request  = $request;
            $this->container= $container;
    }

    /**
     * Builds a subdomain object from the current request.
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return SubdomainInterface|bool
     */
    public function buildSubdomainFromRequest($checkPermission = null)
    {
        $subdomain = $this->buildSubdomain('configureFromRequest', $checkPermission);
        return $subdomain ?: false;
    }

    /**
     * Builds the default subdomain.
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return SubdomainInterface|bool
     */
    public function buildDefaultSubdomain($checkPermission = null)
    {
        $subdomain = $this->buildSubdomain('configureDefault', $checkPermission);
        return $subdomain ?: false;
    }

    /**
     * Builds a subdomain object from an array.
     * @param array $context
     * @return SubdomainInterface
     * @throws \Exception if no subdomain could be configured
     */
    public function buildSubdomainFromArray(array $context)
    {
        $subdomain = $this->buildSubdomain('configureFromArray', $context);
        if ($subdomain) {
            return $subdomain;
        }
        throw new \Exception('No subdomain could be built from this array.');
    }

    /**
     * Get List of Subdomains from Array of Subdomain Settings
     *
     * @param SubdomainSettingsInterface[] $settings Settings to load
     *
     * @return SubdomainInterface[]
     */
    public function getSubdomainsFromSettings(array $settings)
    {
        return array_map(
            function ($subdomainSettings) {
                return $this->getSubdomain($subdomainSettings);
            },
            $settings
        );
    }

    /**
     * Get List of Subdomain types
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->settings['subdomains']);
    }

    /**
     * Get list of available subdomains
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return SubdomainInterface[]
     */
    public function getSubdomainList($checkPermission = null)
    {
        $subdomainList = [];

        foreach ($this->settings['SUBDOMAINS'] as $subdomainType => $subdomainOptions) {
            // Check that Subdomain Options is Enabled
            if (!$this->checkSubdomainModuleEnabled($subdomainOptions)) {
                continue;
            }

            /** @var SubdomainSettingsInterface $settings */
            $settings = $this->container->get(
                $subdomainOptions['classes']['settings']
            );

            $subdomainListPart = $settings->getSubdomainList();
            $subdomainWeight = $subdomainOptions['weight'];

            foreach ($subdomainListPart as $index => $subdomain) {
                if (!$checkPermission || $subdomain->getAuth()->$checkPermission()) {
                    $subdomainList[static::MAX_SUBDOMAINS_PER_TYPE * $subdomainWeight + $index] = $subdomain;
                }
            }
        }
        ksort($subdomainList, SORT_NUMERIC);

        return array_values($subdomainList);
    }

    /**
     * Get all subdomain options
     * @return array
     */
    public function getSubdomainOptions()
    {
        return $this->settings['SUBDOMAINS'];
    }

    /**
     * Check that required subdomain module is enabled
     * @param array $subdomain
     * @return boolean
     */
    public function checkSubdomainModuleEnabled($subdomain)
    {
        $module = $subdomain['module'];
        if (isset($module)) {
            return $this->settings['MODULES'][$module];
        }
        return true;
    }

    /**
     * Builds the default subdomain.
     * @param string $configFrom
     * @param string|null $checkPermission The permission check to run on each subdomain
     * @return SubdomainInterface|false
     */
    protected function buildSubdomain($configFrom, $checkPermission = null)
    {
        foreach ($this->getSubdomainOptions() as $subdomainType => $subdomainOptions) {
            // Check that Subdomain Options is Enabled
            if (!$this->checkSubdomainModuleEnabled($subdomainOptions)) {
                continue;
            }

            /** @var SubdomainSettingsInterface $settings */
            $settings = $this->container->get(
                $subdomainOptions['classes']['settings']
            );
            if ($settings instanceof SubdomainSettingsInterface && $settings->$configFrom($checkPermission)) {
                return $this->container->make(SubdomainInterface::class, ['subdomainSettings' => $settings]);
            }
        }
        return false;
    }
}
