<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Partners extends Backend_AuthCheck_App
{

    // Can Conversion Tracking
    public function view()
    {
        return (
            $this->partners("bombomb") ||
            $this->partners("espresso") ||
            $this->partners("followupboss") ||
            $this->partners("grasshopper") ||
            $this->partners("wiseagent") ||
            $this->partners("zillow")
        );
    }

    // Can Manage Bombbomb
    public function bombomb()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_BOMBBOMB'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_BOMBBOMB_AGENT)))
        ) ? true : false;
    }

    // Can Manage Espresso
    public function espresso()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_ESPRESSO)) ||
            $this->auth->isAssociate())
        ) ? true : false;
    }

    // Can Manage Followupboss
    public function followupboss()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_FOLLOWUPBOSS'] &&
            ($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin())
        ) ? true : false;
    }

    // Can Manage Grasshopper
    public function grasshopper()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_GRASSHOPPER'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_GRASSHOPPER_AGENT)))
        ) ? true : false;
    }

    // Can Manage Wiseagent
    public function wiseagent()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_WISEAGENT'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_WISEAGENT_AGENT)))
        ) ? true : false;
    }

    // Can Manage Zillow
    public function zillow()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_ZILLOW'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_ZILLOW)))
        ) ? true : false;
    }
}
