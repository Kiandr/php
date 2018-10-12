<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Auth;

/**
 * Class DirectoryAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class DirectoryAuth extends Auth
{

    /**
     * Can View Directory
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageDirectories(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY'] &&
            ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_DIRECTORY_SETTINGS));
    }

    /**
     * Can Manage Categories
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageCategories(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY'] &&
            ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_DIRECTORY_CATEGORIES_MANAGE));
    }

    /**
     * Can Delete Categories
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteCategories(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY'] &&
            ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_DIRECTORY_CATEGORIES_DELETE));
    }

    /**
     * Can Manage All Directory Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageListings(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_DIRECTORY_LISTINGS_MANAGE));
    }


    /**
     * Can Manage Own Directory Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwnListings(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY']
            && $auth->hasPermission($auth::PERM_DIRECTORY_AGENT);
    }

    /**
     * Can Delete Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteListings(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_DIRECTORY_LISTINGS_DELETE));
    }
}
