<?php
namespace REW\Factory\Idx\Search;

use REW\Core\Interfaces\Http\HostInterface;
use REW\Model\Idx\Map\Bounds\NorthEastBounds;
use REW\Model\Idx\Map\Bounds\SouthWestBounds;
use REW\Model\Idx\Map\Radius\Radius;
use REW\Model\Idx\Search\ListingRequest;
use REW\Model\Idx\Search\Map\Bounds\Bounds;
use REW\Model\Idx\Search\Map\BoundsInterface;
use REW\Factory\IDX\Search\FeedInfoFactoryInterface;
use REW\Pagination\Cursor;
use REW\Pagination\Cursor\After;
use REW\Pagination\Cursor\Before;
use REW\Pagination\Pagination;
use REW\Datastore\Listing\SearchFieldDatastoreInterface;


class ListingRequestFactory
{
    /** @var string */
    const FLD_LOCATION = 'location';

    /** @var string */
    const FLD_MIN_PRICE = 'min_price';

    /** @var string */
    const FLD_MAX_PRICE = 'max_price';

    /** @var string */
    const FLD_PROPERTY_TYPE = 'property_type';

    /** @var string */
    const FLD_HAS_POOL = 'has_pool';

    /** @var string */
    const FLD_HAS_FIREPLACE = 'has_fireplace';

    /** @var string */
    const FLD_IS_WATERFRONT = 'is_waterfront';

    /** @var string */
    const FLD_MAP_BOUNDS = 'bounds';

    /** @var string */
    const FLD_MAP_RADIUS = 'radius';

    /** @var string */
    const FLD_MAP_POLYGON = 'polygon';

    /** @var string */
    const FLD_LISTING_MLS = 'ListingMLS';

    /** @var integer */
    const DEFAULT_LIMIT = 100;

    /** @var string */
    const DEFAULT_SORT_FIELD = "ListingPrice";

    /** @var string */
    const DEFAULT_SORT_ORDER = 'DESC';

    /**
     * @var \REW\Core\Interfaces\Http\HostInterface
     */
    protected $host;

    /**
     * @var FeedInfoFactoryInterface
     */
    protected $feedInfoFactory;

    /**
     * @var FieldFactoryInterface
     */
    protected $fieldFactory;

    /**
     * @var SearchFieldDatastoreInterface
     */
    protected $searchFieldDatastore;


    /**
     * ListingRequestFactory constructor.
     * @param \REW\Core\Interfaces\Http\HostInterface $host
     * @param FeedInfoFactoryInterface $feedInfoFactory,
     * @param FieldFactoryInterface $fieldFactory,
     * @param SearchFieldDatastoreInterface $searchFieldDatastore
     */
    public function __construct(
        HostInterface $host,
        FeedInfoFactoryInterface $feedInfoFactory,
        FieldFactoryInterface $fieldFactory,
        SearchFieldDatastoreInterface $searchFieldDatastore
    )
    {
        $this->host = $host;
        $this->feedInfoFactory = $feedInfoFactory;
        $this->fieldFactory = $fieldFactory;
        $this->searchFieldDatastore = $searchFieldDatastore;
    }

