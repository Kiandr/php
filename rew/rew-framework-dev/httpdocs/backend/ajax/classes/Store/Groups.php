<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Exception;

/**
 * Groups Store
 * @package REW\Api\Internal\Store
 */
class Groups
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
    public function fetchAssignedGroups($leadIds = [])
    {
        return $this->db->fetchAll(sprintf(
            "SELECT "
            . " `g`.`agent_id`, "
            . " `g`.`associate` AS `associate_id`, "
            . " IF(`g`.`description` != '', `g`.`description`, null) AS `description`, "
            . " `g`.`id`, "
            . " IF(`g`.`user` = 'true', 'true', 'false') AS `is_shared`, "
            . " `g`.`name`, "
            . " `g`.`style`, "
            . " `g`.`timestamp` AS `timestamp_created`, "
            . " `ug`.`users` "
            . " FROM `groups` `g` "
            . " JOIN (SELECT group_id, group_concat(user_id separator ',') users FROM users_groups %s GROUP BY group_id) `ug` on `ug`.`group_id` = `g`.`id` "
            . ";"
            , (!empty($leadIds) ? "WHERE user_id IN (" . implode(',', $leadIds) . ")" : "")));
    }

    /**
     * @param array $leadIds
     * @throws PDOException
     * @returns array
     */
    public function getAssignedGroups($leadIds = [])
    {
        $result = [];

        $groups = $this->fetchAssignedGroups($leadIds);

        foreach ($groups as $group) {
            $users = $group["users"];
            unset($group["users"]);
            foreach (explode(',', $users) as $user) {
                if (!is_array($result[$user])) {
                    $result[$user] = [];
                }
                array_push($result[$user], $group);
            }
        }

        return $result;

    }
}
