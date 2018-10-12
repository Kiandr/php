<?php

namespace REW\Api\Internal\Leads;

use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Auth\LeadsAuth;
use REW\Backend\Auth\TeamsAuth;
use REW\Backend\Leads\CustomFieldFactory;
use REW\Backend\Teams\Manager;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\EnvironmentInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Pagination\Pagination;
use Backend_Team;
use Exception;
use Locale;

class Search
{

    /**
     * Query Filter Methods
     *
     * @var array
     */
    const FILTER_METHODS = [
        'buildPermissionSqlFilters',
        'buildViewSqlFilters',
        'buildContactSqlFilters',
        'buildAssignmentSqlFilters',
        'buildGroupSqlFilters',
        'buildRangeSqlFilters',
        'buildCustomFieldSqlFilters',
        'buildSocialMediaSqlFilters',
        'buildActionPlansSqlFilters',
        'buildDueTasksSqlFilters'
    ];

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var CustomFieldFactory
     */
    protected $custom_fields;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var EnvironmentInterface
     */
    protected $env;

    /**
     * @var LeadsAuth
     */
    protected $leadsAuth;

    /**
     * @var array
     */
    protected $leadsQueryPieces = [];

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var array
     */
    protected $searchCriteria = [];

    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var TeamsAuth
     */
    protected $teamsAuth;

    /**
     * @var Manager (Teams)
     */
    protected $teamsManager;

    /**
     * @param AuthInterface $auth
     * @param CustomFieldFactory $customFields
     * @param DBInterface $db
     * @param EnvironmentInterface $env
     * @param LogInterface $log
     * @param ServerRequestInterface $serverRequest
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        CustomFieldFactory $customFields,
        DBInterface $db,
        EnvironmentInterface $env,
        LogInterface $log,
        ServerRequestInterface $serverRequest,
        SettingsInterface $settings
    ) {
        // Setup Dependencies
        $this->auth = $auth;
        $this->custom_fields = $customFields;
        $this->db = $db;
        $this->env = $env;
        $this->log = $log;
        $this->server_request = $serverRequest;
        $this->settings = $settings;

        // Permission Objects
        $this->leadsAuth = new LeadsAuth($this->settings);
        $this->teamsAuth = new TeamsAuth($this->settings);
        $this->teamsManager = new Manager($this->auth);

        // PSR-Friendly Request Variables
        $this->get = $this->server_request->getQueryParams();
    }

    /**
     * Process Search Filters - Lead / User Assignments
     *
     * @return array
     */
    protected function buildAssignmentSqlFilters()
    {
        $sql = [];
        $params = [];

        // Search by Agent(s)
        if (!empty($this->get['agents']) && is_array($this->get['agents'])) {
            $agent_sql_or = [];
            foreach ($this->get['agents'] as $index => $agent) {
                $agent_sql_or[] = sprintf(
                    " `u`.`agent` = :%s_agent ",
                    __FUNCTION__ . $index
                );
                $params[__FUNCTION__ . $index . '_agent'] = $agent;
            }
            if (!empty($agent_sql_or)) {
                $sql[] = " (" . implode(" OR ", $agent_sql_or) . ") ";
            }
        }

        // Search by Lender(s)
        if (!empty($this->get['lenders']) && is_array($this->get['lenders']) && !empty($this->settings->MODULES['REW_LENDERS_MODULE'])) {
            $lender_sql_or = [];
            foreach ($this->get['lenders'] as $index => $lender) {
                $lender_sql_or[] = sprintf(
                    " `u`.`lender` = :%s_lender ",
                    __FUNCTION__ . $index
                );
                $params[__FUNCTION__ . $index . '_lender'] = $lender;
            }
            if (!empty($lender_sql_or)) {
                $sql[] = " (" . implode(" OR ", $lender_sql_or) . ") ";
            }
        }

        // Search by Team(s)
        if (!empty($this->get['teams']) && is_array($this->get['teams']) && $this->teamsAuth->canViewTeamLeads()) {
            $team_group = [];
            foreach ($this->get['teams'] as $team_id) {
                $team = Backend_Team::load($team_id);
                $team_agents = $this->teamsManager->getAgentsSharingLeads($team, $this->teamsAuth->canManageTeams($this->auth));
                if (!empty($team_agents)) {
                    foreach ($team_agents as $team_agent) {
                        if (!in_array($team_agent, $team_group)) {
                            $team_group[] = $team_agent;
                        }
                    }
                }
            }
            if (!empty($team_group)) {
                $sql[] = " (" . implode(" OR ", $team_group) . ") ";
                $sql[] = sprintf(
                    " (`u`.`agent` = :%s_auth_id OR `u`.`share_lead` = '1') ",
                    __FUNCTION__
                );
                $params[__FUNCTION__ . '_auth_id'] = $this->auth->info('id');
            }
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params
        ];
    }

