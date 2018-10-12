<?php

namespace REW\Backend\Navigation;

use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Backend\Navigation\Navigation;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Auth\PartnersAuth;
use REW\Backend\Auth\CustomAuth;
use REW\Core\Interfaces\SkinInterface;

/**
 * Class Settings Navigation
 *
 * @category Navigation
 * @package  REW\Backend\Navigation
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class SettingsNavigation extends Navigation
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
     * Settings Auth
     * @var SettingsAuth
     */
    protected $settingsAuth;

    /**
     * Partners Auth
     * @var PartnersAuth
     */
    protected $partnersAuth;

    /**
     * Custom Auth
     * @var CustomAuth
     */
    protected $customAuth;

    /**
     * Subdomain Factory
     * @var SubdomainFactoryInterface
     */
    protected $subdomainFactory;

    /**
     * Current Subdomain
     * @var SubdomainInterface
     */
    protected $subdomain;

    /**
     * Skin
     * @var SkinInterface
     */
    protected $skin;

    /**
     * Construct Content Navigation
     * @param AuthInterface $auth;
     * @param SettingsInterface $settings;
     * @param SettingsAuth $settingsAuth
     * @param PartnersAuth $partnersAuth
     * @param SkinInterface $skin
     * @param SubdomainFactoryInterface $subdomainFactory
     */
    public function __construct(AuthInterface $auth, SettingsInterface $settings, SettingsAuth $settingsAuth, PartnersAuth $partnersAuth, CustomAuth $customAuth, SkinInterface $skin, SubdomainFactoryInterface $subdomainFactory)
    {
        $this->auth = $auth;
        $this->settings = $settings;
        $this->settingsAuth = $settingsAuth;
        $this->partnersAuth = $partnersAuth;
        $this->customAuth = $customAuth;
        $this->skin = $skin;
        $this->subdomainFactory = $subdomainFactory;
    }

    /**
     * Load Navigation Links
     * @return array
     */
    protected function loadNavLinks()
    {

        // Navigation: Settings
        $navigation = [];

        // Check if content can be displayed
        $subdomain = $this->getCurrentSubdomain('canManageTracking');

        if ($this->settingsAuth->canManageSettings($this->auth)) {
            $navigation[] = ['title' => __('Leads'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/', 'icon' => 'lead', 'app' => 'settings', 'page' => ''];
        }
        if ($this->settingsAuth->canManageApi($this->auth)) {
            $navigation[] = ['title' => __('API'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/api/', 'icon' => 'cog', 'app' => 'settings', 'page' => 'api'];
        }
        if ($this->settingsAuth->canManageCmsSettings($this->auth, $this->skin)) {
            $navigation[] = ['title' => __('CMS'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/cms/', 'icon' => 'pages', 'app' => 'settings', 'page' => 'cms'];
            $navigation[] = ['title' => __('Add-Ons'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/addons/', 'icon' => 'pages', 'app' => 'settings', 'page' => 'addons'];
        }
        if ($this->settingsAuth->canManageBlogs($this->auth)) {
            $navigation[] = ['title' => __('Blog'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/blog/', 'icon' => 'pages', 'app' => 'settings', 'page' => 'blog'];
        }
        if ($this->settingsAuth->canManageIdxMeta($this->auth)) {
            $navigation[] = ['title' => __('IDX'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/idx/', 'icon' => 'listing', 'app' => 'settings', 'page' => 'idx'];
        }
        if ($subdomain) {
            $navigation[] = ['title' => __('Tracking Codes'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/tracking-codes/' . $subdomain->getPostLink(), 'icon' => 'cog', 'app' => 'settings', 'page' => 'tracking-codes'];
        }
        if ($this->partnersAuth->canViewPartners($this->auth)) {
            $navigation[] = ['title' => __('Integrations'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/partners/', 'icon' => 'integrations', 'app' => 'settings', 'page' => 'partners'];
        }

        if ($this->customAuth->canManageFields($this->auth)) {
            $navigation[] = ['title' => __('Custom Fields'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'settings/fields/', 'icon' => 'tools', 'app' => 'settings', 'page' => 'fields'];
        }

        return $navigation;
    }

    /**
     * Load Add Links
     * @return array
     */
    protected function loadAddLinks()
    {
        return [];
    }
}