    /**
     * Build a new ListingRequest from an array.
     * @param array $data
     * @return ListingRequest
     */
    public function createFromArray(array $data)
    {
        $requestModel = new ListingRequest();
        $cursor = null;

        if (!empty($data['after'])) {
            $cursor = After::decode($data['after']);
        }

        if (!empty($data['before'])) {
            $cursor = Before::decode($data['before']);
        }

        if (empty($cursor)) {
            if (empty($data['limit'])) {
                $data['limit'] = self::DEFAULT_LIMIT;
            }

            if (empty($data['order'])) {
                $data['order'] = self::DEFAULT_SORT_FIELD;
            }

            if (empty($data['sort'])) {
                $data['sort'] = self::DEFAULT_SORT_ORDER;
            }

            $cursor = new Cursor(self::FLD_LISTING_MLS, (int) $data['limit'], $data['order'], $data['sort']);
        }

        /** @todo standardize Pagination through some interface for DI purposes */
        $pagination = new Pagination($cursor);

        // Build Criteria URL
        $criteriaUrl = http_build_query([
            'limit' => $data['limit'],
            'order' => $data['order'],
            'sort' => $data['sort'],
            'criteria' => $data['criteria']
        ]);

        $listingRequest = (new ListingRequest())
            ->withPagination($pagination)
            ->withFeedName($data['feed'])
            ->withBaseSearchUrl(rtrim($this->host->getDomainUrl(), '/') . strtok($_SERVER["REQUEST_URI"],'?') . '?' . $criteriaUrl);

        if (!empty($data['criteria'])) {
            $criteria = json_decode($data['criteria'], true);
        }

        if (!empty($criteria)) {
            // process out criteria

            /** @todo create a ListingResult factory to sort all of this stuff out. */
            // Set the map search bounds
            if (!empty($criteria[self::FLD_MAP_BOUNDS])) {

                $bounds = new Bounds();

                if (!empty($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE])) {
                    $northEastBounds = new NorthEastBounds();
                    if (!empty($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][NorthEastBounds::FLD_LATITUDE])) {
                        $northEastBounds = $northEastBounds->withLatitude($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][NorthEastBounds::FLD_LATITUDE]);
                    }
                    if (!empty($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][NorthEastBounds::FLD_LONGITUDE])) {
                        $northEastBounds = $northEastBounds->withLongitude($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][NorthEastBounds::FLD_LONGITUDE]);
                    }
                    $bounds = $bounds->withNorthEastBounds($northEastBounds);
                }
                if (!empty($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW])) {
                    $southWestBounds = new SouthWestBounds();
                    if (!empty($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][NorthEastBounds::FLD_LATITUDE])) {
                        $southWestBounds = $southWestBounds->withLatitude($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][NorthEastBounds::FLD_LATITUDE]);
                    }

                    if (!empty($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][NorthEastBounds::FLD_LONGITUDE])) {
                        $southWestBounds = $southWestBounds->withLongitude($criteria[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][NorthEastBounds::FLD_LONGITUDE]);
                    }
                    $bounds = $bounds->withSouthWestBounds($southWestBounds);
                }
                $listingRequest = $listingRequest->withBounds($bounds);
                unset($criteria[self::FLD_MAP_BOUNDS]);
            }

            // Map radius criteria
            if (!empty($criteria[self::FLD_MAP_RADIUS]) && is_array($criteria[self::FLD_MAP_RADIUS])) {
                $radiusSearch = new \REW\Model\Idx\Search\Map\Radius\Radius();
                foreach ($criteria[self::FLD_MAP_RADIUS] as $radius) {
                    $radiusModel = new Radius();
                    if (!empty($radius[Radius::FLD_LATITUDE])) {
                        $radiusModel = $radiusModel->withLatitude($radius[Radius::FLD_LATITUDE]);
                    }
                    if (!empty($radius[Radius::FLD_LONGITUDE])) {
                        $radiusModel = $radiusModel->withLongitude($radius[Radius::FLD_LONGITUDE]);
                    }
                    if (!empty($radius[Radius::FLD_RADIUS])) {
                        $radiusModel = $radiusModel->withRadius($radius[Radius::FLD_RADIUS]);
                    }
                    $radiusSearch = $radiusSearch->withRadii($radiusSearch->getRadii() + [$radiusModel]);
                }
                $listingRequest = $listingRequest->withRadius($radiusSearch);
                unset($criteria[self::FLD_MAP_RADIUS]);
            }

            // Polygon search criteria
            if (!empty($criteria[self::FLD_MAP_POLYGON])) {
                if (is_array($criteria[self::FLD_MAP_POLYGON]))
                {
                    $criteria[self::FLD_MAP_POLYGON] = json_encode($criteria[self::FLD_MAP_POLYGON]);
                }
                $listingRequest = $listingRequest->withPolygon($criteria[self::FLD_MAP_POLYGON]);
                unset($criteria[self::FLD_MAP_POLYGON]);
            }
            $feedInfo = $this->feedInfoFactory->create($data['feed']);

            // Process out criteria
            foreach ($criteria as $criterion => $value) {
                $feedInfo = $feedInfo->withAdditionalSearchField($this->fieldFactory->createFromArray(['form_field' => $criterion, 'value' => $value]));
            }

            $feedInfo = $this->searchFieldDatastore->getMissingFieldInfo($feedInfo);
            $listingRequest = $listingRequest->withSearchCriteria($feedInfo->getFields());
        }
        return $listingRequest;
    }
}
