<?php

namespace REW\Backend\Auth\Team;

use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Auth;
use Backend_Team;
use REW\Core\Interfaces\SettingsInterface;
use Util_IDX;

/**
 * Class SubdomainAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth\Team
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
     * @var Backend_Team
     */
    private $team;

    /**
     * SubdomainAuth constructor.
     * @param SettingsInterface $settings
     * @param AuthInterface $auth
     */
    public function __construct(SettingsInterface $settings, AuthInterface $auth)
    {
        parent::__construct($settings);
        $this->settings = $settings;
        $this->auth = $auth;
    }

    /**
     * @param Backend_Team $team
     */
    public function setTeam(Backend_Team $team)
    {
        $this->team = $team;
    }

    /**
     * Can Create Team Subdomains
     *
     * @return bool
     */
    public function canCreateSubdomains()
    {
        return !$this->settings->MODULES['REW_LITE']
            && $this->settings['MODULES']['REW_TEAMS'] && $this->settings['MODULES']['REW_TEAM_CMS']
            && ($this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_TEAMS_MANAGE_ALL)
            || $this->auth->adminPermission(AuthInterface::PERM_TEAMS_MANAGE));
    }

    /**
     * Can Manage All Team Subdomains
     *
     * @return bool
     */
    public function canManageSubdomains()
    {
        return $this->settings['MODULES']['REW_TEAMS'] && $this->settings['MODULES']['REW_TEAM_CMS']
            && ($this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_TEAMS_MANAGE_ALL));
    }

    /**
     * Can Manage Team Subdomains that this authuser belongs to
     *
     * @return bool
     */
    public function canManageOwnSubdomain()
    {
        return $this->settings['MODULES']['REW_TEAMS']
            && $this->settings['MODULES']['REW_TEAM_CMS']
            && !empty($this->team)
            && $this->team['subdomain'] == 'true'
            && (($this->auth->isAgent()
            && in_array($this->auth->info('id'), $this->team->getAgents([Backend_Team::PERM_EDIT_SUBDOMAIN])))
            || $this->auth->isSuperAdmin());
    }

    /**
     * Can Manage Agent CMS Snippets
     *
     * @return bool
     */
    public function canManageSnippets()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * Can Manage Team IDX Snippets
     *
     * @return bool
     */
    public function canManageIDXSnippets()
    {
        // Acquire List Of IDX Feeds
        $feeds = !empty($this->settings['IDX_FEEDS'])
            ? Util_IDX::parseFeeds(array_keys($this->settings['IDX_FEEDS']))
            : Util_IDX::parseFeeds(array($this->settings['IDX_FEEDS']));

        // If It's An Agent Cms Load Up The List Of Feeds The Agent Cms Has Access To
        $teamFeeds = !empty($context) && !empty($context['subdomain_idxs'])
            ? explode(",", $context['subdomain_idxs'])
            : array();

        return $this->settings['MODULES']['REW_IDX_SNIPPETS']
            && (($this->canManageSubdomains() && !empty($feeds))
            || ($this->canManageOwnSubdomain()
            && !empty(array_intersect($teamFeeds, $feeds))));
    }

    /**
     * Can Manage BDX Snippets
     *
     * @return bool
     */
    public function canManageBDXSnippets()
    {
        return $this->settings['MODULES']['REW_BUILDER']
            && $this->canManageSnippets();
    }

    /**
     * Can Manage Team Subdomain Conversion Tracking
     *
     * @return bool
     */
    public function canManageConversionTracking()
    {
        return $this->settings['MODULES']['REW_CONVERSION_TRACKING']
            && ($this->canManageOwnSubdomain()
            || $this->canManageSubdomains());
    }

    /**
     * Can Manage Team Subdomain Radio Landing Pages
     *
     * @return bool
     */
    public function canManageRadioLandingPage()
    {
        return $this->settings['MODULES']['REW_RADIO_LANDING_PAGE']
            && ($this->canManageOwnSubdomain()
            || $this->canManageSubdomains());
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
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * Can Manage CMS Pages
     *
     * @return bool
     */
    public function canManagePages()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * Can Delete CMS Snippets
     *
     * @return bool
     */
    public function canDeleteSnippets()
    {
        return true;
    }

    /**
     * Can View Tools
     *
     * @return bool
     */
    public function canViewTools()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canManageBackup()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageRewrites()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageSlideshow()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageTestimonials()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageTracking()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }


    /**
     * @inheritDoc
     */
    public function canManageHomepage()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canViewPages()
    {
        return $this->canManageSubdomains() || $this->canManageOwnSubdomain();
    }

    /**
     * @inheritDoc
     */
    public function canManageCommunities()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageDevelopments()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageOwnDevelopments()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canDeleteDevelopments()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canManageDirectorySnippets()
    {
        return false;
    }
}
