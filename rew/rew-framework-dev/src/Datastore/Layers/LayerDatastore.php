<?php
namespace REW\Datastore\Layers;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Factory\Idx\Search\LayerRequestFactory;
use REW\Model\Idx\Search\LayerRequest;
use REW\Model\Idx\Search\LayerResults;
use REW\Model\Idx\Search\LayerCountResult;


/**
 * Class SearchDatastore
 * @package REW\Datastore\Listing
 * @todo make an interface for this to conform to (for DI reasons)
 * @todo add means of getting total count of listings matching criteria (exclude pagination stuff).
 */
class LayerDatastore
{

    const SCHOOLS_COLUMNS = "`INSTITUTION_NAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `LOCATION_ADDRESS` AS `address`, `LOCATION_CITY` AS `city`, `ZIP` AS `zip`, `STATE_ABBREV` AS `state`, `GRADE_SPAN_CODE_BLDG_TEXT` AS `grades`, `WEBSITE_URL` AS `url`";

    const SHOPPING_COLUMNS = "`BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip`, `STATENAME` AS `state`, `CATEGORY` AS `category`";

    const AMENITIES_COLUMNS = "`BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip`, `STATENAME` AS `state`, `CATEGORY` AS `category`, `PHONE` AS `phone`, `INDUSTRY` AS `industry`";

