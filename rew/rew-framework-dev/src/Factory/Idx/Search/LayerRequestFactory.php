<?php
namespace REW\Factory\Idx\Search;

use REW\Model\Idx\Map\Bounds\NorthEastBounds;
use REW\Model\Idx\Map\Bounds\SouthWestBounds;
use REW\Model\Idx\Map\Radius\Radius;
use REW\Model\Idx\Search\LayerRequest;
use REW\Model\Idx\Search\Map\Bounds\Bounds;
use REW\Model\Idx\Search\Map\BoundsInterface;
use REW\Factory\IDX\Search\FeedInfoFactoryInterface;
use REW\Datastore\Listing\SearchFieldDatastoreInterface;


class LayerRequestFactory
{

    /** @var string */
    const FLD_MAP_BOUNDS = 'bounds';

    /** @var string */
    const FLD_MAP_RADIUS = 'radius';

    /** @var string */
    const FLD_MAP_POLYGON = 'polygon';

    /** @var string */
    const FLD_LATITUDE = 'latitude';

    /** @var string */
    const FLD_LONGITUDE = 'longitude';

    /** @var string */
    const FLD_ZIP = 'zip';

    /** @var integer */
    const DEFAULT_LIMIT = 100;

    /**
     * @var FeedInfoFactoryInterface
     */
    protected $feedInfoFactory;

    /**
     * @var FieldFactoryInterface
     */
    protected $fieldFactory;


    /**
     * ListingRequestFactory constructor.
     * @param FeedInfoFactoryInterface $feedInfoFactory,
     * @param FieldFactoryInterface $fieldFactory,
     */
    public function __construct(
        FeedInfoFactoryInterface $feedInfoFactory,
        FieldFactoryInterface $fieldFactory
    )
    {
        $this->feedInfoFactory = $feedInfoFactory;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Build a new ListingRequest from an array.
     * @param array $data
     * @return ListingRequest
     */
    public function createFromArray(array $data)
    {
        $requestModel = new LayerRequest();
        $cursor = null;

        $layerRequest = (new LayerRequest())
            ->withType($data['type'])
            ->withFeed($data['feed'])
            ->withLatitude($data['latitude'])
            ->withLongitude($data['longitude']);

        if(!empty($data[self::FLD_ZIP])) {
            $layerRequest = $layerRequest->withZip($data['zip']);
        }

        // Set the map search bounds
        if (!empty($data[self::FLD_MAP_BOUNDS])) {
            $data[self::FLD_MAP_BOUNDS] = json_decode($data[self::FLD_MAP_BOUNDS], true);

            $bounds = new Bounds();

            if (!empty($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE])) {
                $data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE] = explode(',', $data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE]);
                $northEastBounds = new NorthEastBounds();
                if (!empty($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][0])) {
                    $northEastBounds = $northEastBounds->withLatitude($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][0]);
                }
                if (!empty($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][1])) {
                    $northEastBounds = $northEastBounds->withLongitude($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_NE][1]);
                }
                $bounds = $bounds->withNorthEastBounds($northEastBounds);
            }
            if (!empty($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW])) {
                $data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW] = explode(',', $data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW]);
                $southWestBounds = new SouthWestBounds();
                if (!empty($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][0])) {
                    $southWestBounds = $southWestBounds->withLatitude($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][0]);
                }

                if (!empty($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][1])) {
                    $southWestBounds = $southWestBounds->withLongitude($data[self::FLD_MAP_BOUNDS][BoundsInterface::FLD_SW][1]);
                }
                $bounds = $bounds->withSouthWestBounds($southWestBounds);
            }
            $layerRequest = $layerRequest->withBounds($bounds);
        }

        // Map radius criteria
        if (!empty($data[self::FLD_MAP_RADIUS]) && is_array($data[self::FLD_MAP_RADIUS])) {
            $radiusSearch = new \REW\Model\Idx\Search\Map\Radius\Radius();
            foreach ($data[self::FLD_MAP_RADIUS] as $radius) {
                $radius = json_decode($radius, true);
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
            $layerRequest = $layerRequest->withRadius($radiusSearch);
        }

        // Polygon search criteria
        if (!empty($data[self::FLD_MAP_POLYGON])) {
            if (is_array($data[self::FLD_MAP_POLYGON]))
            {
                $polygon = $data[self::FLD_MAP_POLYGON][0];
            }
            $layerRequest = $layerRequest->withPolygon($polygon);
        }
        return $layerRequest;
    }
}
