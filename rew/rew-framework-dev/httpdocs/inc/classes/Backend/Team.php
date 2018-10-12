<?php

/**
 * Backend_Team is a class used for managing.
 *
 * Load Team by ID:
 * <code>
 * $team = Backend_Team::load(1);
 * $team->getId(); // 1
 * </code>
 *
 * @package Backend
 */
class Backend_Team implements ArrayAccess
{

    /************************** Permissions keys **************************/

    /**
     * Granted Permission Checksum Key
     * @var string
     */
    const GRANTED_KEY = 'granted';

    /**
     * Granting Permission Checksum Key
     * @var string
     */
    const GRANTING_KEY = 'granting';

    /******************* Permissions Teams grant Agents *******************/

    /**
     * Team Permission: View Team Leads
     * @var int
     */
    const PERM_ACCESS_LEADS_VIEW = 2;

    /**
     * Team Permission: Edit Team Leads
     * @var int
     */
    const PERM_ACCESS_LEADS_EDIT = 4;

    /**
     * Team Permission: Fully Edit Team Leads
     * @var int
     */
    const PERM_ACCESS_LEADS_FULL = 8;

    /**
     * Team Permission: Assign Agents
     * @var int
     */
    const PERM_ASSIGN = 16;

    /**
     * Team Permission: Edit Team Subdomains
     * @var int
     */
    const PERM_EDIT_SUBDOMAIN = 32;

    /**
     * Team Permission: View Team Leads
     * @var int
     */
    const PERM_FEATURE_LISTINGS = 64;

    /******************* Permissions Agents grant Teams *******************/

    /**
     * Team Permission: Members can View Team agent Leads
     * @var int
     */
    const PERM_SHARE_LEADS_VIEW = 2;

    /**
     * Team Permission: Members can Edit Team agent Leads
     * @var int
     */
    const PERM_SHARE_LEADS_EDIT = 4;

    /**
     * Team Permission: Members can Fully Edit agent Leads
     * @var int
     */
    const PERM_SHARE_LEADS_FULL = 8;

    /**
     * Team Permission: Feature team members on this agents listings
     * @var int
     */
    const PERM_SHARE_FEATURE_LISTINGS = 16;

    /**
     * Team Database
     *
     * @var DB $db
     */
    protected $db;

    /**
     * @var \REW\Core\Interfaces\ContainerInterface
     */
    protected $container;

    /**
     * Team ID
     *
     * @var int $id
     */
    protected $id;

    /**
     * Team Row
     *
     * @var array $row
     */
    protected $row = array();

    /**
     * Agent administering this team
     *
     * @var int $primary_agent
     */
    protected $primary_agent;

    /**
     * Team Agent Data
     *
     * @var array $agent_permissions
     */
    protected $agent_permissions;

    /**
     * Timestamp of last lead assigned to this team
     *
     * @var array $agent_last_assigned
     */
    protected $agent_last_assigned;

    /**
     * Permissions agents can grant this Team
     *
     * @var array $granting_permisisons
     */
    protected $granting_permisisons;

    /**
     * Permissions this team can grant to agents
     *
     * @var array $granted_permisisons
     */
    protected $granted_permisisons;

    /**
     * Create Team from Row
     *
     * <code>
     * <?php
     *
     * // Create Team from $row
     * $team = new Backend_Team ($row);
     *
     * // Display Team Name
     * echo $team->info('name'); // $row['name']
     *
     * ?>
     * </code>
     *
     * @param array $row Data Row
     */
    public function __construct($row = [])
    {

        // Team DB
        $this->db = DB::get('users');
        $this->container = Container::getInstance();

        // Team ID
        if (isset($row['id'])) {
            $this->id = $row['id'];
        }

        // Team Primary Agent
        if (isset($row['agent_id'])) {
            $this->primary_agent = $row['agent_id'];
        }

        // Team Secondary Agents
        $this->agent_permissions = [];
        $this->agent_last_assigned = [];
        if (isset($row['team_agents'])) {
            foreach ($row['team_agents'] as $team_agent) {
                // Set Permissions
                $agentId = intval($team_agent['agent_id']);
                $this->agent_permissions[$agentId] = [
                    self::GRANTED_KEY => $team_agent['granted_permissions'],
                    self::GRANTING_KEY => $team_agent['granting_permissions']
                ];

                // Prepare Agent Timestamps
                $this->agent_last_assigned[$team_agent['agent_id']] = strtotime($team_agent['auto_assign_time']);
            }
        }

        // Build Primary Agent if necessary
        if (!isset($this->agent_permissions[$this->primary_agent])) {
            $insertPrimary = $this->db->prepare(
                "INSERT INTO `team_agents` (`agent_id`, `team_id`, `granted_permissions`, `granting_permissions`)"
                . " VALUES (:agent_id, :team_id, 0, 0);"
            );
            $insertPrimary->execute(['agent_id' => $this->primary_agent, 'team_id' => $this->id]);
        }

        // Team Row
        $this->row = $row;
    }

