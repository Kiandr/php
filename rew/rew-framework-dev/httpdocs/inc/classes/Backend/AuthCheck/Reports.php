<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Reports extends Backend_AuthCheck_App
{

    // Can View Reports
    public function view()
    {
        return (($this->auth->info('mode') == 'admin') &&
            ($this->auth->isSuperAdmin() ||
            $this->auth->adminPermission(Auth::PERM_REPORTS_GOOGLE_ANALYTICS)   ||
            $this->auth->adminPermission(Auth::PERM_REPORTS_AGENT_ALL) ||
            $this->auth->adminPermission(Auth::PERM_REPORTS_LISTINGS)) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_REPORTS_AGENT))
        ) ? true : false;
    }

    // Can View Analytics Report
    public function analytics()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_ESPRESSO))) ? true : false;
    }

    // Can View Agent Response Report
    public function response()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_REPORTS_AGENT_ALL)) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_REPORTS_AGENT))
        ) ? true : false;
    }

    // Can View Listings Report
    public function listing()
    {
        return ($this->auth->info('mode') == 'admin'& (
            $this->auth->isSuperAdmin() ||
            $this->auth->adminPermission(Auth::PERM_REPORTS_LISTINGS))
        ) ? true : false;
    }

    // Can View Dialer Report
    public function dialer()
    {
        return (
            isset(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) && !empty(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) &&
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_ESPRESSO)) ||
            $this->auth->isAssociate())
        ) ? true : false;
    }

    // Can View Tasks Report
    public function tasks()
    {
        return (
            isset(Settings::getInstance()->MODULES['REW_ACTION_PLANS']) && !empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS']) &&
            $this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()
        ) ? true : false;
    }
}
