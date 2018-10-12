<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class ListingsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ListingsAuth extends Auth
{

    /**
     * Can View Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageListings(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LISTINGS_MANAGE);
    }

    /**
     * Can View Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwnListings(AuthInterface $auth)
    {
        return $auth->isAgent() && $auth->hasPermission($auth::PERM_LISTINGS_AGENT);
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
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LISTINGS_DELETE);
    }

    /**
     * Can Import Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canImportListings(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Featured Listings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canFeatureListings(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_FEATURED_LISTINGS'] &&
            ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LISTINGS_FEATURED));
    }
}
