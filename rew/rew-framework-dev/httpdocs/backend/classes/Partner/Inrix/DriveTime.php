<?php

namespace REW\Backend\Partner\Inrix;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use \DateTime;
use \DateTimeZone;
use \Exception;
use \InvalidArgumentException;
use \Map;
use \PDO;
use \PDOException;
use \Util_Curl;

class DriveTime
{
    /**
     * @var string
     */
    const REW_DRIVE_TIME_VERSION = '1.0.0';

    /**
     * @var string
     */
    const API_VENDOR_KEY = '1520973174';

    /**
     * @var string
     */
    const API_CONSUMER_KEY = '47ecc795-a96a-40c9-a780-42fe905317e6';

    /**
     * @var string
     */
    const SETTINGS_PREPEND = 'drive_time';

    /**
     * @var array
     */
    const IDX_FORM_FIELDS = ['place_zip', 'place_lat', 'place_lng', 'place_zoom', 'place_adr', 'dt_address', 'dt_direction', 'dt_travel_duration', 'dt_arrival_time'];

    /**
     * @var array
     */
    const IDX_SAVED_SEARCH_FIELDS = [
        'dt_address' =>         ['name' => 'Drive Time - Address'],
        'dt_arrival_time' =>    ['name' => 'Drive Time - Arrival Time'],
        'dt_direction' =>       ['name' => 'Drive Time - Direction'],
        'dt_travel_duration' => ['name' => 'Drive Time - Travel Duration']
    ];

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * DriveTime constructor.
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param string $address
     * @param string $latitude
     * @param string $longitude
     * @param string $direction
     * @param int $duration
     * @param string $arrival_time
     * @throws PDOException
     * @throws Exception if it fails to load authentication data
     * @return string|null
     */
    public function authenticateAndBuildPolygon($address, $latitude, $longitude, $direction, $duration, $arrival_time)
    {
        if ($authentication = $this->getLocalAuthData()) {
            $utc_offset = $this->findOffsetFromUTC(date_default_timezone_get());

            if (!empty($latitude) && $longitude) {
                $dt_lat = $latitude;
                $dt_lng = $longitude;
            } else {
                $dt_coords = Map::geocode($address);
                $dt_lat = $dt_coords['latitude'];
                $dt_lng = $dt_coords['longitude'];
            }

            if ($poly_endpoint = $this->generatePolygonApiEndpoint(
                $authentication[sprintf('%s.auth_token', self::SETTINGS_PREPEND)][0],
                $authentication[sprintf('%s.server_path_api', self::SETTINGS_PREPEND)][0],
                $dt_lat,
                $dt_lng,
                $direction,
                $duration,
                date(sprintf('Y-m-d\T%s:00-%s', $arrival_time, $utc_offset), strtotime('+14 days now'))
            )) {
                $polygons = $this->requestTravelPolygons($poly_endpoint);
                if (!empty($polygons)) {
                    $polygon_string = $this->generatePolygonString($polygons);
                }
            }
        }
        return $polygon_string;
    }