    /**
     * Process Search Filters - Lead Contact Info
     *
     * @return array
     */
    protected function buildContactSqlFilters()
    {
        $sql = [];
        $params = [];
        $join = [];

        // Lead First Name
        if (!empty($this->get['first_name'])) {
            $sql[] = sprintf(" `u`.`first_name` LIKE :%s_first_name ", __FUNCTION__);
            $params[__FUNCTION__ . '_first_name'] = '%' . $this->get['first_name'] . '%';
        }

        // Lead Last Name
        if (!empty($this->get['last_name'])) {
            $sql[] = sprintf(" `u`.`last_name` LIKE :%s_last_name ", __FUNCTION__);
            $params[__FUNCTION__ . '_last_name'] = '%' . $this->get['last_name'] . '%';
        }

        // Lead Email
        if (!empty($this->get['email'])) {
            $sql[] = sprintf(" `u`.`email` LIKE :%s_email ", __FUNCTION__);
            $params[__FUNCTION__ . '_email'] = '%' . $this->get['email'] . '%';
        }

        // Lead's Primary Property Type
        $search_type = trim($this->get['search_type']);
        if (!empty($this->get['search_type'])) {
            $sql[] = sprintf(" `u`.`search_type` LIKE :%s_search_type ", __FUNCTION__);
            $params[__FUNCTION__ . '_search_type'] = '%' . $search_type . '%';
        }

        // Lead's Primary City
        $search_city = trim($this->get['search_city']);
        if (!empty($this->get['search_city'])) {
            $sql[] = sprintf(" `u`.`search_city` LIKE :%s_search_city ", __FUNCTION__);
            $params[__FUNCTION__ . '_search_city'] = '%' . $search_city . '%';
        }

        // Lead's Primary Subdivision
        $search_subdivision = trim($this->get['search_subdivision']);
        if (!empty($this->get['search_subdivision'])) {
            $sql[] = sprintf(" `u`.`search_subdivision` LIKE :%s_search_subdivision ", __FUNCTION__);
            $params[__FUNCTION__ . '_search_subdivision'] = '%' . $search_subdivision . '%';
        }

        // Lead Search Price Range
        if (!empty($this->get['search_minimum_price']) || !empty($this->get['search_maximum_price'])) {
            $search_minimum_price = preg_replace('/[^0-9]/', '', $this->get['search_minimum_price']);
            $search_maximum_price = preg_replace('/[^0-9]/', '', $this->get['search_maximum_price']);
            if (!empty($search_minimum_price) && !empty($search_maximum_price)) {
                $sql[] = sprintf(
                    '('
                        . '('
                            . ' `u`.`search_minimum_price` >= :%1$s_search_minimum_price '
                            . ' AND `u`.`search_minimum_price` <= :%1$s_search_maximum_price '
                            . ' AND `u`.`search_maximum_price` >= :%1$s_search_minimum_price '
                            . ' AND `u`.`search_maximum_price` <= :%1$s_search_maximum_price '
                        . ') OR ('
                            . ' `u`.`search_minimum_price` >= :%1$s_search_minimum_price '
                            . ' AND `u`.`search_minimum_price` <= :%1$s_search_maximum_price '
                            . ' AND `u`.`search_maximum_price` <= 0 '
                        . ') OR ('
                            . ' `u`.`search_minimum_price` <= 0 '
                            . ' AND `u`.`search_maximum_price` >= :%1$s_search_minimum_price '
                            . ' AND `u`.`search_maximum_price` <= :%1$s_search_maximum_price '
                        . ')'
                    . ')',
                    __FUNCTION__
                );
                $params[__FUNCTION__ . '_search_minimum_price'] = $search_minimum_price;
                $params[__FUNCTION__ . '_search_maximum_price'] = $search_maximum_price;
            } else {
                if (!empty($search_minimum_price)) {
                    $sql[] = sprintf(
                        ' (`u`.`search_minimum_price` > 0 OR `u`.`search_maximum_price` > 0) '
                        . ' AND ('
                            . '('
                                . ' `u`.`search_minimum_price` >= :%1$s_search_minimum_price '
                                . ' AND `u`.`search_maximum_price` >= :%1$s_search_minimum_price '
                            . ') OR ('
                                . ' `u`.`search_minimum_price` >= :%1$s_search_minimum_price '
                                . ' AND `u`.`search_maximum_price` <= 0 '
                            . ') OR ('
                                . ' `u`.`search_minimum_price` <= 0 '
                                . ' AND `u`.`search_maximum_price` >= :%1$s_search_minimum_price '
                            . ')'
                        . ')',
                        __FUNCTION__
                    );
                    $params[__FUNCTION__ . '_search_minimum_price'] = $search_minimum_price;
                }
                if (!empty($search_maximum_price)) {
                    $sql[] = sprintf(
                        ' (`u`.`search_minimum_price` > 0 OR `u`.`search_maximum_price` > 0) '
                        . ' AND ('
                            . '('
                                . ' `u`.`search_minimum_price` <= :%1$s_search_maximum_price '
                                . ' AND `u`.`search_maximum_price` <= :%1$s_search_maximum_price '
                            . ') OR ('
                                . ' `u`.`search_minimum_price` <= :%1$s_search_maximum_price '
                                . ' AND `u`.`search_maximum_price` <= 0 '
                            . ') OR ('
                                . ' `u`.`search_minimum_price` <= 0 '
                                . ' AND `u`.`search_maximum_price` <= :%1$s_search_maximum_price '
                            . ')'
                        . ')',
                        __FUNCTION__
                    );
                    $params[__FUNCTION__ . '_search_maximum_price'] = $search_maximum_price;
                }
            }
        }

        // Lead Phone #
        if (!empty($this->get['phone'])) {
            $phone = [];
            $digits = str_split($this->get['phone']);
            foreach ($digits as $i => $digit) {
                if (is_numeric($digit)) {
                    $phone[] = $digit . (($i != count($digits) - 1) ? '[^[:digit:]]*' : '');
                }
            }
            $sql[] = sprintf(
                "(`u`.`phone` RLIKE :%s_phone "
                . " OR `u`.`phone_work` RLIKE :%s_phone "
                . " OR `u`.`phone_cell` RLIKE :%s_phone "
                . " OR `u`.`phone_fax` RLIKE :%s_phone)",
                __FUNCTION__,
                __FUNCTION__,
                __FUNCTION__,
                __FUNCTION__
            );
            $params[__FUNCTION__ . '_phone'] = !empty($phone)
                ? implode('', $phone)
                : $this->get['phone'];
        }

        // Lead Has a Phone # (Yes/No)
        if (!empty($this->get['has_phone'])) {
            if ($this->get['has_phone'] === 'yes') {
                $sql[] = " (`u`.`phone` != '' OR `u`.`phone_work` != '' OR `u`.`phone_cell` != '') ";
            } else if ($this->get['has_phone'] === 'no') {
                $sql[] = " (`u`.`phone` = '' AND `u`.`phone_work` = '' AND `u`.`phone_cell` = '') ";
            }
        }

        // Lead Preferred Contact Method
        if (in_array($this->get['contact_method'], ['email', 'phone', 'text'])) {
            $sql[] = sprintf(" `u`.`contact_method` = :%s_contact_method ", __FUNCTION__);
            $params[__FUNCTION__ . '_contact_method'] = $this->get['contact_method'];
        }

        // Lead Heat
        if (!empty($this->get['heat'])) {
            $heat_sql = [];
            $heat = array_filter(is_array($this->get['heat']) ? $this->get['heat'] : explode(',', $this->get['heat']));
            foreach($heat as $k => $val) {
                $heat_sql[] = sprintf(" `u`.`heat` = :%s_heat_%d ", __FUNCTION__, $k);
                $params[__FUNCTION__ . '_heat_' . $k] = $val;
            }
            $sql[] = sprintf("(%s)", implode(" OR ", $heat_sql));
        }

        // Lead Status
        if (!empty($this->get['status'])) {
            $status_sql = [];
            $status = array_filter(is_array($this->get['status']) ? $this->get['status'] : explode(',', $this->get['status']));
            foreach($status as $k => $val) {
                $status_sql[] = sprintf(" `u`.`status` = :%s_status_%d ", __FUNCTION__, $k);
                $params[__FUNCTION__ . '_status_' . $k] = $val;
            }
            $sql[] = sprintf("(%s)", implode(" OR ", $status_sql));
        }

        // Opted in for Text Messaging
        if (!empty($this->get['opt_texts']) && in_array($this->get['opt_texts'], ['in', 'out'])) {
            $sql[] = sprintf(" `u`.`opt_texts` = :%s_opt_texts ", __FUNCTION__);
            $params[__FUNCTION__ . '_opt_texts'] = $this->get['opt_texts'];
        }
        
        // Opted in for Campaigns / Auto Emails
        if (!empty($this->get['opt_marketing']) && in_array($this->get['opt_marketing'], ['in', 'out'])) {
            $sql[] = sprintf(" `u`.`opt_marketing` = :%s_opt_marketing ", __FUNCTION__);
            $params[__FUNCTION__ . '_opt_marketing'] = $this->get['opt_marketing'];
        }

        // Opted in for Saved Searches
        if (!empty($this->get['opt_searches']) && in_array($this->get['opt_searches'], ['in', 'out'])) {
            $sql[] = sprintf(" `u`.`opt_searches` = :%s_opt_searches ", __FUNCTION__);
            $params[__FUNCTION__ . '_opt_searches'] = $this->get['opt_searches'];
        }

        // Creation Date
        if (!empty($this->get['date_start']) && !empty($this->get['date_end'])) {
            $sql[] = sprintf(
                " `u`.`timestamp` >= :%s_date_start AND `u`.`timestamp` <= :%s_date_end ",
                __FUNCTION__,
                __FUNCTION__
            );
            $params[__FUNCTION__ . '_date_start'] = $this->get['date_start'] . ' 00:00:00';
            $params[__FUNCTION__ . '_date_end'] = $this->get['date_end'] . ' 23:59:25';
        } else if (!empty($this->get['date_start'])) {
            $sql[] = sprintf(" `u`.`timestamp` >= :%s_date_start ", __FUNCTION__);
            $params[__FUNCTION__ . '_date_start'] = $this->get['date_start'] . ' 00:00:00';
        } else if (!empty($this->get['date_end'])) {
            $sql[] = sprintf(" `u`.`timestamp` <= :%s_date_end ", __FUNCTION__);
            $params[__FUNCTION__ . '_date_end'] = $this->get['date_end'] . ' 23:59:25';
        }

        // Last Active Date
        if (!empty($this->get['active_start']) && !empty($this->get['active_end'])) {
            $sql[] = sprintf(
                " `u`.`timestamp_active` >= :%s_active_start AND `u`.`timestamp_active` <= :%s_active_end ",
                __FUNCTION__,
                __FUNCTION__
            );
            $params[__FUNCTION__ . '_active_start'] = $this->get['active_start'] . ' 00:00:00';
            $params[__FUNCTION__ . '_active_end'] = $this->get['active_end'] . ' 23:59:25';
        } else if (!empty($this->get['active_start'])) {
            $sql[] = sprintf(" `u`.`timestamp_active` >= :%s_active_start ", __FUNCTION__);
            $params[__FUNCTION__ . '_active_start'] = $this->get['active_start'] . ' 00:00:00';
        } else if (!empty($this->get['active_end'])) {
            $sql[] = sprintf(" `u`.`timestamp_active` >= :%s_active_end ", __FUNCTION__);
            $params[__FUNCTION__ . '_active_end'] = $this->get['active_end'] . ' 23:59:25';
        }

        // Lead IP Address
        if (!empty($this->get['search_ip'])) {
            $join[] = sprintf(" LEFT JOIN `%s` `v` ON `u`.`id` = `v`.`user_id` ", $this->settings->TABLES['LM_VISITS']);
            $sql[] = sprintf(" INET_NTOA(`v`.`ip`) = :%s_search_ip ", __FUNCTION__);
            $params[__FUNCTION__ . '_search_ip'] = $this->get['search_ip'];
        }

        // Lead Referer
        if (!empty($this->get['search_referer'])) {
            $sql[] = sprintf(" `u`.`referer` LIKE :%s_search_referer ", __FUNCTION__);
            $params[__FUNCTION__ . '_search_referer'] = '%' . $this->get['search_referer'] . '%';
        }

        // Bounced Lead
        if (!empty($this->get['bounced']) && in_array($this->get['bounced'], ['yes', 'no'])) {
            $sql[] = sprintf(" `u`.`bounced` = :%s_bounced ", __FUNCTION__);
            $params[__FUNCTION__ . '_bounced'] = ($this->get['bounced'] === 'yes' ? 'true' : 'false');
        }

        // FBL Lead
        if (!empty($this->get['fbl']) && in_array($this->get['fbl'], ['yes', 'no'])) {
            $sql[] = sprintf(" `u`.`fbl` = :%s_fbl ", __FUNCTION__);
            $params[__FUNCTION__ . '_fbl'] = ($this->get['fbl'] === 'yes' ? 'true' : 'false');
        }

        if (!empty($this->get['verified'])) {
            switch ($this->get['verified']) {
                case 'yes':
                case 'no':
                    $sql[] = sprintf(" `u`.`verified` = :%s_verified ", __FUNCTION__);
                    $params[__FUNCTION__ . '_verified'] = $this->get['verified'];
                    break;
                case 'pending':
                    $core = $this->env->loadMailCRMSettings()->getCoreConfig();
                    if (!empty($core)) {
                        // Whitelisted Providers
                        if (!empty($core['blacklisted'])) {
                            foreach ($core['blacklisted'] as $index => $provider) {
                                $sql[] = sprintf(
                                    " `u`.`email` NOT LIKE :%s_blacklisted ",
                                    __FUNCTION__ . $index
                                );
                                $params[__FUNCTION__ . $index . '_blacklisted'] = '%@' . $provider;
                            }
                        }
                        // Required Providers
                        $verify_sql_or = [];
                        if (!empty($core['verify_required'])) {
                            foreach ($core['verify_required'] as $index => $provider) {
                                $verify_sql_or[] = sprintf(
                                    " `u`.`email` LIKE :%s_verify_required ",
                                    __FUNCTION__ . $index
                                );
                                $params[__FUNCTION__ . $index . '_verify_required'] = '%@' . $provider;
                            }
                        }
                        // Required for All
                        $sql[] = sprintf(
                            " `u`.`verified` = 'no' "
                            . (!empty($verify_sql_or)
                                ? " %s (" . implode(" OR ", $verify_sql_or) . ") "
                                : ""
                            ),
                            (!empty($this->settings->SETTINGS['registration_verify']) ? 'OR' : 'AND')
                        );
                    }
                    break;
            }
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params,
            'join' => implode(' ', $join)
        ];
    }

