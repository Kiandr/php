<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class TeamsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ToolsAuth extends Auth
{
    /**
     * Can Conversion Tracking
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewTools(AuthInterface $auth)
    {
        return $this->canManageBackup($auth)
            || $this->canManageConversionTracking($auth)
            || $this->canManageRadioLandingPage($auth)
            || $this->canManageRewrites($auth)
            || $this->canManageSlideshow($auth)
            || $this->canManageTestimonials($auth)
            || $this->canManageTracking($auth);
    }

    /**
     * Can View CMS Pages
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageBackup(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_BACKUP);
    }

    /**
     * Can Conversion Tracking
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageConversionTracking(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_CONVERSION_TRACKING']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage Radio Landing Pages
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageRadioLandingPage(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_RADIO_LANDING_PAGE']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_PAGES));
    }

    /**
     * Can Manage Rewrite
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageRewrites(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_REWRITE_MANAGER']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_REDIRECT));
    }

    /**
     * Can Manage Slideshows
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSlideshow(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_SLIDESHOW_MANAGER']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_SLIDESHOW));
    }

    /**
     * Can Manage Testimonials
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageTestimonials(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_TESTIMONIALS']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_TESTIMONIALS));
    }

    /**
     * Can Manage Tracking Codes
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageTracking(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Communities
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageCommunities(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_FEATURED_COMMUNITIES']
            && ($auth->isSuperAdmin() || $auth->adminPermission($auth::PERM_CMS_FEATURED_COMMUNITIES));
    }

    /**
     * Can Delete Communities
     * @param AuthInterface $auth Current Authuser
     * @return boolean
     */
    public function canDeleteCommunities(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_FEATURED_COMMUNITIES']
            && ($auth->isSuperAdmin() || $auth->adminPermission($auth::PERM_CMS_FEATURED_COMMUNITIES_DELETE));
    }

    /**
     * Can Manage Superadmin Settings (Ex. Modules)
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSettings(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }
}
