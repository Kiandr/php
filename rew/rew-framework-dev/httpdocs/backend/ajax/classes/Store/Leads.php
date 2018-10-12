<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Backend_Lead;
use \Exception;

/**
 * Leads Store
 * @package REW\Api\Internal\Store
 */
class Leads
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
    ){
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param int $leadId
     * @throws PDOException
     * @return array
     */
    public function fetchLead($leadId)
    {
        return $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_LEADS']
        ), [
            'id' => $leadId
        ]);
    }

    /**
     * @param int $leadId
     * @throws Exception If lead not found
     * @throws PDOException
     * @returns Backend_Lead
     */
    public function getLead($leadId)
    {
        if (!$lead = $this->fetchLead($leadId)) {
            throw new Exception('Lead not found');
        }
        return new Backend_Lead($lead);
    }
}
