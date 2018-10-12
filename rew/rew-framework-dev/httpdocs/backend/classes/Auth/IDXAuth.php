<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class IDXAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class IDXAuth extends Auth
{

    /**
     * Can Manage IDX
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSearch(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_BUILDER'] && $auth->isSuperAdmin();
    }

    /**
     * Can Manage IDX Metadata
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageMeta(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_BUILDER']
            && $this->settings->MODULES['REW_IDX_META_INFORMATION']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage IDX Quicksearch
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageQuicksearch(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_BUILDER']
            && $this->settings->MODULES['REW_IDX_QUICKSEARCH']
            && $auth->isSuperAdmin();
    }
}
