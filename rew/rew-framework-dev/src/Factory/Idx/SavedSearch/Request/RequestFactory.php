<?php
namespace REW\Factory\Idx\SavedSearch\Request;

use REW\Datastore\Listing\SearchFieldDatastoreInterface;
use REW\Factory\Idx\Search\FeedInfo\FeedInfoFactory;
use REW\Factory\Idx\Search\FieldFactoryInterface;
use REW\Model\Idx\SavedSearch\Request\RequestModel;
use REW\Model\Idx\Map\Bounds\NorthEastBounds;
use REW\Model\Idx\Map\Bounds\SouthWestBounds;
use REW\Model\Idx\Map\Polygon\Polygon;
use REW\Model\Idx\Map\Polygon\PolygonCoordinates;
use REW\Model\Idx\Map\Radius\Radius;
use REW\Model\Idx\Search\Map\Bounds\Bounds;
use REW\Model\Idx\Search\Map\BoundsInterface;

class RequestFactory implements RequestInterface
{
    /** @var string */
    const FLD_MAP_BOUNDS = 'bounds';

    /** @var string */
    const FLD_MAP_RADIUS = 'radius';

    /** @var string */
    const FLD_MAP_POLYGON = 'polygon';

    /**
     * @var FeedInfoFactory
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
     * @param FeedInfoFactory $feedInfoFactory
     * @param FieldFactoryInterface $fieldFactory
     * @param SearchFieldDatastoreInterface $searchFieldDatastore
     */
    public function __construct(
        FeedInfoFactory $feedInfoFactory,
        FieldFactoryInterface $fieldFactory,
        SearchFieldDatastoreInterface $searchFieldDatastore
    ) {
        $this->feedInfoFactory = $feedInfoFactory;
        $this->fieldFactory = $fieldFactory;
        $this->searchFieldDatastore = $searchFieldDatastore;
    }

    /**
     * @param array $data
     * @return \REW\Model\Idx\SavedSearch\Request\RequestInterface
     */
    public function createFromArray(array $data)
    {
        $savedSearchRequestModel = (new RequestModel())
            ->withId($data['id'])
            ->withFeed($data['feed'])
            ->withFrequency($data['frequency'])
            ->withTitle($data['title'])
            ->withUserId($data['userId'])
            ->withAgentId($data['agentId']);

        $criteria = $data['criteria'];

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
            $savedSearchRequestModel = $savedSearchRequestModel->withBounds($bounds);
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
            $savedSearchRequestModel = $savedSearchRequestModel->withRadius($radiusSearch);
            unset($criteria[self::FLD_MAP_RADIUS]);
        }

        // Polygon search criteria
        if (!empty($criteria[self::FLD_MAP_POLYGON]) && is_array($criteria[self::FLD_MAP_POLYGON])) {
            $savedSearchRequestModel = $savedSearchRequestModel->withPolygon(json_encode($criteria[self::FLD_MAP_POLYGON]));
            unset($criteria[self::FLD_MAP_POLYGON]);
        }
        $feedInfo = $this->feedInfoFactory->create($data['feed']);

        // Process out criteria
        foreach ($criteria as $criterion => $value) {
            $feedInfo = $feedInfo->withAdditionalSearchField($this->fieldFactory->createFromArray(['form_field' => $criterion, 'value' => $value]));
        }

        $feedInfo = $this->searchFieldDatastore->getMissingFieldInfo($feedInfo);
        $savedSearchRequestModel = $savedSearchRequestModel->withSearchCriteria($feedInfo->getFields());
        return $savedSearchRequestModel;
    }
}
