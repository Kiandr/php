<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Listings extends Backend_AuthCheck_App
{

    // Can View Listings
    public function manage()
    {
        return ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_LISTINGS_AGENT)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LISTINGS_MANAGE))
        ) ? true : false;
    }

    // Can Delete Listings
    public function delete()
    {
        return ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_LISTINGS_AGENT)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LISTINGS_DELETE))
        ) ? true : false;
    }

    // Can Import Listings
    public function import()
    {
        return $this->is_super_admin_as_admin();
    }

    // Can Manage Featured Listings
    public function featured()
    {
        return (Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LISTINGS_FEATURED)))
        ) ? true : false;
    }
}
