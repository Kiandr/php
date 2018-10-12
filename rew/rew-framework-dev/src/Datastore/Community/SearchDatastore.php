<?php
namespace REW\Datastore\Community;

use REW\Core\Interfaces\CacheInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\Http\HostInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\User\SessionInterface;
use REW\Core\Interfaces\Util\IDXInterface as UtilIdxInterface;
use REW\Factory\Community\DetailsModelFactoryInterface;
use REW\Factory\Community\ListingResultModelFactoryInterface;
use REW\Factory\Community\ResultModelFactoryInterface;
use REW\Model\Community\RequestModelInterface;
use Exception;

class SearchDatastore implements SearchDatastoreInterface
{
    const DEFAULT_DESCRIPTION_LIMIT = 500;

    const THUMBNAIL_RES = '416x284';

    const MODULE_NAME = 'communities';

    /**
     * @var ResultModelFactoryInterface
     */
    protected $resultModelFactory;

    /**
     * @var \REW\Core\Interfaces\DBInterface
     */
    protected $database;

    /**
     * @var \REW\Core\Interfaces\LogInterface
     */
    protected $log;

    /**
     * @var \REW\Core\Interfaces\SettingsInterface
     */
    protected $settings;

    /**
     * @var \REW\Core\Interfaces\FormatInterface
     */
    protected $format;

    /**
     * @var \REW\Core\Interfaces\Factories\IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var \REW\Core\Interfaces\Util\IDXInterface
     */
    protected $idxUtil;

    /**
     * @var \REW\Core\Interfaces\CacheInterface
     */
    protected $cache;

    /**
     * @var \REW\Core\Interfaces\Http\HostInterface
     */
    protected $host;

