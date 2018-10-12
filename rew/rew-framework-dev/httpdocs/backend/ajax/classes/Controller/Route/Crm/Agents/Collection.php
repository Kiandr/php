<?php

namespace REW\Api\Internal\Controller\Route\Crm\Agents;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\AgentsAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\CacheInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Agent Collection Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $cacheEnabled;

    /**
     * @var string
     */
    protected $cacheIndex;

    /**
     * @var DB
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
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var array
     */
    protected $whereSql;

    /**
     * @var array
     */
    protected $whereParams;

    /**
     * @param AuthInterface $auth
     * @param CacheInterface $cache
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        CacheInterface $cache,
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->cache = $cache;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();

        $this->cacheEnabled = (!empty($this->get['pid']));

        $this->checkPermissions();

        if ($this->cacheEnabled) {
            $this->cacheIndex = hash('md5', __FILE__ . serialize($this->get));
            $cachedResponse = $this->getCachedResponse();
        }

        $body = $cachedResponse ?: $this->getResponse();
        $response->setBody(json_encode($body));
    }

    /**
     * @return array
     */
    protected function getResponse()
    {
        // Establish Query Limits
        $this->limit = (!empty($this->get['limit'])) ? intval($this->get['limit']) : null;
        $this->page = (!empty($this->get['page'])) ? intval($this->get['page']) : null;
        $this->offset = ($this->page > 1 && $this->limit >= 1) ? ($this->page - 1) * $this->limit : null;

        // Define API Return Value Structure
        $response = [
            'agents' => [],
            'count'  => 0,
            'limit'  => $this->limit,
            'page'   => $this->page,
        ];

        // Build query filters
        $this->buildSqlFilters();

        // Pull count and results
        $response['count'] = $this->fetchResultsCount();
        if ($response['count'] > 0) {
            $response['agents'] = $this->fetchResults();
        }

        ksort($response);

        // Cache Results
        if ($this->cacheEnabled && !empty($response['agents'])) {
            $this->cache->setCache($this->cacheIndex, $response);
        }

        // Sort and return
        return $response;
    }

    /**
     * Build and store sql filters in this controller
     */
    protected function buildSqlFilters()
    {
        // Search Query Filters
        $this->whereSql = [];
        $this->whereParams = [];

        // Filter agents by name
        if (!empty($this->get['search_name'])) {
            $this->whereSql[] = " CONCAT(`first_name`, ' ', `last_name`) LIKE :search_name ";
            $this->whereParams['search_name'] = '%' . $this->get['search_name'] . '%';
        }
    }

    /**
     * Check if a cached response is available for this request
     *
     * @return array
     */
    protected function getCachedResponse()
    {
        // Check Cache
        $cached = $this->cache->getCache($this->cacheIndex);
        if (!is_null($cached) && is_array($cached)) {
            $return = $cached;
            $return['cached'] = true;
        }

        return $return ?: [];
    }

    /**
     * Check if the authuser has the correct permissions to perform this request data
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        // Check Request VS Permissions
        $agentsAuth = new AgentsAuth($this->settings);
        if (!$agentsAuth->canViewAgents($this->auth)) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
        }
    }

    /**
     * Get results for this request
     *
     * @return array
     */
    protected function fetchResults()
    {
        $query = $this->db->prepare(sprintf(
            "SELECT "
            . " `auth`, "
            . " `first_name`, "
            . " `id`, "
            . " IF(`image` != '', CONCAT('%sagents/', `image`), null) AS `image`, "
            . " `last_name`, "
            . " IF(`title` != '', `title`, null) AS `title` "
            . " FROM `%s` "
            . (!empty($this->whereSql)
                ? " WHERE " . implode(" AND ", $this->whereSql)
                : ""
            )
            . " ORDER BY `first_name` ASC, `last_name` ASC, `id` ASC "
            . (!empty($this->limit)
                ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                : ""
            ),
            $this->settings->URLS['UPLOADS'],
            $this->settings->TABLES['LM_AGENTS']
        ));
        if ($query->execute($this->whereParams)) {
            $agents = $query->fetchAll();
        }
        return $agents ?: [];
    }

    /**
     * Get total result count for this request
     *
     * @return int
     */
    protected function fetchResultsCount()
    {
        // Build Count Query
        $query = $this->db->prepare(sprintf(
            "SELECT "
            . " COUNT(`id`) AS `total` "
            . " FROM `%s` `a` "
            . (!empty($this->whereSql)
                ? " WHERE " . implode(" AND ", $this->whereSql)
                : ""
            )
            . ";",
            $this->settings->TABLES['LM_AGENTS']
        ));
        if ($query->execute($this->whereParams)) {
            $count = $query->fetch();
        }
        return (intval($count['total'] > 0)) ? $count['total'] : 0;
    }
}
