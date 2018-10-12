<?php

namespace REW\Backend\Auth\Lead;

use REW\Backend\Auth\Auth;
use REW\Core\Interfaces\AuthInterface;
use Backend_Team;

/**
 * Class TeamLeadAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth\Lead
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class TeamLeadAuth extends Auth
{

    /**
     * Check if an agent's leads are viewable
     *
     * @param AuthInterface $auth     Current Authuser
     * @param int           $owner_id Agent to check
     *
     * @return array
     */
    public function checkViewableAgent(AuthInterface $auth, $owner_id)
    {
        return $this->checkLeadAgent(
            $auth->info('id'),
            [Backend_Team::PERM_SHARE_LEADS_VIEW,
            Backend_Team::PERM_SHARE_LEADS_EDIT,
            Backend_Team::PERM_SHARE_LEADS_FULL],
            $owner_id,
            [Backend_Team::PERM_ACCESS_LEADS_VIEW,
            Backend_Team::PERM_ACCESS_LEADS_EDIT,
            Backend_Team::PERM_ACCESS_LEADS_FULL]
        );
    }

    /**
     * Check if an agent's leads are editable
     *
     * @param AuthInterface $auth     Current Authuser
     * @param int           $owner_id Agent to check
     *
     * @return array
     */
    public function checkEditableAgent(AuthInterface $auth, $owner_id)
    {

        return $this->checkLeadAgent(
            $auth->info('id'),
            [Backend_Team::PERM_SHARE_LEADS_EDIT,
            Backend_Team::PERM_SHARE_LEADS_FULL],
            $owner_id,
            [Backend_Team::PERM_ACCESS_LEADS_EDIT,
            Backend_Team::PERM_ACCESS_LEADS_FULL]
        );
    }

    /**
     * Check if an agent's leads are fully editable
     *
     * @param AuthInterface $auth     Current Authuser
     * @param int  $owner_id Agent to check
     *
     * @return array
     */
    public function checkFullyEditableAgent(AuthInterface $auth, $owner_id)
    {
        return $this->checkLeadAgent(
            $auth->info('id'),
            [Backend_Team::PERM_SHARE_LEADS_FULL],
            $owner_id,
            [Backend_Team::PERM_ACCESS_LEADS_FULL]
        );
    }

    /**
     * Check if the auth user and the owning agent share a team with the required permissions
     *
     * @param int  $auth_id  Current Authuser
     * @param int  $owner_id Agent to check
     *
     * @return array
     */
    protected function checkLeadAgent($auth_id, array $auth_permissions, $owner_id, array $owner_permissions)
    {
        $teams = Backend_Team::getTeams(
            $owner_id,
            [],
            $owner_permissions
        );
        foreach ($teams as $team) {
            $check = $team->checkAgent(
                $auth_id,
                $auth_permissions
            );
            if ($check) {
                return true;
            }
        }
        return false;
    }
}