    /**
     * SearchDatastore constructor.
     * @param ResultModelFactoryInterface $resultModelFactory
     * @param ListingResultModelFactoryInterface $listingResultModelFactory
     * @param DBInterface $database
     * @param LogInterface $log
     * @param SettingsInterface $settings
     * @param FormatInterface $format
     * @param IDXFactoryInterface $idxFactory
     * @param UtilIdxInterface $idxUtil
     * @param CacheInterface $cache
     * @param HostInterface $host
     */
    public function __construct(
        ResultModelFactoryInterface $resultModelFactory,
        DBInterface $database,
        LogInterface $log,
        SettingsInterface $settings,
        FormatInterface $format,
        IDXFactoryInterface $idxFactory,
        UtilIdxInterface $idxUtil,
        CacheInterface $cache,
        HostInterface $host
    ) {
        $this->resultModelFactory = $resultModelFactory;
        $this->database = $database;
        $this->log = $log;
        $this->settings = $settings;
        $this->format = $format;
        $this->idxFactory = $idxFactory;
        $this->idxUtil = $idxUtil;
        $this->cache = $cache;
        $this->host = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommunities(RequestModelInterface $communityRequest)
    {
        // phpcs:disable
        $limit = false;
        $mode = $communityRequest->getMode();

        // Truncate (Default: 500)
        $truncate = \Skin::hasFeature(\Skin::COMMUNITY_DESCRIPTION_NO_LIMIT) ? false : self::DEFAULT_DESCRIPTION_LIMIT;

        // Number of Thumbnails to Load (Set to -1 for all available)
        $loadImages = (is_numeric($mode)) ? true : 1;

        // Load Community Statistics (# of Listings, AVG Price, MIN Price, MAX Price)
        $loadStats = (is_numeric($mode)) ? true : false;

        // Load Additional Statistics (AVG Beds, AVG Baths, AVG SqFt, AVG Acres)
        $loadExtra = (is_numeric($mode)) ? true : false;

        // Load Community Search Results (Set this to the # of results to load)
        $loadResults = (is_numeric($mode)) ? 12 : false;

        // Sort communities
        $orderBy = 'order';

        // CMS Database
        $database = $this->database;

        // SQL query parts
        $sql_where = ["`is_enabled` = 'Y'"];
        $sql_params = [];
        $sql_select = [];

        // Order
        $sql_order = "`" . $orderBy . "` ASC";

        $imageResolution = self::THUMBNAIL_RES;

        // Featured Community ID
        if (is_numeric($mode)) {
            $imageResolution = null;
            $sql_where = ["`id` = ?"];
            $sql_params[] = $mode;
            if (empty($limit)) {
                $limit = 1;
            }

            // Featured Community Snippet
        } elseif (is_string($mode) && !in_array($mode, ['all', 'featured'])) {
            $sql_where[] = "`snippet` = ?";
            $sql_params[] = $mode;
            $limit = 1;

            // Featured Spotlight
        } elseif (is_string($mode) && $mode == 'featured') {
            $sql_order = "RAND()";
        }

        // SQL SELECT
        $sql_select[] = "`id`, `title`, `subtitle`, `description`, `page_id`";
        $sql_select[] = $loadStats ? "`stats_heading`, `stats_total`, `stats_average`, `stats_highest`, `stats_lowest`" : "";
        $sql_select[] = $loadStats ? "`idx_snippet`, `search_idx`, `search_criteria`" : "";
        if (\Skin::hasFeature(\Skin::COMMUNITY_VIDEO_LINKS)) {
            $sql_select[] = "`video_link`";
        }

        $sql_select = implode(', ', array_filter($sql_select));

        // SQL Limit
        $sql_limit = !empty($limit) ? ' LIMIT ' . (int) $limit : '';

        // SQL Where
        $sql_where = !empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '';

        // SQL Order
        $sql_order = !empty($sql_order) ? ' ORDER BY ' . $sql_order : '';

        try {
            // List of communities
            $communities = [];

            // Fetch communities from database
            $query = $database->prepare("SELECT " . $sql_select . " FROM `featured_communities`" . $sql_where . $sql_order . $sql_limit . ";");
            $query->execute($sql_params);
            $results = $query->fetchAll();
            $linkStmt = $database->prepare("SELECT `file_name` FROM `pages` WHERE `page_id` = ? LIMIT 1;");
            foreach ($results as $community) {
                // Find community's assigned page
                if (!empty($community['page_id']) && is_numeric($community['page_id'])) {
                    try {
                        $link = $linkStmt->execute([$community['page_id']]);

                        if ($link) {
                            $link = $linkStmt->fetch();
                        }

                        if (!empty($link)) {
                            $community['url'] = '/' . $link['file_name'] . '.php';
                        }
                    } catch (Exception $e) {
                        $this->log->log(LogInterface::ERROR, $e);
                    }
                }

                try {
                    // Load community's photos
                    $limit = $loadImages < 0 || $loadImages === true ? '' : ' LIMIT ' . $loadImages;
                    $community['images'] = $database->fetchAll("SELECT `id`, `file` FROM `cms_uploads` WHERE `type` = 'community' AND `row` = " . $database->quote($community['id']) . " ORDER BY `order` ASC" . $limit . ";");
                    $community['images'] = array_map(function ($image) use ($imageResolution) {
                        if (is_null($imageResolution)) {
                            return sprintf('/uploads/%s', $image['file']);
                        }
                        return sprintf('/thumbs/%s/uploads/%s', $imageResolution, $image['file']);
                    }, $community['images']);
                } catch (Exception $e) {
                    $this->log->log(LogInterface::ERROR, $e);
                }

                // 404 Image
                if (empty($community['images'])) {
                    $community['images'] = [sprintf('/thumbs/%s/img/404.gif', $imageResolution)];
                    if (is_null($imageResolution)) {
                        $community['images'] = ['/img/404.gif'];
                    }
                }

                // Community Photo
                $image = array_slice($community['images'], 0, 1);
                if (!empty($image)) {
                    $community['image'] = $image['0'];
                }

                // Truncate Description
                if (!empty($truncate)) {
                    $community['description'] = $this->format->truncate($community['description'], $truncate, '&hellip;', true, false);
                }

                // New Lines
                $community['description'] = nl2br(trim($community['description'], "\r\n "));

                // Strip HTML
                $community['description'] = $this->format->stripTags($community['description']);

                // IDX Feed
                $community['search_idx'] = !empty($community['search_idx']) ? $community['search_idx'] : $this->settings->IDX_FEED;

                // IDX Feed
                $idx = $this->idxFactory->getIdx($community['search_idx']);
                $db_idx = $this->idxFactory->getDatabase($community['search_idx']);

                // Get Community Feed
                $community['feed'] = $idx->getLink();

                try {
                    // Process search criteria
                    if ($loadStats) {
                        // Community search criteria
                        $sql_where = [];
                        $search_query = [];

                        if (!empty($community['idx_snippet'])) {
                            $query = $database->prepare("SELECT `code` FROM `snippets` WHERE `type` = 'idx' AND `agent` <=> :agent AND `team` <=> :team AND `id` = :idx_snippet LIMIT 1;");
                            $query->execute([
                                'agent'       => $this->settings->SETTINGS['agent'],
                                'team'        => $this->settings->SETTINGS['team'],
                                'idx_snippet' => $community['idx_snippet']
                            ]);
                            $community['search_criteria'] = $query->fetch(\PDO::FETCH_COLUMN);
                        }


                        $community['search_criteria'] = !empty($community['search_criteria']) ? unserialize($community['search_criteria']) : [];
                        if (!empty($community['search_criteria'])) {
                            $communityRequest = $community['search_criteria'];

                            // Swap global REQUEST
                            $__REQUEST = $_REQUEST;
                            $_REQUEST = $communityRequest;

                            $search_query = $idx->buildWhere($idx, $db_idx, 't1');
                            $search_where = $search_query['search_where'];

                            /**
                             * Map Queries
                             */

                            // WHERE Queries
                            $search_where = !empty($search_where) ? [$search_where] : [];

                            // HAVING Queries
                            $search_having = [];

                            // Search Group
                            $search_group = [];

                            // Latitude / Longitude Columns
                            $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
                            $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

                            // Search in Bounds
                            if (!empty($communityRequest['map']['bounds']) && $this->settings->IDX_FEED != 'cms') {
                                $idx->buildWhereBounds($communityRequest['map']['ne'], $communityRequest['map']['sw'], $search_group, $col_latitude, $col_longitude);
                            }

                            // Search in Radiuses
                            $idx->buildWhereRadius($communityRequest['map']['radius'], $search_group, $col_latitude, $col_longitude);

                            // Search in Polygons
                            $polygons = $idx->buildWherePolygons($communityRequest['map']['polygon'], $search_group, $search_having, 't2.Point');
                            if (!empty($polygons)) {
                                $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
                            }

                            // Add to Search Criteria
                            if (!empty($search_group)) {
                                $sql_mapping = '(' . implode(' OR ', $search_group) . ')';
                                if (!empty($search_having)) {
                                    $sql_mapping .= ' HAVING ' . implode(' OR ', $search_having);
                                }
                                $search_where[] = $sql_mapping;
                            }

                            // Require mapping data
                            $mapping = !empty($polygons);

                            // Generate search URL from search criteria
                            $community['search_url'] = $this->settings->SETTINGS['URL_IDX'] . '?';
                            if (!empty($communityRequest)) {
                                // Searchable IDX Fields
                                $searchable = array_merge(array_map(function ($field) {
                                    return $field['form_field'];
                                }, search_fields($idx)), ['map', 'search_location']);

                                // Current Search Criteria
                                $criteria = [];
                                array_walk($communityRequest, function ($v, $k) use (&$criteria, $searchable) {
                                    if (!empty($v) && in_array($k, $searchable)) {
                                        $criteria[$k] = $v;
                                    }
                                });

                                $criteria['refine'] = 'true';
                                $criteria['feed'] = $community['feed'];
                                $criteria['search_location'] = trim($criteria['search_location']);
                                $search_url = http_build_query($criteria);
                                if (!empty($search_url)) {
                                    $community['search_url'] .= '&' . $search_url;
                                }
                            }

                            $_REQUEST = $__REQUEST;

                        } else {
                            // Any global criteria
                            $idx->executeSearchWhereCallback($search_where);
                        }

                        // Load community listings
                        $community['listings'] = [];
                        if (is_int($loadResults) && $loadResults > 0) {
                            // Query String (WHERE & HAVING)
                            $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

                            // Order / Sort
                            if (!empty($community['search_criteria']['sort_by'])) {
                                list($direction, $order_by) = explode('-', $community['search_criteria']['sort_by'], 2);
                                $order = '`' . $order_by . '` ' . $direction;
                            } else {
                                $order = '`ListingPrice` DESC';
                            }

                            // Load community search results
                            $search_results = $db_idx->query("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable()
                                . "` JOIN (SELECT `t1`.`id`" . ($mapping ? ", `t2`.`Point`" : "") . " FROM `" . $idx->getTable() . "` `t1`"
                                . ($mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "")
                                . (!empty($search_where) ? " WHERE " . $search_where : '') . ') p USING(`id`)' . " ORDER BY " . $order
                                . " LIMIT " . $loadResults);
                            while ($search_result = $db_idx->fetchArray($search_results)) {
                                $listing = $this->idxUtil->parseListing($search_result);
                                $favouriteQuery = "SELECT 1 FROM `featured_listings` WHERE `mls_number` = ? AND `table` = ? AND `idx` = ? LIMIT 1";
                                $stmt = $this->database->prepare($favouriteQuery);
                                $stmt->execute([$listing['ListingMLS'], $idx->getTable(), $listing['idx']]);
                                $listing['featured'] = (!empty($stmt->fetchColumn()));
                                $listing['new'] = ($idx->getMaxAgeOfNewListingInDays() >= $listing['ListingDOM']);
                                $listing['priceDiff']['value'] = null;
                                $listing['priceDiff']['direction'] = null;
                                if (!empty($listing['ListingPriceOld'])) {
                                    $listing['priceDiff']['value'] = abs($listing['ListingPrice'] - $listing['ListingPriceOld']);

                                    $listing['priceDiff']['direction'] = 'down';

                                    if ($listing['ListingPrice'] > $listing['ListingPriceOld']) {
                                        $listing['priceDiff']['direction'] = 'up';
                                    }
                                }
                                $community['listings'][] = $listing;
                            }
                        }

                        // Load search stats
                        if (!empty($loadStats)) {
                            // Community Defaults
                            $community['stats_heading']   = !empty($community['stats_heading'])   ? $community['stats_heading']   : 'Real Estate Statistics';
                            $community['stats_total']     = !empty($community['stats_total'])     ? $community['stats_total']     : 'Total Listings';
                            $community['stats_average']   = !empty($community['stats_average'])   ? $community['stats_average']   : 'Average Price';
                            $community['stats_highest']   = !empty($community['stats_highest'])   ? $community['stats_highest']   : 'Highest Price';
                            $community['stats_lowest']    = !empty($community['stats_lowest'])    ? $community['stats_lowest']    : 'Lowest Price';

                            // Query String (WHERE & HAVING)
                            if (is_array($search_where)) {
                                $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');
                            }

                            // Statistics Query
                            $query = "SELECT SQL_CACHE COUNT(*) AS `total`, ROUND(AVG(`" . $idx->field('ListingPrice') . "`)) AS `average`, MAX(`" . $idx->field('ListingPrice') . "`) AS `max`, MIN(`" . $idx->field('ListingPrice') . "`) AS `min` "
                                . ($loadExtra ? ", ROUND(AVG(`" . $idx->field('NumberOfBedrooms') . "`)) AS `beds` "
                                    // Average property stats
                                    . ", ROUND(AVG(`" . $idx->field('NumberOfBathrooms') . "`)) AS `baths`, ROUND(AVG(`"
                                    . $idx->field('NumberOfSqFt') . "`)) AS `sqft`, ROUND(AVG(`" . $idx->field('NumberOfAcres')
                                    . "`), 2) AS `acres`, ROUND(AVG(`" . $idx->field('YearBuilt') . "`)) AS `built` "
                                    . ", ROUND(AVG(`" . $idx->field('ListingDOM') . "`)) AS `avg_dom`"
                                    // Price per square foot
                                    . ", ROUND(AVG(`" . $idx->field('ListingPrice') . "` / `" . $idx->field('NumberOfSqFt')
                                    . "`)) AS `avg_price_sqft`, ROUND(MAX(`" . $idx->field('ListingPrice') . "`/`"
                                    . $idx->field('NumberOfSqFt') . "`)) AS `max_price_sqft`, ROUND(MIN(`"
                                    . $idx->field('ListingPrice') . "`/`" . $idx->field('NumberOfSqFt') . "`)) AS `min_price_sqft`" : "")
                                . " FROM `" . $idx->getTable() . "` JOIN (SELECT `t1`.`id`"
                                . ($mapping ? ", `t2`.`Point`" : "")
                                . " FROM `" . $idx->getTable() . "` `t1`"
                                . ($mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`"
                                    . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`"
                                    . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "")
                                . (!empty($search_where) ? " WHERE " . $search_where : '')
                                . ') p USING(`id`)'
                                . ";";

                            $propTypeStatsQuery = "SELECT SQL_CACHE `" . $idx->field('ListingType') . "`, COUNT(*) as `total`"
                                . " FROM `" . $idx->getTable() . "` `t1` JOIN (SELECT `t1`.`id`"
                                . ($mapping ? ", `t2`.`Point`" : "")
                                . " FROM `" . $idx->getTable() . "` `t1`"
                                . ($mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`"
                                    . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`"
                                    . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "")
                                . (!empty($search_where) ? " WHERE " . $search_where : '')
                                . ') p USING(`id`) GROUP BY `t1`.`' . $idx->field('ListingType') . "`"
                                . ";";

                            // Load statistics for display (use Memcached)
                            $index = self::MODULE_NAME . ':' . md5($query);
                            $cached = $this->cache->getCache($index);
                            if (!empty($cached)) {
                                $community['stats'] = $cached;
                            } else {
                                $community['stats'] = $db_idx->fetchQuery($query);
                                $this->cache->setCache($index, $community['stats']);
                            }

                            // Load property type stats (use Memcached)
                            $index = self::MODULE_NAME . ':' . md5($propTypeStatsQuery);
                            $cached = $this->cache->getCache($index);
                            if (!empty($cached)) {
                                $community['propTypeStats'] = $cached;
                            } else {
                                $community['propTypeStats'] = $db_idx->query($propTypeStatsQuery)->fetch_all(\MYSQLI_ASSOC);
                                $this->cache->setCache($index, $community['propTypeStats']);
                            }

                        }
                    }

                    // Error Occurred
                } catch (Exception $e) {
                    $this->log->log(LogInterface::ERROR, $e);
                }

                // Make sure we're only displaying communities that have links
                if (!empty($community['url'])) {
                    // Add to list of communities
                    $communities[] = $this->resultModelFactory->createFromArray($community);
                }
            }

            // Error Occurred
        } catch (Exception $e) {
            $this->log->log(LogInterface::ERROR, $e);
        }
        // phpcs:enable
        return $communities;

    }

    /**
     * Creates the link to the community.
     * @param string $pageId
     * @return string
     */
    protected function buildLinkForCommunity($pageId)
    {
        $stmt = $this->database->prepare("SELECT `file_name` FROM `pages` WHERE `page_id` = ? LIMIT 1;");
        $stmt->execute([$pageId]);
        $link = $stmt->fetchColumn();
        if (!empty($link)) {
            $link = '/' . $link . '.php';
        }
        return $link;
    }
}