    /**
     * Load Backend_Team by ID
     *
     * <code>
     * <?php
     *
     * // Load Team from ID
     * $team = Backend_Team::load(1);
     *
     * // Get Team ID (1)
     * $team->getId();
     *
     * ?>
     * </code>
     *
     * @param int $id Team ID
     * @return Backend_Team|null
     * @throws PDOException
     */
    public static function load($id)
    {

        // App DB
        $db = DB::get('users');

        // Find Team by ID
        $team_query = $db->prepare("SELECT *"
            . " FROM `teams` "
            . " WHERE `id` = :id"
            . ";");

        // Execute Query
        $team_query->execute(['id' => $id]);

        // Return Row
        $team = $team_query->fetch();
        if ($team) {
            // Agent not Found
            if (empty($team)) {
                return null;
            }

            // Load team agents and permissions
            $team['team_agents'] = self::loadAgents($id);
            return new self ($team);
        } else {
            return false;
        }
    }

    /**
     * Load Backend_Team agents
     * @param int $id Team ID
     * @return array
     * @throws PDOException
     */
    public static function loadAgents($id)
    {

        // App DB
        $db = DB::get('users');

        // Find team agents by id
        $team_agents_query = $db->prepare("SELECT * FROM `team_agents` WHERE `team_id` = :team_id;");
        $team_agents_query->execute(['team_id' => $id]);
        return $team_agents_query->fetchAll();
    }


    /**
     * Assign agent to team.  Returns the team assigned to or false (depending on success)
     * @param int $agent_id Agent Id to be assigned
     * @param int $granted_permissions New agent permissions for team
     * @param int $granting_permissions New agent permissions for team
     * @return bool
     * @throws PDOException
     */
    public function assignAgent($agent_id, $granted_permissions = 0, $granting_permissions = 0)
    {

        // Ensure Agent to be added exists
        $agent = Backend_Agent::load($agent_id);
        if (!isset($agent)) {
            $errors[] = 'The agent to be added does not exist.';
        }

        // Ensure Agent to be added is not the primary agent
        if (in_array($agent_id, $this->getAgents())) {
            throw new InvalidArgumentException('The agent, '.$agent->getName().', is already a member of this team.');
        }

        // Create New Agent Array
        $new_agent = [
            'agent_id' => $agent_id,
            'team_id' => $this->getId(),
            'granted_permissions' => $granted_permissions,
            'granting_permissions' => $granting_permissions
        ];

        // Insert Agent into Team
        $assign_query = $this->db->prepare("INSERT INTO `team_agents` (`agent_id`, `team_id`, `granted_permissions`, `granting_permissions`)"
            . " VALUES (:agent_id, :team_id, :granted_permissions, :granting_permissions);");

        // Save Agent
        if ($assign_query->execute($new_agent)) {
            $this->agent_permissions[$agent_id] = [
                self::GRANTED_KEY => $new_agent['granted_permissions'],
                self::GRANTING_KEY => $new_agent['granting_permissions']
            ];
        } else {
            throw new InvalidArgumentException('New agent could not be added to this team.');
        }
    }

    /**
     * Unassign agent from team
     * @param int $agent Agent to remove from team
     * @return bool
     * @throws PDOException
     */
    public function unassignAgent($agent_id)
    {

        // Ensure Agent to be removed is in the team
        if (!in_array($agent_id, $this->getAgents())) {
            throw new InvalidArgumentException('This agent is not a member of this team.');
        }

        // Ensure Agent to be removed is in the team
        if ($agent_id == $this->getPrimaryAgent()) {
            throw new InvalidArgumentException('This agent is this teams owner and can not be deleted.');
        }

        // Remove Agent into Team
        $unassign_query = $this->db->prepare("DELETE FROM `team_agents` "
            . " WHERE `team_id` = :team_id"
            . " AND `agent_id` = :agent_id;");
        $unassign_params = ['team_id' => $this->getId(), 'agent_id' => $agent_id];

        // Find Team by ID
        if ($unassign_query->execute($unassign_params)) {
            unset($this->agent_permissions[$agent_id]);
            return true;
        } else {
            throw new InvalidArgumentException('Agent could not be removed from this team.');
        }
    }