    /**
     * Process Search Filters - Assigned Action Plans
     *
     * @return array
     */
    protected function buildActionPlansSqlFilters() {

        $sql = [];
        $params = [];

        $function = __FUNCTION__;

        // Search by Action Plans
        if (!empty($this->get['action_plans'])) {
            // Require Array
            $action_plans = array_filter(is_array($this->get['action_plans']) ? $this->get['action_plans'] : explode(',', $this->get['action_plans']));

            // Search Leads by Action Plan Status
            $sql_action_plan_status = '';
            if (!empty($this->get['action_plan_status'])) {
                $action_plan_status = $this->get['action_plan_status'];

                if ($action_plan_status == 'progress') {
                    $timestamp_completed = 'NULL';
                } else if ($action_plan_status == 'completed') {
                    $timestamp_completed = 'NOT NULL';
                }

                if (!empty($timestamp_completed)) {
                    $sql_action_plan_status = sprintf(" AND `timestamp_completed` IS %s",
                        $timestamp_completed);
                }
            }

            // Check Action Plans
            $sql[] = sprintf("`u`.`id` IN (SELECT `user_id` FROM `%s` WHERE `actionplan_id` IN(%s)" . $sql_action_plan_status . ")",
                             $this->settings->TABLES['LM_USER_ACTIONPLANS'], implode(', ',
                             array_map(function ($id) use($function, &$params) {
                $params[$function . '_action_plan' . $id] = $id;
                return ':' . $function . '_action_plan' . $id;
            }, $action_plans)));
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params
        ];
    }

