<?php

use REW\Core\Interfaces\IDXInterface;

/**
 * IDX
 *
 */
class IDX implements IDXInterface
{

    /**
     * IDX Name
     * @var string
     */
    private $name;

    /**
     * IDX Database
     * @var string
     */
    private $database;

    /**
     * IDX Table Name
     * @var string
     */
    private $table;

    /**
     * IDX Title
     * @var string
     */
    private $title = '';

    /**
     * IDX Geo Table Name
     */
    private $geo_table = '_rewidx_geo';

    /**
     * IDX Image Table Name
     */
    private $image_table = '_rewidx_images';

    /**
     * Listing Fields (Mapped Array)
     * @var array
     */
    private $fields;

    /**
     * Listing Details
     * @var array
     */
    private $details = array();

    /**
     * Page Number
     * @var int
     */
    private $page = 1;

    /**
     * Page Limit
     * @var int
     */
    private $page_limit = 12;

    /**
     * Default Page Limit
     * @var int
     */
    private $page_limit_base = 12;

    /**
     * Sort Field
     * @var string
     */
    private $search_order = 'ListingPrice';

    /**
     * Default Sort Field
     * @var string
     */
    private $search_order_base = 'ListingPrice';

    /**
     * Sort Order
     * @var string
     */
    private $search_sort = 'DESC';

    /**
     * Default Sort Order
     * @var string
     */
    private $search_sort_base = 'DESC';

    /**
     * Last updated key field, used for identifying correct entry in
     * _rewidx_feed. Usually ListingFeed, except on some comminglers.
     * @var string
     */
    private $last_updated_key_field = 'ListingFeed';

    /**
     * Last updated key, used for selecting correct entry in _rewidx_feed
     * @var string
     */
    private $last_updated_key = null;

    /**
     * Search where callback, used to manipulate the WHERE clause for IDX queries
     * @var Callable
     */
    private $search_where_callback = null;

    /**
     * Specifically for IDXs that contain more than one feed.
     * @var boolean
     */
    private $commingled = false;

    /**
     * List of feeds that a commingled IDX is made up of
     * @var string array
     */
    private $feeds = array();

    /**
     * Number of days a listing is considered to be new. This range is also used for price reduction
     * filtering
     * @var int
     */
    private $max_new_listing_age_in_days = 7;

    /**
     * The default map latitude and longitude for this feed
     * @var array
     */
    private $mapCenterpoint = [];

    /**
     * Distance of the earth on the major axis in meters.
     *
     * @var int
     */
    const EARTH_AXIS_DISTANCE = 6378137;

    /**
     * The roundness of the earth squared.
     * This is used to get a more accurate distance between two lat/lng pairs
     *
     * @var int
     */
    const EARTH_SQUARED_ECCENTRICITY = 0.00669438000426;

    public function __construct($config = array())
    {

        // Load Settings
        if (!empty($config['settings'])) {
            $this->loadSettings($config['settings']);
        }

        // Load Fields
        if (!empty($config['fields'])) {
            $this->loadFields($config['fields']);
        }
    }

    /**
     * Reset IDX to Defaults
     *
     * @return void
     */
    public function reset()
    {
        $this->page = 1;
        $this->page_limit_base   = $this->page_limit;
        $this->search_sort_base  = $this->search_sort;
        $this->search_order_base = $this->search_order;
    }

    /**
     * Load IDX Settings from Array
     *
     * @param array $settings    Array of IDX Settings
     * @return void
     */
    public function loadSettings($settings)
    {
        foreach ($settings as $settings => $setting) {
            $this->$settings = $setting;
        }
        $this->reset();
    }

    /**
     * Load IFX Fields from Array
     *
     * @param array $fields    Array of IDX Fields
     * @return void
     */
    public function loadFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Set IDX Details fields
     *
     * @param array $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * Set IDX Map Settings
     *
     * @param array $map
     */
    public function setMapCenterpoint($map)
    {
        $this->mapCenterpoint = $map;
    }

    /**
     * Set Page
     *
     * @param int $page    Page Number
     * @return void
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Set Page Limit
     *
     * @param int $limit    Max # of Results per Page
     * @return void
     */
    public function setPageLimit($limit)
    {
        $this->page_limit_base = $limit;
    }

    /**
     * Set Sort Field
     *
     * @param string $field    IDX Field Name
     * @return void
     */
    public function setSearchOrder($field)
    {
        $this->search_order_base = $field;
    }

