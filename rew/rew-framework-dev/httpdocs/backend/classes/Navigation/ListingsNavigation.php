<?php

namespace REW\Backend\Navigation;

use REW\Backend\Navigation\Navigation;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\Auth\ContentAuth;
use REW\Backend\Auth\ListingsAuth;
use REW\Backend\Auth\IDXAuth;
use REW\Backend\Auth\DevelopmentsAuth;
use \Skin;

/**
 * Class Listings Navigation
 *
 * @category Navigation
 * @package  REW\Backend\Navigation
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ListingsNavigation extends Navigation
{

    /**
     * User to get content nv for
     * @var AuthInterface
     */
    protected $auth;

    /**
     * Backend Settings
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Subdomain being edited
     * @var SubdomainInterface
     */
    protected $subdomain;

    /**
     * Content Auth
     * @var ContentAuth
     */
    protected $contentAuth;

    /**
     * Listings Auth
     * @var ListingsAuth
     */
    protected $listingAuth;

    /**
     * IDX Auth
     * @var IDXAuth
     */
    protected $idxAuth;

    /**
     * Developments Auth
     * @var DevelopmentsAuth
     */
    protected $developmentsAuth;

    /**
     * @var SubdomainFactory
     */
    protected $subdomainFactory;

    /**
     * Construct Content Navigation
     * @param AuthInterface $auth;
     * @param SettingsInterface $settings
     * @param ContentAuth $contentAuth
     * @param ListingsAuth $listingAuth
     * @param IDXAuth $idxAuth
     * @param DevelopmentsAuth $developmentsAuth
     * @param SubdomainInterface | NULL $subdomain
     */
    public function __construct(AuthInterface $auth, SettingsInterface $settings, ContentAuth $contentAuth, ListingsAuth $listingAuth, IDXAuth $idxAuth, DevelopmentsAuth $developmentsAuth, SubdomainFactoryInterface $subdomainFactory)
    {
        $this->auth = $auth;
        $this->settings = $settings;
        $this->contentAuth = $contentAuth;
        $this->listingAuth = $listingAuth;
        $this->idxAuth = $idxAuth;
        $this->developmentsAuth = $developmentsAuth;
        $this->subdomainFactory = $subdomainFactory;
    }

    /**
     * Load Navigation Links
     * @return array
     */
    protected function loadNavLinks()
    {
        $navigation = [];
        if ($this->listingAuth->canManageListings($this->auth)
            || $this->listingAuth->canManageOwnListings($this->auth)
        ) {
            $navigation[] = ['title' => __('Listings'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'listings/', 'icon' => 'listing', 'app' => 'listings', 'page' => ['','add','edit']];
        }
        if (Skin::hasFeature(Skin::REW_DEVELOPMENTS) && ($this->developmentsAuth->canManageDevelopments($this->auth) || $this->developmentsAuth->canManageOwnDevelopments($this->auth))) {
            $navigation[] = ['title' => __('Developments'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'developments/', 'icon' => 'tools', 'app' => 'developments', 'page' => ['','add','edit','search']];
        }
        if ($this->idxAuth->canManageSearch($this->auth)) {
            $navigation[] = ['title' => __('Searches'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'idx/', 'icon' => 'search', 'app' => 'idx', 'page' => ['','search']];
        }
        if ($this->listingAuth->canFeatureListings($this->auth)) {
            $navigation[] = ['title' => __('Featured'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'listings/tools/featured/', 'icon' => 'star', 'app' => 'listings', 'page' => 'tools/featured'];
        }
        if ($this->listingAuth->canImportListings($this->auth)) {
            $navigation[] = ['title' => __('Import'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'listings/import/', 'icon' => 'in', 'app' => 'listings', 'page' => 'import'];
        }
        return $navigation;
    }

    /**
     * Load Add Links
     * @return array
     */
    protected function loadAddLinks()
    {

        // Current Subdomain
        $subdomain = $this->getCurrentSubdomain('canManageIDXSnippets');

        $addLinks = [];
        if ($this->listingAuth->canManageListings($this->auth) || $this->listingAuth->canManageOwnListings($this->auth)) {
            $addLinks[] = ['title' => __('Listings'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'listings/add/', 'icon' => 'listing'];
        }
        if (Skin::hasFeature(Skin::REW_DEVELOPMENTS) && ($this->developmentsAuth->canManageDevelopments($this->auth) || $this->developmentsAuth->canManageOwnDevelopments($this->auth))) {
            $addLinks[] = ['title' => __('Developments'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'developments/add/', 'icon' => 'tools'];
        }
        if ($this->idxAuth->canManageSearch($this->auth)) {
            $addLinks[] = ['title' => __('IDX Search'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'idx/searches/add/', 'icon' => 'search'];
        }
        if ($subdomain) {
            $addLinks[] = ['title' => __('IDX Snippet'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'idx/snippets/add/' . $subdomain->getPostLink(), 'icon' => 'snippet'];
        }

        return $addLinks;
    }
}
