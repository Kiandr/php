<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class AgentsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class AgentsAuth extends Auth
{

    /**
     * Check if authorized to view Agents
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewAgents(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_AGENTS_VIEW)
            || $this->canManageAgents($auth)
            || $auth->isAssociate()
            || $auth->isLender();
    }

    /**
     * Check if authorized to manage Agents
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageAgents(AuthInterface $auth)
    {
        return !$this->settings->MODULES['REW_LITE']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_AGENTS_MANAGE));
    }

    /**
     * Check if authorized to manage the Super Agent
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSuperAgent(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Check if authorized to delete agents
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteAgents(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
        || $auth->adminPermission($auth::PERM_AGENTS_DELETE);
    }

    /**
     * Check if authorized to manage offices
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOffices(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Check if authorized to email agents
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canEmailAgents(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_AGENTS_EMAIL)
            || $auth->isAssociate();
    }

    /**
     * Check if authorized to manage Agent Lead Rotation
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageApp(AuthInterface $auth)
    {
        return !empty($this->settings->MODULES['REW_CRM_API'])
            && $auth->isSuperAdmin();
    }
}
