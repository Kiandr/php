<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Tools extends Backend_AuthCheck_App
{

    // Can Conversion Tracking
    public function view()
    {
        return (
            $this->backup() ||
            $this->conversion_tracking() ||
            $this->radio_landing_page() ||
            $this->rewrite() ||
            $this->slideshow() ||
            $this->testimonials() ||
            $this->tracking()
        );
    }

    // Can View CMS Pages
    public function backup()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_BACKUP))
        ) ? true : false;
    }

    // Can Conversion Tracking
    public function conversion_tracking()
    {
        return (Settings::getInstance()->MODULES['REW_CONVERSION_TRACKING'] &&
            $this->is_super_admin_as_admin()
        ) ? true : false;
    }

    // Can Manage Radio Landing Pages
    public function radio_landing_page()
    {
        return (Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_PAGES)))
        ) ? true : false;
    }

    // Can Manage Rewrite
    public function rewrite()
    {
        return (Settings::getInstance()->MODULES['REW_REWRITE_MANAGER'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_REDIRECT)))
        ) ? true : false;
    }

    // Can Manage Slideshows
    public function slideshow()
    {
        return (Settings::getInstance()->MODULES['REW_SLIDESHOW_MANAGER'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_SLIDESHOW)))
        ) ? true : false;
    }

    // Can Manage Testimonials
    public function testimonials()
    {
        return (Settings::getInstance()->MODULES['REW_TESTIMONIALS'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_TESTIMONIALS)))
        ) ? true : false;
    }

    // Can Manage Tracking Codes
    public function tracking()
    {
        return $this->is_super_admin_as_admin() ? true : false;
    }
}