    /**
     * Process Search Filters - Due Tasks
     *
     * @return array
     */
    protected function buildDueTasksSqlFilters() {

        $sql = [];
        $params = [];

        // Search Leads by Due Tasks
        if (!empty($this->get['action_plan_due_tasks'])) {
            $action_plan_due_tasks = $this->get['action_plan_due_tasks'];
            // Search No Action Plan
            if (in_array($action_plan_due_tasks, ['true', 'false'])) {
                // Check for Specific Types
                $action_plan_types_sql = [];
                $action_plan_types_params = [];
                if (!empty($this->get['action_plan_types']) && $action_plan_due_tasks === "true") {
                    $action_plan_types = array_filter(is_array($this->get['action_plan_types']) ? $this->get['action_plan_types'] : explode(',', $this->get['action_plan_types']));
                    foreach ($action_plan_types as $key => $val) {
                        $action_plan_types_sql[] = sprintf("`type` = :%s_type_%d", __FUNCTION__, $key);
                        $action_plan_types_params[__FUNCTION__ . "_type_" . $key] = $val;
                    }
                }
                $sql[] = sprintf("`u`.`id` %s (SELECT `user_id` FROM `%s` WHERE `status` = 'Pending' AND `timestamp_due` <= NOW()%s)",
                    ($action_plan_due_tasks === 'false' ? 'NOT IN' : 'IN'),
                    $this->settings->TABLES['LM_USER_TASKS'],
                    (!empty($action_plan_types_sql) ? sprintf(" AND (%s)", implode(" OR ", $action_plan_types_sql)) : "")
                );
            }
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => array_merge($params, (!empty($action_plan_types_sql) ? $action_plan_types_params : []))
        ];
    }

