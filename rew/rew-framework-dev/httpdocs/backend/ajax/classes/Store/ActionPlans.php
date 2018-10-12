<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Exception;

/**
 * Action Plans Store
 * @package REW\Api\Internal\Store
 */
class ActionPlans
{
    /**
     * @var SettingsInterface
     */
    protected $db;

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
    ){
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param array $leadIds
     * @throws PDOException
     * @return array
     */
    public function fetchAssignedActionPlans($leadIds = [])
    {
        return $this->db->fetchAll(sprintf(
            "SELECT "
            . " `a`.`id`, "
            . " `a`.`name`, "
            . " IF(`a`.`description` != '', `a`.`description`, null) AS `description`, "
            . " `a`.`day_adjust`, "
            . " `a`.`style`, "
            . " `a`.`timestamp_created`, "
            . " `a`.`timestamp_updated`, "
            . " `ua`.`users` "
            . " FROM `action_plans` `a` "
            . " JOIN (SELECT actionplan_id, group_concat(user_id separator ',') users FROM users_action_plans %s GROUP BY actionplan_id) `ua` on `ua`.`actionplan_id` = `a`.`id` "
            . ";"
            , (!empty($leadIds) ? "WHERE user_id IN (" . implode(',', $leadIds) . ")" : "")));
    }

    /**
     * @param array $leadIds
     * @throws PDOException
     * @returns array
     */
    public function getAssignedActionPlans($leadIds = [])
    {
        $result = [];

        $action_plans = $this->fetchAssignedActionPlans($leadIds);

        foreach ($action_plans as $action_plan) {
            $users = $action_plan["users"];
            unset($action_plan["users"]);
            foreach (explode(',', $users) as $user) {
                if (!is_array($result[$user])) {
                    $result[$user] = [];
                }
                array_push($result[$user], $action_plan);
            }
        }

        return $result;

    }
}
