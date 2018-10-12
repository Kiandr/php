<?php

namespace REW\Backend\Navigation;

use REW\Backend\Navigation\Navigation;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\AgentsAuth;
use REW\Backend\Auth\TeamsAuth;
use REW\Backend\Auth\AssociateAuth;
use REW\Backend\Auth\LendersAuth;
use REW\Backend\Auth\ReportsAuth;

/**
 * Class People Navigation
 *
 * @category Navigation
 * @package  REW\Backend\Navigation
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class PeopleNavigation extends Navigation
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
     * Agent Auth
     * @var AgentsAuth
     */
    protected $agentsAuth;

    /**
     * Team Auth
     * @var TeamsAuth
     */
    protected $teamAuth;

    /**
     * Associate Auth
     * @var AssociateAuth
     */
    protected $associateAuth;

    /**
     * Lenders Auth
     * @var LendersAuth
     */
    protected $lendersAuth;

    /**
     * Reports Auth
     * @var ReportsAuth
     */
    protected $reportsAuth;

    /**
     * Construct People Navigation
     * @param AuthInterface $auth;
     * @param SettingsInterface $settings
     * @param AgentsAuth $agentsAuth
     * @param TeamsAuth $teamAuth
     * @param AssociateAuth $associateAuth
     * @param LendersAuth $lendersAuth
     * @param ReportsAuth $reportsAuth
     */
    public function __construct(AuthInterface $auth, SettingsInterface $settings, AgentsAuth $agentsAuth, TeamsAuth $teamAuth, AssociateAuth $associateAuth, LendersAuth $lendersAuth, ReportsAuth $reportsAuth)
    {
        $this->auth = $auth;
        $this->settings = $settings;
        $this->agentsAuth = $agentsAuth;
        $this->teamAuth = $teamAuth;
        $this->associateAuth = $associateAuth;
        $this->lendersAuth = $lendersAuth;
        $this->reportsAuth = $reportsAuth;
    }

    /**
     * Load Navigation Links
     * @return array
     */
    protected function loadNavLinks()
    {
        $navigation = [];
        if ($this->agentsAuth->canViewAgents($this->auth)) {
            $navigation []= ['title' => __('Agents'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'agents/', 'icon' => 'agent', 'app' => 'agents', 'page' => '!offices'];
        }
        if ($this->teamAuth->canViewTeams($this->auth)) {
            $navigation []= ['title' => __('Teams'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'teams/', 'icon' => 'team', 'app' => 'teams'];
        }
        if ($this->agentsAuth->canManageOffices($this->auth)) {
            $navigation []= ['title' => __('Offices'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'agents/offices/', 'icon' => 'office', 'app' => 'agents', 'page' => 'offices'];
        }
        if ($this->associateAuth->canViewAssociates($this->auth)) {
            $navigation []= ['title' => __('Associates'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'associates/', 'icon' => 'agent', 'app' => 'associates'];
        }
        if ($this->lendersAuth->canViewLenders($this->auth)) {
            $navigation []= ['title' => __('Lenders'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'lenders/', 'icon' => 'agent', 'app' => 'lenders'];
        }
        if ($this->reportsAuth->canViewReports($this->auth)) {
            $navigation []= ['title' => __('Reports'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'reports/', 'icon' => 'pages', 'app' => 'reports'];
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
        if ($this->agentsAuth->canManageAgents($this->auth)) {
            $addLinks []= ['title' => __('Agent'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'agents/add/', 'icon' => 'agent'];
        }
        if ($this->teamAuth->canManageTeams($this->auth) || $this->teamAuth->canManageOwn($this->auth)) {
            $addLinks []= ['title' => __('Team'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'teams/add/', 'icon' => 'team'];
        }
        if ($this->agentsAuth->canManageOffices($this->auth)) {
            $addLinks []= ['title' => __('Office'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'agents/offices/add/', 'icon' => 'office'];
        }
        if ($this->associateAuth->canManageAssociates($this->auth)) {
            $addLinks []= ['title' => __('Associate'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'associates/add/', 'icon' => 'agent'];
        }
        if ($this->lendersAuth->canManageLenders($this->auth)) {
            $addLinks []= ['title' => __('Lender'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'lenders/add/', 'icon' => 'agent'];
        }
        return $addLinks;
    }
}
