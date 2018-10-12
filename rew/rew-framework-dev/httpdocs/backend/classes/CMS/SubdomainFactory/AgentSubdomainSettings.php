<?php

namespace REW\Backend\CMS\SubdomainFactory;

use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Auth\DomainAuth;
use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Agent\SubdomainAuth;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

class AgentSubdomainSettings extends SubdomainSettings
{
    /**
     * The type of subdomain that this represents. This is also the SQL field that will be used.
     * @const string
     */
    const TYPE = 'agent';

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var DBInterface
     */
    private $db;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DomainAuth
     */
    private $domainAuth;

    /**
     * @var \Backend_Agent
     */
    private $agent;

    /**
     * @var array
     */
    protected $subdomains;

    /**
     * Agent Subdomain Settings constructor.
     *
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param ServerRequestInterface $serverRequest
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param ContainerInterface $container
     * @param ContentAuth $contentAuth
     * @param SettingsInterface $settings
     * @param IDXFactoryInterface $idxFactory
     * @param \Backend_Agent $agent
     */
    public function __construct(
        SubdomainFactoryInterface $subdomainFactory,
        ServerRequestInterface $serverRequest,
        AuthInterface $auth,
        DBInterface $db,
        ContainerInterface $container,
        DomainAuth $domainAuth,
        SettingsInterface $settings,
        IDXFactoryInterface $idxFactory,
        \Backend_Agent $agent = null
    ) {
        parent::__construct($subdomainFactory, $settings, $idxFactory, static::TYPE);
        $this->serverRequest = $serverRequest;
        $this->auth = $auth;
        $this->db = $db;
        $this->container = $container;
        $this->domainAuth = $domainAuth;
        $this->agent = $agent;
    }

    /**
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
     */
    public function configureDefault($checkPermission = null)
    {
        if (!$checkPermission || ($checkPermission && $this->domainAuth->$checkPermission())) {
            // We can edit the root domain. This is not the default subdomain.
            return false;
        }

        if ($this->auth->info('cms') == 'true') {
            $this->configureFromArray([$this->getType() => $this->auth->info('id')]);

            if ($checkPermission && !$this->getAuth()->$checkPermission()) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Configures this subdomain from the current request, if possible.
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
     * @throws \REW\Backend\Exceptions\MissingId\MissingAgentException if agent could not be loaded
     */
    public function configureFromRequest($checkPermission = null)
    {
        $body = $this->serverRequest->getParsedBody();
        $query = $this->serverRequest->getQueryParams();
        $type = $this->getType();
        $id = isset($body[$type]) ? $body[$type] : (isset($query[$type]) ? $query[$type] : null);

        if ($this->configureFromArray([$type => $id])) {
            return !$checkPermission || $this->getAuth()->$checkPermission();
        }

        return false;
    }

    /**
     * @return SubdomainAuthInterface
     */
    public function getAuth()
    {
        $subdomainAuth = $this->container->make(SubdomainAuth::class);

        if (isset($this->agent)) {
            $subdomainAuth->setAgent($this->agent);
        }

        return $subdomainAuth;
    }

    /**
     * Get All Editable Agent Subdomain Settings
     * @throws PDOException
     * @return AgentSubdomainSettings[]
     */
    public function getSubdomainList()
    {
        if (!empty($this->subdomains)) {
            return $this->subdomains;
        }

        $whereSql = '';
        $whereParams = [];
        if (!$this->getAuth()->canManageSubdomains()) {
            $whereSql = ' AND `a`.`id` = ?';
            $whereParams = [$this->auth->info('id')];
        }

        $queryAgentsSubdomains = $this->db->prepare(
            'SELECT `a`.*, `auth`.`username`, `auth`.`password`, `auth`.`last_logon`'
            . ' FROM `' . LM_TABLE_AGENTS . '` `a` JOIN `auth` ON `a`.`auth` = `auth`.`id`'
            . ' WHERE `a`.`cms` = "true"'
            . $whereSql
            . ' ORDER BY `a`.`cms_link`'
        );

        $queryAgentsSubdomains->execute($whereParams ?: []);

        $subdomains = [];
        while ($row = $queryAgentsSubdomains->fetch()) {
            $agent = new \Backend_Agent($row);

            $subdomain = $this->container->make(static::class, ['agent' => $agent]);
            $subdomains[] =  $this->container->make(SubdomainInterface::class, ['subdomainSettings' => $subdomain]);
        }

        return $this->subdomains = $subdomains;
    }

    /**
     * Gets the Subdomains Id
     * @return int|null
     */
    public function getId()
    {
        return isset($this->agent) ? $this->agent->info('id') : null;
    }

    /**
     * Gets the Subdomains Link
     * @return string
     */
    public function getLink()
    {
        return isset($this->agent) ? sprintf(URL_AGENT_SITE, $this->agent->info('cms_link')) : null;
    }

    /**
     * Gets the Subdomain Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getLink();
    }

    /**
     * @inheritDoc
     */
    public function configureFromArray(array $context)
    {
        if ($id = $this->getSubdomainIdFromArray($context)) {
            if ($id !== '1') {
                $this->agent = \Backend_Agent::load($id);
                return true;
            }
        }

        return false;
    }

    /**
     * Validates that this is adequately configured
     * @throws \REW\Backend\Exceptions\MissingId\MissingAgentException
     */
    public function validate()
    {
        if (!$this->agent) {
            throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
        }
    }

    /**
     * Get the default feed for this subdomain
     * @return string
     */
    public function getDefaultFeed()
    {
        if (!$this->agent) {
            return '';
        }

        // Get List Of Available IDX Feeds For The Agent Site
        $default_feed = $this->getDefaultFeedFromArray(explode(",", $this->agent['cms_idxs']));

        return $default_feed;
    }
}
