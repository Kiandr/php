<?php

namespace REW\Api\Internal\Controller\Route\Crm\User\Inbox;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * User Inbox Item Collection Controller
 * @package REW\Api\Internal\Controller
 *
 * @NOTE - the method used to query/combine/trim the results is only necessary because of the way events are currently stored.
 * @SUGGESTED: Combine + track events into a single DB table
 * @CHALLENGE: Events are generated in various ways, all would need to be tracked
 * @CHALLENGE: DB table will need to be up to date when events are resolved organically
 */
class Collection implements ControllerInterface
{
    /**
     * @var int
     */
    const DEFAULT_LIMIT = 10;

    /**
     * @var array
     */
    const MODES = ['register', 'message', 'inquiry', 'showing', 'selling'];

    /**
     * @var array
     */
    const FORM_TYPES = [
        'selling' => ['Seller Form','CMA Form','Radio Seller Form','Guaranteed Sold Form'],
        'showing' => ['Property Showing','Quick Showing']
    ];

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        // Store Slim Vars
        $this->get = $request->get();

        $this->checkRequestValidity();

        // Establish query limit
        $this->limit = (!empty($this->get['limit'])) ? intval($this->get['limit']) : self::DEFAULT_LIMIT;

        // Get the requested events
        $events = $this->getResponseByMode();