    /**
     * @param string $dt_address
     * @param string $mls_address
     * @return bool
     */
    public static function checkMapMarkerDuplicate($dt_address, $mls_address)
    {
        if (!empty($dt_address) && !empty($mls_address)) {
            // Format Drive Time Center Address
            $drive_time_address = explode(', ', strtolower($dt_address));
            $drive_time_address = $drive_time_address[0];
            $drive_time_address = explode(' ', $drive_time_address);
            unset($drive_time_address[count($drive_time_address) - 1]);
            $drive_time_address = implode(' ', $drive_time_address);

            // Format MLS Listing Address
            $compare_mls_address = explode(' ', strtolower($mls_address));
            unset($compare_mls_address[count($compare_mls_address) - 1]);
            $compare_mls_address = implode(' ', $compare_mls_address);

            // Compare Addresses - Return false if DT marker overlaps a listing marker
            if ($drive_time_address == $compare_mls_address) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $min_hour
     * @param int $max_hour
     * @throws InvalidArgumentException if time range exceeds 0-24
     * @return array
     */
    public static function getArrivalTimeOptions($min_hour = 6, $max_hour = 20)
    {
        if ((int) $min_hour > 24 || (int) $max_hour <= 0 || (int) $max_hour > 24) {
            throw new InvalidArgumentException('Invalid time range provided to generate Drive Time arrival time options.');
        }
        $options = [];
        for ($i = (int) $min_hour; $i <= (int) $max_hour; $i++) {
            for ($j = 0; $j <= 45; $j += 15) {
                if ($i >= 24 || ($i === (int) $max_hour && $j > 0)) break;
                $time_display = ($i > 12 ? $i - 12 : ($i === 0 ? $i + 12 : $i)) . ':' . ($j === 0 ? '00' : $j) . ' ' . ($i >= 12 && $i < 24 ? 'pm' : 'am');
                $time = ($i <= 9 ? '0' . $i : $i) . ':' . ($j === 0 ? '00' : $j);
                $options[] = [
                    'value' => $time,
                    'display' => $time_display
                ];
            }
        }
        return $options;
    }

    /**
     * @param int $min_duration
     * @param int $max_duration
     * @param int $increment_duration
     * @throws InvalidArgumentException
     * @return array
     */
    public static function getTravelDurationOptions($min_duration = 15, $max_duration = 90, $increment_duration = 15)
    {
        if ((int) $min_duration <= 0 || (int) $max_duration < (int) $min_duration || (int) $increment_duration <= 0) {
            throw new InvalidArgumentException('Invalid time range provided to generate Drive Time arrival time options.');
        }
        $options = [];
        for ($i = (int) $min_duration; $i <= (int) $max_duration; $i += (int) $increment_duration) {
            $time_display = $i . ' min';
            $time = $i;
            $options[] = [
                'value' => $time,
                'display' => $time_display
            ];
        }
        return $options;
    }

    /**
     * @param string $address
     * @param string $direction
     * @param string $duration
     * @param string $arrival_time
     * @param int $zoom
     * @param string $latitude
     * @param string $longitude
     * @throws PDOException
     * @throws Exception if it fails to load authentication data
     */
    public function modifyServerMapRequests($address, $direction, $duration, $arrival_time, $zoom, $latitude, $longitude)
    {
        $travel_poly = $this->authenticateAndBuildPolygon(
            $address,
            $latitude,
            $longitude,
            $direction,
            $duration,
            $arrival_time
        );
        if (!empty($travel_poly)) {
            $_REQUEST['map']['polygon'] = $travel_poly;
            $_REQUEST['view'] = 'map';
        }
        if (!empty($latitude) && !empty($longitude)) {
            $_REQUEST['map']['latitude'] = (float) $latitude;
            $_REQUEST['map']['longitude'] = (float) $longitude;
        }
        if (!empty($zoom)) {
            $_REQUEST['map']['zoom'] = (int) $zoom;
        }
    }

    /**
     * @param $endpoint_url
     * @param array $params
     * @param $request_type
     * @return mixed
     */
    protected function executeCurlRequest($endpoint_url, $params = [], $request_type = Util_Curl::REQUEST_TYPE_GET)
    {
        $curl_opts = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => str_replace(" ", '%20', $endpoint_url)
        ];

        return Util_Curl::executeRequest($endpoint_url, $params, $request_type, $curl_opts);
    }

    /**
     * @throws PDOException
     * @return array|null
     */
    protected function getLocalAuthData()
    {
        $token_data = $this->getLocalToken();
        if (!empty($token_data)) {
            $token_exp_key = sprintf('%s.auth_token_expiry', self::SETTINGS_PREPEND);
            $token_ts = strtotime($token_data[$token_exp_key][0] . " UTC");
            $current_ts = strtotime(date("Y-m-d H:i:s", time() - date("Z")));
        }

        // Request a New Token if Stored Token Expired
        if (empty($token_data) || $token_ts <= $current_ts) {
            $raw_token_data = $this->requestNewTokenData(sprintf(
                'http://na-api.inrix.com/traffic/inrix.ashx'
                . '?action=GetSecuritytoken'
                . '&vendorid=%s'
                . '&consumerid=%s',
                self::API_VENDOR_KEY,
                self::API_CONSUMER_KEY
            ));

            if ($raw_token_data) {
                if ($this->updateLocalToken(
                    $raw_token_data['auth_token'],
                    $raw_token_data['auth_token_expiry'],
                    $raw_token_data['server_path'],
                    $raw_token_data['server_path_api']
                )) {
                    $token_data = $this->getLocalToken();
                }
            }
        }
        return $token_data;
    }

    /**
     * @param string $endpoint_url
     * @return array|null
     */
    protected function requestNewTokenData($endpoint_url)
    {
        $response = $this->executeCurlRequest($endpoint_url);

        $xml = simplexml_load_string($response);

        // need to format the expiry time (ex: 2014-05-15T00:51:00Z)
        $authToken = $xml->AuthResponse->AuthToken;
        if ($authToken) {
            $format_expiry = trim((string) $authToken->attributes()->expiry);

            return [
                'auth_token' => $xml->AuthResponse->AuthToken,
                'auth_token_expiry' => $format_expiry,
                'server_path' => $xml->AuthResponse->ServerPath,
                'server_path_api' => $xml->AuthResponse->ServerPaths->ServerPath->__toString()
            ];
        }
        return null;
    }

    /**
     * @throws PDOException
     * @return array
     */
    protected function getLocalToken()
    {
        $token_query = sprintf(
            "SELECT * "
            . " FROM %s "
            . " WHERE `name` LIKE :settings_prepend "
            . ";",
            $this->settings->TABLES['SETTINGS']
        );
        $token_params = [
            'settings_prepend' => sprintf('%%%s%%', self::SETTINGS_PREPEND)
        ];
        $sth = $this->db->prepare($token_query);
        $sth->execute($token_params);
        $token_data = $sth->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP);
        return $token_data ?: [];
    }