    /**
     * Set Sort Order
     *
     * @param string $sort    "ASC" or "DESC"
     * @return void
     */
    public function setSearchSort($sort)
    {
        $this->search_sort_base = $sort;
    }

    /**
     * Get IDX Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get IDX Table Name
     *
     * @param type Blank for the main IDX listings table, geo for geocodes, and
     * image for images.
     * @return string
     */
    public function getTable($type = '')
    {
        $key = $type === '' ? 'table' : $type . '_table';

        return $this->$key;
    }

    /**
     * Get IDX Database Name
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Get IDX Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get IDX Last Updated ListingFeed value
     *
     * @return string
     */
    public function getLastUpdatedKey()
    {
        // last updated key in reality can never be null. If we're using
        // null, use the feed name (default)
        if ($this->last_updated_key === null) {
            return $this->getName();
        }

        return $this->last_updated_key;
    }

    /**
     * Get IDX Last Updated field (ListingFeed or equivalent of)
     *
     * @return string
     */
    public function getLastUpdatedKeyField()
    {
        return $this->last_updated_key_field;
    }

    /**
     * Get the callback used for manipulating the search query
     *
     * @return Callable|null
     */
    public function getSearchWhereCallback()
    {
        return $this->search_where_callback;
    }

    /**
     * Execute the callback used for manipulating the search query. If there is
     * no callback, this will do nothing. The result of the callback will be
     * appended to $search_where.
     *
     * @param array|string $search_where The query to append to
     * @param string The alias to use for the table
     */
    public function executeSearchWhereCallback(&$search_where, $alias = '')
    {
        $callback = $this->getSearchWhereCallback();

        if (is_callable($callback)) {
            $append = $callback($alias, $this, $search_where);
            if (!empty($append)) {
                if (is_array($search_where)) {
                    $search_where[] = $append;
                } else {
                    // Trim String
                    $search_where = rtrim($search_where, "AND ");

                    if (!empty($search_where)) {
                        // And AND if there is other criteria
                        $search_where .= " AND ";
                    }
                    $search_where .= '(' . $append . ')';
                }
            }
        }
    }

    /**
     * Get a URL-Friendly Version of IDX Name
     *
     * @return string
     * @uses Format::slufigy
     */
    public function getLink()
    {
        $link = strtolower($this->getName());
        $link = str_replace('/', 'or', $link);
        $link = str_replace('&amp;', 'and', $link);
        $link = str_replace('&', 'and', $link);
        $link = str_replace('+', 'plus', $link);
        return Format::slugify($link);
    }

    /**
     * Get Current Page Number
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get Current Page Limit
     *
     * @return int
     */
    public function getPageLimit()
    {
        return $this->page_limit_base;
    }

    /**
     * Get Current Sort Field
     *
     * @return string
     */
    public function getSearchOrder()
    {
        return $this->search_order_base;
    }

    /**
     * Get Current Sort Order
     *
     * @return string
     */
    public function getSearchSort()
    {
        return $this->search_sort_base;
    }

    /**
     * Get IDX Fields Array
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get IDX Details Array
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Get IDX Map Settings
     *
     * @return array
     */
    public function getMapCenterpoint()
    {
        return $this->mapCenterpoint;
    }

    /**
     * Get days listings are considered new
     *
     * @return int
     */
    public function getMaxAgeOfNewListingInDays()
    {
        return $this->max_new_listing_age_in_days;
    }

    /**
     * Build SQL Code to Select IDX Fields (`Prefix`.`RightKey` AS `LeftKey`)
     *
     * @param string|null $prefix SQL Pre-Fix (Example: `tableAlias`)
     * @param array|null $fields Only use these fields
     * @return string
     */
    public function selectColumns($prefix = null, array $fields = null)
    {
        $columns = array();
        $sel_cols = $this->getFields();
        foreach ($sel_cols as $name => $idx_field) {
            if (empty($idx_field)) {
                continue;
            }
            if (is_array($fields) && !in_array($name, $fields)) {
                continue;
            }
            $idx_field = (!is_array($idx_field)) ? $idx_field : $idx_field[0];
            $columns[] = $prefix . "`" . $idx_field . "` AS '" . $name . "'";
        }
        $columns = implode(', ', $columns);
        return $columns;
    }

