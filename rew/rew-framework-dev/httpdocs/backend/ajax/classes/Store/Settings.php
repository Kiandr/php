<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * Agents Store
 * @package REW\Api\Internal\Store
 */
class Settings
{
    /**
     * @var DBInterface
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
    )
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * Gets active site partners
     * @throws PDOException
     * @returns array
     */
    public function getPartners()
    {
        $result = $this->db->fetch(sprintf(
            "SELECT "
            . " `a`.`partners` "
            . " FROM `%s` `a` "
            . "WHERE `id` = 1;",
            $this->settings->TABLES['LM_AGENTS']
        ));

        $result = json_decode($result['partners'], true);

        return $result;
    }
}