    /**
     * @param string $token
     * @param string $expiry
     * @param string $server_path
     * @param string $server_path_api
     * @throws PDOException
     * @throws InvalidArgumentException
     * @return bool|null
     */
    protected function updateLocalToken($token, $expiry, $server_path, $server_path_api)
    {
        if (empty($token) || empty($expiry) || empty($server_path) || empty($server_path_api)) {
            return null;
        }
        // Update database
        $update_query = sprintf(
            " INSERT INTO `%s` (`name`, `value`) VALUES "
            . " ('%s', :auth_token), "
            . " ('%s', :auth_token_expiry), "
            . " ('%s', :server_path), "
            . " ('%s', :server_path_api) "
            . " ON DUPLICATE KEY UPDATE "
            . " `value` = VALUES(`value`) "
            . ";",
            'settings',
            sprintf('%s.auth_token', self::SETTINGS_PREPEND),
            sprintf('%s.auth_token_expiry', self::SETTINGS_PREPEND),
            sprintf('%s.server_path', self::SETTINGS_PREPEND),
            sprintf('%s.server_path_api', self::SETTINGS_PREPEND)
        );
        $update_params = [
            'auth_token' => $token,
            'auth_token_expiry' => $expiry,
            'server_path' => $server_path,
            'server_path_api' => $server_path_api
        ];

        $sth = $this->db->prepare($update_query);
        return $sth->execute($update_params);
    }

    /**
     * @param string $endpoint_url
     * @return array|string
     */
    protected function requestTravelPolygons($endpoint_url)
    {
        $xml = simplexml_load_string($this->executeCurlRequest($endpoint_url));

        if (empty($xml) || !isset($xml->Polygons)) {
            return [];
        }

        $polygons = [];
        foreach ($xml->Polygons->children() as $child) {
            foreach ($child as $a => $b) {
                if ($a == 'Polygon') {
                    $polygons[] = (string) $b->exterior->LinearRing->posList;
                }
            }
        }

        return $polygons;
    }

    /**
     * @param array $polygons
     * @return string|null
     */
    protected function generatePolygonString($polygons = [])
    {
        $polygon_strings = [];
        if (!empty($polygons)) {
            foreach ($polygons as $polygon) {
                $points = [];
                $pieces = explode(' ', $polygon);
                $count = count($pieces);
                for ($i = 0; $i < $count; $i += 2) {
                    $points[] = $pieces[$i] . ' ' . $pieces[$i + 1];
                }
                $polygon_strings[] = '["' . implode(',', $points) . '"]';
            }
        }
        return (!empty($polygon_strings)
            ? implode(',', $polygon_strings)
            : null);
    }

    /**
     * @param array $options
     * @throws InvalidArgumentException
     * @return string|null
     */
    protected function generatePolygonApiEndpoint($token, $server, $latitude, $longitude, $direction, $duration, $time)
    {
        if (!empty($token)
            && !empty($server)
            && !empty($latitude)
            && !empty($longitude)
            && !empty($direction)
            && !empty($duration)
            && !empty($time)
        ) {
            $findPolygonString = sprintf(
                '%s'
                . '?Action=GetDriveTimePolygons'
                . '&Center=%s|%s'
                . '&RangeType=%s'
                . '&Duration=%s'
                . '&DateTime=%s'
                . '&Token=%s'
                . '&vendorid=%s'
                . '&consumerid=%s',
                $server,
                $latitude,
                $longitude,
                $direction,
                $duration,
                $time,
                $token,
                self::API_VENDOR_KEY,
                self::API_CONSUMER_KEY
            );
        } else {
            throw new InvalidArgumentException('Invalid arguments passed to generate Drive Time polygon request endpoint.');
        }
        return $findPolygonString ?: null;
    }

    /**
     * @param string $timezone
     * @return string
     */
    protected function findOffsetFromUTC($timezone)
    {
        $UTC = new DateTimeZone("UTC");
        $UTCTime = new DateTime("now", $UTC);
        $dateTimeZone = new DateTimeZone($timezone);
        $timeOffset = $dateTimeZone->getOffset($UTCTime);
        $hours = (abs($timeOffset) / 60 / 60);

        return ($hours < 10)
            ? sprintf('0%s:00', $hours)
            : sprintf('%s:00', $hours);
    }

}
