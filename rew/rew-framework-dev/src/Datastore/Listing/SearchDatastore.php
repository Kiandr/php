<?php
namespace REW\Datastore\Listing;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\IDX\ComplianceInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Util\IDXInterface as UtilIDXInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Factory\Idx\Search\ListingResultFactory;
use REW\Model\Idx\Search\ListingRequest;
use REW\Model\Idx\Search\ListingResults;
use REW\Model\Idx\Search\ListingCountResult;


/**
 * Class SearchDatastore
 * @package REW\Datastore\Listing
 * @todo make an interface for this to conform to (for DI reasons)
 * @todo add means of getting total count of listings matching criteria (exclude pagination stuff).
 */
class SearchDatastore
{
    const DEFAULT_SELECT_FIELDS = [
        'id', 'ListingImage', 'Address', 'AddressCity',
        'ListingPrice', 'ListingDOM', 'ListingPriceOld', 'ListingType',
        'NumberOfBedrooms', 'NumberOfBathrooms', 'NumberOfSqFt', 'ListingMLS',
        'Latitude', 'Longitude'
    ];

    /** @var DBFactoryInterface */
    protected $dbFactory;

    /** @var IDXFactoryInterface */
    protected $idxFactory;

    /** @var UtilIDXInterface */
    protected $utilIdx;

    /** @var ListingResultFactory */
    protected $listingResultFactory;

