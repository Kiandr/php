<?php

namespace REW\Backend\CMS\SubdomainFactory;

use Backend_Team;
use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Auth\DomainAuth;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Team\SubdomainAuth;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

class TeamSubdomainSettings extends SubdomainSettings
{
    /**
     * The type of subdomain that this represents. This is also the SQL field that will be used.
     * @const string
     */
    const TYPE = 'team';

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
     * @var Backend_Team
     */
    private $team;

    /**
     * @var array SubdomainInterface
     */
    private $teamSubdomains;

    /**
     * Agent Subdomain Settings constructor.
     *
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param ServerRequestInterface $serverRequest
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param ContainerInterface $container
     * @param DomainAuth $domainAuth
     * @param SettingsInterface $settings
     * @param IDXFactoryInterface $idxFactory
     * @param Backend_Team $team
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
        Backend_Team $team = null
    ) {
        parent::__construct($subdomainFactory, $settings, $idxFactory, static::TYPE);
        $this->serverRequest = $serverRequest;
        $this->auth = $auth;
        $this->db = $db;
        $this->container = $container;
        $this->domainAuth= $domainAuth;
        $this->team = $team;
    }

    /**
     * Gets the Default Team Subdomain
     *
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
     */
    public function configureDefault($checkPermission = null)
    {
        if (!$checkPermission || ($checkPermission && $this->domainAuth->canManagePages())) {
            // We can edit the root domain. This is not the default subdomain.
            return false;
        }

        // Require permission to edit any team subdomains
        if (!$this->getAuth()->canManageSubdomains()) {
            // Does not have permission to view all and not an agent
            if (!$this->auth->isAgent()) {
                return false;
            }

            // Get Team Specific Queries
            $join_sql = ' JOIN `' . TABLE_TEAM_AGENTS . '` AS `ta` ON `t`.`id` = `ta`.`team_id`';
            $where_sql = ' AND ((`ta`.`agent_id` = ? AND `ta`.`granted_permissions` & ?) OR `t`.`agent_id` = ?)';
            $where_params = [$this->auth->info('id'), Backend_Team::PERM_EDIT_SUBDOMAIN, $this->auth->info('id')];
        }

        // Query Team Subdomain
        $team_query = $this->db->prepare(
            "SELECT `t`.`id`"
            . " FROM `" . TABLE_TEAMS . "` AS `t`"
            . $join_sql
            . " WHERE `t`.`subdomain` = 'true'"
            . $where_sql
            . " ORDER BY `t`.`id` ASC LIMIT 1"
        );
        $team_query->execute($where_params ?: []);
        $teamId = $team_query->fetchColumn();
        $team = Backend_Team::load($teamId);
        if (isset($team) && $team instanceof Backend_Team) {
            // Return Settings from Auth's First Team Subdomain
            $this->configureFromArray([$this->getType() => $team->getId()]);

            if ($checkPermission && !$this->getAuth()->$checkPermission()) {
                return false;
            }

            return true;
        }

        // Return false if no default team exists
        return false;
    }

    /**
     * Configures this subdomain from the current request, if possible.
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
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
     * Sets the correct team for our auth class and returns said auth class.
     *
     * @return SubdomainAuth
     */
    public function getAuth()
    {
        $subdomainAuth = $this->container->make(SubdomainAuth::class);

        if ($this->team) {
            $subdomainAuth->setTeam($this->team);
        }

        return $subdomainAuth;
    }

    /**
     * Get All Editable Team Subdomain Settings
     *
     * @return AgentSubdomainSettings[]
     */
    public function getSubdomainList()
    {
        if (!empty($this->teamSubdomains)) {
            return $this->teamSubdomains;
        }

        $join_sql = '';
        $where_sql = '';
        $where_params = [];
        if (!$this->getAuth()->canManageSubdomains()) {
            $join_sql = ' JOIN `' . TABLE_TEAM_AGENTS . '` AS `ta` ON `t`.`id` = `ta`.`team_id`';
            $where_sql = ' AND ((`ta`.`agent_id` = ? AND `ta`.`granted_permissions` & ?) OR `t`.`agent_id` = ?)';
            $where_params = [$this->auth->info('id'), Backend_Team::PERM_EDIT_SUBDOMAIN, $this->auth->info('id')];
        }

        $queryTeamSubdomains = $this->db->prepare(
            "SELECT `t`.`id` AS `id`"
            . " FROM `" . TABLE_TEAMS . "` AS `t`"
            . $join_sql
            . " WHERE `t`.`subdomain` = 'true'"
            . $where_sql
            . " GROUP BY `t`.`id`"
            . " ORDER BY `t`.`subdomain_link`"
        );
        $queryTeamSubdomains->execute($where_params ?: []);

        $teamSubdomains = [];
        while ($teamSubdomainId = $queryTeamSubdomains->fetchColumn()) {
            $team = Backend_Team::load($teamSubdomainId);

            $subdomain = $this->container->make(static::class, ['team' => $team]);
            $teamSubdomains[] = $this->container->make(SubdomainInterface::class, ['subdomainSettings' => $subdomain]);
        }

        return $this->teamSubdomains = $teamSubdomains;
    }

    /**
     * Gets the Subdomains Id
     * @return int|null
     */
    public function getId()
    {
        return isset($this->team) ? $this->team->getId() : null;
    }

    /**
     * Gets the Subdomains Link
     * @return string
     */
    public function getLink()
    {
        return isset($this->team) ? sprintf(URL_AGENT_SITE, $this->team->info('subdomain_link')) : null;
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
            $this->team = \Backend_Team::load($id);
            return true;
        }

        return false;
    }

    /**
     * Validates that this is adequately configured
     * @throws \REW\Backend\Exceptions\MissingId\MissingTeamException
     */
    public function validate()
    {
        if (!$this->team) {
            throw new \REW\Backend\Exceptions\MissingId\MissingTeamException();
        }
    }

    /**
     * Get the default feed for this subdomain
     * @return string
     */
    public function getDefaultFeed()
    {
        if (!$this->team) {
            return '';
        }

        // Get List Of Available IDX Feeds For The Team Site
        $default_feed = $this->getDefaultFeedFromArray(explode(",", $this->team['subdomain_idxs']));

        return $default_feed;
    }
}
