<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;
use Backend_Team;

/**
 * Class TeamsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class TeamsAuth extends Auth
{

    /**
     * Can View Team Leads
     *
     * @return bool
     */
    public function canViewTeamLeads()
    {
        return $this->settings->MODULES['REW_TEAMS'];
    }

    /**
     * Can View Teams
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewTeams(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_TEAMS']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_TEAMS_VIEW)
            || $auth->adminPermission($auth::PERM_TEAMS_MANAGE)
            || $auth->adminPermission($auth::PERM_TEAMS_MANAGE_ALL));
    }

    /**
     * Can Manage Teams
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageTeams(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_TEAMS']
            && ($auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_TEAMS_MANAGE_ALL));
    }

    /**
     * Can Manage Teams
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwn(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_TEAMS']
            && $auth->adminPermission($auth::PERM_TEAMS_MANAGE);
    }
}
