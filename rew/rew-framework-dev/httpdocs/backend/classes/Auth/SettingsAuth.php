<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SkinInterface;

/**
 * Class SettingsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class SettingsAuth extends Auth
{

    /**
     * Can Manage Settings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSettings(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * @param SkinInterface $skin
     * @return bool
     */
    public function canManageCmsSettings(AuthInterface $auth)
    {
        return (
            $auth->isSuperAdmin()
        );
    }

    /**
     * Can Manage IDX Settings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageIdxMeta(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_BUILDER']
            && $this->settings->MODULES['REW_IDX_META_INFORMATION']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage API Settings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageApi(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_CRM_API']
            && $auth->isSuperAdmin();
    }

    /**
     * Can View Blog Settings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageBlogs(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_BLOG_SETTINGS));
    }
}
