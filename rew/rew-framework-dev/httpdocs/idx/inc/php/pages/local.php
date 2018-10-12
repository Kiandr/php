<?php

// Get Requested Listing
$listing = requested_listing();

// Module Not Enabled, Re-Direct
if (empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD']) || empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    header("Location: " . $listing['url_details'], true, 301);
    exit;
}

// Require Listing
if (!empty($listing)) {
    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_ONBOARD_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_ONBOARD_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_ONBOARD_META_DESCRIPTION', $listing);

    // List Tracking
    if (!empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
        IDX_COMPLIANCE::trackPageLoad($page, $listing);
    }
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}

// Current View
$view = isset($_GET['view']) ? $_GET['view'] : false;
if (!empty($view) && !in_array($view, array('nearby-amenities', 'nearby-schools', 'community-information'))) {
    $view = 'nearby-amenities';
}

// Onboard Tables
define('ONBOARD_TABLE_COMMUNITIES', 'onboard_community_profile');

$db_onboard = DB::get('onboard');

// Select Location
$location = $db_onboard->prepare("SELECT `t1`.*, `t2`.`id`, `t2`.`lft`, `t2`.`rgt` FROM `" . ONBOARD_TABLE_COMMUNITIES . "` `t1` LEFT JOIN `locations` `t2` ON `t1`.`OB_ID` = `t2`.`OB_ID` WHERE `t1`.`ZIP` = :zip AND `t1`.`ZIP` != 0 AND `t1`.`ZIP` != ''");
$location->execute(array('zip' => $listing['AddressZipCode']));
$location = $location->fetch();

