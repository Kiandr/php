<?php

namespace REW\Backend\Navigation;

use REW\Backend\Controller\Leads\SharktankController;
use REW\Backend\Navigation\Navigation;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\LeadsAuth;
use REW\Backend\Auth\CalendarAuth;

/**
 * Class CRM Navigation
 *
 * @category Navigation
 * @package  REW\Backend\Navigation
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CrmNavigation extends Navigation
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
     * Lead Auth
     * @var LeadsAuth
     */
    protected $leadsAuth;

    /**
     * Calendar Auth
     * @var CalendarAuth
     */
    protected $calendarAuth;

    /**
     * SharkTank Controller
     * @var SharkTank
     */
    protected $sharkTank;

    /**
     * CRM Content Navigation
     * @param AuthInterface $auth;
     * @param SettingsInterface $settings
     * @param LeadsAuth $leadsAuth
     * @param CalendarAuth $calendarAuth
     */
    public function __construct(AuthInterface $auth, SettingsInterface $settings, LeadsAuth $leadsAuth, CalendarAuth $calendarAuth, SharktankController $sharkTank)
    {
        $this->auth = $auth;
        $this->settings = $settings;
        $this->leadsAuth = $leadsAuth;
        $this->calendarAuth = $calendarAuth;
        $this->sharkTank = $sharkTank;
    }

    /**
     * Load Navigation Links
     * @return array
     */
    protected function loadNavLinks()
    {

        $navigation = [];
        if ($this->leadsAuth->canManageLeads($this->auth) || $this->leadsAuth->canViewOwn($this->auth)) {
            $navigation []= ['title' => __('Leads'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/', 'icon' => 'lead', 'app' => 'leads', 'page' => ['','add','edit','lead','search','!campaigns','!action_plans','!auto_responders','!groups']];
        }
        if ($this->leadsAuth->canAccessSharkTank($this->auth) && $this->sharkTank->isSharktankEnabled()) {
            $navigation []= ['title' => __('Shark Tank'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/sharktank/', 'icon' => 'lead', 'app' => 'leads', 'page' => 'shark_tank'];
        }
        if ($this->leadsAuth->canManageGroups($this->auth) || $this->leadsAuth->canManageOwnGroups($this->auth)) {
            $navigation []= ['title' => __('Groups'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/groups/', 'icon' => 'groups', 'app' => 'leads', 'page' => 'groups'];
        }
        if ($this->leadsAuth->canManageCampaigns($this->auth) || $this->leadsAuth->canManageOwnCampaigns($this->auth)) {
            $navigation []= ['title' => __('Campaigns'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/campaigns/', 'icon' => 'campaign', 'app' => 'leads', 'page' => ['campaigns']];
        }
        if ($this->leadsAuth->canManageActionPlans($this->auth)) {
            $navigation []= ['title' => __('Action Plans'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/action_plans/', 'icon' => 'actionplan', 'app' => 'leads', 'page' => 'action_plans'];
        }
        if ($this->leadsAuth->canManageDocuments($this->auth) || $this->leadsAuth->canViewOwn($this->auth)) {
            $navigation []= ['title' => __('Templates'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/templates/', 'icon' => 'email', 'app' => 'leads', 'page' => 'templates'];
        }
        if ($this->leadsAuth->canManageDocuments($this->auth) || $this->leadsAuth->canViewOwn($this->auth)) {
            $navigation []= ['title' => __('Form Letters'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/docs/', 'icon' => 'email', 'app' => 'leads', 'page' => 'docs'];
        }
        if ($this->leadsAuth->canManageAutoresponders($this->auth)) {
            $navigation []= ['title' => __('Responders'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/auto_responders/', 'icon' => 'email', 'app' => 'leads', 'page' => 'auto_responders'];
        }
        if ($this->leadsAuth->canViewFiles($this->auth) || $this->leadsAuth->canManageOwnFiles($this->auth)) {
            $navigation []= ['title' => __('Files'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/tools/files/', 'icon' => 'pages', 'app' => 'leads', 'page' => 'tools/files'];
        }
        if ($this->calendarAuth->canManageCalendars($this->auth) || $this->calendarAuth->canManageOwnCalendars($this->auth)) {
            $navigation []= ['title' => __('Calendar'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'calendar/', 'icon' => 'calendar', 'app' => 'calendar'];
        }
        if ($this->leadsAuth->canViewTools($this->auth)) {
            $navigation []= ['title' => __('Tools'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/tools/', 'icon' => 'tools', 'app' => 'leads', 'page' => ['tools','!files']];
        }
        return $navigation;
    }

    /**
     * Load Add Links
     * @return array
     */
    protected function loadAddLinks()
    {

        $addLinks = [];
        if ($this->leadsAuth->canManageLeads($this->auth) || $this->leadsAuth->canManageOwn($this->auth)) {
            $addLinks []= ['title' => __('Lead'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/add/', 'icon' => 'lead'];
        }
        if ($this->leadsAuth->canManageGroups($this->auth) || $this->leadsAuth->canManageOwnGroups($this->auth)) {
            $addLinks []= ['title' => __('Group'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/groups/add/', 'icon' => 'groups'];
        }
        if ($this->leadsAuth->canManageCampaigns($this->auth) || $this->leadsAuth->canManageOwnCampaigns($this->auth)) {
            $addLinks []= ['title' => __('Campaign'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/campaigns/add/', 'icon' => 'campaign'];
        }
        if ($this->leadsAuth->canManageDocuments($this->auth)) {
            $addLinks []= ['title' => __('Form Letter'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'leads/docs/document/', 'icon' => 'formletter'];
        }
        if ($this->calendarAuth->canManageCalendars($this->auth) || $this->calendarAuth->canManageOwnCalendars($this->auth)) {
            $addLinks []= ['title' => __('Event'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'calendar/event/add/', 'icon' => 'calendar', 'app' => 'calendar'];
        }
        return $addLinks;
    }
}
