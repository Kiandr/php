<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class AssociateAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class AssociateAuth extends Auth
{

    /**
     * Check if authorized to view associates
     *
     * @param AuthInterface $auth Auth User
     *
     * @return bool
     */
    public function canViewAssociates(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
        && ($auth->isSuperAdmin()
                || $auth->isAgent());
    }

    /**
     * Check if authorized to manage associates
     *
     * @param AuthInterface $auth Auth User
     *
     * @return bool
     */
    public function canManageAssociates(AuthInterface $auth)
    {
        return !$this->settings->MODULES['REW_LITE']
            && $this->settings->MODULES['REW_ISA_MODULE']
            && $auth->isSuperAdmin();
    }

    /**
     * Check if authorized to delete associates
     *
     * @param AuthInterface $auth Auth User
     *
     * @return bool
     */
    public function canDeleteAssociates(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && $auth->isSuperAdmin();
    }

    /**
     * Check if authorized to manage self
     *
     * @param AuthInterface $auth Auth User
     *
     * @return bool
     */
    public function canManageSelf(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
        && $auth->isAssociate();
    }

    /**
     * Check if authorized to email self
     *
     * @param AuthInterface $auth Auth User
     *
     * @return bool
     */
    public function canEmailAssociates(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && ($this->canManageAssociates($auth)
            || $auth->isAgent());
    }
}
