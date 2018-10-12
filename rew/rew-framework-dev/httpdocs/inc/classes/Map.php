<?php

/**
 * Map Utilities
 *
 */
class Map
{

    /**
     * Geocode Address (Requires Accuracy Score of 6 or Higher)
     *
     * @param string $address
     * @return array(longitude, latitude)
     * @uses Util_Curl
     */
    public static function geocode($address)
    {

        // Geocoder API URL
        $geocoderRequest = sprintf('http://maps.googleapis.com/maps/api/geocode/json?address=%s', rawurlencode($address));

        // Process Request
        $json = Util_Curl::executeRequest($geocoderRequest);

        // Response Info
        $info = Util_Curl::info();

        // Require 200 Response
        if ($info['http_code'] == 200) {
            $data = json_decode($json, true);
            // Location
            $result = $data['results'][0];
            if (!empty($result)) {
                $location = $result['geometry']['location'];
                if (!empty($location)) {
                    return array('longitude' => $location['lng'], 'latitude' => $location['lat'], 'raw' => $result);
                }
            }
        }

        // Could Not Geocode
        return false;
    }

    /**
     * Find Latitude / Longitude in Polygon (WKT expected)
     *
     * @param array $polygon
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public static function findPoint($polygon, $latitude, $longitude)
    {

        // Parse WKT MULTIPOLYGON into Array of Points
        //$polygon = preg_replace("/MULTIPOLYGON\(\(\((.+)\)\)\)/", "$1", $polygon);
        $polygon = explode(",", $polygon);
        $points = array();
        foreach ($polygon as $polypoints) {
            list($point['latitude'], $point['longitude']) = explode(" ", $polypoints);
            $points[] = $point;
        }
        $j = count($points) - 1;
        $return = false;

        // Loop through vertex points
        for ($i = 0; $i < count($points); $i++) {
            $iPoint = $points[$i];
            $jPoint = $points[$j];
            if (($iPoint['longitude'] < $longitude && $jPoint['longitude'] >= $longitude) ||
                ($jPoint['longitude'] < $longitude && $iPoint['longitude'] >= $longitude)) {
                if ($iPoint['latitude'] + ($longitude - $iPoint['longitude']) / ($jPoint['longitude'] - $iPoint['longitude']) * ($jPoint['latitude'] - $iPoint['latitude']) < $latitude) {
                    $return = !$return;
                }
            }
            $j = $i;
        }
        return $return;
    }

    /**
     * Calculate Distance Between Points
     *
     * @param array[LATITUDE,LONGITUDE] $point1
     * @param array[LATITUDE,LONGITUDE] $point2
     * @return int
     */
    public static function distance($point1, $point2)
    {
        $radius      = 3958;      // Earth's radius (miles)
        $pi          = 3.1415926;
        $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)
        $distance = ($radius * $pi * sqrt(
            ($point1['LATITUDE'] - $point2['LATITUDE'])
            * ($point1['LATITUDE'] - $point2['LATITUDE'])
            + cos($point1['LATITUDE'] / $deg_per_rad)  // Convert these to
            * cos($point2['LATITUDE'] / $deg_per_rad)  // radians for cos()
            * ($point1['LONGITUDE'] - $point2['LONGITUDE'])
            * ($point1['LONGITUDE'] - $point2['LONGITUDE'])
        ) / 180);
        return $distance;  // Returned using the units used for $radius.
    }
}
