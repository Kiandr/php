<?php

namespace REW\Api\Internal\Controller\Route\Crm\ActionPlans;

use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * ActionPlan Collection Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{

    /**
     * @var int
     */
    protected $countPlansTotal;

    /**
     * @var int
     */
    protected $countTasksTotal;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var array
     */
    protected $plans;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        DBInterface $db,
        SettingsInterface $settings
    ) {
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

        // Fetch Action Plans
        $this->plans = $this->fetchPlans();

        // Fetch Action Plan Tasks
        $this->fetchPlansTasks();

        $body = [
            'action_plans' => $this->plans,
            'count_total_action_plans' => ($this->countPlansTotal ?: 0),
            'count_total_tasks' => ($this->countTasksTotal ?: 0)
        ];
        $response->setBody(json_encode($body));
    }

    /**
     * @return array
     */
    protected function fetchPlans()
    {
        $plans = $this->db->fetchAll(sprintf(
            "SELECT "
            . " `day_adjust`, "
            . " `description`, "
            . " `id`, "
            . " `name`, "
            . " `style`, "
            . " `timestamp_created`, "
            . " `timestamp_updated` "
            . " FROM %s ",
            $this->settings->TABLES['LM_ACTION_PLANS']
        ));

        $this->countPlansTotal = count($plans);

        return $plans;
    }

    protected function fetchPlansTasks()
    {
        if (!empty($this->plans)) {
            foreach ($this->plans as $k => $plan) {
                $tasks = $this->db->fetchAll(sprintf(
                    "SELECT "
                    . " IF(`t`.`automated` = 'Y', 'true', 'false') AS `automated`, "
                    . " `t`.`offset` AS `due_after_days`, "
                    . " `t`.`time` AS `due_time`, "
                    . " `t`.`expire` AS `expire_after_days`, "
                    . " `t`.`id`, "
                    . " IF(`t`.`info` != '', `t`.`info`, null) AS `info`, "
                    . " `t`.`name`, "
                    . " `t`.`parent_id` AS `parent_task_id`, "
                    . " `t`.`performer`, "
                    . " `t`.`type`, "
                    . " `t`.`timestamp_created`, "
                    . " `t`.`timestamp_updated` "
                    . " FROM %s `t` "
                    . " WHERE `actionplan_id` = :plan_id ",
                    $this->settings->TABLES['LM_TASKS']
                ), [
                    'plan_id' => $plan['id']
                ]);

                $this->plans[$k]['count_tasks'] = count($tasks);

                if ($this->get['hide_tasks'] !== 'true') {
                    foreach ($tasks as $task) {
                        $this->plans[$k]['tasks'][] = $task;
                    }
                }

                ksort($this->plans[$k]);

                $this->countTasksTotal += count($tasks);
            }
        }
    }
}