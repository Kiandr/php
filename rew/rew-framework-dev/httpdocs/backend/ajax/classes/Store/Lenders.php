<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Exception;

/**
 * Lender Store
 * @package REW\Api\Internal\Store
 */
class Lenders
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
    public function fetchLenders($lenderIds = [])
    {
        return $this->db->fetchAll(sprintf(
            "SELECT "
            . " `l`.`id`, "
            . " `l`.`first_name`, "
            . " `l`.`last_name`, "
            . ' IF(`lu`.`file` != \'\', CONCAT(\'%s\', `lu`.`file`), null) AS `image` '
            . " FROM `%s` `l` "
            . " LEFT JOIN `%s` `lu` ON `l`.`id` = `lu`.`row` AND `lu`.`type` = 'lender' "
            . (!empty($lenderIds) ? " WHERE `l`.`id` IN(%s) " : "")
            . ";",
            $this->settings->URLS['UPLOADS'],
            $this->settings->TABLES['LM_LENDERS'],
            $this->settings->TABLES['UPLOADS'],
            implode(',', $lenderIds)
        ));
    }

    /**
     * @param array $agentIds
     * @throws PDOException
     * @returns array
     */
    public function getLenders($lenderIds = [])
    {
        $result = [];

        $lenders = $this->fetchLenders($lenderIds);

        foreach ($lenders as $lender) {
            $result[$lender["id"]] = $lender;
        }

        return $result;

    }
}