        // Return the response
        $body = [
            'agent_id' => $this->auth->info('id'),
            'count'  => ($events['count'] ?: 0),
            'items' => ($events['events'] ?: []),
            'limit'  => $this->limit
        ];
        $response->setBody(json_encode($body));
    }

    /**
     * Build an array key for sorting multiple arrays by timestamps
     *
     * @param string $timestamp
     * @param array $event
     *
     * @return string
     */
    protected function buildKey($timestamp, $event)
    {
        return $timestamp . '--' . hash('sha256', json_encode($event));
    }

    /**
     * Build filters for form mode requests
     *
     * @return array
     */
    protected function buildFormEventFilters()
    {
        $sql = "";
        $params = [];
        // Filter by request mode if applicable
        switch ($this->get['mode']) {
            case 'inquiry':
                $sql = " AND `f`.`form` NOT IN('" . implode("','", self::FORM_TYPES['selling']) . "','" . implode("','", self::FORM_TYPES['showing']) . "') "
                    . " AND `f`.`data` NOT LIKE :form_type "
                    . " AND `f`.`data` NOT LIKE :form_type_two ";
                $params['form_type'] = '%s:12:\"inquire_type\";s:16:\"Property Showing\";%';
                $params['form_type_two'] = '%s:12:\"inquire_type\";s:7:\"Selling\";%';
                break;
            case 'showing':
                $sql = " AND (`f`.`form` IN ('" . implode("','", self::FORM_TYPES['showing']) . "') OR ( "
                    . " `f`.`form` = 'IDX Inquiry' "
                    . " AND `f`.`data` LIKE :form_type "
                    . " )) ";
                $params['form_type'] = '%s:12:\"inquire_type\";s:16:\"Property Showing\";%';
                break;
            case 'selling':
                $sql = " AND (`f`.`form` IN ('" . implode("','", self::FORM_TYPES['selling']) . "') OR ( "
                    . " `f`.`form` = 'IDX Inquiry' "
                    . " AND `f`.`data` LIKE :form_type "
                    . " )) ";
                $params['form_type'] = '%s:12:\"inquire_type\";s:7:\"Selling\";%';
                break;
        }
        return [
            'sql' => $sql,
            'params' => $params
        ];
    }

    /**
     * Check permissions and mode
     * @throws BadRequestException
     * @throws InsufficientPermissionsException
     */
    protected function checkRequestValidity()
    {
        // Limit inbox to agents
        if (!$this->auth->isAgent()) {
            throw new InsufficientPermissionsException('Invalid user type.');
        }

        // Make sure requested mode is valid
        if (!empty($this->get['mode']) && !in_array($this->get['mode'], self::MODES)) {
            throw new BadRequestException('Invalid mode.');
        }
    }

    /**
     * Fetch the total count of form event types
     *
     * @return int
     */
    protected function fetchFormEventCount()
    {
        // Build form mode filters
        $extras = $this->buildFormEventFilters();

        $sql = sprintf(
            "SELECT "
            . " COUNT(`f`.`id`) AS `total` "
            . " FROM `%s` `u` "
            . " LEFT JOIN `%s` `a` ON `u`.`agent` = `a`.`id` "
            . " LEFT JOIN `%s` `f` ON `u`.`id` = `f`.`user_id` "
            . " LEFT JOIN `dashboard_dismissed` `d` ON `f`.`id` = `d`.`event_id` AND `d`.`event_mode` IN('inquiry','selling','showing') AND `d`.`agent` = :auth_agent "
            . " WHERE `d`.`id` IS NULL "
            . (!empty($extras['sql']) ? $extras['sql'] : "")
            . " AND ( "
                . " `u`.`agent` = :auth_agent "
                . ($this->auth->isSuperAdmin() ? " OR `u`.`status` != 'accepted' " : "")
            . ") "
            . " AND `f`.`reply` IS NULL "
            . ";",
            $this->settings->TABLES['LM_LEADS'],
            $this->settings->TABLES['LM_AGENTS'],
            $this->settings->TABLES['LM_USER_FORMS']
        );

        $params = array_merge($extras['params'], [
            'auth_agent' => $this->auth->info('id')
        ]);

        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            $count = $query->fetch();
        }

        return !empty($count) ? intval($count['total']) : 0;
    }

    /**
     * Fetch all message events
     *
     * @return array
     */
    protected function fetchFormEvents()
    {
        // Build form mode filters
        $extras = $this->buildFormEventFilters();

        $sql = sprintf(
            "SELECT "
            . " `f`.`data` AS `additional_info`, "
            . " `f`.`id` AS `item_id`, "
            . " IF(`f`.`form` IN ('" . implode("','", self::FORM_TYPES['selling']) . "'), 'selling', "
                . " IF(`f`.`form` IN ('" . implode("','", self::FORM_TYPES['showing']) . "'), 'showing', "
                    . " 'inquiry' "
                . " ) "
            . " ) AS `item_mode`, "
            . " `f`.`timestamp` AS `item_timestamp`, "
            . " `a`.`first_name` AS `lead_agent_first_name`, "
            . " `u`.`agent` AS `lead_agent_id`, "
            . " `a`.`last_name` AS `lead_agent_last_name`, "
            . " `u`.`first_name` AS `lead_first_name`, "
            . " `u`.`id` AS `lead_id`, "
            . " `u`.`last_name` AS `lead_last_name` "
            . " FROM `%s` `u` "
            . " LEFT JOIN `%s` `a` ON `u`.`agent` = `a`.`id` "
            . " LEFT JOIN `%s` `f` ON `u`.`id` = `f`.`user_id` "
            . " LEFT JOIN `dashboard_dismissed` `d` ON `f`.`id` = `d`.`event_id` AND `d`.`event_mode` IN('inquiry','selling','showing') AND `d`.`agent` = :auth_agent "
            . " WHERE `d`.`id` IS NULL "
            . (!empty($extras['sql']) ? $extras['sql'] : "")
            . " AND ( "
                . " `u`.`agent` = :auth_agent "
                . ($this->auth->isSuperAdmin() ? " OR `u`.`status` != 'accepted' " : "")
            . ") "
            . " AND `f`.`reply` IS NULL "
            . " ORDER BY `f`.`timestamp` DESC "
            . sprintf(" LIMIT %d ", $this->limit)
            . ";",
            $this->settings->TABLES['LM_LEADS'],
            $this->settings->TABLES['LM_AGENTS'],
            $this->settings->TABLES['LM_USER_FORMS']
        );

        $params = array_merge($extras['params'], [
            'auth_agent' => $this->auth->info('id')
        ]);

        $events = [];
        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            while ($event = $query->fetch()) {
                if ($event['additional_info'] = unserialize($event['additional_info'])) {
                    ksort($event['additional_info']);
                }
                $events[$this->buildKey($event['item_timestamp'], $event)] = $event;
            }
        }
        return $events ?: [];
    }

    /**
     * Fetch the total count of message event types
     *
     * @return int
     */
    protected function fetchMessageEventCount()
    {
        $sql = sprintf(
            "SELECT "
            . " COUNT(`m`.`id`) AS `total` "
            . " FROM `%s` `u` "
            . " LEFT JOIN `%s` `a` ON `u`.`agent` = `a`.`id` "
            . " LEFT JOIN `%s` `m` ON `u`.`id` = `m`.`user_id` "
            . " LEFT JOIN `dashboard_dismissed` `d` ON `m`.`id` = `d`.`event_id` AND `d`.`event_mode` = 'message' AND `d`.`agent` = :auth_agent "
            . " WHERE `d`.`id` IS NULL "
            . " AND `m`.`agent_id` = :auth_agent "
            . " AND ( "
                . " `u`.`agent` = :auth_agent "
                . " OR `u`.`status` != 'accepted' "
            . " ) "
            . " AND `m`.`sent_from` = 'lead' "
            . " AND `m`.`agent_read` = 'N' "
            . " AND `m`.`reply` = 'N' "
            . ";",
            $this->settings->TABLES['LM_LEADS'],
            $this->settings->TABLES['LM_AGENTS'],
            $this->settings->TABLES['LM_USER_MESSAGES']
        );

        $params = [
            'auth_agent' => $this->auth->info('id')
        ];

        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            $count = $query->fetch();
        }

        return !empty($count) ? intval($count['total']) : 0;
    }

    /**
     * Fetch all message events
     *
     * @return array
     */
    protected function fetchMessageEvents()
    {
        $sql = sprintf(
            "SELECT "
            . " `m`.`id` AS `item_id`, "
            . " 'message' AS `item_mode`, "
            . " `m`.`timestamp` AS `item_timestamp`, "
            . " `a`.`first_name` AS `lead_agent_first_name`, "
            . " `u`.`agent` AS `lead_agent_id`, "
            . " `a`.`last_name` AS `lead_agent_last_name`, "
            . " `u`.`first_name` AS `lead_first_name`, "
            . " `u`.`id` AS `lead_id`, "
            . " `u`.`last_name` AS `lead_last_name`, "
            . " IF(`m`.`message` != '', `m`.`message`, null) AS `message`, "
            . " IF(`m`.`subject` != '', `m`.`subject`, null) AS `subject` "
            . " FROM `%s` `u` "
            . " LEFT JOIN `%s` `a` ON `u`.`agent` = `a`.`id` "
            . " LEFT JOIN `%s` `m` ON `u`.`id` = `m`.`user_id` "
            . " LEFT JOIN `dashboard_dismissed` `d` ON `m`.`id` = `d`.`event_id` AND `d`.`event_mode` = 'message' AND `d`.`agent` = :auth_agent "
            . " WHERE `d`.`id` IS NULL "
            . " AND `m`.`agent_id` = :auth_agent "
            . " AND ( "
                . " `u`.`agent` = :auth_agent "
                . " OR `u`.`status` != 'accepted' "
            . " ) "
            . " AND `m`.`sent_from` = 'lead' "
            . " AND `m`.`agent_read` = 'N' "
            . " AND `m`.`reply` = 'N' "
            . " ORDER BY `m`.`timestamp` DESC "
            . sprintf(" LIMIT %d ", $this->limit)
            . ";",
            $this->settings->TABLES['LM_LEADS'],
            $this->settings->TABLES['LM_AGENTS'],
            $this->settings->TABLES['LM_USER_MESSAGES']
        );

        $params = [
            'auth_agent' => $this->auth->info('id')
        ];

        $events = [];
        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            while ($event = $query->fetch()) {
                $events[$this->buildKey($event['item_timestamp'], $event)] = $event;
            }
        }
        return $events ?: [];
    }

    /**
     * Fetch the total count of registration event types
     *
     * @return int
     */
    protected function fetchRegisterEventCount()
    {
        $sql = sprintf(
            "SELECT "
            . " COUNT(`u`.`id`) AS `total` "
            . " FROM `%s` `u` "
            . " LEFT JOIN `dashboard_dismissed` `d` ON `u`.`id` = `d`.`event_id` AND `d`.`event_mode` = 'register' AND `d`.`agent` = :auth_agent "
            . " WHERE `d`.`id` IS NULL "
            . " AND ( "
                . " ( " . ((!$this->auth->isSuperAdmin()) ? " `u`.`agent` = :auth_agent AND " : "") . " `u`.`status` IN('pending', 'unassigned')) "
                . " OR (`u`.`agent` != 1 AND `u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending') "
            . " ) "
            . ";",
            $this->settings->TABLES['LM_LEADS']
        );

        $params = [
            'auth_agent' => $this->auth->info('id')
        ];

        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            $count = $query->fetch();
        }

        return !empty($count) ? intval($count['total']) : 0;
    }

    /**
     * Fetch all registration events
     *
     * @return array
     */
    protected function fetchRegisterEvents()
    {
        $sql = sprintf(
            "SELECT "
            . " `u`.`id` AS `item_id`, "
            . " 'register' AS `item_mode`, "
            . " IF (`u`.`timestamp_assigned` != '0000-00-00 00:00:00', `u`.`timestamp_assigned`, `u`.`timestamp`) AS `item_timestamp`, "
            . " `a`.`first_name` AS `lead_agent_first_name`, "
            . " `u`.`agent` AS `lead_agent_id`, "
            . " `a`.`last_name` AS `lead_agent_last_name`, "
            . " `u`.`first_name` AS `lead_first_name`, "
            . " `u`.`id` AS `lead_id`, "
            . " `u`.`last_name` AS `lead_last_name`, "
            . " IF(`u`.`phone` != '', `u`.`phone`, null) AS `lead_phone`, "
            . " IF(`u`.`phone_cell` != '', `u`.`phone_cell`, null) AS `lead_phone_cell`, "
            . " IF(`u`.`phone_fax` != '', `u`.`phone_fax`, null) AS `lead_phone_fax`, "
            . " IF(`u`.`phone_work` != '', `u`.`phone_work`, null) AS `lead_phone_work`, "
            . " `u`.`score` AS `lead_score`, "
            . " `u`.`value` AS `lead_value`, "
            . " CONCAT('$', FORMAT(`u`.`value`, 0)) AS `lead_value_formatted` "
            . " FROM `%s` `u` "
            . " LEFT JOIN `%s` `a` ON `u`.`agent` = `a`.`id` "
            . " LEFT JOIN `dashboard_dismissed` `d` ON `u`.`id` = `d`.`event_id` AND `d`.`event_mode` = 'register' AND `d`.`agent` = :auth_agent "
            . " WHERE `d`.`id` IS NULL "
            . " AND ( "
                . " (" . ((!$this->auth->isSuperAdmin()) ? " `u`.`agent` = :auth_agent AND " : "") . " `u`.`status` IN('pending', 'unassigned')) "
                . " OR (`u`.`agent` != 1 AND `u`.`timestamp_assigned` < DATE_SUB(NOW(), INTERVAL 1 DAY) AND `u`.`status` = 'pending') "
            . " ) "
            . " ORDER BY `u`.`timestamp_assigned` DESC, `u`.`timestamp` DESC "
            . sprintf(" LIMIT %d ", $this->limit)
            . ";",
            $this->settings->TABLES['LM_LEADS'],
            $this->settings->TABLES['LM_AGENTS']
        );

        $params = [
            'auth_agent' => $this->auth->info('id')
        ];

        $events = [];
        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            while ($event = $query->fetch()) {
                $events[$this->buildKey($event['item_timestamp'], $event)] = $event;
            }
        }
        return $events ?: [];
    }

    /**
     * Fetch the total count of all event types
     *
     * @return int
     */
    protected function fetchAllEventCount()
    {
        $total = $this->fetchFormEventCount() + $this->fetchMessageEventCount() + $this->fetchRegisterEventCount();
        return !empty($total) ? intval($total) : 0;
    }

    /**
     * Fetch all events of all types
     *
     * @return array
     */
    protected function fetchAllEvents()
    {
        $events = array_merge(
            $this->fetchFormEvents(),
            $this->fetchMessageEvents(),
            $this->fetchRegisterEvents()
        );

        return $events ?: [];
    }

    /**
     * Get events and event count - determined by requested mode
     *
     * @return array
     */
    protected function getResponseByMode()
    {
        $count = 0;
        $events = [];

        switch ($this->get['mode']) {
            case 'register':
                $count = $this->fetchRegisterEventCount();
                $events = $this->fetchRegisterEvents();
                break;
            case 'message':
                $count = $this->fetchMessageEventCount();
                $events = $this->fetchMessageEvents();
                break;
            case 'inquiry':
            case 'showing':
            case 'selling':
                $count = $this->fetchFormEventCount();
                $events = $this->fetchFormEvents();
                break;
            default:
                $count = $this->fetchAllEventCount();
                $events = $this->fetchAllEvents();
                break;
        }

        // Sort the combined array by timestamp, then strip the array keys
        if (!empty($events)) {
            krsort($events);
            $events = array_slice(array_values($events), 0, $this->limit);
        }

        return [
            'count' => $count,
            'events' => $events
        ];
    }
}
