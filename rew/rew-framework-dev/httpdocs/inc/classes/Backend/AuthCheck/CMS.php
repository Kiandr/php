<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_CMS extends Backend_AuthCheck_App
{

    // Can Edit Homepage
    public function homepage()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_HOMEPAGE))
        ) ? true : false;
    }

    // Can View CMS Pages
    public function manage()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_PAGES))
        ) ? true : false;
    }

    // Can Manage IDX Snippets
    public function idx()
    {
        return (Settings::getInstance()->MODULES['REW_IDX_SNIPPETS'] &&
            (($this->is_super_admin_as_admin()) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS)))
        ) ? true : false;
    }

    // Can Delete CMS Pages
    public function delete()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_PAGES_DELETE))
        ) ? true : false;
    }

    // Can View CMS Snippets
    public function snippets()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS))
        ) ? true : false;
    }

    // Can Delete CMS Snippets
    public function delete_snippets()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SNIPPETS_DELETE))
        ) ? true : false;
    }
}
