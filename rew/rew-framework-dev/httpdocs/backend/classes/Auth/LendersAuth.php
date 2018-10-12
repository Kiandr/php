<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class LendersAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class LendersAuth extends Auth
{

    /**
     * Can View Lenders
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewLenders(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_LENDERS_MODULE']
            && ($auth->isSuperAdmin()
            || $auth->isAssociate());
    }

    /**
     * Can View Lender Summary
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewLenderSummary(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_LENDERS_MODULE']
            && ($auth->isSuperAdmin()
            || $auth->isAgent()
            || $auth->isAssociate());
    }

    /**
     * Can Manage Lenders
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageLenders(AuthInterface $auth)
    {
        return !$this->settings->MODULES['REW_LITE']
            && $this->settings->MODULES['REW_LENDERS_MODULE']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Delete Lenders
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteLenders(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_LENDERS_MODULE']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage Lenders
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSelf(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_LENDERS_MODULE'] &&
            $auth->isLender();
    }
}
