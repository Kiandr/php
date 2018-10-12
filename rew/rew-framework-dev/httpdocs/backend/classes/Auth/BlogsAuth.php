<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class BlogsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class BlogsAuth extends Auth
{

    /**
     * Can View Blog Agents
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSelf(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_BLOG_AGENT));
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
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_BLOG_CATEGORIES));
    }

    /**
     * Can Manage Comments
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageComments(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_BLOG_COMMENTS));
    }

    /**
     * Can Manage Entries
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageEntries(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_BLOG_ENTRIES));
    }

    /**
     * Can Manage Blog Links
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageLinks(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_BLOG_LINKS));
    }

    /**
     * Can Manage Pings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManagePings(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BLOG_INSTALLED']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_BLOG_PINGBACKS));
    }
}
