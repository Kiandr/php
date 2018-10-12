<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Blogs extends Backend_AuthCheck_App
{

    // Can View Blog Settings
    public function settings()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_SETTINGS)))
        ) ? true : false;
    }

    // Can View Blog Agents
    public function agent()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_AGENT)))
        ) ? true : false;
    }

    // Can Manage Categories
    public function categories()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_CATEGORIES)))
        ) ? true : false;
    }

    // Can Manage Comments
    public function comments()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_COMMENTS)) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_BLOG_AGENT)))
        ) ? true : false;
    }

    // Can Manage Entries
    public function entries()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_ENTRIES)) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_BLOG_AGENT)))
        ) ? true : false;
    }

    // Can Manage Links
    public function links()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_LINKS)))
        ) ? true : false;
    }

    // Can Manage Pings
    public function pings()
    {
        return (Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_BLOG_AGENT)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_BLOG_PINGBACKS)))
        ) ? true : false;
    }
}
