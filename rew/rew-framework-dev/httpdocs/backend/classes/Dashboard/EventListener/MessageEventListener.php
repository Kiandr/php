<?php

namespace REW\Backend\Dashboard\EventListener;

use REW\Backend\Dashboard\AbstractEventListener;
use REW\Backend\Dashboard\EventFactory\MessageEventFactory;

/**
 * Class MessageEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class MessageEventListener extends AbstractEventListener
{

    /**
     * Message Mode
     * @var string
     */
    const MODE = 'message';

    /**
     * GetEvents
     * @param int|null $limit
     * @return array
     */
    public function getEventsIds($limit = null)
    {
        $messageEventIds = $this->queryEventIds(null, [], $limit);
        return $this->parseEventIds($messageEventIds, $this->getMode());
    }

    /**
     * Get Regirstion Event Id's since Timestamp
     * @param int $timestamp
     * @return array
     */
    public function getNewerEventIds($timestamp)
    {

        // Get Registration Event Timestamp Query
        $updateFunc = "(`%1\$s` > ?)";
        $updateString = ' AND ' . sprintf($updateFunc, 'um`.`timestamp');
        $updateTimestamp = date("Y-m-d H:i:s", intval($timestamp));
        $updateParams = [$updateTimestamp];

        // Get Event Id's From Query
        $messageEventIds = $this->queryEventIds($updateString, $updateParams, null);
        return $this->parseEventIds($messageEventIds, $this->getMode());
    }

    /**
     * Get Regirstion Event Id's before Timestamp
     * @param int $timestamp
     * @param int $id
     * @param int|null $limit
     * @return array
     */
    public function getOlderEventIds($timestamp, $id, $limit = null)
    {

        // Get Registration Event Timestamp Query
        $updateString = " AND (`um`.`timestamp` < ? OR (`um`.`timestamp` = ? AND `um`.`id` >= ?))";
        $updateTimestamp = date("Y-m-d H:i:s", intval($timestamp));
        $updateParams = [$updateTimestamp, $updateTimestamp, $id];

        // Get Event Id's From Query
        $messageEventIds = $this->queryEventIds($updateString, $updateParams, $limit);
        return $this->parseEventIds($messageEventIds, $this->getMode());
    }

    /**
     * Get Event Count
     * @return int
     */
    public function getEventsCount()
    {

        // Get Form Event Agent Query
        $usersQuery = $this->getUsersQuery('u');
        if (!empty($usersQuery)) {
            $agentQueryString = ' AND ' . $usersQuery;
        }

        // Get Form Event Users Site Query
        $userSiteQuery = $this->getSiteQuery('u');
        if (!empty($userSiteQuery)) {
            $agentSiteQueryString = ' AND ' . $userSiteQuery;
        }

        // Get Form Event Timestamp Query
        $datetimeQuery = $this->getDaysQuery();
        if (!empty($datetimeQuery)) {
            $datetimeQueryString = ' AND ' . sprintf($datetimeQuery, 'um`.`timestamp');
        }

        // Get Dismissed Query
        $dismissedQuery= $this->getDismissedQuery('um');
        if (!empty($dismissedQuery)) {
            $dismissedString = ' AND ' . $dismissedQuery;
        }

        // Get Form IDs
        $messageEventQuery = $this->db->prepare(
            'SELECT COUNT(`um`.`id`)'
            . ' FROM ' . LM_TABLE_MESSAGES . ' `um`'
            . ' JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `um`.`user_id`'
            . ' WHERE  `um`.`sent_from` = \'lead\''
            . ' AND `um`.`agent_read` = \'N\' AND `um`.`user_del` = \'N\''
            . (!empty($agentQueryString) ? $agentQueryString: '')
            . (!empty($agentSiteQueryString) ? $agentSiteQueryString : '')
            . (!empty($datetimeQueryString) ? $datetimeQueryString: '')
            . (!empty($dismissedString) ? $dismissedString : '')
        );

        $messageEventQuery->execute(array_merge($this->getUsersParams(), $this->getSiteParams(), $this->getDismissedParams()));
        return $messageEventQuery->fetchColumn();
    }

    /**
     * Get Event Mode
     * @return string
     */
    public function getMode()
    {
        return self::MODE;
    }

    /**
     * Get Event Factory
     * @return MessageEventFactory
     * @throws Exception
     */
    public function getFactory()
    {
        return $this->container->get(MessageEventFactory::class);
    }

    /**
     * Query Event Id's
     * @param string   $updateQuery
     * @param array    $updateParams
     * @param int|null $limit
     * @return array
     */
    protected function queryEventIds($updateQuery = '', $updateParams = [], $limit)
    {

        // Get Form Event Agent Query
        $usersQuery = $this->getUsersQuery('u');
        if (!empty($usersQuery)) {
            $agentQueryString = ' AND ' . $usersQuery;
        }

        // Get Form Event Users Site Query
        $userSiteQuery = $this->getSiteQuery('u');
        if (!empty($userSiteQuery)) {
            $agentSiteQueryString = ' AND ' . $userSiteQuery;
        }

        // Get Form Event Timestamp Query
        $datetimeQuery = $this->getDaysQuery();
        if (!empty($datetimeQuery)) {
            $datetimeQueryString = ' AND ' . sprintf($datetimeQuery, 'um`.`timestamp');
        }

        // Get Dismissed Query
        $dismissedQuery= $this->getDismissedQuery('um');
        if (!empty($dismissedQuery)) {
            $dismissedString = ' AND ' . $dismissedQuery;
        }

        // Get Limit Query
        $limitString = $this->getLimitQuery($limit);

        // Get Message IDs
        $messageEventQuery = $this->db->prepare(
            'SELECT `um`.`id`, `u`.`agent`, `um`.`timestamp`, `u`.`status`'
            . ' FROM ' . LM_TABLE_MESSAGES . ' `um`'
            . ' JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `um`.`user_id`'
            . ' WHERE  `um`.`sent_from` = \'lead\''
            . ' AND `um`.`agent_read` = \'N\' AND `um`.`user_del` = \'N\''
            . (!empty($agentQueryString) ? $agentQueryString: '')
            . (!empty($agentSiteQueryString) ? $agentSiteQueryString : '')
            . (!empty($datetimeQueryString) ? $datetimeQueryString: '')
            . $updateQuery
            . (!empty($dismissedString) ? $dismissedString : '')
            . ' ORDER BY `um`.`timestamp` DESC, `um`.`id` ASC'
            . $limitString
        );

        $messageEventQuery->execute(array_merge($this->getUsersParams(), $this->getSiteParams(), $updateParams, $this->getDismissedParams()));
        return $messageEventQuery->fetchAll();
    }
}