    /**
     * Get the real field name based on its configed name
     * ie: $field = 'price' then you might get 'ListPrice' back
     * array format: array(0 => idx_field, 1 => reference_table_field, 2 => reference_table, 3 => reference_table_field_compare, 4 => reference_table_additional)
     *
     * @param string $field         IDX Field Name
     * @param bool $return_array    If true, return the lookup arrays if available
     * @return mixed
     */
    public function field($field, $return_array = false)
    {
        $idx_field = isset($this->fields[$field]) ? $this->fields[$field] : false;
        if (is_array($idx_field)) {
            if ($return_array) {
                return $idx_field;
            } else {
                return $idx_field[0];
            }
        } else {
            return $idx_field;
        }
        return false;
    }

    /**
     * Build IDX Search Query
     *  - Build a SQL WHERE clause based on $_REQUEST
     *  - Return Readable Search Title
     *  - Return Array of Search Criteria
     *
     * @param IDX $idx                IDX
     * @param Database $db_idx        IDX Database
     * @param string $alias           IDX Table Alias
     * @param array $search_fields    IDX Search Fields Array
     * @return array [search_where => string, search_title => string, search_criteria => array]
     */
    public function buildWhere($idx, $db_idx, $alias = '', $search_fields = array())
    {

        // Include the $_REQUEST modifiers
        include DIR_INCLUDE . 'pages/search_request_mod.php';

        // Select Search Fields
        $search_fields = !empty($search_fields) ? $search_fields : search_fields($idx);

        // Modify $_REQUEST
        $_REQUEST = search_criteria($idx, $_REQUEST);

        // Search by MLS Number (Override)
        if (!empty($_REQUEST['search_mls']) && empty($_REQUEST['snippet'])) {
            // String to Array
            $search_mls = is_array($_REQUEST['search_mls']) ? $_REQUEST['search_mls'] : explode(',', $_REQUEST['search_mls']);
            foreach ($search_mls as $k => $v) {
                $search_mls[$k] = $db_idx->cleanInput($v);
            }

            // Return Array
            return array(
                'search_where'    => "`" . (!empty($alias) ? $alias . '`.`' : '') . $idx->field('ListingMLS') . "` IN ('" . implode("', '", $search_mls) . "')",
                'search_title'    => Lang::write('MLS') . ' Number: ' . Format::htmlspecialchars(implode(', ', $search_mls)),
                'search_criteria' => array('search_mls' => implode(', ', $search_mls))
            );
        }

        // Search Information
        $search_criteria = array();

        // Search Query
        $search_where = '';
        $search_or_where = array();

        // Process Search Fields
        foreach ($search_fields as $searchField) {
            // Skip Empty Values
            if (!isset($_REQUEST[$searchField['form_field']])) {
                continue;
            }

            // Search Value
            $searchValue = $_REQUEST[$searchField['form_field']];

            // IDX Field(s)
            if ($searchField['idx_fields']) {
                $search_db_field = array_map(function ($idx_field) use ($idx, $alias) {
                    return (!empty($alias) ? $alias . '`.`' : '') . $idx->field($idx_field);
                }, $searchField['idx_fields']);
            } else {
                $search_db_field = (!empty($alias) ? $alias . '`.`' : '') . $idx->field($searchField['idx_field']);
            }

            // Group Search Query
            if (!empty($searchField['group'])) {
                if (!isset($search_or_where[$searchField['group']])) {
                    $search_or_where[$searchField['group']] = '';
                }
                if (!empty($searchValue)) {
                    $search_or_where[$searchField['group']] .= $db_idx->buildQueryString($search_db_field, $searchValue, $searchField['match'], 'OR');
                }

            // Search Query
            } elseif (!empty($searchValue)) {
                $search_where .= $db_idx->buildQueryString($search_db_field, $searchValue, $searchField['match'], 'AND');
            }

            // Search Criteria String
            if (!empty($searchValue)) {
                $search_value = Format::trim($searchValue);
                if (!empty($search_value)) {
                    $search_value = (is_array($searchValue)) ? implode(', ', array_filter($searchValue)) : $searchValue;
                    $search_criteria[$searchField['form_field']] = $search_value;
                }
            }
        }

        // Search Groups
        if (!empty($search_or_where)) {
            foreach ($search_or_where as $search_or_group) {
                if (!empty($search_or_group)) {
                    $search_where .= '(' . rtrim($search_or_group, 'OR ') . ') AND ';
                }
            }
        }

        $idx->executeSearchWhereCallback($search_where, $alias);

        // Trim String
        $search_where = rtrim($search_where, ' AND ');

        // Build Search Title
        if(empty($_REQUEST['search_title'])) {
            $search_title = $this->buildSearchTitle($search_criteria);
        } else {
            $search_title = $_REQUEST['search_title'];
        }

        // Return Array
        return array(
            'search_where' => $search_where,
            'search_title' => $search_title,
            'search_criteria' => $search_criteria
        );
    }