    // @var ComplianceInterface
    protected $compliance;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param IDXFactoryInterface $idxFactory
     * @param UtilIDXInterface $utilIdx
     * @param ListingResultFactory $listingResultFactory
     * @param ComplianceInterface $compliance
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        IDXFactoryInterface $idxFactory,
        UtilIDXInterface $utilIdx,
        ListingResultFactory $listingResultFactory,
        ComplianceInterface $compliance
    ) {
        $this->dbFactory = $dbFactory;
        $this->idxFactory = $idxFactory;
        $this->utilIdx = $utilIdx;
        $this->listingResultFactory = $listingResultFactory;
        $this->compliance = $compliance;
    }

    /**
     * @param ListingRequest $listingRequest
     * @return \REW\Model\Idx\Search\ListingResults
     * @throws \PDOException on database error
     * @throws \Exception on IDX settings error
     */
    public function getListings(ListingRequest $listingRequest)
    {
        $database = $this->dbFactory->get($listingRequest->getFeedName());
        $idx = $this->idxFactory->getIdx($listingRequest->getFeedName());
        $paginate = $listingRequest->getPagination();
        $this->compliance->load($listingRequest->getFeedName());

        list($whereParts, $whereParams, $whereHaving) = $this->getWhereQuery($idx, $listingRequest);

        // Add in pagination criteria
        $whereParts = array_merge($whereParts, [$paginate->getWhere(['ListingMLS' => 't1.ListingMLS'])]);
        $whereParts = array_filter($whereParts, function ($elem) {
            if (empty($elem)) {
                return false;
            }
            return true;
        });

        $whereQuery = '';
        $where = implode(' AND ', $whereParts);
        if (!empty($where)) {
            $whereQuery = sprintf(' WHERE %s', $where);
        }

        $whereParams = array_merge($whereParams, $paginate->getParams());

        // Create SQL statement
        $sql = "SELECT %s FROM `%s` `t1`"
        . " JOIN `%s` `t2`"
        . " ON `t1`.`%s` = `t2`.`ListingMLS`"
        . " AND `t1`.`%s` = `t2`.`ListingType`%s";

        // Append compliance based columns
        $columns = array_merge(self::DEFAULT_SELECT_FIELDS);
        if ($this->compliance['results']['show_agent']) {
            $columns = array_merge($columns, ['ListingAgent']);
        }
        if ($this->compliance['results']['show_office']) {
            $columns = array_merge($columns, ['ListingOffice']);
        }

        $sql = sprintf($sql,
            $idx->selectColumns('`t1`.', $columns),
            $idx->getTable(),
            $idx->getTable('geo'),
            $idx->field('ListingMLS'),
            $idx->field('ListingType'),
            $whereQuery
        );

        $sqlOrdering = [];
        if ($order = $paginate->getOrder()) {
            $sql .= ' ORDER BY ';
            foreach ($order as $sort => $dir) {

                // Check column validity
                $validColumn = false;
                $idxFieldSettings = $idx->getFields();

                if (in_array($sort, $idxFieldSettings)) {
                    $validColumn = true;
                }

                if (!$validColumn) {
                    throw new \RuntimeException(
                        sprintf('Column "%s" is not a valid ordering column!', $sort)
                    );
                }

                // $dir is safe here, Pagination takes care of it.
                $sql .= sprintf('t1.%s %s, ', $sort, $dir);
                $sqlOrdering[$sort] = $dir;
            }
            $sql = rtrim($sql, ', ');
        }

        // Build LIMIT query string
        $limit = $paginate->getLimit();
        if (!empty($limit)) {
            $sql = sprintf('%s LIMIT %d', $sql, $limit);
        }

        $stmt = $database->prepare($sql);

        // @todo: update this so that Global variables are not used
        // https://realestatewebmasters.atlassian.net/browse/DISC-658
        $queryString = $stmt->queryString;
        foreach($whereParams as $param) {
            $queryString = preg_replace('/\?/', '\'' . $param . '\'', $queryString, 1);
        }
        $_SESSION['last_search'] = $queryString;
        // end TODO

        $stmt->execute($whereParams);
        $listings = $stmt->fetchAll();

        // Paginate results
        $paginate->processResults($listings);

        $feed = $listingRequest->getFeedName();
        $listingResults = [];

        $compliance_logo = $this->compliance['results']['show_icon'] ?: '';
        foreach ($listings as $listing) {
            // Generate a ListingResult; some of these properties are hardcoded for now.
            $processedListing = $this->utilIdx->parseListing($listing);
            $listing['url_details'] = (!empty($processedListing['url_details']) ? $processedListing['url_details'] : null);
            $listing['enhanced'] = (!empty($processedListing['enhanced']) ? $processedListing['enhanced'] : null);
            $listing['feed'] = $feed;
            $listing['compliance'] = $compliance_logo;
            $listingResult = $this->listingResultFactory->createFromArray($listing);
            $listingResults[] = $listingResult;
        }
        return (new ListingResults())->withPagination($paginate)->withBaseSearchUrl($listingRequest->getBaseSearchUrl())->withListingResults($listingResults);
    }


    /**
     * @param ListingRequest $listingRequest
     * @return \REW\Model\Idx\Search\ListingCountResult
     * @throws \PDOException on database error
     * @throws \Exception on IDX settings error
     */
    public function getCount(ListingRequest $listingRequest)
    {
        $database = $this->dbFactory->get($listingRequest->getFeedName());
        $idx = $this->idxFactory->getIdx($listingRequest->getFeedName());

        list($whereParts, $whereParams, $whereHaving) = $this->getWhereQuery($idx, $listingRequest);

        $whereQuery = '';
        $where = implode(' AND ', $whereParts);
        if (!empty($where)) {
            $whereQuery = sprintf(' WHERE %s', $where);
        }

        // Create SQL statement
        $sql = "SELECT COUNT(`t1`.`id`) FROM `%s` `t1`"
            . " JOIN `%s` `t2`"
            . " ON `t1`.`%s` = `t2`.`ListingMLS`"
            . " AND `t1`.`%s` = `t2`.`ListingType`%s";

        $sql = sprintf($sql,
            $idx->getTable(),
            $idx->getTable('geo'),
            $idx->field('ListingMLS'),
            $idx->field('ListingType'),
            $whereQuery
        );

        $stmt = $database->prepare($sql);
        $stmt->execute($whereParams);
        $count = (int) $stmt->fetchColumn();
        return (new ListingCountResult())->withCount($count);
    }

    /**
     * Get Where Query and Parameters from data
     * @param IDXInterface $idx
     * @param ListingRequest $listingRequest
     * @return [array, array, array]
     */
    protected function getWhereQuery(IDXInterface $idx, ListingRequest $listingRequest)
    {
        $whereParams = [];
        $whereParts = [];
        $whereMaps = [];

        // @todo make use of this for aggregate queries.
        // This is passed into buildWhereBounds, buildWhereRadius, and buildWherePolygons.
        $whereHaving = [];

        $searchFields = $listingRequest->getSearchCriteria();
        foreach ($searchFields as $searchField) {
            $searchValue = $searchField->getSearchValue();
            $searchOp = $searchField->getSearchOperation();

            // Skip non-fields
            $dbFields = $searchField->getDbFields();
            if (!$dbFields) continue;

            // Skip empty search values for range fields (including 0).
            if (empty($searchValue) && in_array($searchOp, [
                        DBInterface::COMP_LESS_THAN,
                        DBInterface::COMP_LESS_THAN_INTERVAL,
                        DBInterface::COMP_MORE_THAN,
                        DBInterface::COMP_MORE_THAN_INTERVAL])) {
                continue;
            }

            // Skip empty values for regular search fields (that aren't 0).
            if (empty($searchValue) && !is_numeric($searchValue)) continue;

            // Skip if search field is not configured
            $dbFields = $searchField->getDbFields();
            if (!is_array($dbFields) || empty($dbFields)) {
                continue;
            }

            // Build query string
            $fieldWhereParts = [];
            foreach ($dbFields as $dbField) {
                $idxField = sprintf('`t1`.`%s`', $dbField);

                if (!is_array($searchValue)) {
                    $fieldWhereParts[] = sprintf($this->matchString($searchOp), $idxField);
                    switch ($searchOp) {
                        case DBInterface::COMP_BEGINS_LIKE:
                            $whereParams[] = sprintf('%%s%%', $searchValue);
                            break;
                        case DBInterface::COMP_ENDS_LIKE:
                            $whereParams[] = sprintf('%%%s', $searchValue);
                            break;
                        case DBInterface::COMP_LIKE:
                            $whereParams[] = sprintf('%%%s%%', $searchValue);
                            break;
                        case DBInterface::COMP_NOT_LIKE:
                            $whereParams[] = sprintf('%%%s%%', $searchValue);
                            break;
                        default:
                            $whereParams[] = $searchValue;
                    }
                } else {
                    if ($searchOp == 'equals') {
                        $whereParams = array_merge($whereParams, $searchValue);
                        $fieldWhere = sprintf('%s IN (%s)', $idxField,
                            implode(',', array_fill(0, count($searchValue), '?')));
                        $fieldWhereParts[] = $fieldWhere;
                    }
                    if ($searchOp == 'notequals') {
                        $whereParams = array_merge($whereParams, $searchValue);
                        $fieldWhere = sprintf('%s NOT IN (%s)', $idxField,
                            implode(',', array_fill(0, count($searchValue), '?')));
                        $fieldWhereParts[] = $fieldWhere;
                    }
                    if ($searchOp == 'like') {
                        $whereParams = array_merge($whereParams, $searchValue);
                        $fieldWhere = sprintf('(%s)',
                            implode(' OR ', array_fill(0, count($searchValue), sprintf('%s LIKE CONCAT(\'%%\', ?, \'%%\')', $idxField))));
                        $fieldWhereParts[] = $fieldWhere;
                    }
                }
            }

            if (!empty($fieldWhereParts)) {
                $whereParts[] = sprintf('(%s)', implode(' OR ', $fieldWhereParts));
            }
        }

        // Check for map bounds
        if (!empty($listingRequest->getBounds())) {
            $bounds = $listingRequest->getBounds();
            $idx->buildWhereBounds((string) $bounds->getNorthEastBounds(), (string) $bounds->getSouthWestBounds(), $whereParts);
        }

        // Check for radius search
        if (!empty($listingRequest->getRadius())) {
            $radius = $listingRequest->getRadius();
            $idx->buildWhereRadius(json_encode($radius), $whereMaps);
        }

        // Check for polygon search
        if (!empty($listingRequest->getPolygon())) {
            $polygon = $listingRequest->getPolygon();
            $idx->buildWherePolygons($polygon, $whereMaps, $whereHaving, 't2.Point');
        }

        // Append map queries
        if (!empty($whereMaps)) {
            $whereParts[] = sprintf(
                '(%s)',
                implode(' OR ', $whereMaps)
            );
        }

        return [$whereParts, $whereParams, $whereHaving];
    }

    /**
     * Get the format string to use in the where for different conditions
     *
     * @param string $matchType
     * @return string
     */
    protected function matchString($matchType)
    {
        switch ($matchType) {
            case DBInterface::COMP_EQUALS:
                $sqlMatch = "%s = ?";
                break;
            case DBInterface::COMP_NOT_EQUALS:
                $sqlMatch = "%s != ?";
                break;
            case DBInterface::COMP_MORE_THAN:
                $sqlMatch = "%s >= ?";
                break;
            case DBInterface::COMP_MORE_THAN_INTERVAL:
                $sqlMatch = "%s >= ?";
                break;
            case DBInterface::COMP_LESS_THAN:
                $sqlMatch = "%s <= ?";
                break;
            case DBInterface::COMP_LESS_THAN_INTERVAL:
                $sqlMatch = "%s <= ?";
                break;
            case DBInterface::COMP_LIKE:
                $sqlMatch = "%s LIKE ?";
                break;
            case DBInterface::COMP_BEGINS_LIKE:
                $sqlMatch = "%s LIKE ?";
                break;
            case DBInterface::COMP_ENDS_LIKE:
                $sqlMatch = "%s LIKE ?";
                break;
            case DBInterface::COMP_NOT_LIKE:
                $sqlMatch = "%s NOT LIKE ?";
                break;
            case DBInterface::COMP_FIND_IN_SET:
                $sqlMatch = "FIND_IN_SET(?, REPLACE(%s, ', ', ','))";
                break;
            case DBInterface::COMP_REPLACE:
                $sqlMatch = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(%s, '.', ''), '/', ''), ')', ''), '(', ''), '  ', ' ') = UPPER(?)";
                break;
        }
        return $sqlMatch;
    }
}
