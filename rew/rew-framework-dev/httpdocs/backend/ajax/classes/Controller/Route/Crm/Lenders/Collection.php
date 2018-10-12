<?php

namespace REW\Api\Internal\Controller\Route\Crm\Lenders;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\LendersAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\CacheInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Class Collection
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var bool
     */
    protected $cacheEnabled;

    /**
     * @var string
     */
    protected $cacheIndex;

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
     * @var array
     */
    protected $sqlParams;

    /**
     * @var array
     */
    protected $sqlWhere;

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
        $this->cache = $cache;
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
        $this->get = $request->get();

        $this->cacheEnabled = (!empty($this->get['pid'])) ? true : false;

        if ($this->cacheEnabled) {
            $this->cacheIndex = hash('md5', __FILE__ . serialize($this->get));
            $cachedResponse = $this->getCachedResponse();
        }

        $body = $cachedResponse ?: $this->getResponse();
        $response->setBody(json_encode($body));
    }

    /**
     * Build query filters
     */
    protected function buildQueryFilters()
    {
        // Search Query Filters
        $this->sqlWhere = [];
        $this->sqlParams = [];

        // Filter lenders by name
        if (!empty($this->get['search_name'])) {
            $this->sqlWhere[] = " CONCAT(`first_name`, ' ', `last_name`) LIKE :search_name ";
            $this->sqlParams['search_name'] = '%' . $this->get['search_name'] . '%';
        }
    }

    /**
     * Check permissions against request
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        // Check Request VS Permissions
        $lendersAuth = new LendersAuth($this->settings);
        if (!$lendersAuth->canViewLenders($this->auth)) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
        }
    }

    /**
     * Get count of lenders matching request filters
     *
     * @return int
     */
    protected function fetchResultsCount()
    {
        $query = $this->db->prepare(sprintf(
            "SELECT "
            . " COUNT(`id`) AS `total` "
            . " FROM `%s` "
            . (!empty($this->sqlWhere)
                ? " WHERE " . implode(" AND ", $this->sqlWhere)
                : ""
            ),
            $this->settings->TABLES['LM_LENDERS']
        ));
        if ($query->execute($this->sqlParams)) {
            $count = $query->fetch();
        }
        return (intval($count['total'] > 0)) ? $count['total'] : 0;
    }

    /**
     * Get lenders matching request filters
     *
     * @return array
     */
    protected function fetchResults()
    {
        $query = $this->db->prepare(sprintf(
            "SELECT "
            . " `auth`, `first_name`, `id`, `last_name` "
            . " FROM `%s` "
            . (!empty($this->sqlWhere)
                ? " WHERE " . implode(" AND ", $this->sqlWhere)
                : ""
            )
            . " ORDER BY `first_name` ASC, `last_name` ASC, `id` ASC "
            . (!empty($this->limit)
                ? " LIMIT " . (!empty($this->offset) ? $this->offset . ',' : '') . $this->limit
                : ""
            ),
            $this->settings->TABLES['LM_LENDERS']
        ));
        if ($query->execute($this->sqlParams)) {
            $lenders = $query->fetchAll();
        }
        return $lenders ?: [];
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
     * Get the collection of lenders
     *
     * @return array
     */
    protected function getResponse()
    {
        // Establish Query Limis
        $this->limit = (!empty($this->get['limit'])) ? intval($this->get['limit']) : null;
        $this->page = (!empty($this->get['page'])) ? intval($this->get['page']) : null;
        $this->offset = ($this->page > 1 && $this->limit >= 1) ? ($this->page - 1) * $this->limit : null;

        // Define API Return Value Structure
        $response = [
            'lenders' => [],
            'count'   => 0,
            'limit'   => $this->limit,
            'page'    => $this->page,
        ];

        $this->buildQueryFilters();

        // Get results count
        $response['count'] = $this->fetchResultsCount();

        // Build Search Query
        if ($response['count'] > 0) {
            $response['lenders'] = $this->fetchResults();
        }

        ksort($response);

        if ($this->cacheEnabled && !empty($response)) {
            $this->cache->setCache($index, $response);
        }

        return $response;
    }
}
