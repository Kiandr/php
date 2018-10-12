<?php

namespace REW\Api\Internal\Controller\Route\Crm\User\Tasks;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * User Task Collection Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var int
     */
    protected $limit = 500;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var int
     */
    protected $modeId;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var array
     */
    protected $sqlParams;

    /**
     * @var array
     */
    protected $sqlWhere;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();

        $this->checkModuleEnabled();

        $this->setMode();
        $this->setModeId();

        $this->checkModeValidity();

        $this->limit = (intval($this->get['limit']) > 0) ? intval($this->get['limit']) : $this->limit;
        $this->page = (intval($this->get['page']) > 0) ? intval($this->get['page']) : null;
        $this->offset = ($this->page > 1 && $this->limit >= 1) ? ($this->page - 1) * $this->limit : null;

        $this->buildQueryFilters();

        // Define API Return Value Structure
        $tasks = [
            'count' => $this->fetchResultsCount(),
            'limit' => $this->limit,
            'page' => $this->page,
            'tasks' => [],
        ];

        if ($tasks['count'] > 0) {
            $tasks['tasks'] = $this->fetchResults();
        }

        $response->setBody(json_encode($tasks));
    }

    /**
     * Build filters for querying tasks
     */
    protected function buildQueryFilters()
    {
        // Query Filters
        $this->sqlWhere = [];
        $this->sqlParams = [];

        // Task Status Filter
        $statuses = !empty($this->get['statuses']) ? $this->get['statuses'] : null;
        if (!empty($statuses)) {
            $statuses = (is_array($statuses)) ? $statuses : [$statuses];
            $this->sqlWhere[] = " FIND_IN_SET(`ut`.`status`, :statuses) ";
            $this->sqlParams['statuses'] = implode(",", $statuses);
        }

        // Toggle Filtering of Automated Tasks
        if ($this->get['hide_automated'] === 'true') {
            $this->sqlWhere[] = " `t`.`automated` = 'N' ";
        }

        // Filter by user task timestamps
        foreach (['timestamp_due', 'timestamp_expire', 'timestamp_resolved', 'timestamp_scheduled'] as $timestampType) {
            $minDateType = 'min_' . $timestampType;
            $maxDateType = 'max_' . $timestampType;
            if (!empty($this->get[$minDateType]) || !empty($this->get[$maxDateType])) {
                // Ranges
                if (!empty($this->get[$minDateType]) && !empty($this->get[$maxDateType])) {
                    $this->sqlWhere[] = sprintf(
                        ' `ut`.`%s` >= :%s AND `ut`.`%s` <= :%s ',
                        $timestampType,
                        $minDateType,
                        $timestampType,
                        $maxDateType
                    );
                    $this->sqlParams[$minDateType] = $this->get[$minDateType];
                    $this->sqlParams[$maxDateType] = $this->get[$maxDateType];
                    // Min || Max
                } else {
                    $dateDirection = !empty($this->get[$minDateType]) ? 'min' : 'max';
                    $dateField = (!empty($this->get[$minDateType])) ? $minDateType : $maxDateType;
                    $this->sqlWhere[] = sprintf(
                        ' `ut`.`%s` %s :%s ',
                        $timestampType,
                        ($dateDirection === 'min') ? '>=' : '<=',
                        $dateField
                    );
                    $this->sqlParams[$dateField] = $this->get[$dateField];
                }
            }
        }

        // Mode-Specific Filters
        switch ($this->mode) {
            case 'agent':
                $this->sqlWhere[] = " `u`.`agent` = :agent_id "
                    . " AND `t`.`performer` = 'Agent' ";
                $this->sqlParams['agent_id'] = $this->modeId;
                break;
            case 'lender':
                $this->sqlWhere[] = " `u`.`lender` = :lender_id AND `t`.`performer` = 'Lender' ";
                $this->sqlParams['lender_id'] = $this->modeId;
                break;
            case 'associate':
                $this->sqlWhere[] = " `t`.`performer` = 'Associate' ";
                break;
        }
    }

    /**
     * Check if the action plans module is enabled on this site
     * @throws InsufficientPermissionsException
     */
    protected function checkModuleEnabled()
    {
        if (empty($this->settings->MODULES['REW_ACTION_PLANS'])) {
            throw new InsufficientPermissionsException('The action plan feature is not enabled on this site.');
        }
    }

    /**
     * Check validity of the API request
     * @throws InsufficientPermissionsException
     */
    protected function checkModeValidity()
    {
        if (empty($this->mode)) {
            throw new InsufficientPermissionsException('Invalid user type.');
        }

        if (empty($this->modeId)) {
            throw new InsufficientPermissionsException('Failed to load user mode details.');
        }
    }

    /**
     * @param int $taskId
     * @return array
     */
    protected function fetchEmailDetails($taskId)
    {
        $emailDetails = $this->db->fetch(sprintf(
            "SELECT "
            . " `body` AS `message`, "
            . " `subject` "
            . " FROM %s "
            . " WHERE `task_id` = :task_id "
            . " LIMIT 1 "
            . ";",
            $this->settings->TABLES['LM_TASK_EMAILS']
        ), [
            'task_id' => $taskId
        ]);
        return $emailDetails ?: ['message' => null, 'subject' => null];
    }

    /**
     * @param int $taskId
     * @return array
     */
    protected function fetchGroupDetails($taskId)
    {
        $results = $this->db->fetchAll(sprintf(
            "SELECT "
            . " `g`.`id`, "
            . " `g`.`name`, "
            . " `g`.`style` "
            . " FROM %s `tg` "
            . " LEFT JOIN %s `g` ON `g`.`id` = `tg`.`group_id` "
            . " WHERE `tg`.`task_id` = :task_id "
            . ";",
            $this->settings->TABLES['LM_TASK_GROUPS'],
            $this->settings->TABLES['LM_GROUPS']
        ), [
            'task_id' => $taskId
        ]);
        $groups = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                $groups[] = [
                    'id' => (int) $result['id'],
                    'name' => $result['name'],
                    'style' => $result['style']
                ];
            }
        }
        $groupDetails = ['groups' => $groups];
        return $groupDetails;
    }

    /**
     * @param int $leadId
     * @return array
     */
    protected function fetchSearchDetails($leadId)
    {
        $searchDetails = sprintf(
            '%s?create_search=true&lead_id=%s',
            $this->settings->SETTINGS['URL_IDX_SEARCH'],
            $leadId
        );

        return ['search_url' => ($searchDetails ?: null)];
    }

    /**
     * @param int $taskId
     * @return array
     */
    protected function fetchTextDetails($taskId)
    {
        $textDetails = $this->db->fetch(sprintf(
            "SELECT "
            . " `message` "
            . " FROM %s "
            . " WHERE `task_id` = :task_id "
            . " LIMIT 1 "
            . ";",
            $this->settings->TABLES['LM_TASK_TEXTS']
        ), [
            'task_id' => $taskId
        ]);
        return $textDetails ?: ['message' => null];
    }

    /**
     * @return array
     */
    protected function fetchResults()
    {
        // Build Tasks Query
        $sql = sprintf(
            "SELECT "
            . " `ut`.`performer`, "
            . " `ut`.`performer_id`, "
            . " IF (`t`.`info` != '', `t`.`info`, null) AS `info`, "
            . " IF(`t`.`automated` = 'Y', 'true', 'false') AS `is_automated`, "
            . " IF(`u`.`first_name` != '', `u`.`first_name`, null) AS `lead_first_name`, "
            . " `u`.`id` AS `lead_id`, "
            . " IF(`u`.`image` != '', CONCAT('%sleads/', `u`.`image`), null) AS `lead_image`, "
            . " IF(`u`.`last_name` != '', `u`.`last_name`, null) AS `lead_last_name`, "
            . " IF (`ut`.`status` != '', `ut`.`status`, null) AS `status`, "
            . " `ut`.`task_id`, "
            . " IF (`ut`.`name` != '', `ut`.`name`, null) AS `title`, "
            . " `ut`.`timestamp_due` AS `timestamp_due`, "
            . " `ut`.`timestamp_expire` AS `timestamp_expire`, "
            . " `ut`.`timestamp_resolved` AS `timestamp_resolved`, "
            . " `ut`.`timestamp_scheduled` AS `timestamp_scheduled`, "
            . " IF(`ut`.`type` != '', `ut`.`type`, null) AS `type`, "
            . " `ut`.`id` AS `user_task_id` "
            . " FROM `%s` `ut` "
            . " JOIN `%s` `t` ON `t`.`id` = `ut`.`task_id` "
            . " LEFT JOIN `%s` `u` ON `ut`.`user_id` = `u`.`id` "
            . (!empty($this->sqlWhere)
                ? " WHERE " . implode(' AND ', $this->sqlWhere)
                : ""
            )
            . " ORDER BY `ut`.`timestamp_due` ASC "
            . (!empty($this->limit)
                ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                : ""
            ),
            $this->settings->URLS['UPLOADS'],
            $this->settings->TABLES['LM_USER_TASKS'],
            $this->settings->TABLES['LM_TASKS'],
            $this->settings->TABLES['LM_LEADS']
        );

        // Run Tasks Query
        $results = $this->db->fetchAll($sql, $this->sqlParams);

        if (!empty($results)) {
            foreach ($results as $k => $result) {
                switch ($result['type']) {
                    case 'Email':
                        $results[$k]['context_data'] = $this->fetchEmailDetails($result['task_id']);
                        break;
                    case 'Group':
                        $results[$k]['context_data'] = $this->fetchGroupDetails($result['task_id']);
                        break;
                    case 'Text':
                        $results[$k]['context_data'] = $this->fetchTextDetails($result['task_id']);
                        break;
                    case 'Search':
                        $results[$k]['context_data'] = $this->fetchSearchDetails($result['lead_id']);
                }
                ksort($results[$k]);
            }
        }

        return $results;
    }

    /**
     * @return int
     */
    protected function fetchResultsCount()
    {
        // Build Count Query
        $sql = sprintf(
            "SELECT "
            . " COUNT(`ut`.`id`) AS `total` "
            . " FROM `%s` `ut` "
            . " JOIN `%s` `t` ON `t`.`id` = `ut`.`task_id` "
            . " LEFT JOIN `%s` `u` ON `ut`.`user_id` = `u`.`id` "
            . (!empty($this->sqlWhere)
                ? " WHERE " . implode(' AND ', $this->sqlWhere)
                : ""
            ),
            $this->settings->TABLES['LM_USER_TASKS'],
            $this->settings->TABLES['LM_TASKS'],
            $this->settings->TABLES['LM_LEADS']
        );
        $count = $this->db->fetch($sql, $this->sqlParams);

        return $count['total'] ?: 0;
    }

    /**
     * Determine and set the request mode
     */
    protected function setMode()
    {
        $this->mode = null;
        if ($this->auth->isAgent()) {
            $this->mode = 'agent';
        } else if ($this->auth->isLender()) {
            $this->mode = 'lender';
        } else if ($this->auth->isAssociate()) {
            $this->mode = 'associate';
        }
    }

    /**
     * Determine and set the request mode ID (IE: agent ID, lender ID, etc)
     */
    protected function setModeId()
    {
        $checkTable = null;
        switch ($this->mode) {
            case 'agent':
                $checkTable = $this->settings->TABLES['LM_AGENTS'];
                break;
            case 'associate':
                $checkTable = $this->settings->TABLES['LM_ASSOCIATES'];
                break;
            case 'lender':
                $checkTable = $this->settings->TABLES['LM_LENDERS'];
                break;
        }

        if (!empty($checkTable)) {
            $userCheck = $this->db->fetch(sprintf(
                "SELECT "
                . " `u`.`id` "
                . " FROM `%s` `u` "
                . " LEFT JOIN `%s` `a` ON `a`.`id` = `u`.`auth` "
                . " WHERE `a`.`id` = :auth_id "
                . ";",
                $checkTable,
                $this->settings->TABLES['LM_AUTH']
            ), [
                'auth_id' => $this->auth->info('auth'),
            ]);
        }

        $this->modeId = (!empty($userCheck['id'])) ? $userCheck['id'] : null;
    }
}