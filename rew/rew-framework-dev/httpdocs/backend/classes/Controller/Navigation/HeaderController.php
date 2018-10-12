<?php

namespace REW\Backend\Controller\Navigation;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\DashboardAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Navigation\ContentNavigation;
use REW\Backend\Navigation\CrmNavigation;
use REW\Backend\Navigation\ListingsNavigation;
use REW\Backend\Navigation\PeopleNavigation;
use REW\Backend\Navigation\SettingsNavigation;
use REW\Backend\Asset\Interfaces\LoaderInterface;
use REW\Backend\View\Interfaces\FactoryInterface;

/**
 * HeaderController
 * @package REW\Backend\Controller\Navigation
 */
class HeaderController extends AbstractController
{

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var DashboardAuth
     */
    protected $dashboardAuth;

    /**
     * @var ContentNavigation
     */
    protected $contentNavigation;

    /**
     * @var CrmNavigation
     */
    protected $crmNavigation;

    /**
     * @var ListingsNavigation
     */
    protected $listingsNavigation;

    /**
     * @var PeopleNavigation
     */
    protected $peopleNavigation;

    /**
     * @var SettingsNavigation
     */
    protected $settingsNavigation;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @param AuthInterface $auth
     * @param SettingsInterface $settings
     * @param DashboardAuth $dashboardAuth
     * @param ContentNavigation $contentNavigation
     * @param CrmNavigation $crmNavigation
     * @param ListingsNavigation $listingsNavigation
     * @param PeopleNavigation $peopleNavigation
     * @param SettingsNavigation $settingsNavigation
     * @param LoaderInterface $loader
     * @param FactoryInterface $view
     */
    public function __construct(
        AuthInterface $auth,
        SettingsInterface $settings,
        DashboardAuth $dashboardAuth,
        ContentNavigation $contentNavigation,
        CrmNavigation $crmNavigation,
        ListingsNavigation $listingsNavigation,
        PeopleNavigation $peopleNavigation,
        SettingsNavigation $settingsNavigation,
        LoaderInterface $loader,
        FactoryInterface $view
    ) {
        $this->auth               = $auth;
        $this->settings           = $settings;
        $this->dashboardAuth      = $dashboardAuth;
        $this->contentNavigation  = $contentNavigation;
        $this->crmNavigation      = $crmNavigation;
        $this->listingsNavigation = $listingsNavigation;
        $this->peopleNavigation   = $peopleNavigation;
        $this->settingsNavigation = $settingsNavigation;
        $this->loader             = $loader;
        $this->view               = $view;
    }

    /**
     * Render Navigation Header
     */
    public function __invoke()
    {

        // Get Navigation Array
        $navigation = $this->getNavigationArray();

        // Build Agent Informaiton
        $userName = $this->auth->getName();
        $userInfo = $this->auth->getInfo();
        if ($userInfo['image']) {
            $userImage = "/thumbs/200x200/uploads/agents/" . $userInfo['image'];
        } else {
            $userImage = "/thumbs/200x200/uploads/agents/na.png";
        }

        // Build links
        $editLink = $this->auth->getEditURL();
        $logoutLink = $this->settings->URLS['URL_BACKEND'] . "logout/";
        $helpLink = $this->settings->URLS['URL_BACKEND'] . "help/";

        // Load route's template file
        $template = $this->loader->getTemplateFile("navigation/header");

        // Render template file
        echo $this->view->render($template, [
            'navigation' => $navigation,
            'userName' => $userName,
            'userImage' => $userImage,
            'editLink' => $editLink,
            'logoutLink' => $logoutLink,
            'helpLink' => $helpLink
        ]);
    }

    /**
     * Get Navigation Array
     * @return array
     */
    public function getNavigationArray()
    {

        // Header Navigation Array
        $navigation = [];

        // Dashboard Navigation
        if ($this->dashboardAuth->canViewDashboard()) {
            $navigation[]= ['title' => __('Dashboard'), 'link' => '/backend/', 'icon' => 'dashboard'];
        }

        // CRM Navigation
        if ($this->crmNavigation->isEnabled()) {
            $navigation[]= ['title' => __('CRM'), 'link' => $this->crmNavigation->getLandingLink(), 'icon' => 'crm'];
        }

        // People Navigation
        if ($this->peopleNavigation->isEnabled()) {
            $navigation[]= ['title' => __('Company'), 'link' => $this->peopleNavigation->getLandingLink(), 'icon' => 'people'];
        }

        // Content Navigation
        if ($this->contentNavigation->isEnabled()) {
            $navigation[]= ['title' => __('Content'), 'link' => $this->contentNavigation->getLandingLink(), 'icon' => 'content'];
        }

        // Listings Navigation
        if ($this->listingsNavigation->isEnabled()) {
            $navigation[]= ['title' => __('Listings'), 'link' => $this->listingsNavigation->getLandingLink(), 'icon' => 'listings'];
        }

        // Settings Navigation
        if ($this->settingsNavigation->isEnabled()) {
            $navigation[]= ['title' => __('Settings'), 'link' => $this->settingsNavigation->getLandingLink(), 'icon' => 'settings'];
        }

        // Return Header Navigation Options
        return $navigation;
    }
}