    const GENERIC_COLUMNS = "`BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip`, `STATENAME` AS `state`";

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var LayerResultFactory
     */
    protected $layerRequestFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param IDXFactoryInterface $idxFactory
     * @param LayerRequestFactory $layerResultFactory
     * @param SettingsInterface $settings
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        IDXFactoryInterface $idxFactory,
        LayerRequestFactory $layerRequestFactory,
        SettingsInterface $settings
    ) {
        $this->dbFactory = $dbFactory;
        $this->idxFactory = $idxFactory;
        $this->layerRequestFactory = $layerRequestFactory;
        $this->settings = $settings;
    }

    public function getCommunityInfo(LayerRequest $layerRequest)
    {
        $db_onboard = $this->dbFactory->get('onboard');
        $zip = $layerRequest->getZip();
        $query = "SELECT `t1`.*, `t2`.`id`, `t2`.`lft`, `t2`.`rgt` FROM `onboard_community_profile` `t1` LEFT JOIN `locations` `t2` ON `t1`.`OB_ID` = `t2`.`OB_ID` WHERE `t1`.`ZIP` = :zip AND `t1`.`ZIP` != 0 AND `t1`.`ZIP` != ''";
        $params = [
            'zip' => $zip
        ];


        // Select Location
        $location = $db_onboard->prepare($query);
        $location->execute($params);
        $location = $location->fetch();


        // Statistics
        $statistics   = [];

        if (!empty($location['COMMCHAR'])) {
            $statistics['Community Characteristics for Zip Code ' . $zip] = [
                ['title' => 'Community Characteristics',  'value' => $location['COMMCHAR']]
            ];
        }

        $title = 'Population Statistics' . (empty($location['COMMCHAR']) ? (' for Zip Code ' . $zip) : '');
        $statistics[$title] = [
            ['title' => 'Population',           'value' => number_format($location['POPCY'])],
            ['title' => 'Population Male',      'value' => number_format($location['POPMALE'])],
            ['title' => 'Population Female',    'value' => number_format($location['POPFEMALE'])],
            ['title' => 'Population Density',   'value' => number_format($location['POPDNSTY'])],
            ['title' => 'Median Age',           'value' => number_format($location['MEDIANAGE'])],
        ];

        $statistics['Financial Statistics'] = [
            ['title' => 'Average Household Net Worth',   'value' => '$' . number_format($location['WRHCYAVEHH'])],
            ['title' => 'Median Household Income',       'value' => '$' . number_format($location['INCCYMEDD'])],
            ['title' => 'Average Household Income',      'value' => '$' . number_format($location['INCCYAVEHH'])],
        ];

        $rate = ($location['CRMCYTOTC'] - 100);
        $rate = (($rate < 0) ? ($rate * -1) . '% Below' : $rate . '% Above') .  ' National Average';

        $statistics['Crime Rate Information'] = [
            ['title' => 'Total Crime Risk', 'value' => $rate],
        ];

        $statistics['Weather & Climate'] = [
            ['title' => 'Average January High Temperature', 'value' => $location['TMPMAXJAN'] . ' &deg;F'],
            ['title' => 'Average January Low Temperature',  'value' => $location['TMPMINJAN'] . ' &deg;F'],
            ['title' => 'Average July High Temperature',    'value' => $location['TMPMAXJUL'] . ' &deg;F'],
            ['title' => 'Average July Low Temperature',     'value' => $location['TMPMINJUL'] . ' &deg;F'],
            ['title' => 'Annual Precipitation',             'value' => $location['PRECIPANN'] . '&Prime;'],
        ];

        $statistics['Nearby Locations'] = [
            ['title' => 'Closest Major Airport',           'value' => ucwords(strtolower($location['AIRPORT'])) . ' (' . $location['AIRPORTDIST'] . ' miles)'],
            ['title' => 'Closest 2-Year Public College',   'value' => ucwords(strtolower($location['JC'])) . ' (' . $location['JCDIST'] . ' miles)'],
            ['title' => 'Closest 4-Year Public College',   'value' => ucwords(strtolower($location['4YR'])) . ' (' . $location['4YRDIST'] . ' miles)'],
            ['title' => 'Closest Major Sports Team',       'value' => ucwords(strtolower($location['TEAM'])) . ' (' . $location['TEAMDIST'] . ' miles)'],
        ];

        return $statistics;
        return (new LayerResults())->withCommunityResult($statistics);
    }

    /**
     * @param LayerRequest $layerRequest
     * @return \REW\Model\Idx\Search\LayerResults
     * @throws \PDOException on database error
     * @throws \Exception on IDX settings error
     */
    public function getLayers(LayerRequest $layerRequest)
    {
        $db_onboard = $this->dbFactory->get('onboard');

        $query = $this->getQuery($layerRequest);
        $stmt = $db_onboard->prepare($query);

        $stmt->execute();
        $layers = $stmt->fetchAll();

        $layerResults = [];
        if ($layers) {
            foreach ($layers as $row) {

                // Tooltip HTML
                $tooltip = '<div class="popover">'
                    . '<header class="title">'
                    . '<strong>' . ucwords(strtolower($row['title'])) . '</strong>'
                    . '<a href="javascript:void(0);" class="action-close hidden">&times;</a>'
                    . '</header>'
                    . '<div class="body">'
                    . '<p>' . ucwords(strtolower($row['address'])) . ', ' . ucwords(strtolower($row['city'])) . ' ' . $row['zip'] . '</p>'
                    . '</div>'
                    . '<div class="tail"></div>'
                    . '</div>';

                // Add to Collection
                $layerResults[] = [
                    'tooltip'   => $tooltip,
                    'lat'       => $row['latitude'],
                    'lng'       => $row['longitude'],
                    'title'     => $row['title'],
                    'address'   => $row['address'],
                    'city'      => $row['city'],
                    'state'     => $row['state'],
                    'zip'       => $row['zip'],
                    'distance'  => $this->getDistance($row, ['latitude' => $layerRequest->getLatitude(), 'longitude' => $layerRequest->getLongitude()]),
                    'category'  => ($row['category'] ? $row['category'] : null),
                    'grades'    => ($row['grades'] ? $row['grades'] : null),
                    'url'       => ($row['url'] ? $row['url'] : null)
                ];
            }
        }
        return (new LayerResults())->withLayerResults($layerResults);
    }

    /**
     * Get Where Query and Parameters from data
     * @param IDXInterface $idx
     * @param LayerRequest $layerRequest
     * @return [array, array, array]
     */
    protected function getWhereQuery(IDXInterface $idx, LayerRequest $layerRequest)
    {
        $whereParts = [];

        // This is passed into buildWhereBounds, buildWhereRadius, and buildWherePolygons.
        $whereHaving = [];

        // Check for map bounds
        if (!empty($layerRequest->getBounds())) {
            $bounds = $layerRequest->getBounds();
            $idx->buildWhereBounds((string) $bounds->getNorthEastBounds(), (string) $bounds->getSouthWestBounds(), $whereParts);
        }

        // Check for radius search
        if (!empty($layerRequest->getRadius())) {
            $radius = $layerRequest->getRadius();
            $idx->buildWhereRadius(json_encode($radius), $whereParts);
        }

        // Check for polygon search
        if (!empty($layerRequest->getPolygon())) {
            $polygon = $layerRequest->getPolygon();
            $idx->buildWherePolygons($polygon, $whereParts, $whereHaving, 't2.Point');
        }
        return [$whereParts, $whereHaving];
    }


    /**
     * Calculate Distance Between Points
     * @param array[latitude,longitude] $point1
     * @param array[latitude,longitude] $point2
     * @return int
     */
    protected function getDistance($point1, $point2)
    {
        $radius      = 3958;      // Earth's radius (miles)
        $pi          = 3.1415926;
        $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)
        $distance = ($radius * $pi * sqrt(
                ($point1['latitude'] - $point2['latitude'])
                * ($point1['latitude'] - $point2['latitude'])
                + cos($point1['LATITUDE'] / $deg_per_rad)  // Convert these to
                * cos($point2['LATITUDE'] / $deg_per_rad)  // radians for cos()
                * ($point1['longitude'] - $point2['longitude'])
                * ($point1['longitude'] - $point2['longitude'])
            ) / 180);
        return $distance;  // Returned using the units used for $radius.
    }

    protected function getQuery(LayerRequest $layerRequest)
    {
        // Onboard Database
        $onBoardDatabase = 'onboard';
        // Onboard Tables
        $tableSchools = sprintf('onboard_schools_%s', $this->settings->SETTINGS['map_state']);
        $tableAmenities = sprintf('onboard_amenities_%s', $this->settings->SETTINGS['map_state']);
        $idx = $this->idxFactory->getIdx($layerRequest->getFeed());

        // Search Limit
        $search_limit = ' LIMIT 100';

        list($whereParts, $whereHaving) = $this->getWhereQuery($idx, $layerRequest);

        // Query String
        $search_where = (!empty($whereParts) ? implode(' AND ', $whereParts) : '');

        // Select Query
        switch ($layerRequest->getType()) {
            case 'schools':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::SCHOOLS_COLUMNS,
                    $onBoardDatabase,
                    $tableSchools,
                    (!empty($search_where) ? " WHERE " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'hospitals':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::GENERIC_COLUMNS,
                    $onBoardDatabase,
                    $tableAmenities,
                    "WHERE `CATEGORY` = 'HEALTH CARE SERVICES' AND `LINEOFBUS` = 'HOSPITALS'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'airports':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::GENERIC_COLUMNS,
                    $onBoardDatabase,
                    $tableAmenities,
                    "WHERE `CATEGORY` = 'TRAVEL' AND `LINEOFBUS` = 'AIRPORTS'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'parks':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::GENERIC_COLUMNS,
                    $onBoardDatabase,
                    $tableAmenities,
                    "WHERE `CATEGORY` = 'ATTRACTIONS - RECREATION ' AND `LINEOFBUS` = 'OUTDOOR ACTIVITES' AND `INDUSTRY` = 'PARKS'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'golf-courses':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::GENERIC_COLUMNS,
                    $onBoardDatabase,
                    $tableAmenities,
                    "WHERE `CATEGORY` = 'ATTRACTIONS - RECREATION' AND `LINEOFBUS` = 'GOLF'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'churches':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::GENERIC_COLUMNS,
                    $onBoardDatabase,
                    $tableAmenities,
                    "WHERE `CATEGORY` = 'ORGANIZATIONS - ASSOCIATIONS' AND `LINEOFBUS` = 'PLACE OF WORSHIP' AND `INDUSTRY` = 'CHURCHES'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'shopping':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::SHOPPING_COLUMNS,
                    $onBoardDatabase,
                    $tableSchools,
                    "WHERE `CATEGORY` = 'SHOPPING' AND `LINEOFBUS` = 'SHOPPING CENTERS AND MALLS'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
            case 'amenities':
                return sprintf("SELECT SQL_NO_CACHE %s FROM `%s`.`%s` %s %s",
                    static::AMENITIES_COLUMNS,
                    $onBoardDatabase,
                    $tableAmenities,
                    "WHERE `PRIMARY` = 'PRIMARY'" . (!empty($search_where) ? " AND " . $search_where : ''),
                    (!empty($search_limit) ? $search_limit : '')
                );
                break;
        }
    }
}
