<?php

namespace REW\Backend\Dashboard\EventFactory;

use REW\Backend\Dashboard\AbstractEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use \Exception;

/**
 * Class RegistrationEventFactory
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class RegistrationEventFactory extends AbstractEventFactory
{

    /**
     * Registration Event String
     * @var string
     */
    const MODE = 'register';

    /**
     * Get Event Mode
     * @return string
     */
    public function getMode()
    {
        return self::MODE;
    }

    /**
     * Query Event
     * @param EventIdInterface $eventId
     * @return array|null
     */
    protected function queryEvent(EventIdInterface $eventId)
    {

        // Query Event Data
        try {
            $dataQuery = $this->db->prepare(
                'SELECT `l`.`id` AS \'user_id\', `l`.`first_name`, `l`.`last_name`, `l`.`status`, `l`.`email`, `l`.`phone_cell`, `l`.`image`, `l`.`agent`,'
                . ' CONCAT(`a`.`first_name`,\' \',`a`.`last_name`) AS \'agent_name\' '
                . ' FROM ' . LM_TABLE_LEADS . ' l'
                . ' LEFT JOIN ' . LM_TABLE_AGENTS . ' a ON `l`.`agent` = `a`.`id`'
                . ' WHERE `l`.`id` = :id'
            );
            $dataQuery->execute(['id' => $eventId->getId()]);
            return $dataQuery->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Parse Event
     * @param array $event
     * @param array $eventData
     * @return array
     */
    protected function parseEvent(array $event, array $eventData)
    {

        // Save Event Lead Data
        $event['data']['lead'] = $this->parseEventLead($eventData);
        return $event;
    }
}
