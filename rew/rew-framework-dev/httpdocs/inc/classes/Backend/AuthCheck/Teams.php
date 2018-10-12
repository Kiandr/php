<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Teams extends Backend_AuthCheck_App
{

    // Can View Reports
    public function view()
    {
        return (Settings::getInstance()->MODULES['REW_TEAMS'] &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_TEAMS_VIEW) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
            ) ? true : false;
    }

    // Can Manage Teams
    public function manage()
    {
        return (Settings::getInstance()->MODULES['REW_TEAMS'] &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
            ) ? true : false;
    }

    // Can Manage Teams
    public function manage_own()
    {
        return (Settings::getInstance()->MODULES['REW_TEAMS'] &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
            ) ? true : false;
    }

    // Can Manage Team Subdomains
    public function subdomain()
    {
        return (Settings::getInstance()->MODULES['REW_TEAMS'] && Settings::getInstance()->MODULES['REW_TEAM_CMS'] &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_TEAMS_VIEW) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
        ) ? true : false;
    }

    // Can Manage Team Subdomains
    public function idx()
    {
        return (Settings::getInstance()->MODULES['REW_TEAM_CMS'] && Settings::getInstance()->MODULES['REW_IDX_SNIPPETS'] &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_TEAMS_VIEW) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
            ) ? true : false;
    }


    // Can Manage Team Subdomains Conversion Tracking
    public function conversion_tracking()
    {
        return ((Settings::getInstance()->MODULES['REW_TEAM_CMS'] && Settings::getInstance()->MODULES['REW_CONVERSION_TRACKING']) &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_TEAMS_VIEW) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
            ) ? true : false;
    }


        // Can Manage Team Subdomains Conversion Tracking
    public function radio_landing_page()
    {
        return ((Settings::getInstance()->MODULES['REW_TEAM_CMS'] && Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE']) &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_TEAMS_VIEW) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE) ||
            $this->auth->adminPermission(Auth::PERM_TEAMS_MANAGE_ALL))
            ) ? true : false;
    }
}
