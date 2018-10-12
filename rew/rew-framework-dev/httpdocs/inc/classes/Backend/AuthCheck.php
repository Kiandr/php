<?php


/**
 * Backend_AuthCheck hashes user authorization use
 *
 * @category Backend
 * @package  Backend
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class Backend_AuthCheck
{

    //Authenticated Application
    const APPS = [
        'agents',
        'associates',
        'blogs',
        'calendar',
        'directory',
        'idx',
        'leads',
        'lenders',
        'listings',
        'partners',
        'reports',
        'teams',
        'cms',
        'subdomain',
        'tools',
        'settings'
    ];

    /**
     * Application Database connection
     *
     * @var DB $db
     */
    protected $db;

    /**
     * Application Authuser
     *
     * @var Auth $auth
     */
    protected $auth;

    /**
     * Application Auths
     *
     * @var array $apps
     */
    protected $apps;

    /**
     * Hashmap of computed user authentication
     *
     * @var array $hashmap
     */
    protected $hashmap = array();

    /**
     * Config Rules
     *
     * @var array $row
     */
    protected $config = array();

    /**
     * Construct Authcheck
     *
     * @param Auth $auth User Auth to check against
     */
    public function __construct(Auth $auth)
    {

        $this->db = DB::get();
        $this->auth = $auth;
        foreach (self::APPS as $app) {
            $app_name = in_array($app, ['idx','cms']) ? strtoupper($app) : ucfirst(strtolower($app));
            $className = 'Backend_AuthCheck_' . $app_name;
            $this->apps[$app] = new $className($auth);
        }
    }

    /******************** Check/Hash Permissions ********************/

    /**
     * Get an authorization app for the current page
     *
     * @param string $mode   Current Object being Edited
     * @param string $action Current Action
     *
     * @return bool Does authuser have the required permissions
     * @throws InvalidArgumentException
     */
    public function getAuthorization($mode, $action = 'view')
    {
        // Check for permission function
        $hash = md5($mode.'_'.$action);
        if (!isset($this->hashmap[$hash])) {
            if (!in_array($mode, self::APPS)) {
                $this->hashmap[$hash] = true;
            } else if (!method_exists($this->apps[$mode], $action)) {
                $this->hashmap[$hash] = true;
            } else {
                $this->hashmap[$hash] = $this->apps[$mode]->$action();
            }
        }
        return $this->hashmap[$hash];
    }

    /******************** Subdomain Authorization ********************/

    /**
     * Get CMS Authorization
     *
     * @param string   $permission Subdomain Permission Type to check (Manage, snippet, radio-buttons, etc...)
     * @param int|null $team_id    Id of team to check (If on team subdomain)
     * @param int|null $agent_id   Id of agent to check (If on agent subdomain)
     *
     * @return bool
     */
    public function getSubdomainAuthorization($permission, $team_id = null, $agent_id = null)
    {

        // Check Team Subdomain
        if (isset($team_id) && $this->getAuthorization('teams', ($permission == 'manage' ? 'subdomain' : $permission))) {
            // Get Auth Teams
            $team_ids = array_map(
                function (Backend_Team $team) {
                    return $team->getId();
                },
                Backend_Team::getTeams($this->auth->info('id'), [Backend_Team::PERM_EDIT_SUBDOMAIN], null, true)
            );

            // Check against Auth Teams
            if (in_array($team_id, $team_ids) || $this->getAuthorization('teams', 'manage')) {
                // Check Team Subdomain Existence
                $team = Backend_Team::load($team_id);
                if (!empty($team) && $team instanceof Backend_Team && $team['subdomain'] == 'true') {
                    return true;
                }
            }

        // Check Agent Subdomain
        } else if (isset($agent_id)) {
            if (($this->getAuthorization('subdomain', $permission)
                && $agent_id == $this->auth->info('id'))
                || $this->getAuthorization('subdomain', 'manage_all')
            ) {
                // Check Agent Subdomain Existance
                $agent = Backend_Agent::load($agent_id);
                if (!empty($agent) && $agent instanceof Backend_Agent && $agent->info('cms') == 'true') {
                    return true;
                }
            }

        // Check Domain
        } else if ($this->getAuthorization('cms', $permission)) {
            return true;
        }

        // Unauthorized
        return false;
    }

    /**
     * Gets the Default Agent Subdomain
     *
     * @param string $permission Subdomain Permission Type to check (Manage, snippet, radio-buttons, etc...)
     *
     * @return int|NULL
     */
    public function getDefaultSubdomainAgent($permission = 'manage')
    {
        if ($this->getAuthorization('subdomain', $permission)) {
            return $this->auth->info('id');
        }
        return null;
    }

    /**
     * Gets the Default Team Subdomain
     *
     * @param string $permission Subdomain Permission Type to check (Manage, snippet, radio-buttons, etc...)
     *
     * @return int|NULL
     */
    public function getDefaultSubdomainTeam($permission = 'subdomain')
    {
        if ($this->getAuthorization('teams', ($permission == 'manage' ? 'subdomain' : $permission))) {
            if ($this->getAuthorization('teams', 'manage')) {
                $team_query = $this->db->prepare("SELECT `id` FROM `" . TABLE_TEAMS . "` WHERE `subdomain` = 'true' ORDER BY `id` ASC LIMIT 1");
                $team_query->execute();
                $team = $team_query->fetch();
                if (!empty($team)) {
                    return $team['id'];
                }
            } else {
                $team_ids = array_map(
                    function (Backend_Team $team) {
                        return $team->getId();
                    },
                    Backend_Team::getTeams($this->auth->info('id'), [Backend_Team::PERM_EDIT_SUBDOMAIN], null, true)
                );
                if (!empty($team_ids)) {
                    return array_shift($team_ids);
                }
            }
        }
        return null;
    }

    /**
     * Get Subdomain Variables
     *
     * @param int $team_id  Team Id if team subdomain
     * @param int $agent_id Agent Id if agent subdomain
     *
     * @return array
     */
    public function getSubdomainVariables($team_id = null, $agent_id = null)
    {

        if (isset($team_id)) {
            /* Get Team Subdomain Variables*/
            if ($team = Backend_Team::load($team_id)) {
                return [
                    'owner_sql'  => "`team` = " . $this->db->quote($team_id),
                    'assign_sql' => "`team` = " . $this->db->quote($team_id) . ", `agent` = NULL, ",
                    'link'       => sprintf(URL_AGENT_SITE, $team->info('subdomain_link')),
                    'title'      => $team->info('subdomain_link'),
                    'href'       => '?team=' . $team_id,
                    'and_href'   => '&team=' . $team_id,
                    'input'      => '<input type="hidden" name="team" value="' . $team_id . '">'
                ];
            }
        } else if (isset($agent_id)) {
            /* Get Agent  Subdomain Variables*/
            if ($agent = Backend_Agent::load($agent_id)) {
                return [
                    'owner_sql'  => "`agent` = " . $this->db->quote($agent_id),
                    'assign_sql' => "`agent` = " . $this->db->quote($agent_id) . ", `team` = NULL, ",
                    'link'       => sprintf(URL_AGENT_SITE, $agent->info('cms_link')),
                    'title'      => $agent->info('cms_link'),
                    'href'       => '?agent=' . $agent_id,
                    'and_href'   => '&agent=' . $agent_id,
                    'input'      => '<input type="hidden" name="agent" value="' . $agent_id . '">'
                ];
            }
        }

        /* Get Site Variables */
        return [
            'owner_sql'  => "`agent` = '1'",
            'assign_sql' => "`agent` = '1', `team` = NULL, ",
            'link'       => Settings::getInstance()->SETTINGS['URL']
        ];
    }

    /**
     * Get Editable CMS Subdomains
     *
     * @return array ['name','id','type']
     */
    public function getSubdomains()
    {

        //Editable Subdomains
        $subdomains = [];

        //Get All Agent Subdomains
        if ($this->getAuthorization('subdomain', 'manage_all')) {
            $queryCMSAgents = $this->db->prepare('SELECT `id`, `cms_link` FROM `' . LM_TABLE_AGENTS . '` WHERE `cms` = "true" ORDER BY `cms_link`');
            $queryCMSAgents->execute();
            $cms_agents = $queryCMSAgents->fetchAll();
            foreach ($cms_agents as $cms_agent) {
                $subdomains[] = ['name' => $cms_agent['cms_link'], 'id' => $cms_agent['id'], 'type' => 'agent'];
            }

            //Get This Agents Subdomain
        } else if ($this->getAuthorization('subdomain', 'manage')) {
            $subdomains[] = ['name' => $this->auth->info('cms_link'), 'id' => $this->auth->info('id'), 'type' => 'agent'];
        }

        //Get All Team Subdomains
        if ($this->getAuthorization('teams', 'manage')) {
            $queryCMSTeams = $this->db->prepare('SELECT `id`, `subdomain_link` FROM `' . TABLE_TEAMS . '` WHERE `subdomain` = "true" ORDER BY `subdomain_link`');
            $queryCMSTeams->execute();
            $cms_teams = $queryCMSTeams->fetchAll();
            foreach ($cms_teams as $cms_team) {
                $subdomains[] = ['name' => $cms_team['subdomain_link'], 'id' => $cms_team['id'], 'type' => 'team'];
            }

        //Get This Agents Subdomain
        } else if ($this->getAuthorization('subdomain', 'manage')) {
            $team_subdomains = [];
            $teams = Backend_Team::getTeams($this->auth->info('id'), [Backend_Team::PERM_EDIT_SUBDOMAIN], null, true);
            foreach ($teams as $team) {
                $subdomains[] = ['name' => $team['subdomain_link'], 'id' => $team['id'], 'type' => 'team'];
            }
            usort(
                $team_subdomains,
                function ($a, $b) {
                    if ($a['name'] == $b['name']) {
                        return 0;
                    }
                    return ($a['name'] < $b['name']) ? -1 : 1;
                }
            );
            array_merge($subdomains, $team_subdomains);
        }

        // Append Domain if Allowed
        if ($this->getAuthorization('cms', 'homepage')) {
            array_unshift($subdomains, ['name' => Settings::getInstance()->SETTINGS['URL'], 'id' => '1', 'type' => 'domain']);
        }
        return $subdomains;
    }
}
