<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Calendar extends Backend_AuthCheck_App
{

    // Can Edit Homepage
    public function homepage()
    {
        return ($this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_HOMEPAGE))
        ? true : false;
    }

    // Can View CMS Pages
    public function manage()
    {
        return ($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_PAGES))
        ? true : false;
    }

    // Can Manage IDX Snippets
    public function idx()
    {
        return (Settings::getInstance()->MODULES['REW_IDX_SNIPPETS'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS)))
        ) ? true : false;
    }

    // Can Delete CMS Pages
    public function delete()
    {
        return ($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_PAGES_DELETE))
        ? true : false;
    }

    // Can View CMS Snippets
    public function snippets()
    {
        return ($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS))
        ? true : false;
    }

    // Can Delete CMS Snippets
    public function delete_snippets()
    {
        return ($this->auth->info('mode') == 'admin' && $this->auth->is_super_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS_DELETE))
        ? true : false;
    }
}
