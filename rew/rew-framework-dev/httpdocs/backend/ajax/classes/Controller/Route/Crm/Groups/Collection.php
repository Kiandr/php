<?php

namespace REW\Api\Internal\Controller\Route\Crm\Groups;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Collection Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{
    /**
     * Permitted request types
     */
    const ALLOWED_TYPES = ['agent', 'lead', 'campaign'];

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
     * @var string
     */
    protected $sqlJoin;

    /**
     * @var array
     */
    protected $sqlParams;

    /**
     * @var string
     */
    protected $sqlSelect;

    /**
     * @var string
     */
    protected $sqlQuery;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db
    ){
        $this->auth = $auth;
        $this->db = $db;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();

        $this->limit = (!empty($this->get['limit'])) ? intval($this->get['limit']) : null;
        $this->page = (!empty($this->get['page'])) ? intval($this->get['page']) : null;
        $this->offset = ($this->page > 1 && $this->limit >= 1) ? ($this->page - 1) * $this->limit : null;

        $this->checkPermissions();
        $this->checkRequestType();

        $body = $this->getResults();
        $response->setBody(json_encode($body));
    }

    /**
     * Build the group count query and parameters
     */
    protected function buildCountQuery()
    {
        $this->buildQuery(true);
    }

    /**
     * Build the group select query and parameters
     *
     * @param bool $countOnly
     */
    protected function buildQuery($countOnly = false)
    {
        // Establish pieces
        if (!empty($countOnly)) {
            $this->sqlSelect = [
                "COUNT(`g`.`id`) AS `total`"
            ];
        } else {
            $this->sqlSelect = [
                "CONCAT(`agents`.`first_name`, ' ', `agents`.`last_name`) AS `agent`",
                "CONCAT(`associates`.`first_name`, ' ', `associates`.`last_name`) AS `associate`"
            ];
        }
        $this->sqlJoin   = "LEFT JOIN `agents` ON `agents`.`id` = `g`.`agent_id` LEFT JOIN `associates` ON `associates`.`id` = `g`.`associate`";
        $this->sqlQuery = '';
        $this->sqlParams = [];

        // Lead Groups
        if ($this->get['type'] === 'lead' && !empty($this->get['type_id'])) {
            if (empty($countOnly)) {
                $this->sqlSelect = array_merge($this->sqlSelect, [
                    "`g`.`id`",
                    "`g`.`name`",
                    "`g`.`agent_id`",
                    "`g`.`description`",
                    "`g`.`style`",
                    "`g`.`user`"
                ]);
            }
            $this->sqlQuery = sprintf(
                "SELECT %s "
                . " FROM `groups` `g` "
                . " JOIN `users_groups` `ug` ON `g`.`id` = `ug`.`group_id` AND `ug`.`user_id` = :user_id "
                . " LEFT JOIN `users` `u` ON `u`.`id` = `ug`.`user_id` "
                . " %s "
                . ($this->auth->info('mode') === 'agent'
                    ? " WHERE ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id` = :auth_id) "
                    : ""
                )
                . ($this->auth->isAssociate()
                    ? " WHERE ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id` = `u`.`agent` OR `g`.`associate`	= :auth_id) "
                    : ""
                )
                . " ORDER BY `g`.`name` ASC "
                . (!empty($this->limit)
                    ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                    : ""
                )
                . ";",
                implode(', ', $this->sqlSelect),
                $this->sqlJoin
            );
            $this->sqlParams['user_id'] = $this->db->quote($this->get['type_id']);
            if ($this->auth->info('mode') === 'agent' || $this->auth->isAssociate()) {
                $this->sqlParams['auth_id'] = $this->auth->info('auth');
            }

        // Agent Groups
        } elseif ($this->get['type'] === 'agent' && !empty($this->get['type_id'])) {
            if (empty($countOnly)) {
                $this->sqlSelect = array_merge($this->sqlSelect, [
                    "`g`.`id`",
                    "`g`.`name`",
                    "`g`.`description`",
                    "`g`.`style`",
                    "`g`.`user`"
                ]);
            }
            $this->sqlQuery = sprintf(
                "SELECT %s "
                . " FROM `groups` `g` "
                . " %s "
                . " WHERE (`g`.`agent_id` = :agent_id OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL)) "
                . " ORDER BY `g`.`name` ASC "
                . (!empty($this->limit)
                    ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                    : ""
                )
                . ";",
                implode(', ', $this->sqlSelect),
                $this->sqlJoin
            );
            $this->sqlParams['agent_id'] = $this->db->quote($this->get['type_id']);

        // Campaign Groups
        } elseif ($this->get['type'] === 'campaign' && !empty($this->get['type_id'])) {
            if (empty($countOnly)) {
                $this->sqlSelect = array_merge($this->sqlSelect, [
                    "`g`.`id`",
                    "`g`.`name`",
                    "`g`.`description`",
                    "`g`.`style`",
                    "`g`.`user`"
                ]);
            }
            $this->sqlQuery = sprintf(
                "SELECT %s "
                . " FROM `campaigns_groups` `cg` "
                . " LEFT JOIN `groups` `g` ON `cg`.`group_id` = `g`.`id` "
                . " %s "
                . " WHERE `cg`.`campaign_id` = :campaign_id "
                . ($this->auth->info('mode') == 'agent'
                    ? " AND ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id`	= :auth_id) "
                    : ""
                )
                . ($this->auth->isAssociate()
                    ? " AND ((`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`associate` = :auth_id) "
                    : ""
                )
                . " ORDER BY `g`.`name` ASC "
                . (!empty($this->limit)
                    ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                    : ""
                )
                . ";",
                implode(', ', $this->sqlSelect),
                $this->sqlJoin
            );
            $this->sqlParams['campaign_id'] = $this->db->quote($this->get['type_id']);
            if ($this->auth->info('mode') === 'agent' || $this->auth->isAssociate()) {
                $this->sqlParams['auth_id'] = $this->auth->info('auth');
            }

        // All Available Groups
        } elseif (is_null($this->get['type']) && is_null($this->get['type_id'])) {
            if (empty($countOnly)) {
                $this->sqlSelect = array_merge($this->sqlSelect, [
                    "`g`.`id`",
                    "`g`.`name`",
                    "`g`.`description`",
                    "`g`.`style`",
                    "`g`.`user`",
                    "count(`users_groups`.`group_id` = `g`.`id`) as `count`"
                ]);
            }
            $this->sqlQuery = sprintf(
                "SELECT %s "
                . " FROM `groups` `g` "
                . (empty($countOnly) ? "  LEFT JOIN `users_groups` ON `users_groups`.`group_id` = `g`.`id`" : "")
                . " %s "
                . ($this->auth->info('mode') == 'admin'
                    ? " WHERE (`g`.`agent_id` IS NULL OR `g`.`agent_id` = :auth_id) "
                    : ""
                )
                . ($this->auth->info('mode') === 'agent'
                    ? " WHERE (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`agent_id` = :auth_id "
                    : ""
                )
                . ($this->auth->isAssociate()
                    ? " WHERE (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL) OR `g`.`associate` = :auth_id "
                    : ""
                )
                . (empty($countOnly) ? " GROUP BY  `g`.`id` " : "")
                . " ORDER BY `name` ASC "
                . (!empty($this->limit)
                    ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                    : ""
                )
                . ";",
                implode(', ', $this->sqlSelect),
                $this->sqlJoin
            );
            if (in_array($this->auth->info('mode'), ['admin', 'agent']) || $this->auth->isAssociate()) {
                $this->sqlParams['auth_id'] = $this->auth->info('auth');
            }
        }
    }

    /**
     * Check auth permissions to perform this request
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        if ($this->auth->isLender()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
        }
    }

    /**
     * Ensure a valid request type and ID was provided
     * @throws NotFoundException
     */
    protected function checkRequestType()
    {
        $error = null;

        if (!empty($this->get['type']) && !in_array($this->get['type'], self::ALLOWED_TYPES)) {
            $error = sprintf('%s is not a valid request type.', $this->get['type']);
        }

        // if type is specified an ID is required
        if (!empty($this->get['type']) && empty($this->get['type_id'])) {
            $error = 'A type ID is required when a specific group type is requested.';
        }

        if (!empty($error)) {
            throw new NotFoundException($error);
        }
    }

    /**
     * Build and run the group count query
     * @throws NotFoundException
     * @return array
     */
    protected function fetchCountQuery()
    {
        $count = 0;

        $this->buildCountQuery();

        // Fetch Count
        if (!empty($this->sqlQuery)) {
            $result = $this->db->fetch($this->sqlQuery, $this->sqlParams);
            if (!empty($result['total'])) {
                $count = $result['total'];
            }
        } else {
            throw new NotFoundException('Failed to query groups due to bad request.');
        }

        return $count;
    }

    /**
     * Run the group collection query
     * @throws NotFoundException
     * @return array
     */
    protected function fetchQuery()
    {
        $groups = [];

        $this->buildQuery();

        // Fetch Groups & Build Collection
        if (!empty($this->sqlQuery)) {
            array_walk($this->db->fetchAll($this->sqlQuery, $this->sqlParams), function (&$group) use (&$groups) {
                // Group Title (for Mouseover)
                $group['title'] = (is_null($group['agent']) && is_null($group['associate']) ? ($group['user'] == 'false' ? '(Global)' : '(Shared)')
                    : (!empty($group['agent']) ? '(' . $group['agent'] . ')' : (!empty($group['associate']) ? '(' . $group['associate'] . ')' : ''))
                );
                // Add to Collection
                $groups[] = $group;
            });
        } else {
            throw new NotFoundException('Failed to query groups due to bad request.');
        }

        return $groups;
    }

    /**
     * Fetch the group collection
     *
     * @return array
     */
    protected function getResults()
    {
        $response = [
            'count' => 0,
            'groups' => [],
            'limit' => $this->limit,
            'page' => $this->page,
        ];

        $response['count'] = $this->fetchCountQuery();
        if ($response['count'] > 0) {
            $response['groups'] = $this->fetchQuery();
        }

        return $response;
    }
}