    /**
     * Process Search Filters - Custom Fields
     *
     * @return array
     */
    protected function buildCustomFieldSqlFilters()
    {
        $sql = [];
        $join = [];
        $custom_fields = $this->custom_fields->loadCustomFields(true);

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_field) {
                $custom_join = $custom_field->getSearchJoin($this->get, 'u');
                if (!empty($custom_join)) {
                    $join[] = $custom_join;
                }
                $custom_sql = $custom_field->getSearchWhere($this->get);
                if (!empty($custom_sql)) {
                    $sql[] = $custom_sql;
                }
            }
        }

        return [
            'sql' => implode(' AND ', $sql),
            'join' => implode(' ', $join)
        ];
    }

    /**
     * Process Search Filters - CRM Groups
     *
     * @return array
     */
    protected function buildGroupSqlFilters()
    {
        $sql = [];
        $params = [];

        if (!empty($this->get['groups'])) {
            $groups = array_filter(is_array($this->get['groups']) ? $this->get['groups'] : explode(',', $this->get['groups']));

            if (!empty($groups)) {
                // Search leads not in any groups
                if (in_array('none', $groups)) {
                    $sql[] = sprintf(
                        " `u`.`id` NOT IN (SELECT `user_id` FROM `%s`) ",
                        $this->settings->TABLES['LM_USER_GROUPS']
                    );
                // Search leads in specific groups
                } else {
                    if (!empty($groups)) {
                        $sql[] = sprintf(
                            " `u`.`id` IN ("
                            . " SELECT `user_id` FROM `%s` WHERE FIND_IN_SET(`group_id`, :%s_group)"
                            . ") ",
                            $this->settings->TABLES['LM_USER_GROUPS'],
                            __FUNCTION__
                        );
                        $params[__FUNCTION__ . '_group'] = implode(",", $groups);
                    }
                }
            }
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params
        ];
    }

    /**
     * Process Search Filters - User Permissions
     *
     * @return array
     */
    protected function buildPermissionSqlFilters()
    {
        $sql = [];
        $params = [];
        if (!$this->leadsAuth->canManageLeads($this->auth)) {
            if ($this->auth->isAgent()) {
                if ($this->teamsAuth->canViewTeamLeads()) {
                    $teamAgents = $this->teamsManager->getAgentsSharingLeads();
                    if (!in_array($this->auth->info('id'), $teamAgents)) {
                        $teamAgents[] = $this->auth->info('id');
                    }
                    $sql[] = sprintf(
                        " FIND_IN_SET(`u`.`agent`, :%s_team_agents) "
                        . " AND (`u`.`agent` = :%s_agent_id "
                        . " OR `u`.`share_lead` = '1') ",
                        __FUNCTION__,
                        __FUNCTION__
                    );
                    $params[__FUNCTION__ . '_team_agents'] = implode(',', $teamAgents);
                    $params[__FUNCTION__ . '_agent_id'] = $this->auth->info('id');
                } else {
                    $sql[] = sprintf(" `u`.`agent` = :%s_agent_id ", __FUNCTION__);
                    $params[__FUNCTION__ . '_agent_id'] = $this->auth->info('id');
                }
            } else if ($this->auth->isLender()) {
                $sql[] = sprintf(" `u`.`lender` = :%s_lender_id ", __FUNCTION__);
                $params[__FUNCTION__ . '_lender_id'] = $this->auth->info('id');
            }
        }
        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params
        ];
    }

    /**
     * Process Search Filters - Sliding Ranges
     *
     * @return array
     */
    protected function buildRangeSqlFilters()
    {
        $sql = [];
        $params = [];
        $join = [];
        $filters = $this->getRangeFilters();

        // Process Range Filters
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                // Range Values
                $min = $this->get[$filter['min']];
                $max = $this->get[$filter['max']];
                // Empty Values
                if (empty($min) && empty($max)) {
                    continue;
                // More Than
                } else if (!empty($min) && empty($max)) {
                    $sql[] = sprintf(
                        " `%s`.`%s` >= %d ",
                        $filter['table_alias'],
                        $filter['field'],
                        intval($min)
                    );
                // Less Than
                } else if (!empty($max) && empty($min)) {
                    $sql[] = sprintf(
                        " `%s`.`%s` < %d ",
                        $filter['table_alias'],
                        $filter['field'],
                        intval($max)
                    );
                // Search Exact
                } else if ($min === $max) {
                    $sql[] = sprintf(
                        " `%s`.`%s` = %d ",
                        $filter['table_alias'],
                        $filter['field'],
                        intval($min)
                    );
                // Search Range
                } else {
                    $sql[] = sprintf(
                        " (`%s`.`%s` >= %d AND `%s`.`%s` <= %d) ",
                        $filter['table_alias'],
                        $filter['field'],
                        intval($min),
                        $filter['table_alias'],
                        $filter['field'],
                        intval($max)
                    );
                }
            }
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params,
            'join' => implode(' ', $join)
        ];
    }

    /**
     * Process Search Filters - Social Media
     *
     * @return array
     */

    protected function buildSocialMediaSqlFilters()
    {
        $sql = [];
        $sql_group = [];

        if (!empty($this->get['social'])) {
            foreach ($this->get['social'] as $social) {
                $network = $this->getNetworks("network_" . $social);
                if (!empty($network)) {
                    $field = str_replace('network_', '', $social);
                    $sql_group[] = sprintf(" `oauth_%s` != '' ", $field);
                }
            }
        }
        if (!empty($sql_group)) {
            $sql[] = sprintf('(%s)', implode(' OR ', $sql_group));
        }

        return [
            'sql' => implode(' AND ', $sql),
        ];
    }


    /**
     * Get the current sort order
     *
     * @throws \InvalidArgumentException If invalid sort option is provided
     * @return string
     */
    protected function buildSqlSortOrder($orderArray)
    {
        $sql_order = [];
        foreach ($orderArray as $order => $sort) {
            switch ($order) {
                case 'id':
                case 'score':
                case 'value':
                case 'email':
                case 'status':
                case 'referer':
                case 'num_visits':
                case 'num_forms':
                case 'num_emails':
                case 'num_calls':
                case 'num_texts':
                case 'num_listings':
                case 'num_favorites':
                case 'first_name':
                case 'last_name':
                    $sql_order[] = sprintf('`u`.`%s` %s', $order, $sort);
                    break;
                case 'name':
                    $sql_order[] = sprintf(
                        '(`u`.`first_name` IS NULL && `u`.`last_name` IS NULL) %s, '
                        . ' `u`.`last_name` %s, '
                        . ' `u`.`first_name` %s',
                        $sort,
                        $sort,
                        $sort
                    );
                    break;
                case 'timestamp_created':
                    $sql_order[] = sprintf('`timestamp_created` %s', $sort);
                    break;
                case 'timestamp_active':
                    $sql_order[] = sprintf('`timestamp_active` %s', $sort);
                    break;
                case 'last_touched':
                    $sql_order[] = sprintf('GREATEST(`last_call`, `last_email`, `last_text`) %s', $sort);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf(
                        'Not a valid sort option: %s',
                        $order
                    ));
            }
        }
        return !empty($sql_order) ? sprintf(' ORDER BY %s ', implode(', ', $sql_order)) : '';
    }

    /**
     * Build view filter SQL
     *
     * @return array
     */
    protected function buildViewSqlFilters()
    {
        $sql = [];
        $params = [];
        $join = [];

        $view = $this->getCurrentView();
        switch ($view) {
            case 'all-leads':
                $this->searchCriteria['view'] = 'All Leads';
                break;
            case 'my-leads':
                $this->searchCriteria['view'] = 'My Leads';
                if ($this->leadsAuth->canManageLeads($this->auth)) {
                    if ($this->auth->isAgent()) {
                        $sql[] = sprintf(" `u`.`agent` = :%s_agent_id ", __FUNCTION__);
                        $params[__FUNCTION__ . '_agent_id'] = $this->auth->info('id');
                    } else if ($this->auth->isLender()) {
                        $sql[] = sprintf(" `u`.`lender` = :%s_lender_id ", __FUNCTION__);
                        $params[__FUNCTION__ . '_lender_id'] = $this->auth->info('id');
                    }
                }
                break;
            case 'inquiries':
                $this->searchCriteria['view'] = 'Inquired';
                $sql[] = " `f`.`user_id` IS NOT NULL ";
                $sql[] = " `u`.`num_forms` > 0 ";
                $join[] = sprintf(
                    " LEFT JOIN `%s` `f` ON `u`.`id` = `f`.`user_id` ",
                    $this->settings->TABLES['LM_FORMS']
                );
                break;
            case 'rejected':
                $this->searchCriteria['view'] = 'Rejected';
                $sql[] = " `u`.`agent` = 1 AND `u`.`status` = 'rejected' ";
                break;
            case 'online':
                $this->searchCriteria['view'] = 'Online';
                $sql[] = " `u`.`timestamp_active` >= NOW() - INTERVAL 60 SECOND ";
                break;
            case 'accepted':
                $this->searchCriteria['view'] = 'Accepted';
                $sql[] = " `u`.`status` = 'accepted' ";
                break;
            case 'pending':
                $this->searchCriteria['view'] = 'Pending';
                $sql[] = " `u`.`status` = 'pending' ";
                break;
            case 'unassigned':
                $this->searchCriteria['view'] = 'Unassigned';
                $sql[] = " `u`.`status` = 'unassigned' ";
                break;
        }

        return [
            'sql' => implode(' AND ', $sql),
            'params' => $params,
            'join' => implode(' ', $join)
        ];
    }

    /**
     * Get the entire compiled lead results count query string
     *
     * @return string
     */
    protected function compileLeadsCountQuery()
    {
        $sql = sprintf(
            " SELECT "
            . " COUNT(`u`.`id`) as `total` "
            . " FROM `%s` `u` "
            . " %s "
            . " WHERE 1 "
            . " %s ",
            $this->settings->TABLES['LM_LEADS'],
            $this->getSqlJoins(),
            $this->getSqlWhere()
        );
        return $sql;
    }

    /**
     * Get the entire compiled leads query string
     *
     * @return string
     */
    protected function compileLeadsQuery($pagination)
    {

        $orderArray = $pagination->getOrder();
        $sort = $this->buildSqlSortOrder($orderArray);

        // @todo: Only select columns when needed
        $sql = sprintf(
            "SELECT "
            . " `u`.`id`, "
            . " `u`.`first_name`, "
            . " `u`.`last_name`, "
            . " `u`.`email`, "
            . ' IF(`u`.`image` != \'\', CONCAT(\'%sleads/\', `u`.`image`), null) AS `image`, '
            . " IF(`u`.`timestamp_active` >= NOW() - INTERVAL 60 SECOND, 'true', 'false') AS `online`, "
            . " IF(`u`.`opt_marketing` = 'in', 'in', 'out') AS `opt_marketing`, "
            . " IF(`u`.`opt_searches` = 'in', 'in', 'out') AS `opt_searches`, "
            . " IF(`u`.`opt_texts` = 'in', 'in', 'out') AS `opt_texts`, "
            . " IF(`u`.`phone` != '', `u`.`phone`, null) AS `phone`, "
            . " IF(`u`.`phone_cell` != '', `u`.`phone_cell`, null) AS `phone_cell`, "
            . " IF(`u`.`keywords` != '', `u`.`keywords`, null) AS `keywords`, "
            . " IF(`u`.`notes` != '', `u`.`notes`, null) AS `notes`, "
            . " `u`.`score`, "
            . " IF (`u`.`referer` != '', `u`.`referer`, null) AS `source`, "
            . " `u`.`status`, "
            . " `u`.`timestamp` AS `timestamp_created`, "
            . " `u`.`timestamp_active` AS `timestamp_active`, "
            . " `u`.`value`, "
            . " `u`.`num_calls`, "
            . " `u`.`num_emails`, "
            . " `u`.`num_texts`, "
            . " `u`.`num_forms`, "
            . " `u`.`num_messages`, "
            . " `u`.`num_favorites`, "
            . " `u`.`num_visits`, "
            . " `u`.`num_listings`, "
            . " `u`.`num_searches`, "
            . " `u`.`last_action`, "
            . " @lt:=GREATEST(`last_call`, `last_email`, `last_text`) as `last_touched`, "
            . " ELT(FIELD(@lt, `last_call`, `last_email`, `last_text`), 'call', 'email', 'text') as `last_touched_method`, "
            . " `u`.`agent`, "
            . " `u`.`lender` "
            . " FROM `%s` `u` "
            . " %s "
            . " LEFT JOIN `lenders` `l` ON `u`.`lender` = `l`.`id` "
            . " WHERE 1 "
            . " %s "
            . " GROUP BY `u`.`id` "
            . " %s "
            . " %s "
            . ";",
            $this->settings->URLS['UPLOADS'],
            $this->settings->TABLES['LM_LEADS'],
            $this->getSqlJoins(),
            $this->getSqlWhere(true),
            $sort,
            $this->getSqlLimit()
        );
        return $sql;
    }

    /**
     * Get the count of leads matching the search query
     *
     * @return int
     */
    public function fetchLeadsCount()
    {
        $query = $this->db->prepare($this->compileLeadsCountQuery());
        $query->execute($this->getSqlWhereParams());
        $return = $query->fetch();
        return $return['total'] ?: 0;
    }

    /**
     * Run the current lead query and return the results
     *
     * @return array
     */
    public function fetchLeads($pagination)
    {
        $query = $this->db->prepare($this->compileLeadsQuery($pagination));
        $query->execute($this->getSqlWhereParams(true));
        return $query->fetchAll();
    }

    /**
     * Get the current view filter
     *
     * @return string
     */
    protected function getCurrentView()
    {
        return !empty($this->get['view']) ? $this->get['view'] : 'all-leads';
    }

    /**
     * Get a list of social networks, or a specific network
     *
     * @param bool $single_network (optional)
     * @return array
     */
    public function getNetworks($single_network = null)
    {
        $networks = [
            'network_facebook'  => ['title' => 'Facebook',     'image' => 'facebook_16x16.svg'],
            'network_microsoft' => ['title' => 'Windows Live', 'image' => 'windows_16x16.svg'],
            'network_google'    => ['title' => 'Google',       'image' => 'google_16x16.svg'],
            'network_linkedin'  => ['title' => 'LinkedIn',     'image' => 'linkedin_16x16.svg'],
            'network_twitter'   => ['title' => 'Twitter',      'image' => 'twitter_16x16.svg'],
            'network_yahoo'     => ['title' => 'Yahoo!',       'image' => 'yahoo_16x16.svg'],
        ];
        return !empty($single_network) ? $networks[$single_network] : $networks;
    }

    /**
     * Get search form range filters
     *
     * @return array
     */
    protected function getRangeFilters()
    {
        return [
            ['table_alias' => 'u', 'min' => 'visits_min',           'max' => 'visits_max',           'field' => 'num_visits',         'title' => 'Visits'],
            ['table_alias' => 'u', 'min' => 'listings_min',         'max' => 'listings_max',         'field' => 'num_listings',       'title' => 'Viewed Listings'],
            ['table_alias' => 'u', 'min' => 'favorites_min',        'max' => 'favorites_max',        'field' => 'num_favorites',      'title' => Locale::spell('Favorite') . ' Listings'],
            ['table_alias' => 'u', 'min' => 'searches_min',         'max' => 'searches_max',         'field' => 'num_saved',          'title' => 'Saved Searches'],
            ['table_alias' => 'u', 'min' => 'inquiries_min',        'max' => 'inquiries_max',        'field' => 'num_forms',          'title' => 'Inquiries'],
            ['table_alias' => 'u', 'min' => 'calls_min',            'max' => 'calls_max',            'field' => 'num_calls',          'title' => 'Calls'],
            ['table_alias' => 'u', 'min' => 'emails_min',           'max' => 'emails_max',           'field' => 'num_emails',         'title' => 'Emails'],
            ['table_alias' => 'u', 'min' => 'texts_incoming_min',   'max' => 'texts_incoming_max',   'field' => 'num_texts_incoming', 'title' => 'Incoming Texts'],
            ['table_alias' => 'u', 'min' => 'texts_outgoing_min',   'max' => 'texts_outgoing_max',   'field' => 'num_texts_outgoing', 'title' => 'Outgoing Texts'],
        ];
    }

    /**
     * Get alternate Where Columns
     *
     * @return array
     */
    protected function getWhereColumns()
    {
        return [
            'id'                => 'u.id',
            'score'             => 'u.score',
            'value'             => 'u.value',
            'email'             => 'u.email',
            'status'            => 'u.status',
            'referer'           => 'u.referer',
            'num_visits'        => 'u.num_visits',
            'num_forms'         => 'u.num_forms',
            'num_emails'        => 'u.num_emails',
            'num_calls'         => 'u.num_calls',
            'num_texts'         => 'u.num_texts',
            'num_listings'      => 'u.num_listings',
            'num_favorites'     => 'u.num_favorites',
            'num_searches'      => 'u.num_searches',
            'timestamp_created' => 'u.timestamp',
            'timestamp_active'  => 'timestamp_active',
            'last_name'         => 'u.last_name',
            'first_name'        => 'u.first_name'
        ];
    }

    /**
     * Get query JOINs
     *
     * @return string
     */
    protected function getSqlJoins()
    {
        return $this->leadsQueryPieces['sql_join'] ?: '';
    }

    /**
     * Get query LIMIT
     *
     * @return string
     */
    protected function getSqlLimit()
    {
        return $this->leadsQueryPieces['sql_limit'] ?: '';
    }

    /**
     * Get query WHERE sql
     *
     * @param bool $paginated
     * @return string
     */
    protected function getSqlWhere($paginated = false)
    {
        $sqlWhere = $this->leadsQueryPieces['sql_where']['sql'] ?: '';
        if (!empty($paginated)) {
            $paginateQuery = $this->leadsQueryPieces['paginate']['query'];
            if (!empty($paginateQuery)) {
                $sqlWhere .= ' AND ' . $paginateQuery;
            }
        }
        return $sqlWhere;
    }

    /**
     * Get query WHERE params
     *
     * @param bool $paginated
     * @return array
     */
    protected function getSqlWhereParams($paginated = false)
    {
        $sqlParams = $this->leadsQueryPieces['sql_where']['params'] ?: [];
        if (!empty($paginated)) {
            $paginateParams = $this->leadsQueryPieces['paginate']['params'];
            if (!empty($paginateParams)) {
                $sqlParams = array_merge($sqlParams, $paginateParams);
            }
        }
        return $sqlParams;
    }

    /**
     * Set query JOINs
     *
     * @param array $sql_join
     */
    protected function setSqlJoinExtras($sql_join = [])
    {
        $this->leadsQueryPieces['sql_join'] = (!empty($sql_join)
            ? implode(' ', $sql_join)
            : ''
        );
    }

    /**
     * Set query LIMIT and OFFSET
     *
     * @param int $limit (optional)
     * @param class $paginate
     */
    protected function setSqlLimit($limit = null, $paginate)
    {
        $limit = $paginate->getLimit();
        if (!empty($limit)) {
            $this->leadsQueryPieces['sql_limit'] = sprintf(
                ' LIMIT %s ',
                $limit
            );
        }
    }

    /**
     * Set query WHERE
     *
     * @param array $sql_where
     * @param array $params
     * @param class $pagination
     */
    protected function setSqlWhere($sql_where = [], $params = [], $pagination)
    {
        // Get query string and params from pagination instance
        $where = $pagination->getWhere($this->getWhereColumns());
        $whereParams = $pagination->getParams();

        // Remap to named parameters
        // Switching To named parameters as mixing positional and named causes issues.
        $queryParams = [];
        $queryString = preg_replace_callback('/\?/', function () use (&$queryParams, &$whereParams) {
            $queryId = uniqid();
            $queryParams[$queryId] = array_shift($whereParams);
            return sprintf(':%s', $queryId);
        }, $where);

        $this->leadsQueryPieces['sql_where']['sql'] = (!empty($sql_where)
            ? ' AND ' . implode(' AND ', $sql_where)
            : ''
        );
        $this->leadsQueryPieces['paginate']['query'] = $queryString;
        $this->leadsQueryPieces['paginate']['params'] = $queryParams;
        $this->leadsQueryPieces['sql_where']['params'] = $params;
    }


    /**
     * Update the leads sql query based on the current GET variables
     *
     * @class pagination
     * @var int $after
     */
    public function updateLeadsQuery($limit = null, $pagination)
    {
        $where = [];
        $params = [];
        $join = [];

        // Apply Query Filters
        foreach (self::FILTER_METHODS as $fMethod) {
            $filter = $this->$fMethod();
            if (!empty($filter['sql'])) {
                $where[] = $filter['sql'];
            }
            if (!empty($filter['params'])) {
                $params = array_merge($params, $filter['params']);
            }
            if (!empty($filter['join'])) {
                $join[] = $filter['join'];
            }
        }

        // Update Query Pieces
        $this->setSqlWhere($where, $params, $pagination);
        $this->setSqlJoinExtras($join);
        $this->setSqlLimit($limit, $pagination);
    }
}
