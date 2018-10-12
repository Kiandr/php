<?php

namespace REW\Backend\Teams;

use REW\Core\Interfaces\AuthInterface;
use Backend_Team;

/**
 * Class TeamsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class Manager
{

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * Create Auth
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get all agents sharing leads in a team
     *
     * @return array
     */
    public function getTeamsAccessingLeads($viewAll = false)
    {

        return Backend_Team::getTeams(
            $this->auth->info('id'),
            !$viewAll ? [Backend_Team::PERM_ACCESS_LEADS_VIEW,
            Backend_Team::PERM_ACCESS_LEADS_EDIT,
            Backend_Team::PERM_ACCESS_LEADS_FULL] : []
        );
    }

    /**
     * Get all agents sharing leads in a team
     *
     * @param Backend_Team|NULL $team    Filter by team
     * @param bool              $viewAll Can view every team
     *
     * @return array
     */
    public function getAgentsSharingLeads(Backend_Team $team = null, $viewAll = false)
    {

        // Get all teams or Check provided team
        if (!isset($team)) {
            $teams = $this->getTeamsAccessingLeads($viewAll);
        } else {
            $teams = ($team->checkAgent(
                $this->auth->info('id'),
                [Backend_Team::PERM_ACCESS_LEADS_VIEW,
                Backend_Team::PERM_ACCESS_LEADS_EDIT,
                Backend_Team::PERM_ACCESS_LEADS_FULL]
            ) || $viewAll) ? [$team] : [];
        }

        // Iterate through teams
        $agents = [];
        foreach ($teams as $team) {
            // Get All Agents Sharing Leads with the Team
            $teamAgents = $team->getAgents(
                [],
                [Backend_Team::PERM_SHARE_LEADS_VIEW,
                Backend_Team::PERM_SHARE_LEADS_EDIT,
                Backend_Team::PERM_SHARE_LEADS_FULL]
            );

            // Iterate through agents
            foreach ($teamAgents as $teamAgent) {
                if (!in_array($teamAgent, $agents)) {
                    $agents[] = $teamAgent;
                }
            }
        }
        return $agents;
    }
}
