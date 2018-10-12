<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Directory extends Backend_AuthCheck_App
{

    // Can View Directory
    public function view()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_DIRECTORY_SETTINGS)))
        ) ? true : false;
    }

    // Can Manage Categories
    public function categories()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_DIRECTORY_CATEGORIES_MANAGE)))
        ) ? true : false;
    }

    // Can Delete Categories
    public function delete_categories()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            (($this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_DIRECTORY_CATEGORIES_DELETE)))
        ) ? true : false;
    }

    // Can Manage Listings
    public function listings()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_DIRECTORY_AGENT)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_DIRECTORY_LISTINGS_MANAGE)))
        ) ? true : false;
    }

    // Can Delete Listings
    public function delete_listings()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_DIRECTORY_AGENT)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_DIRECTORY_LISTINGS_DELETE)))
        ) ? true : false;
    }


    // Can Manage Snippets
    public function snippets()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS)))
        ) ? true : false;
    }

    // Can Delete Snippets
    public function delete_snippets()
    {
        return (Settings::getInstance()->MODULES['REW_DIRECTORY'] ||
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS_DELETE)))
        ) ? true : false;
    }
}
