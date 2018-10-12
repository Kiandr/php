<?php

namespace REW\Backend\Auth\Agent;

use REW\Backend\Auth\ContentAuth;
use REW\Backend\Auth\DevelopmentsAuth;
use REW\Backend\Auth\ToolsAuth;
use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Auth;
use REW\Core\Interfaces\SettingsInterface;
use \Util_IDX;
use \Backend_Agent;

/**
 * Class SubdomainAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth\Agent
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class SubdomainAuth extends Auth implements SubdomainAuthInterface
{
    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var ToolsAuth
     */
    private $toolsAuth;

    /**
     * @var DevelopmentsAuth
     */
    private $developmentsAuth;

    /**
     * @var ContentAuth
     */
    private $contentAuth;

    /**
     * @var Backend_Agent
     */
    private $agent;

    /**
     * SubdomainAuth constructor.
     * @param SettingsInterface $settings
     * @param AuthInterface $auth
     * @param ToolsAuth $toolsAuth
     * @param DevelopmentsAuth $developmentsAuth
     * @param ContentAuth $contentAuth
     */
    public function __construct(
        SettingsInterface $settings,
        AuthInterface $auth,
        ToolsAuth $toolsAuth,
        DevelopmentsAuth $developmentsAuth,
        ContentAuth $contentAuth
    ) {
        parent::__construct($settings);
        $this->settings = $settings;
        $this->auth = $auth;
        $this->toolsAuth = $toolsAuth;
        $this->developmentsAuth = $developmentsAuth;
        $this->contentAuth = $contentAuth;
    }

    /**
     * @param Backend_Agent $agent
     */
    public function setAgent(Backend_Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Can Create Agent Subdomain
     *
     * @return bool
     */
    public function canCreateSubdomains()
    {
        return !$this->settings->MODULES['REW_LITE']
            && $this->settings['MODULES']['REW_AGENT_CMS']
            && ($this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_AGENTS_MANAGE));
    }

    /**
     * Can Manage ALL CMS Pages
     *
     * @return bool
     */
    public function canManageSubdomains()
    {
        return $this->settings['MODULES']['REW_AGENT_CMS']
            && $this->auth->isSuperAdmin();
    }

    /**
     * Can Manage Agent CMS Pages
     *
     * @return bool
     */
    public function canManageOwnSubdomain()
    {
        return $this->settings['MODULES']['REW_AGENT_CMS']
            && !empty($this->agent)
            && $this->agent['cms'] == 'true'
            && $this->auth->isAgent()
            && $this->auth->info('id') == $this->agent->getId();
    }

    /**
     * Can Manage Active subdomain
     *
     * @return bool
     */
    public function canManageActiveSubdomain()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * Can Manage Agent CMS Snippets
     *
     * @return bool
     */
    public function canManageSnippets()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * Can Manage Agent IDX Snippets
     *
     * @return bool
     */
    public function canManageIDXSnippets()
    {
        // Acquire List Of IDX Feeds
        $feeds = !empty($this->settings->IDX_FEEDS)
            ? Util_IDX::parseFeeds(array_keys($this->settings->IDX_FEEDS))
            : Util_IDX::parseFeeds(array($this->settings->IDX_FEED));

        // If It's An Agent Cms Load Up The List Of Feeds The Agent Cms Has Access To
        $agent = Backend_Agent::load($this->auth->info('id'));
        $agentFeeds = !empty($agent['cms_idxs'])
            ? explode(",", $agent['cms_idxs'])
            : array();

        return $this->settings->MODULES['REW_IDX_SNIPPETS']
            && (($this->canManageSubdomains() && !empty($feeds))
            || ($this->canManageOwnSubdomain()
            && !empty(array_intersect($agentFeeds, $feeds))));
    }

    /**
     * Can Manage BDX Snippets
     *
     * @return bool
     */
    public function canManageBDXSnippets()
    {
        return $this->settings->MODULES['REW_BUILDER']
            && $this->canManageSnippets();
    }

    /**
     * Can Manage Conversion Tracking
     *
     * @return bool
     */
    public function canManageConversionTracking()
    {
        return $this->settings->MODULES['REW_CONVERSION_TRACKING'] && $this->canManageActiveSubdomain();
    }

    /**
     * Can Manage Radio Landing Pages
     *
     * @return bool
     */
    public function canManageRadioLandingPage()
    {
        return $this->settings->MODULES['REW_RADIO_LANDING_PAGE'] && $this->canManageActiveSubdomain();
    }

    /**
     * @return bool
     */
    public function canManageNav()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * Can Delete CMS Pages
     *
     * @return bool
     */
    public function canDeletePages()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * Can Manage CMS Pages
     *
     * @return bool
     */
    public function canManagePages()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * Can Delete CMS Snippets
     *
     * @return bool
     */
    public function canDeleteSnippets()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * Can View Tools
     *
     * @return bool
     */
    public function canViewTools()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canManageBackup()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canManageRewrites()
    {
        return $this->toolsAuth->canManageRewrites($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageSlideshow()
    {
        return $this->toolsAuth->canManageSlideshow($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageTestimonials()
    {
        return $this->toolsAuth->canManageTestimonials($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageTracking()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canManageHomepage()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canViewPages()
    {
        return $this->canManageActiveSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canManageCommunities()
    {
        return $this->toolsAuth->canManageCommunities($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageDevelopments()
    {
        return $this->developmentsAuth->canManageDevelopments($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageOwnDevelopments()
    {
        return $this->developmentsAuth->canManageOwnDevelopments($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canDeleteDevelopments()
    {
        return $this->developmentsAuth->canDeleteDevelopments($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageDirectorySnippets()
    {
        return $this->contentAuth->canManageDirectorySnippets($this->auth);
    }
}
