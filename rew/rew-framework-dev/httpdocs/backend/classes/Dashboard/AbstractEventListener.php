<?php

namespace REW\Backend\Dashboard;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\Dashboard\EventId;
use REW\Backend\Dashboard\Interfaces\EventFactoryInterface;
use REW\Backend\Auth\LeadsAuth;

/**
 * Class AbstractEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
abstract class AbstractEventListener
{

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LeadsAuth
     */
    protected $leadsAuth;

    /**
     * Create Event Listener
     * @param DBInterface $db
     * @param AuthInterface $auth
     * @param ContainerInterface $container
     * @param LeadsAuth $leadsAuth
     */
    public function __construct(
        DBInterface $db,
        AuthInterface $auth,
        ContainerInterface $container,
        LeadsAuth $leadsAuth
    ) {

        $this->db = $db;
        $this->auth = $auth;
        $this->container = $container;
        $this->leadsAuth = $leadsAuth;
    }

    /**
     * Get Events
     * @param int|null $limit
     * @return array
     */
    abstract public function getEventsIds($limit);

    /**
     * GetEvents
     * @param int $timestamp
     * @return array
     */
    abstract public function getNewerEventIds($timestamp);

    /**
     * GetEvents
     * @param int $timestamp
     * @param int $id
     * @param int|null $limit
     * @return array
     */
    abstract public function getOlderEventIds($timestamp, $id, $limit);

    /**
     * Get Event Count
     * @return int
     */
    abstract public function getEventsCount();

    /**
     * Get Event Listener Mode
     * @return string
     */
    abstract public function getMode();

    /**
     * Get Event Factory
     * @return EventFactoryInterface
     * @throws Exception
     */
    abstract public function getFactory();

    /**
     * Get Days Query String
     * @return string|NULL
     */
    protected function getDaysQuery()
    {
        return (!empty($this->days)) ? "(`%1\$s` > DATE_SUB(now(), INTERVAL " . $this->days . " DAY))" : null;
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

        if ($this->leadsAuth->canAssignLeads($this->auth)) {
            // Get All Leads
            return '(' . $alias . '`agent` = ? OR ' . $alias . '`agent` = 1 OR (' . $alias . '`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND ' . $alias . '`status` = \'pending\'))';
        } else {
            // Get Agent Leads
            return $alias . '`agent` = ?';
        }
        return '';
    }

    /**
     * Get Users Site Query String
     * @param string|null alias
     * @return string
     */
    protected function getSiteQuery($alias = '')
    {

        // Get alias
        if (!empty($alias)) {
            $alias = '`'.$alias.'`.';
        }
        return '(' . $alias . '`site_type` != \'agent\' OR ' . $alias . '`site` = ? )';
    }

    /**
     * Get Users Params
     * @return array
     */
    protected function getUsersParams()
    {
        return [$this->auth->info('id')];
    }

    /**
     * Get Users Site Params
     * @return array
     */
    protected function getSiteParams()
    {
        return [$this->auth->info('id')];
    }

    /**
     * Get Query to skip dismissed listings
     * @param string|null $alias
     * @return string
     */
    protected function getDismissedQuery($alias = '')
    {

        // Get alias
        if (!empty($alias)) {
            $alias = '`' . $alias . '`.';
        }

        // Return Dismissed Events Query
        return $alias . '`id` NOT IN (SELECT `dd`.`event_id`'
            . ' FROM `dashboard_dismissed` `dd`'
            . ' WHERE `dd`.`agent` = ? AND `dd`.`event_mode` = ?'
            . ' GROUP BY `dd`.`event_id`)';
    }

    /**
     * Get Parameters to skip dismissed listings
     * @return array
     */
    protected function getDismissedParams()
    {

        // Return Dismissed Events Parameters
        return [$this->auth->info('id'), $this->getMode()];
    }

    /**
     * Get Limit Query
     * @param number $limit
     * @return string
     * @throws UnauthorizedPageException
     */
    protected function getLimitQuery($limit = null)
    {
        return (!empty($limit) && is_int($limit))
            ? (' LIMIT ' . ($limit + 1)) : '';
    }

    /**
     * Parse Event Id
     * @return EventId[]
     */
    protected function parseEventIds($events, $mode)
    {
        return array_map(function ($event) use ($mode) {
            return new EventId($event['id'], $mode, $event['status'], strtotime($event['timestamp']));
        }, $events);
    }
}
