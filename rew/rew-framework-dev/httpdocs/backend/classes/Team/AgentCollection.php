<?php

namespace REW\Backend\Team;

use REW\Core\Interfaces\DBInterface;

class AgentCollection
{
    /**
     * @var \Backend_Team
     */
    private $team;

    /**
     * @var array
     */
    private $grantedPermissions = [];

    /**
     * @var array
     */
    private $grantingPermissions = [];

    /**
     * @var bool
     */
    private $useCache = false;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * AgentCollection constructor.
     * @param \Backend_Team $team
     * @param DBInterface $db
     */
    public function __construct(\Backend_Team $team, DBInterface $db)
    {
        $this->team = $team;
    }

    /**
     * Sets this collection to cache in memory so that multiple operations can be done without repeating db operations
     * @return $this
     */
    public function cache()
    {
        $this->useCache = true;
        return $this;
    }

    /**
     * Filters by granted permissions
     * @param array $grantedPermissions
     * @return $this
     */
    public function filterByGrantedPermissions($grantedPermissions)
    {
        $this->grantedPermissions = $grantedPermissions;
        $this->cache = [];

        return $this;
    }

    /**
     * Filters by granting permissions
     * @param array $grantingPermissions
     * @return $this
     */
    public function filterByGrantingPermissions($grantingPermissions)
    {
        $this->grantingPermissions = $grantingPermissions;
        $this->cache = [];

        return $this;
    }

    /**
     * Loads IDX agent ids for all agents
     * @return array
     */
    public function getAllIdxAgentIds()
    {
        if ($this->useCache && isset($this->cache[__FUNCTION__])) {
            return $this->cache[__FUNCTION__];
        }

        $agents = $this->getAllAgents();
        $agentIdxIds = [];

        foreach ($agents as $agent) {
            $agentIdxIds = array_merge($agentIdxIds, $this->getIdxAgentIdsForAgent($agent));
        }
        $this->cache[__FUNCTION__] = $agentIdxIds;

        return $agentIdxIds;
    }

    /**
     * Get all available agents
     * @return array
     */
    public function getAllAgents()
    {
        if ($this->useCache && isset($this->cache[__FUNCTION__])) {
            return $this->cache[__FUNCTION__];
        }

        // Check Primary Agent
        $agents = array_merge([$this->team->getPrimaryAgent()], $this->team->getSecondaryAgents());
        $agents = $this->applyAgentFilters($agents);

        if ($this->useCache) {
            $this->cache[__FUNCTION__] = $agents;
        }

        return $agents;
    }

    /**
     * Get next available agent in rotation
     * @return array
     */
    public function getNextAgent()
    {

        // Get Valid Agents
        $agents = array_merge([$this->team->getPrimaryAgent()], $this->team->getSecondaryAgents());
        $agents = $this->applyAgentFilters($agents);

        // Get Next Agent
        if (!empty($agents)) {
            $team = $this->team;
            usort($agents, function ($a, $b) use ($team) {
                $aLastAssigned = $team->getAgentLastAssigned($a);
                $bLastAssigned = $team->getAgentLastAssigned($b);
                if ($aLastAssigned === $bLastAssigned) {
                    return 0;
                }
                return $aLastAssigned > $bLastAssigned ? 1 : -1;
            });
            return array_shift($agents);
        // Get Default Agent
        } else {
            return null;
        }
    }

    /**
     * Applies agent filters to an array of agents
     * @param array $agents
     * @return array
     */
    public function applyAgentFilters($agents)
    {
        return array_filter($agents, function ($agent) {
            return $this->team->checkAgent($agent, $this->grantedPermissions, $this->grantingPermissions);
        });
    }

    /**
     * Loads IDX agent id for the requested agent
     * @param int $cmsAgentId
     * @return array
     */
    public function getIdxAgentIdsForAgent($cmsAgentId)
    {
        $agent = \Backend_Agent::load($cmsAgentId);
        $agentIdxIds = [];
        if (!$agentId = $agent->info('agent_id')) {
            return [];
        }

        $agent = \Backend_Agent::load($cmsAgentId);
        if ($agentId = $agent->info('agent_id')) {
            $feedIds = json_decode($agentId, true);
            foreach ($feedIds as $feed => $feedId) {
                if (!isset($agentIdxIds[$feed])) {
                    $agentIdxIds[$feed] = [];
                }
                $agentIdxIds[$feed][] = [
                    'id' => $agent->getId(),
                    'idx_id' => $feedId,
                    'name' => $agent->getName()
                ];
            }
        }

        return $agentIdxIds;
    }

    /**
     * Check if provided agent is in this collection
     * @param int $agentId
     * @return bool
     */
    public function checkAgentInCollection($agentId)
    {
        return in_array($agentId, $this->getAllAgents());
    }
}
