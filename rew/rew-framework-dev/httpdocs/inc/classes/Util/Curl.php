<?php

/**
 * Util_Curl is a Utility class containing methods used for quickly making cURL requests
 *
 */
class Util_Curl
{

    private static $_url;
    private static $_cookie;
    private static $_info;
    private static $_redirect_count;
    private static $_timeout = 10;

    const REQUEST_TYPE_GET                  = 1;
    const REQUEST_TYPE_POST                 = 2;
    const REQUEST_TYPE_POST_MULTIPART       = 3;
    const REQUEST_TYPE_PUT                  = 4;
    const REQUEST_TYPE_DELETE               = 5;
    const REDIRECT_COUNT_MAX                = 10;

    /**
     * Set the base URL from which all requests happen
     *
     * @param string $url
     * @param string $cookie_file
     */
    public static function setBaseURL($url, $cookie_file = null)
    {

        // Set base URL for all cURL requests
        self::$_url = rtrim($url, '/');

        // Save cookies?
        self::$_cookie = $cookie_file;
    }

    /**
     * Set the timeout for Curl
     *
     * @param int $timeout
     */
    public static function setTimeout($timeout)
    {
        self::$_timeout = (int) $timeout;
    }

    /**
     * Get the timeout for Curl
     *
     * @return int
     */
    public static function getTimeout()
    {
        return self::$_timeout;
    }

    /**
     * Execute a cURL request
     *
     * @param string $path Path within the base URL to request
     * @param array $params Request parameters (appended to query string for GET requests and POSTFIELDS for POST requests)
     * @param int $request_type The HTTP request type
     * @param array $opt_override cURL options to override
     * @return mixed
     */
    public static function executeRequest($path = '/', $params = array(), $request_type = Util_Curl::REQUEST_TYPE_GET, $opt_override = array(), $redirect = false)
    {

        // Create new cURL resource
        $ch = curl_init();

        // Reset redirect count
        if (empty($redirect)) {
            self::$_redirect_count = 0;
        }

        // Build request URL (base URL + path)
        $request_url = !stristr($path, 'http://') && !stristr($path, 'https://') ? self::$_url . $path : $path;

        // Build common cURL options
        $options = array(
            CURLOPT_HEADER          => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => (ini_get('open_basedir') === '') ? true : false,
            CURLINFO_HEADER_OUT     => true,
            CURLOPT_USERAGENT       => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_TIMEOUT         => self::getTimeout(),
        );

        // Encode parameters
        if ($request_type == self::REQUEST_TYPE_POST
            || $request_type == self::REQUEST_TYPE_PUT
        ) {
            // Send request data as JSON
            $headers = $opt_override[CURLOPT_HTTPHEADER];
            $json_request = is_array($headers) && in_array('Content-Type: application/json', $headers);
            if ($json_request) {
                $params = json_encode($params);
            } else {
                $params = http_build_query($params);
            }
        }

        // Process params (GET or POST)
        if ($request_type == Util_Curl::REQUEST_TYPE_GET) {
            // Get current query string
            $url = parse_url($request_url);

            // Parse as array
            parse_str($url['query'], $query_arr);

            // Merge with GET params
            $query_arr = array_merge($query_arr, $params);
            $query_arr_string = http_build_query($query_arr);

            // Authentication
            $auth = '';
            if (!empty($url['user']) && !empty($url['pass'])) {
                $auth = $url['user'] . ':' . $url['pass'] . '@';
            }

            // Set new URL
            $request_url = $url['scheme'] . '://' . $auth . $url['host'] . $url['path'] . (!empty($query_arr_string) ? '?' . $query_arr_string : '');
        } elseif ($request_type == Util_Curl::REQUEST_TYPE_POST) {
            // Set POST fields
            $options = $options + array(
                CURLOPT_POST        => true,
                CURLOPT_POSTFIELDS  => $params
            );
        } elseif ($request_type == Util_Curl::REQUEST_TYPE_POST_MULTIPART) {
            // Set POST fields
            $options = $options + array(
                CURLOPT_POST        => true,
                CURLOPT_POSTFIELDS  => $params,
            );
        } elseif ($request_type == Util_Curl::REQUEST_TYPE_PUT) {
            // Set PUT fields
            $options = $options + array(
                CURLOPT_CUSTOMREQUEST   => 'PUT',
                CURLOPT_POSTFIELDS      => $params
            );
        } elseif ($request_type == Util_Curl::REQUEST_TYPE_DELETE) {
            // Set DELETE fields
            $options = $options + array(
                CURLOPT_CUSTOMREQUEST   => 'DELETE'
            );
        }

        // Set URL
        $options[CURLOPT_URL] = $request_url;

        // Set cookies?
        if (!empty(self::$_cookie)) {
            $options = $options + array(
                CURLOPT_COOKIEFILE  => self::$_cookie,
                CURLOPT_COOKIEJAR   => self::$_cookie,
            );
        }

        // Set & merge with overrides
        $options = $opt_override + $options;
        curl_setopt_array($ch, $options);

        // Time request
        $timer = Profile::timer()
            ->stopwatch('<code>' . __METHOD__ . '</code>')
            ->start();

        // Execute
        $output = curl_exec($ch);

        // Remember info
        self::$_info = curl_getinfo($ch);

        // Stop timer
        $http_code = self::$_info['http_code'];
        $size_download = self::$_info['size_download'];
        $request_method =  $options[CURLOPT_CUSTOMREQUEST] ?: self::getRequestMethod($request_type);
        $timer->setDetails('<strong><code>' . $request_method . ' ' . $request_url . ' (' . $http_code . ') (' . Format::filesize($size_download) . ')</code></strong>')
            ->stop();

        // Close resource
        curl_close($ch);

        // Manual redirect
        $http_code = self::$_info['http_code'];
        if (!empty($http_code) && in_array($http_code, array('301', '302')) && empty($options[CURLOPT_FOLLOWLOCATION])) {
            if (!empty(self::$_info['redirect_url'])) {
                // Maximum redirects reached
                if (self::$_redirect_count >= self::REDIRECT_COUNT_MAX) {
                    throw new Exception('Redirect limit reached at ' . $request_url . ' (redirect to ' . self::$_info['redirect_url'] . ')');
                }

                // Load new URL
                self::$_redirect_count++;
                return self::executeRequest(self::$_info['redirect_url'], array(), Util_Curl::REQUEST_TYPE_GET, $opt_override, true);
            }
        }

        // Return output
        return $output;
    }

    /**
     * Get information about the last cURL request
     * @return mixed
     */
    public static function info()
    {
        return self::$_info;
    }

    /**
     * Get method name for request type
     * @param int $type
     * @return string|NULL
     */
    public static function getRequestMethod($type)
    {
        switch ($type) {
            case self::REQUEST_TYPE_GET:
                return 'GET';
            case self::REQUEST_TYPE_PUT:
                return 'PUT';
            case self::REQUEST_TYPE_POST_MULTIPART:
            case self::REQUEST_TYPE_POST:
                return 'POST';
            case self::REQUEST_TYPE_DELETE:
                return 'DELETE';
        }
        return null;
    }
}