if (!empty($location)) {
    // States - http://www.itl.nist.gov/fipspubs/fip5-2.htm
    $states = array();
    $states['01'] = array('state' => 'Alabama', 'abbr' => 'AL');
    $states['02'] = array('state' => 'Alaska', 'abbr' => 'AK');
    $states['04'] = array('state' => 'Arizona', 'abbr' => 'AZ');
    $states['05'] = array('state' => 'Arkansas', 'abbr' => 'AR');
    $states['06'] = array('state' => 'California', 'abbr' => 'CA');
    $states['08'] = array('state' => 'Colorado', 'abbr' => 'CO');
    $states['09'] = array('state' => 'Connecticut', 'abbr' => 'CT');
    $states['10'] = array('state' => 'Delaware', 'abbr' => 'DE');
    $states['11'] = array('state' => 'District of Columbia', 'abbr' => 'DC');
    $states['12'] = array('state' => 'Florida', 'abbr' => 'FL');
    $states['13'] = array('state' => 'Georgia', 'abbr' => 'GA');
    $states['15'] = array('state' => 'Hawaii', 'abbr' => 'HI');
    $states['16'] = array('state' => 'Idaho', 'abbr' => 'ID');
    $states['17'] = array('state' => 'Illinois', 'abbr' => 'IL');
    $states['18'] = array('state' => 'Indiana', 'abbr' => 'IN');
    $states['19'] = array('state' => 'Iowa', 'abbr' => 'IA');
    $states['20'] = array('state' => 'Kansas', 'abbr' => 'KS');
    $states['21'] = array('state' => 'Kentucky', 'abbr' => 'KY');
    $states['22'] = array('state' => 'Louisiana', 'abbr' => 'LA');
    $states['23'] = array('state' => 'Maine', 'abbr' => 'ME');
    $states['24'] = array('state' => 'Maryland', 'abbr' => 'MD');
    $states['25'] = array('state' => 'Massachusetts', 'abbr' => 'MA');
    $states['26'] = array('state' => 'Michigan', 'abbr' => 'MI');
    $states['27'] = array('state' => 'Minnesota', 'abbr' => 'MN');
    $states['28'] = array('state' => 'Mississippi', 'abbr' => 'MS');
    $states['28'] = array('state' => 'Mississippi', 'abbr' => 'MS');
    $states['29'] = array('state' => 'Missouri', 'abbr' => 'MO');
    $states['30'] = array('state' => 'Montana', 'abbr' => 'MT');
    $states['31'] = array('state' => 'Nebraska', 'abbr' => 'NE');
    $states['32'] = array('state' => 'Nevada', 'abbr' => 'NV');
    $states['33'] = array('state' => 'New Hampshire', 'abbr' => 'NH');
    $states['34'] = array('state' => 'New Jersey', 'abbr' => 'NJ');
    $states['35'] = array('state' => 'New Mexico', 'abbr' => 'NM');
    $states['36'] = array('state' => 'New York', 'abbr' => 'NY');
    $states['37'] = array('state' => 'North Carolina', 'abbr' => 'NC');
    $states['38'] = array('state' => 'North Dakota', 'abbr' => 'ND');
    $states['39'] = array('state' => 'Ohio', 'abbr' => 'OH');
    $states['40'] = array('state' => 'Oklahoma', 'abbr' => 'OK');
    $states['41'] = array('state' => 'Oregon', 'abbr' => 'OR');
    $states['42'] = array('state' => 'Pennsylvania', 'abbr' => 'PA');
    $states['44'] = array('state' => 'Rhode Island', 'abbr' => 'RI');
    $states['45'] = array('state' => 'South Carolina', 'abbr' => 'SC');
    $states['46'] = array('state' => 'South Dakota', 'abbr' => 'SD');
    $states['47'] = array('state' => 'Tennessee', 'abbr' => 'TN');
    $states['48'] = array('state' => 'Texas', 'abbr' => 'TX');
    $states['49'] = array('state' => 'Utah', 'abbr' => 'UT');
    $states['50'] = array('state' => 'Vermont', 'abbr' => 'VT');
    $states['51'] = array('state' => 'Virginia', 'abbr' => 'VA');
    $states['53'] = array('state' => 'Washington', 'abbr' => 'WA');
    $states['54'] = array('state' => 'West Virginia', 'abbr' => 'WV');
    $states['55'] = array('state' => 'Wisconsin', 'abbr' => 'WI');
    $states['56'] = array('state' => 'Wyoming', 'abbr' => 'WY');

    // State Abbreviation
    $state = str_pad(intval($location['STATE']), 2, "0", STR_PAD_LEFT);
    $state = $states[$state]['abbr'];

    // Amenity Information
    define('ONBOARD_TABLE_AMENITIES', 'onboard_amenities_' . $state);

    // School Information
    define('ONBOARD_TABLE_SCHOOLS', 'onboard_schools_' . $state);

    /**
     * Neighborhood Information
     */
    if ($view == 'community-information') {
        // Statistics
        $statistics   = array();

        if (!empty($location['COMMCHAR'])) {
            $statistics[] = array('title' => 'Community Characteristics for Zip Code ' . $location['ZIP'], 'statistics' => array(
                array('title' => 'Community Characteristics',  'value' => $location['COMMCHAR'])
            ));
        }

        $statistics[] = array('title' => 'Population Statistics' . (empty($location['COMMCHAR']) ? ' for Zip Code ' . $location['ZIP'] : ''), 'statistics' => array(
            array('title' => 'Population',                     'value' => number_format($location['POPCY'])),
            array('title' => 'Population Male',               'value' => number_format($location['POPMALE'])),
            array('title' => 'Population Female',               'value' => number_format($location['POPFEMALE'])),
            array('title' => 'Population Density',             'value' => number_format($location['POPDNSTY'])),
            array('title' => 'Median Age',                     'value' => number_format($location['MEDIANAGE'])),
        ));

        $statistics[] = array('title' => 'Financial Statistics', 'statistics' => array(
            array('title' => 'Average Household Net Worth',   'value' => '$' . number_format($location['WRHCYAVEHH'])),
            array('title' => 'Median Household Income',       'value' => '$' . number_format($location['INCCYMEDD'])),
            array('title' => 'Average Household Income',         'value' => '$' . number_format($location['INCCYAVEHH'])),
        ));

        $rate = ($location['CRMCYTOTC'] - 100);
        $rate = (($rate < 0) ? ($rate * -1) . '% Below' : $rate . '% Above') .  ' National Average';

        $statistics[] = array('title' => 'Crime Rate Information', 'statistics' => array(
            array('title' => 'Total Crime Risk',                 'value' => $rate),
        ));

        $statistics[] = array('title' => 'Weather & Climate', 'statistics' => array(
            array('title' => 'Average January High Temperature', 'value' => $location['TMPMAXJAN'] . ' &deg;F'),
            array('title' => 'Average January Low Temperature',  'value' => $location['TMPMINJAN'] . ' &deg;F'),
            array('title' => 'Average July High Temperature',   'value' => $location['TMPMAXJUL'] . ' &deg;F'),
            array('title' => 'Average July Low Temperature',     'value' => $location['TMPMINJUL'] . ' &deg;F'),
            array('title' => 'Annual Precipitation',             'value' => $location['PRECIPANN'] . '&Prime;'),
        ));

        $statistics[] = array('title' => 'Nearby Locations', 'statistics' => array(
            array('title' => 'Closest Major Airport',           'value' => ucwords(strtolower($location['AIRPORT'])) . ' (' . $location['AIRPORTDIST'] . ' miles)'),
            array('title' => 'Closest 2-Year Public College',   'value' => ucwords(strtolower($location['JC'])) . ' (' . $location['JCDIST'] . ' miles)'),
            array('title' => 'Closest 4-Year Public College',   'value' => ucwords(strtolower($location['4YR'])) . ' (' . $location['4YRDIST'] . ' miles)'),
            array('title' => 'Closest Major Sports Team',       'value' => ucwords(strtolower($location['TEAM'])) . ' (' . $location['TEAMDIST'] . ' miles)'),
        ));
    }

    /**
     * Build Bound Box for Nearby Schools & Nearby Amenities
     */
    if (($view == 'nearby-schools') || ($view == 'nearby-amenities')) {
        // Co-Ordinates
        $latitude  = !empty($listing['Latitude'])  ? $listing['Latitude']  : $location['LATITUDE'];
        $longitude = !empty($listing['Longitude']) ? $listing['Longitude'] : $location['LONGITUDE'];

        // Build Bounding Box (5 Miles from Point)
        $miles   = 2.5;
        $meters = $miles * 1.60934 * 1000;
        $bounds = IDX::buildGeospaceSquare($latitude, $longitude, $meters);
    }

    /**
     * View Nearby Schools
     */
    if ($view == 'nearby-schools') {
        // Nearby Schools
        $query = $db_onboard->prepare("SELECT SQL_NO_CACHE * FROM `" . ONBOARD_TABLE_SCHOOLS . "` "
            . (!empty($bounds) ? ' WHERE `LATITUDE` BETWEEN :south AND :north AND `LONGITUDE` BETWEEN :west AND :east;' : ''));
        (!empty($bounds) ? $query->execute($bounds) : $query->execute());
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // Collection
        $nearby_schools = array();
        $distances = array();

        // Count
        $index = 0;

        // Loop through Results
        foreach ($result as $row) {
            // Calculate Distance between Points
            $row['distance'] = Map::distance($row, array('LATITUDE' => $latitude, 'LONGITUDE' => $longitude));

            // Add to Collcetion
            $nearby_schools[] = $row;
            $distances[$index] = $row['distance'];

            // Increment
            $index++;
        }

        // Sort Array
        array_multisort($distances, SORT_ASC, $nearby_schools);

        // Slice to 10
        $nearby_schools = array_slice($nearby_schools, 0, 10);
    }

    /**
     * View Nearby Amenities
     */
    if ($view == 'nearby-amenities') {
        // Find Nearby Amenities
        $query = $db_onboard->prepare("SELECT SQL_NO_CACHE BUSNAME, STREET, CITY, STATENAME, PHONE, CATEGORY, INDUSTRY, LATITUDE, LONGITUDE"
                                    . " FROM `" . ONBOARD_TABLE_AMENITIES . "` WHERE `PRIMARY` = 'PRIMARY'"
            . (!empty($bounds) ? ' AND `LATITUDE` BETWEEN :south AND :north AND `LONGITUDE` BETWEEN :west AND :east;' : ''));
        (!empty($bounds) ? $query->execute($bounds) : $query->execute());
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // Collection
        $nearby_amenities = array();
        $distances = array();

        // Count
        $index = 0;

        // Loop through Results
        foreach ($result as $row) {
            // Calculate Distance between Points
            $row['distance'] = Map::distance($row, array('LATITUDE' => $latitude, 'LONGITUDE' => $longitude));

            // Add to Collcetion
            $nearby_amenities[$index] = $row;
            $distances[$index] = $row['distance'];

            // Increment
            $index++;
        }

        // Sort Array
        array_multisort($distances, SORT_ASC, $nearby_amenities);

        // Group by Category
        foreach ($nearby_amenities as $key => $nearby_amenity) {
            $category = $nearby_amenity['CATEGORY'];
            if (!isset($nearby_amenities[$category])) {
                $nearby_amenities[$category] = $nearby_amenity;
            }
            unset($nearby_amenities[$key]);
        }

        // Slice to 10
        $nearby_amenities = array_slice($nearby_amenities, 0, 10);
    }
}
