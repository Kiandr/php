<?php

namespace REW\Api\Internal\Controller\Route\Crm\User\Tasks\Task;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Task;

/**
 * User Task Update Controller
 * @package REW\Api\Internal\Controller
 */
class Update implements ControllerInterface
{
    const SNOOZE_UNITS = ['minutes', 'hours', 'days', 'weeks'];

    /**
     * @var string
     */
    protected $action;

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
     * @var array
     */
    protected $routeParams;

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
     * @var Backend_Task
     */
    protected $task;

    /**
     * @var int
     */
    protected $userId;

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
     * @return array
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $body = json_decode($request->getBody());
        $this->put = (!empty($body) ? (array) $body : []);
        $this->routeParams = $routeParams;

        $this->checkModuleEnabled();

        $this->translateActionRequest();

        $this->checkActionValidity();

        $this->setMode();
        $this->setModeId();

        $this->checkModeValidity();

        $this->buildQueryFilters();

        $this->task = $this->fetchTask();

        $this->performRequestAction();
    }

    /**
     * @throws BadRequestException
     * @throws ServerSuccessException
     */
    protected function performRequestAction()
    {
        // Define the Task Performer (IE Current User)
        $performer = [
            'id'   => $this->auth->info('id'),
            'type' => $this->auth->getType(),
        ];

        // Handle the requested action
        switch ($this->action) {
            case 'complete':
                $this->task->resolve($this->userId, $performer, 'Completed', $this->put['note']);
                break;
            case 'note':
                if (empty($this->put['note'])) {
                    throw new BadRequestException('A note is required for this action request.');
                }
                $this->task->addNote($this->userId, $this->put['note']);
                break;
            case 'snooze':
                $duration = intval($this->put['duration']);
                if ($duration <= 0) {
                    throw new BadRequestException('A snooze duration is required.');
                }
                if (!in_array($this->put['unit'], self::SNOOZE_UNITS)) {
                    throw new BadRequestException('A snooze duration is required.');
                }

                $this->task->snooze($this->userId, $performer, $duration, $this->put['note'], $this->put['unit']);

                break;
            case 'dismiss':
                $this->task->resolve(
                    $this->userId,
                    $performer,
                    'Dismissed',
                    $this->put['note'],
                    (!empty($this->put['dismiss_followup_tasks']))
                );
                break;
        }

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The task has been updated successfully.');
    }

    /**
     * Build filters to query/check for existing task
     */
    protected function buildQueryFilters()
    {
        // Query Filters
        $this->sqlWhere = [];
        $this->sqlParams = [];

        // Mode-Specific Filters
        switch ($this->mode) {
            case 'agent':
                $this->sqlWhere[] = " `u`.`agent` = :agent_id AND `t`.`performer` = 'Agent' ";
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

        // Limit query to requested user task ID
        $this->sqlWhere[] = " `ut`.`id` = :user_task_id ";
        $this->sqlParams['user_task_id'] = $this->routeParams['userTaskId'];
    }

    /**
     * @throws BadRequestException
     */
    protected function checkActionValidity()
    {
        // Validate Requested Action
        if (empty($this->action) || !in_array($this->action, ['complete','note','snooze','dismiss'])) {
            throw new BadRequestException('Invalid action request.');
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
     * @return array
     * @throws BadRequestException
     */
    protected function fetchTask()
    {
        // Check if the requested task exists + is permitted to this user
        $userTask = $this->db->fetch(sprintf(
            "SELECT "
            . " `t`.`id` AS `task_id`, "
            . " `u`.`id` AS `user_id`, "
            . " `ut`.`status` AS `status` "
            . " FROM `%s` `ut` "
            . " JOIN `%s` `t` ON `ut`.`task_id` = `t`.`id` "
            . " LEFT JOIN `%s` `u` ON `ut`.`user_id` = `u`.`id` "
            . (!empty($this->sqlWhere)
                ? " WHERE " . implode(' AND ', $this->sqlWhere)
                : ""
            )
            . ";",
            $this->settings->TABLES['LM_USER_TASKS'],
            $this->settings->TABLES['LM_TASKS'],
            $this->settings->TABLES['LM_LEADS']
        ),
            $this->sqlParams
        );
        if (empty($userTask)) {
            throw new BadRequestException('Failed to load the requested task.');
        }
        if ($userTask['status'] != 'Pending') {
            throw new BadRequestException('The requested task has already been resolved.');
        }

        $this->userId = $userTask['user_id'];

        if (empty($this->userId)) {
            throw new BadRequestException('Failed to load the task\'s lead information.');
        }

        // Load the Task Object
        $task = Backend_Task::load($userTask['task_id']);

        return $task ?: [];
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

    /**
     * Bridge the action terminology between front-end design + existing framework
     */
    protected function translateActionRequest()
    {
        $this->action = null;
        if (!empty($this->put['action'])) {
            switch ($this->put['action']) {
                case 'ok':
                case 'complete':
                    $this->action = 'complete';
                    break;
                case 'snooze':
                    $this->action = 'snooze';
                    break;
                case 'dismiss':
                case 'skip':
                    $this->action = 'dismiss';
                    break;
                case 'note':
                    $this->action = 'note';
                    break;
            }
        }
    }
}