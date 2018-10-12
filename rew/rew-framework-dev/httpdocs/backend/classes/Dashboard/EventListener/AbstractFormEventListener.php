<?php

namespace REW\Backend\Dashboard\EventListener;

use REW\Backend\Dashboard\AbstractEventListener;

/**
 * Class RegistrationEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
abstract class AbstractFormEventListener extends AbstractEventListener
{

    /**
     * Get Form Events
     * @param int|null $limit
     * @return array
     */
    public function getEventsIds($limit = null)
    {
        $formEventIds = $this->queryEventIds(null, [], $limit);
        return $this->parseEventIds($formEventIds, $this->getMode());
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
        $updateString = ' AND ' . sprintf($updateFunc, 'uf`.`timestamp');
        $updateTimestamp = date("Y-m-d H:i:s", intval($timestamp));
        $updateParams = [$updateTimestamp];

        // Get Event Id's From Query
        $newFormEventIds = $this->queryEventIds($updateString, $updateParams, null);
        return $this->parseEventIds($newFormEventIds, $this->getMode());
    }

    /**
     * Get Registration Event Id's before Timestamp
     * @param int $timestamp
     * @param int $id
     * @param int|null $limit
     * @return array
     */
    public function getOlderEventIds($timestamp, $id, $limit = null)
    {

        // Get Registration Event Timestamp Query
        $updateString = " AND (`uf`.`timestamp` < ? OR (`uf`.`timestamp` = ? AND `uf`.`id` >= ?))";
        $updateTimestamp = date("Y-m-d H:i:s", intval($timestamp));
        $updateParams = [$updateTimestamp, $updateTimestamp, $id];

        // Get Event Id's From Query
        $oldFormEventIds = $this->queryEventIds($updateString, $updateParams, $limit);
        return $this->parseEventIds($oldFormEventIds, $this->getMode());
    }

    /**
     * Get Event Count
     * @return int
     */
    public function getEventsCount()
    {

        // Get Form Event Users Query
        $usersQuery = $this->getUsersQuery('u');
        if (!empty($usersQuery)) {
            $agentQueryString = ' AND ' . $usersQuery;
        }

        // Get Form Event Users Site Query
        $userSiteQuery = $this->getSiteQuery('u');
        if (!empty($userSiteQuery)) {
            $agentSiteQueryString = ' AND ' . $userSiteQuery;
        }

        // Get Form Event Maximum Days Back Query
        $datetimeQuery = $this->getDaysQuery();
        if (!empty($datetimeQuery)) {
            $datetimeQueryString = ' AND ' . sprintf($datetimeQuery, 'uf`.`timestamp');
        }

        // Get Form Event - Form Type Query
        $formTypeQuery = $this->getFormTypeQuery();
        if (!empty($formTypeQuery)) {
            $formTypeQueryString = ' AND ' . $formTypeQuery;
        }

        // Get Dismissed Query
        $dismissedQuery = $this->getDismissedQuery('uf');
        if (!empty($dismissedQuery)) {
            $dismissedString = ' AND ' . $dismissedQuery;
        }

        // Get Form IDs
        $formEventQuery = $this->db->prepare(
            'SELECT COUNT(`uf`.`id`)'
            . ' FROM ' . LM_TABLE_FORMS . ' `uf`'
            . ' JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `uf`.`user_id`'
            . ' WHERE `uf`.`reply` IS NULL'
            . (!empty($agentQueryString) ? $agentQueryString : '')
            . (!empty($agentSiteQueryString) ? $agentSiteQueryString : '')
            . (!empty($datetimeQueryString) ? $datetimeQueryString : '')
            . (!empty($formTypeQueryString) ? $formTypeQueryString : '')
            . (!empty($dismissedString) ? $dismissedString : '')
        );
        $formEventQuery->execute(array_merge($this->getUsersParams(), $this->getSiteParams(), $this->getFormTypeParams(), $this->getDismissedParams()));
        return $formEventQuery->fetchColumn();
    }

    /**
     * Query Form Event Id's
     * @param string $updateQuery
     * @param array  $updateParams
     * @return array
     */
    protected function queryEventIds($updateQuery = '', $updateParams = [], $limit)
    {

        // Get Form Event Users Query
        $usersQuery = $this->getUsersQuery('u');
        if (!empty($usersQuery)) {
            $agentQueryString = ' AND ' . $usersQuery;
        }

        // Get Form Event Users Site Query
        $userSiteQuery = $this->getSiteQuery('u');
        if (!empty($userSiteQuery)) {
            $agentSiteQueryString = ' AND ' . $userSiteQuery;
        }

        // Get Form Event Maximum Days Back Query
        $datetimeQuery = $this->getDaysQuery();
        if (!empty($datetimeQuery)) {
            $datetimeQueryString = ' AND ' . sprintf($datetimeQuery, 'uf`.`timestamp');
        }

        // Get Form Event - Form Type Query
        $formTypeQuery = $this->getFormTypeQuery();
        if (!empty($formTypeQuery)) {
            $formTypeQueryString = ' AND ' . $formTypeQuery;
        }

        // Get Dismissed Query
        $dismissedQuery = $this->getDismissedQuery('uf');
        if (!empty($dismissedQuery)) {
            $dismissedString = ' AND ' . $dismissedQuery;
        }

        // Get Limit Query
        $limitString = $this->getLimitQuery($limit);

        // Get Form IDs
        $formEventQuery = $this->db->prepare(
            'SELECT `uf`.`id`, `uf`.`timestamp`, `uf`.`form`, `u`.`status`'
            . ' FROM ' . LM_TABLE_FORMS . ' `uf`'
            . ' JOIN ' . LM_TABLE_LEADS . ' `u` ON `u`.`id` = `uf`.`user_id`'
            . ' WHERE `uf`.`reply` IS NULL'
            . (!empty($agentQueryString) ? $agentQueryString : '')
            . (!empty($agentSiteQueryString) ? $agentSiteQueryString : '')
            . (!empty($datetimeQueryString) ? $datetimeQueryString : '')
            . (!empty($formTypeQueryString) ? $formTypeQueryString : '')
            . $updateQuery
            . (!empty($dismissedString) ? $dismissedString : '')
            . ' ORDER BY `uf`.`timestamp` DESC, `uf`.`id` ASC'
            . $limitString
        );

        $formEventQuery->execute(array_merge($this->getUsersParams(), $this->getSiteParams(), $this->getFormTypeParams(), $updateParams, $this->getDismissedParams()));
        return $formEventQuery->fetchAll();
    }

    /**
     * Get the form types query
     * @return string
     */
    abstract protected function getFormTypeQuery();

    /**
     * Get the form types parameters
     * @return array
     */
    abstract protected function getFormTypeParams();
}