    /**
     * Assigns a new primary agent
     * @param int $agent_id
     * @param number $granted_permissions
     * @param number $granting_permissions
     * @return bool
     * @throws InvalidArgumentException
     */
    public function reassignAgent($agent_id, $granted_permissions = 0, $granting_permissions = 0)
    {

        // Ensure Agent to be removed is in the team
        if (!in_array($agent_id, $this->getAgents())) {
            $this->assignAgent($agent_id, $granted_permissions, $granting_permissions);
        } else {
            $this->updateAgent($agent_id, $granted_permissions, $granting_permissions);
        }

        if ($agent_id != $this->getPrimaryAgent()) {
            // Delete existing permissions
            $team_agent_update_query = $this->db->prepare("UPDATE `teams`"
                . " SET `agent_id` = :agent_id"
                . " WHERE `id` = :id");
            if ($team_agent_update_query->execute(['agent_id' => $agent_id, 'id' => $this->getId()])) {
                // Updated Primary Agent variables
                $this->primary_agent = $agent_id;
            } else {
                throw new InvalidArgumentException('Primary agent could not be updated.');
            }
        }
        return true;
    }

    /**
     * Update a secondary agents permissions teams permissions
     * @param int $agent_id Agent to update
     * @param int $granted_permissions New agent permissions for team
     * @param int $granting_permissions New agent permissions for team
     * @throws PDOException
     */
    public function updateAgent($agent_id, $granted_permissions = 0, $granting_permissions = 0)
    {

        // Ensure Agent to be removed is in the team
        if (!in_array($agent_id, $this->getAgents())) {
            throw new InvalidArgumentException('This agent is not a member of this team.');
        }

        // Create New Agent Array
        $existing_agent = [
            'agent_id' => $agent_id,
            'team_id' => $this->getId(),
            'granted_permissions' => $granted_permissions,
            'granting_permissions' => $granting_permissions
        ];

        // Delete existing permissions
        $team_agent_update_query = $this->db->prepare("UPDATE `team_agents`"
            . " SET `granted_permissions` = :granted_permissions, `granting_permissions` = :granting_permissions"
            . " WHERE `team_id` = :team_id AND `agent_id` = :agent_id");
        if ($team_agent_update_query->execute($existing_agent)) {
            $this->agent_permissions[$agent_id] = [
                self::GRANTED_KEY => $existing_agent['granted_permissions'],
                self::GRANTING_KEY => $existing_agent['granting_permissions']
            ];
        } else {
            throw new InvalidArgumentException('Agent permissions could not be updated.');
        }
        return true;
    }

    /**
     * Get ID
     * @return int Team ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get this Teams Primary Agent
     * @return int
     */
    public function getPrimaryAgent()
    {
        return intval($this->primary_agent);
    }

    /**
     * Get this Teams Secondary Agents
     * @return array
     */
    public function getSecondaryAgents()
    {
        $primaryAgent = $this->getPrimaryAgent();
        return array_filter(array_keys($this->agent_permissions), function ($agent) use ($primaryAgent) {
            return $agent != $primaryAgent;
        });
    }

    /**
     * Get this Teams Secondary Agents
     * @param $agent_id Agent Id to get permissions for
     * @return array|false
     */
    public function getAgentPermissions($agent_id)
    {
        return $this->agent_permissions[$agent_id];
    }

    /**
     * Get ID
     * @return int Team ID
     */
    public function getAgentLastAssigned($agent_id)
    {
        return $this->agent_last_assigned[$agent_id];
    }

    /**
     * Get this Teams Agents
     * @param array $granted_permissions
     * @param array $granting_permissions
     * @return array
     * @deprecated use getAgentsCollection
     */
    public function getAgents($granted_permissions = [], $granting_permissions = [])
    {

        return $this->getAgentCollection()->filterByGrantedPermissions($granted_permissions)
            ->filterByGrantingPermissions($granting_permissions)
            ->getAllAgents();
    }

    /**
     * Get an Agents Teams
     * @param int $agent_id
     * @param array $granted_permissions
     * @param array $granting_permissions
     * @param bool $subdomains Limit to teams with subdomains
     * @return Backend_Team[]
     */
    public static function getTeams($agent_id, $granted_permissions = [], $granting_permissions = [], $subdomains = false)
    {

        // App DB
        $db = DB::get('users');

        $team_query = "SELECT `t`.`id`"
            . " FROM `teams` `t`"
            . " LEFT JOIN `team_agents` `ta` ON `t`.`id` = `ta`.`team_id`"
            . " WHERE (`ta`.`agent_id` = ?";
        $team_query_params = [$agent_id];

        // Agent Permissions Query
        if (!empty($granted_permissions)) {
            self::checkTeamPermission('`ta`.`granted_permissions`', $granted_permissions, $team_query, $team_query_params);
        }
        if (!empty($granting_permissions)) {
            self::checkTeamPermission('`ta`.`granting_permissions`', $granting_permissions, $team_query, $team_query_params);
        }

        $team_query .= ")";

        if ($subdomains) {
            $team_query .= " AND `t`.`subdomain` = 'true'";
        }

        $team_query .= " GROUP BY `t`.`id`;";

        // Execute Query
        $team_query = $db->prepare($team_query);
        $team_query->execute($team_query_params);

        // Return Teams created from array
        return array_map(function ($team) use ($db) {
            return self::load($team['id']);
        }, $team_query->fetchAll());
    }

