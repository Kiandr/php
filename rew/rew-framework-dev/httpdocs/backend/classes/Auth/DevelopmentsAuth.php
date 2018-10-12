<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class DevelopmentsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class DevelopmentsAuth extends Auth
{

    /**
     * Can Manage All Developments
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageDevelopments(AuthInterface $auth)
    {
        return  !empty($this->settings->MODULES['REW_DEVELOPMENTS'])
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LISTINGS_MANAGE));
    }

    /**
     * Can Manage Own Developments
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwnDevelopments(AuthInterface $auth)
    {
        return  !empty($this->settings->MODULES['REW_DEVELOPMENTS'])
            && $auth->hasPermission($auth::PERM_LISTINGS_AGENT);
    }

    /**
     * Can Delete Developments
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteDevelopments(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LISTINGS_DELETE);
    }
}
