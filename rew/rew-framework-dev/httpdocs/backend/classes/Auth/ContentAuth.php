<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class ContentAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ContentAuth extends Auth
{

    /**
     * Can Manage Homepage
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageHomepage(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_HOMEPAGE);
    }

    /**
     * Can Manage CMS Pages
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManagePages(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_PAGES);
    }

    /**
     * Can View CMS Pages
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewPages(AuthInterface $auth)
    {
        return $this->canManageHomepage($auth) || $this->canManagePages($auth);
    }

    /**
     * Can Delete CMS Pages
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeletePages(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_PAGES_DELETE);
    }

    /**
     * Can Manage Navigation
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageNav(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
        || $auth->adminPermission($auth::PERM_CMS_NAV);
    }

    /**
     * Can Manage CMS Snippets
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSnippets(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_SNIPPETS);
    }

    /**
     * Can Manage IDX Snippets
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageIDXSnippets(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_SNIPPETS']
            && $this->canManageSnippets($auth);
    }

    /**
     * Can Manage BDX Snippets
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageBDXSnippets(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_BUILDER']
            && $this->canManageSnippets($auth);
    }

    /**
     * Can Manage Directory Snippets
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageDirectorySnippets(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_DIRECTORY']
            && $this->canManageSnippets($auth);
    }

    /**
     * Can Delete CMS Snippets
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteSnippets(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_SNIPPETS_DELETE);
    }
}