    /**
     * Get Row
     * @return array Lead Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get Granted permissions
     * @return array Team Permissions
     */
    public function getGrantedPermissions()
    {

        if (!isset($this->granted_permisisons)) {
            $this->granted_permisisons = Backend_Team_Permission::loadGrantedPermissions();
        }
        return $this->granted_permisisons;
    }

    /**
     * Get Granting permissions
     * @return array Team Permissions
     */
    public function getGrantingPermissions()
    {

        if (!isset($this->granting_permisisons)) {
            $this->granting_permisisons = Backend_Team_Permission::loadGrantingPermissions();
        }
        return $this->granting_permisisons;
    }

    /**
     * Check if an agent is in this team (with requried permissions)
     * @param $agent_id Agent to check
     * @param array $required_permissions Limit results to agents with the following permissions
     * @return bool
     */
    public function checkAgent($agent_id, $required_granted_permissions = [], $required_granting_permissions = [])
    {

        return (
            $this->checkPermisison($this->agent_permissions[$agent_id][self::GRANTED_KEY] ?: 0, $required_granted_permissions) &&
            $this->checkPermisison($this->agent_permissions[$agent_id][self::GRANTING_KEY] ?: 0, $required_granting_permissions)
        );
    }

    /**
     * Check if a given permission is met by an agent
     * @param array $agent_permissions
     * @param array $required_permissions
     * @return bool
     */
    public function checkPermisison($agent_permissions, $required_permissions = [])
    {

        // If no permissions are required -> valid agent
        if (empty($required_permissions)) {
            return true;
        }

        // If permissions are required and agent does not have any permissions -> invalid agent
        if ($agent_permissions == 0) {
            return false;
        }

        // If permissions are required and agent has at least one matching permissions -> valid agent
        foreach ($required_permissions as $required_permission) {
            if ($agent_permissions & $required_permission) {
                return true;
            }
        }

        // If permissions are required and agent does not have any matching permissions -> invalid agent
        return false;
    }

    /**
     * Get / Set Info
     *
     * @param string $info
     * @param mixed $value
     * @return void|mixed
     */
    public function info($info, $value = null)
    {
        // Get Information
        if (is_string($info) && is_null($value)) {
            return $this->row[$info];
        }
        // Set Information
        if (is_string($info) && !is_null($value)) {
            $this->row[$info] = $value;
        }
    }

    /************************* ArrayAccess *************************/

    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($index)
    {
        return isset($this->row[$index]);
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return $this->row[$index];
        }
        return false;
    }

    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($index, $value)
    {
        if ($index) {
            $this->row[$index] = $value;
        } else {
            $this->row[] = $value;
        }
        return true;
    }

    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($index)
    {
        unset($this->row[$index]);
        return true;
    }

    /**
     * Gets the collection of agents belonging to this team.
     * @return \REW\Backend\Team\AgentCollection
     */
    public function getAgentCollection()
    {
        return $this->container->make(\REW\Backend\Team\AgentCollection::class, ['team' => $this]);
    }

    /**
     * @param \REW\Backend\Team\AgentCollection|null $agentCollection
     * @return \REW\Backend\Team\ListingCollection
     */
    public function getListingCollection(\REW\Backend\Team\AgentCollection $agentCollection = null)
    {
        if ($agentCollection === null) {
            // Build default agent collection
            $agentCollection = $this->getAgentCollection();
        }

        return $this->container->make(
            \REW\Backend\Team\ListingCollection::class,
            ['team' => $this, 'agentCollection' => $agentCollection]
        );
    }

    /************************* Internal *************************/

   /**
     * Check individual permission for validity
     * @param $string $column
     * @param string $required_permission
     * @param string|array $required_value
    */
    protected static function checkTeamPermission($column, $required_permissions = [], &$team_query, &$team_query_params)
    {

        $permission_query = [];
        $permission_params = [];
        foreach ($required_permissions as $required_permission) {
            if (is_int($required_permission)) {
                $permission_query []= $column . " & ?";
                $permission_params []= $required_permission;
            }
        }
        if (!empty($permission_query)) {
            $team_query .= " AND (" . implode(" OR ", $permission_query) . ") ";
        }

        if (!empty($permission_params)) {
            $team_query_params = array_merge($team_query_params, $permission_params);
        }
    }
}
