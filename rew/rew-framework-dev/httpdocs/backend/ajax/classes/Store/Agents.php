<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Exception;

/**
 * Agents Store
 * @package REW\Api\Internal\Store
 */
class Agents
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
     * @param array $agentIds
     * @throws PDOException
     * @return array
     */
    public function fetchAgents($agentIds = [])
    {
        return $this->db->fetchAll(sprintf(
                "SELECT "
                . " `a`.`id`, "
                . " `a`.`first_name`, "
                . " `a`.`last_name`, "
                . " IF(`a`.`image` != '', CONCAT('%sagents/', `a`.`image`), null) AS `image` "
                . " FROM `%s` `a` "
                . (!empty($agentIds) ? " WHERE `id` IN(%s) " : "")
                . ";",
                $this->settings->URLS['UPLOADS'],
                $this->settings->TABLES['LM_AGENTS'],
                implode(',', $agentIds)
            ));
    }

    /**
     * @param array $agentIds
     * @throws PDOException
     * @returns array
     */
    public function getAgents($agentIds = [])
    {
        $result = [];

        $agents = $this->fetchAgents($agentIds);

        foreach ($agents as $agent) {
            $result[$agent["id"]] = $agent;
        }

        return $result;

    }
}