    /**
     * Generate Search Title from Criteria
     *
     * @param array $search_criteria
     * @return string
     */
    public function buildSearchTitle($search_criteria)
    {

        // Gather Locations
        $msgloc  = array();
        if (!empty($search_criteria['search_state'])) {
            $msgloc = array_merge($msgloc, is_array($search_criteria['search_state'])        ? $search_criteria['search_state']       : explode(',', ucwords(strtolower($search_criteria['search_state']))));
        }
        if (!empty($search_criteria['search_city'])) {
            $msgloc = array_merge($msgloc, is_array($search_criteria['search_city'])         ? $search_criteria['search_city']        : explode(',', ucwords(strtolower($search_criteria['search_city']))));
        }
        if (!empty($search_criteria['search_zip'])) {
            $msgloc = array_merge($msgloc, is_array($search_criteria['search_zip'])          ? $search_criteria['search_zip']         : explode(',', $search_criteria['search_zip']));
        }
        if (!empty($search_criteria['search_subdivision'])) {
            $msgloc = array_merge($msgloc, is_array($search_criteria['search_subdivision'])  ? $search_criteria['search_subdivision'] : explode(',', ucwords(strtolower($search_criteria['search_subdivision']))));
        }
        if (!empty($search_criteria['search_area'])) {
            $msgloc = array_merge($msgloc, is_array($search_criteria['search_area'])         ? $search_criteria['search_area']        : explode(',', ucwords(strtolower($search_criteria['search_area']))));
        }

        // Polygon / Radius Search
        if (!empty($_REQUEST['map']['polygon']) || !empty($_REQUEST['map']['radius'])) {
            $msgloc = array();
            if (!empty($_REQUEST['map']['polygon']) && !empty($_REQUEST['map']['radius'])) {
                $msgloc[] = 'Polygon & Radius';
            } else if (!empty($_REQUEST['map']['polygon'])) {
                $msgloc[] = 'Polygon';
            } else if (!empty($_REQUEST['map']['radius'])) {
                $msgloc[] = 'Radius';
            }
        }

        // Bounds Search
        if (!empty($_REQUEST['map']['bounds'])) {
            $msgloc = array('Map Bounds');
        }

        // Search Location
        if (!empty($_REQUEST['search_location'])) {
            $msgloc = is_array($_REQUEST['search_location']) ? array(implode(', ', $_REQUEST['search_location'])) : array($_REQUEST['search_location']);
        }

        // Location Message
        if (!empty($msgloc)) {
            if (count($msgloc) <= 1) {
                $msgloc = implode(' & ', $msgloc);
            } else {
                $msgloc = implode(', ', array_slice($msgloc, 0, 1)) . ' & more...';
            }
        } else {
            $msgloc = 'All Cities';
        }

        // Searching Property Types
        $msgtype = 'All Property Types';
        $msgtype = !empty($search_criteria['search_type'])    ? $search_criteria['search_type']    : $msgtype;
        $msgtype = !empty($search_criteria['search_subtype']) ? $search_criteria['search_subtype'] : $msgtype;

        // Searching all property types
        if (is_array($_REQUEST['search_type']) && in_array('', $_REQUEST['search_type'])) {
            $msgtype = 'All Property Types';
        }

        // Searching Price Range
        $ranges = array(array('minimum_price', 'maximum_price'), array('minimum_rent', 'maximum_rent'));
        foreach ($ranges as $range) {
            list ($min, $max) = $range;
            if (!empty($search_criteria[$min]) && !empty($search_criteria[$max])) {
                $msgpricerange = 'from $' . number_format($search_criteria[$min]) . ' to $' . number_format($search_criteria[$max]);
            } else {
                if (!empty($search_criteria[$min])) {
                    $msgpricerange = 'over $' . number_format($search_criteria[$min]);
                }
                if (!empty($search_criteria[$max])) {
                    $msgpricerange = 'under $' . number_format($search_criteria[$max]);
                }
            }
        }

        // Return Search title
        return Format::htmlspecialchars(trim($msgtype . ' in ' . $msgloc . ' ' . $msgpricerange));
    }

