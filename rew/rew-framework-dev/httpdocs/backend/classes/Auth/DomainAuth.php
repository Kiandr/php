<?php

namespace REW\Backend\Auth;

use REW\Backend\Auth\Agent\SubdomainAuth;
use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Core\Interfaces\AuthInterface;

/**
 * Class DomainAuth
 * @package REW\Backend\Auth
 *
 * A simple auth router for domains, since they use multiple auth classes, unlike subdomains.
 */
class DomainAuth implements SubdomainAuthInterface
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var ContentAuth
     */
    private $contentAuth;

    /**
     * @var ToolsAuth
     */
    private $toolsAuth;

    /**
     * @var DevelopmentsAuth
     */
    private $developmentsAuth;

    /**
     * @var SubdomainAuth
     */
    private $agentSubdomainAuth;

    /**
     * DomainAuth constructor.
     * @param AuthInterface $auth
     * @param ContentAuth $contentAuth
     * @param ToolsAuth $toolsAuth
     * @param DevelopmentsAuth $developmentsAuth
     * @param SubdomainAuth $agentSubdomainAuth
     */
    public function __construct(
        AuthInterface $auth,
        ContentAuth $contentAuth,
        ToolsAuth $toolsAuth,
        DevelopmentsAuth $developmentsAuth,
        SubdomainAuth $agentSubdomainAuth
    ) {
        $this->auth = $auth;
        $this->contentAuth = $contentAuth;
        $this->toolsAuth = $toolsAuth;
        $this->developmentsAuth = $developmentsAuth;
        $this->agentSubdomainAuth = $agentSubdomainAuth;
    }

    /**
     * Can Create Subdomains
     *
     * @param mixed $context The context for this check
     *
     * @return bool
     */
    public function canCreateSubdomains()
    {
        return $this->agentSubdomainAuth->canCreateSubdomains();
    }

    /**
     * Can Manage All Subdomains
     *
     * @return bool
     */
    public function canManageSubdomains()
    {
        return $this->agentSubdomainAuth->canManageSubdomains();
    }

    /**
     * Can Manage Subdomains that this authuser belongs to
     *
     * @return bool
     */
    public function canManageOwnSubdomain()
    {
        return $this->agentSubdomainAuth->canManageOwnSubdomain();
    }

    /**
     * Can Manage CMS Snippets
     *
     * @return bool
     */
    public function canManageSnippets()
    {
        return $this->contentAuth->canManageSnippets($this->auth);
    }

    /**
     * Can Manage IDX Snippets
     *
     * @return bool
     */
    public function canManageIDXSnippets()
    {
        return $this->contentAuth->canManageIDXSnippets($this->auth);
    }

    /**
     * Can Manage Snippets
     *
     * @return bool
     */
    public function canManageBDXSnippets()
    {
        return $this->contentAuth->canManageBDXSnippets($this->auth);
    }

    /**
     * Can Manage Subdomain Conversion Tracking
     *
     * @return bool
     */
    public function canManageConversionTracking()
    {
        return $this->toolsAuth->canManageConversionTracking($this->auth);
    }

    /**
     * Can Manage Subdomain Radio Landing Pages
     *
     * @return bool
     */
    public function canManageRadioLandingPage()
    {
        return $this->toolsAuth->canManageRadioLandingPage($this->auth);
    }

    /**
     * Can Manage Navigation
     *
     * @return bool
     */
    public function canManageNav()
    {
        return $this->contentAuth->canManageNav($this->auth);
    }

    /**
     * Can Delete CMS Pages
     *
     * @return bool
     */
    public function canDeletePages()
    {
        return $this->contentAuth->canDeletePages($this->auth);
    }

    /**
     * Can Manage CMS Pages
     *
     * @return bool
     */
    public function canManagePages()
    {
        return $this->contentAuth->canManagePages($this->auth);
    }

    /**
     * Can Delete CMS Snippets
     *
     * @return bool
     */
    public function canDeleteSnippets()
    {
        return $this->contentAuth->canDeleteSnippets($this->auth);
    }

    /**
     * Can View Tools
     *
     * @return bool
     */
    public function canViewTools()
    {
        return $this->toolsAuth->canViewTools($this->auth);
    }

    /**
     * Can Manage Rewrite
     *
     * @return bool
     */
    public function canManageRewrites()
    {
        return $this->toolsAuth->canManageRewrites($this->auth);
    }

    /**
     * Can Manage Slideshows
     *
     * @return bool
     */
    public function canManageSlideshow()
    {
        return $this->toolsAuth->canManageSlideshow($this->auth);
    }

    /**
     * Can Manage Testimonials
     *
     * @return bool
     */
    public function canManageTestimonials()
    {
        return $this->toolsAuth->canManageTestimonials($this->auth);
    }

    /**
     * Can Manage Tracking Codes
     *
     * @return bool
     */
    public function canManageTracking()
    {
        return $this->toolsAuth->canManageTracking($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageBackup()
    {
        return $this->toolsAuth->canManageBackup($this->auth);
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
    public function canManageHomepage()
    {
        return $this->contentAuth->canManageHomepage($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canViewPages()
    {
        return $this->contentAuth->canViewPages($this->auth);
    }

    /**
     * @inheritDoc
     */
    public function canManageDirectorySnippets()
    {
        return $this->contentAuth->canManageDirectorySnippets($this->auth);
    }
}
