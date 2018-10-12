<?php

namespace REW\Api\Internal\Controller\Route\Crm\User\Tasks\Task;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * User Task Get Controller
 * @package REW\Api\Internal\Controller
 */
class Get implements ControllerInterface
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
     * @var string
     */
    protected $mode;

    /**
     * @var int
     */
    protected $modeId;

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
     * @var array
     */
    protected $task;

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
        $this->routeParams = $routeParams;

        $this->checkModuleEnabled();

        $this->setMode();
        $this->setModeId();

        $this->checkModeValidity();

        $this->buildQueryFilters();

        $this->fetchResult();

        $body = $this->task;
        $response->setBody(json_encode($body));
    }

    /**
     * Build filters for querying task
     */
    protected function buildQueryFilters()
    {
        // Query Filters
        $this->sqlWhere = [];
        $this->sqlParams = [];

        // Mode-Specific Filters
        switch ($this->mode) {
            case 'agent':
                $this->sqlWhere[] = " ("
                    . "(`ut`.`status` = 'Pending' AND `u`.`agent` = :agent_id AND `t`.`performer` = 'Agent') "
                    . " OR (`ut`.`status` != 'Pending' AND `ut`.`performer` = 'Agent' AND `ut`.`performer_id` = :agent_id)"
                    . ") ";
                $this->sqlParams['agent_id'] = $this->modeId;
                break;
            case 'lender':
                $this->sqlWhere[] = " ("
                    . "(`ut`.`status` = 'Pending' AND `u`.`lender` = :lender_id AND `t`.`performer` = 'Lender') "
                    . " OR (`ut`.`performer` = 'Lender' AND `ut`.`performer_id` = :lender_id)"
                    . ") ";
                $this->sqlParams['lender_id'] = $this->modeId;
                break;
            case 'associate':
                $this->sqlWhere[] = " ("
                    . "(`ut`.`status` = 'Pending' AND `t`.`performer` = 'Associate') "
                    . " OR (`ut`.`performer` = 'Associate' AND `ut`.`performer_id` = :associate_id)"
                    . ") ";
                $this->sqlParams['associate_id'] = $this->modeId;
                break;
        }

        // Limit query to requested user task ID
        $this->sqlWhere[] = " `ut`.`id` = :user_task_id ";
        $this->sqlParams['user_task_id'] = $this->routeParams['userTaskId'];
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
     * @return void
     */
    protected function fetchEmailDetails()
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
            'task_id' => $this->task['task_id']
        ]);
        $this->task['context_extras'] = $emailDetails ?: ['message' => null, 'subject' => null];
    }

    /**
     * @return void
     */
    protected function fetchGroupDetails()
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
            'task_id' => $this->task['task_id']
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
        $this->task['context_extras'] = ['groups' => $groups];
    }

    /**
     * @return void
     */
    protected function fetchSearchDetails()
    {
        $searchDetails = sprintf(
            '%s?create_search=true&lead_id=%s',
            $this->settings->SETTINGS['URL_IDX_SEARCH'],
            $this->task['lead_id']
        );

        $this->task['context_extras'] = ['search_url' => ($searchDetails ?: null)];
    }

    /**
     * @return void
     */
    protected function fetchTextDetails()
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
            'task_id' => $this->task['task_id']
        ]);
        $this->task['context_extras'] = $textDetails ?: ['message' => null];
    }

    /**
     * @return void
     */
    protected function fetchResult()
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
            . " `t`.`id` AS `task_id`, "
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
            ),
            $this->settings->URLS['UPLOADS'],
            $this->settings->TABLES['LM_USER_TASKS'],
            $this->settings->TABLES['LM_TASKS'],
            $this->settings->TABLES['LM_LEADS']
        );

        // Run Tasks Query
        $this->task = $this->db->fetch($sql, $this->sqlParams);

        // Check if the task exists
        if (empty($this->task)) {
            throw new NotFoundException('Failed to locate the requested task.');
        }

        if (!empty($this->task)) {
            switch ($this->task['type']) {
                case 'Email':
                    $this->fetchEmailDetails();
                    break;
                case 'Group':
                    $this->fetchGroupDetails();
                    break;
                case 'Search':
                    $this->fetchSearchDetails();
                    break;
                case 'Text':
                    $this->fetchTextDetails();
                    break;
            }
        }

        ksort($this->task);
    }

    /**
     * Determine and set the request mode
     * @return void
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
     * @return void
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