    /**
     * Build Search Query for Map Bounds
     * @param string $ne
     * @param string $sw
     * @param array $search_where
     * @param string $col_latitude
     * @param string $col_longitude
     * @return array $bounds
     */
    public function buildWhereBounds($ne, $sw, &$search_where = array(), $col_latitude = 'Latitude', $col_longitude = 'Longitude')
    {

        // Search by Bounds
        if (!empty($ne) && !empty($sw)) {
            // Map Bounds
            $bounds = array();
            list ($bounds['ne']['lat'], $bounds['ne']['lng']) = explode(',', $ne);
            list ($bounds['sw']['lat'], $bounds['sw']['lng']) = explode(',', $sw);

            // Search by Bounding Box (SW/NE Bounds)
            if ($bounds['ne']['lng'] > $bounds['sw']['lng']) {
                $search_where[]  = "(" . $col_longitude . " > " . floatval($bounds['sw']['lng']) . " AND " . $col_longitude . " < " . floatval($bounds['ne']['lng']) . ")"
                    . " AND (" . $col_latitude . " <= " . floatval($bounds['ne']['lat']) . " AND " . $col_latitude . " >= " . floatval($bounds['sw']['lat']) . ")";

            // Split over Meridian
            } else {
                $search_where[]  = "(" . $col_longitude . " >= " . floatval($bounds['sw']['lng']) . " OR " . $col_longitude . " <= " . floatval($bounds['ne']['lng']) . ")"
                    . " AND (" . $col_latitude . " <= " . floatval($bounds['ne']['lat']) . " AND " . $col_latitude . " >= " . floatval($bounds['sw']['lat']) . ")";
            }

            // Return Bounds
            return $bounds;
        }

        // No Bounds
        return false;
    }

    /**
     * Build Search Query for Radius Searches
     * @param string $radiuses
     * @param array $search_where
     * @param string $col_latitude
     * @param string $col_longitude
     * @return array $radiuses
     */
    public function buildWhereRadius($radiuses, &$search_where = array(), $col_latitude = 'Latitude', $col_longitude = 'Longitude')
    {

        // Search Group
        $search_group = array();
        $search_square_group = array();

        // Search Radiuses
        if (!empty($radiuses) && is_string($radiuses)) {
            $radiuses = json_decode($radiuses, true); // Parse as JSON Array
            if (is_array($radiuses) && !empty($radiuses)) {
                foreach ($radiuses as $radius) {
                    list ($latitude, $longitude, $radius) = explode(',', $radius);
                    if (!empty($latitude) && !empty($longitude) && !empty($radius)) {

                        $search_group[] = "(((Acos("
                            . "Sin((" . floatval($latitude) . " * Pi() / 180)) * "
                            . "Sin((" . $col_latitude . " * Pi() / 180)) + "
                            . "Cos((" . floatval($latitude) . " * Pi() / 180)) * "
                            . "Cos((" . $col_latitude . " * Pi() / 180)) * "
                            . "Cos(((" . floatval($longitude) . " - " . $col_longitude . ") * Pi() / 180))"
                            . ")) * 180 / Pi()) * 60 * 1.1515) <= " . floatval($radius);

                        // Radius search on it's own is slow, beacuse it is computing math without an index, we will build a square around the radiuses
                        $square = $this->buildGeospaceSquare(floatval($latitude), floatval($longitude), floatval($radius));
                        if (!empty($square)) {
                            $search_square_group[] = '('. $col_latitude . ' BETWEEN \'' . $square['south'] . '\' AND \'' . $square['north'] . '\' '
                                . 'AND ' . $col_longitude . ' BETWEEN \'' . $square['west'] . '\' AND \'' . $square['east'] . '\')';
                        }

                    }
                }
            } else {
                return false;
            }
        }


        // Add to Search Criteria
        if (!empty($search_square_group)) {
            $search_where[] = '(' . implode(' OR ', $search_square_group) . ')';
        }

        // Add to Search Criteria
        if (!empty($search_group)) {
            $search_where[] = "(" . implode(" OR ", $search_group) . ")";
        }

        // Return Radius Searches
        return $radiuses;
    }

