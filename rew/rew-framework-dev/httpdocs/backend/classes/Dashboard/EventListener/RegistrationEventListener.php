<?php

namespace REW\Backend\Dashboard\EventListener;

use REW\Backend\Dashboard\AbstractEventListener;
use REW\Backend\Dashboard\EventFactory\RegistrationEventFactory;

/**
 * Class RegistrationEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class RegistrationEventListener extends AbstractEventListener
{

    /**
     * Registration Event String
     * @var string
     */
    const MODE = 'register';

    /**
     * Get Event Ids
     * @param int|null $limit
     * @return array
     */
    public function getEventsIds($limit = null)
    {
        $registerEventIds = $this->queryEventIds(null, [], $limit);
        return $this->parseEventIds($registerEventIds, $this->getMode());
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
        $updateString = ' AND ('
            . implode(' OR ', [sprintf($updateFunc, 'timestamp'), sprintf($updateFunc, 'timestamp_assigned')])
            . ')';
        $updateTimestamp = date("Y-m-d H:i:s", intval($timestamp));
        $updateParams = [$updateTimestamp, $updateTimestamp];

        // Get Event Id's From Query
        $registerEventIds = $this->queryEventIds($updateString, $updateParams, null);
        return $this->parseEventIds($registerEventIds, $this->getMode());
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
        $updateString = ' AND ((`timestamp` >= `timestamp_assigned` AND (`timestamp` < ? OR (`timestamp` = ? AND `id` >= ?)))'
            . ' OR (`timestamp` < `timestamp_assigned` AND (`timestamp_assigned` < ? OR (`timestamp_assigned` = ? AND `id` >= ?))))';
        $updateTimestamp = date("Y-m-d H:i:s", intval($timestamp));
        $updateParams = [$updateTimestamp, $updateTimestamp, $id, $updateTimestamp, $updateTimestamp, $id];

        // Get Event Id's From Query
        $registerEventIds = $this->queryEventIds($updateString, $updateParams, $limit);
        return $this->parseEventIds($registerEventIds, $this->getMode());
    }

    /**
     * Get Event Count
     * @return int
     */

    public function getEventsCount()
    {

        // Get Registration Event Agent Query
        $usersQuery = $this->getUsersQuery();
        if (!empty($usersQuery)) {
            $agentQueryString = ' AND ' . $usersQuery;
        }

        // Get Form Event Users Site Query
        $userSiteQuery = $this->getSiteQuery('u');
        if (!empty($userSiteQuery)) {
            $agentSiteQueryString = ' AND ' . $userSiteQuery;
        }

        // Get Registration Event Timestamp Query
        $datetimeQuery = $this->getDaysQuery();
        if (!empty($datetimeQuery)) {
            $datetimeQueryString = ' AND ('
            . implode(' OR ', [sprintf($datetimeQuery, 'timestamp'), sprintf($datetimeQuery, 'timestamp_assigned')])
            . ')';
        }

        // Get Dismissed Query
        $dismissedQuery= $this->getDismissedQuery('u');
        if (!empty($dismissedQuery)) {
            $dismissedString = ' AND ' . $dismissedQuery;
        }

        // Get Registration IDs
        $registeredEventQuery = $this->db->prepare(
            'SELECT COUNT(`id`)'
            . ' FROM `' . LM_TABLE_LEADS . '` `u`'
            . ' WHERE  (`status` = \'pending\' OR `status` = \'unassigned\')'
            . (!empty($agentQueryString) ? $agentQueryString: '')
            . (!empty($agentSiteQueryString) ? $agentSiteQueryString : '')
            . (!empty($datetimeQueryString) ? $datetimeQueryString: '')
            . (!empty($dismissedString) ? $dismissedString : '')
        );

        $registeredEventQuery->execute(array_merge($this->getUsersParams(), $this->getSiteParams(), $this->getDismissedParams()));
        return $registeredEventQuery->fetchColumn();
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
     * @return RegistrationEventFactory
     */
    public function getFactory()
    {
        return $this->container->get(RegistrationEventFactory::class);
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

        // Get Registration Event Agent Query
        $usersQuery = $this->getUsersQuery();
        if (!empty($usersQuery)) {
            $agentQueryString = ' AND ' . $usersQuery;
        }

        // Get Form Event Users Site Query
        $userSiteQuery = $this->getSiteQuery('u');
        if (!empty($userSiteQuery)) {
            $agentSiteQueryString = ' AND ' . $userSiteQuery;
        }

        // Get Registration Event Timestamp Query
        $datetimeQuery = $this->getDaysQuery();
        if (!empty($datetimeQuery)) {
            $datetimeQueryString = ' AND ('
                . implode(' OR ', [sprintf($datetimeQuery, 'timestamp'), sprintf($datetimeQuery, 'timestamp_assigned')])
                . ')';
        }

        // Get Dismissed Query
        $dismissedQuery= $this->getDismissedQuery('u');
        if (!empty($dismissedQuery)) {
            $dismissedString = ' AND ' . $dismissedQuery;
        }

        // Get Limit Query
        $limitString = $this->getLimitQuery($limit);

        // Get Registration IDs
        $registeredEventQuery = $this->db->prepare(
            'SELECT `id`, (CASE WHEN `timestamp` > `timestamp_assigned` THEN `timestamp` ELSE `timestamp_assigned` END) AS \'timestamp\', `status`'
            . ' FROM `' . LM_TABLE_LEADS . '` `u`'
            . ' WHERE  (`status` = \'pending\' OR `status` = \'unassigned\')'
            . (!empty($agentQueryString) ? $agentQueryString: '')
            . (!empty($agentSiteQueryString) ? $agentSiteQueryString : '')
            . (!empty($datetimeQueryString) ? $datetimeQueryString: '')
            . $updateQuery
            . (!empty($dismissedString) ? $dismissedString : '')
            . ' ORDER BY `timestamp` DESC, `id` ASC'
            . $limitString
        );

        $registeredEventQuery->execute(array_merge($this->getUsersParams(), $this->getSiteParams(), $updateParams, $this->getDismissedParams()));
        return $registeredEventQuery->fetchAll();
    }

    /**
     * Get Users Query String
     * @param string|null alias
     * @return string
     */
    protected function getUsersQuery($alias = '')
    {

        // Get alias
        if (!empty($alias)) {
            $alias = '`'.$alias.'`.';
        }

        if ($this->leadsAuth->canManageLeads($this->auth) && $this->leadsAuth->canAssignLeads($this->auth)) {
            // Get All Leads
            return '(' . $alias . '`agent` = ? OR ' . $alias . '`agent` = 1 OR (' . $alias . '`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND ' . $alias . '`status` = \'pending\'))';
        } else {
            // Get Agent Leads
            return $alias . '`agent` = ?';
        }
        return '';
    }
}
