<?php

namespace REW\Backend\Dashboard\EventFactory;

use REW\Backend\Dashboard\AbstractEventFactory;
use REW\Backend\Dashboard\Interfaces\EventIdInterface;
use \Exception;

/**
 * Class MessageEventFactory
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class MessageEventFactory extends AbstractEventFactory
{

    /**
     * Message Mode
     * @var string
     */
    const MODE = 'message';

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
                'SELECT `u`.`id` AS \'user_id\', `u`.`first_name`, `u`.`last_name`, `u`.`status`,'
                . ' `u`.`email`, `u`.`phone_cell`, `um`.`agent_id` AS \'agent\', `u`.`image`,'
                . ' `a`.`first_name` AS \'agent_first_name\', `a`.`last_name` AS \'agent_last_name\','
                . ' `um`.`id` AS \'message_id\', `um`.`subject`, `um`.`message`'
                . ' FROM ' . LM_TABLE_MESSAGES . ' `um`'
                . ' JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `um`.`user_id`'
                . ' LEFT JOIN ' . LM_TABLE_AGENTS . ' `a` ON `a`.`id` = `um`.`agent_id`'
                . ' WHERE `um`.`id` = :id'
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

        // Save Event Lead & Lead Agent Data
        $eventData['agent_name'] = $eventData['agent_first_name'] . ' ' . $eventData['agent_last_name'];
        $event['data']['lead'] = $this->parseEventLead($eventData);

        // Save Event Message
        $event['data']['message'] = [
            'id' => $eventData['message_id'],
            'subject' => $eventData['subject'],
            'body' => $eventData['message']
        ];
        return $event;
    }
}