    /**
     * Build Search Query for Polygon Searches
     * @param string $polygons
     * @param array $search_where
     * @param array $search_having
     * @param string $col_point
     * @return array $polygons
     */
    public function buildWherePolygons($polygons, &$search_where = array(), &$search_having = array(), $col_point = 'Point')
    {

        // Search Group
        $search_group = array();

        // Check Polygons
        $polys = $polygons;
        $polygons = array();
        if (!empty($polys) && is_string($polys)) {
            $poly = $polys;
            $polys = json_decode($polys, true); // Parse as JSON Array
            if (json_last_error() != JSON_ERROR_NONE) {
                $polys = array($poly); // Backwards Compatibility: Not JSON Array, Single Polygon Only
            }
            if (is_array($polys) && !empty($polys)) {
                foreach ($polys as $poly) {
                    // Safe Polygon
                    $points = explode(',', $poly);
                    $polygon = array();
                    foreach ($points as $point) {
                        list ($lat, $lng) = explode(' ', $point);
                        if (is_numeric($lat) && is_numeric($lng)) {
                            $polygon[] = implode(' ', array($lat, $lng));
                        } else {
                            return false;
                        }
                    }
                    // Search Polygon
                    $polygons[] = implode(',', $polygon);
                }
            }
        }

        // Search Polygons
        if (is_array($polygons) && !empty($polygons)) {
            foreach ($polygons as $polygon) {
                if (!empty($polygon)) {
                    $search_group[] = "ST_INTERSECTS(" . $col_point . ", GeomFromText('MULTIPOLYGON(((" . $polygon . ")))'))";
                }
            }
        } else {
            return false;
        }

        // Add to Search Criteria
        if (!empty($search_group)) {
            $search_where[] = "(" . implode(" OR ", $search_group) . ")";
        }

        // Return Polygon Searches
        return $polygons;
    }

    /**
     * Build a rectangular 'near square' shape around a lat/lng point from the distance (Possible Radius of a circle)
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $distance Distance from the point in meters
     * @return array:number
     */
    public function buildGeospaceSquare($latitude, $longitude, $distance = 1000)
    {

        // Initialize the return square array
        $square= array();

        // We should almost always be in the north west hemisphere, but just in case we will check if we are positive or negative
        $lat_pos = true;
        $lng_pos = true;
        // Lat is in south hemisphere
        if ($latitude < 0) {
            $lat_pos = false;
        }
        // Lng is in west hemisphere
        if ($longitude < 0) {
            $lng_pos = false;
        }

        // Convert our lat to radians
        $lat = deg2rad($latitude);

        // Calculate $k, which is used in both calculations
        $k = pow(sqrt(1 - self::EARTH_SQUARED_ECCENTRICITY * pow(sin($lat), 2)), 3);

        // Calculate the meters
        $r = self::EARTH_AXIS_DISTANCE * cos($lat) / $k;
        $s = self::EARTH_AXIS_DISTANCE * (1 - self::EARTH_SQUARED_ECCENTRICITY) / $k;

        // Calculate the offsets of the meters in degrees
        $df = rad2deg($distance / $r);
        $dl = rad2deg($distance / $s);

        // Calculate the Latitude square, we use the lat_pos to determine the hemisphere
        $square['north'] = ($lat_pos ? $latitude + $dl : $latitude - $dl);
        $square['south'] = ($lat_pos ? $latitude - $dl : $latitude + $dl);

        // Calculate the Longitude square, we use the lng_pos to determine the hemisphere
        $square['east'] = ($lng_pos ? $longitude - $dl : $longitude + $dl);
        $square['west'] = ($lng_pos ? $longitude + $dl : $longitude - $dl);

        // Return the square
        return $square;
    }

    /**
     * Returns an array of feeds (strings) that the IDX contains
     * This method is particularly useful for commingled feeds.
     * @return array
     */
    public function getFeeds()
    {

        if (!empty($this->feeds)) {
            return $this->feeds;
        } else {
            return array($this->name);
        }
    }

    /**
     * Searches the feed to see whether the IDX contains the requested feed
     * This method is particularly useful for commingled feeds.
     * @param string $feed
     * @return boolean
     */
    public function containsFeed($feed)
    {
        return in_array($feed, $this->getFeeds());
    }

    public function isCommingled()
    {
        return (boolean) $this->commingled;
    }

    /**
     * True if this feed has mapping data, false if not.
     * @return bool
     */
    public function hasMappingData()
    {
        return (boolean) $this->geo_table;
    }
}
