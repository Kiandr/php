<?php

/**
 * Http_Uri
 * Parses base uri and application uri from Request.
 *
 */
class Http_Uri
{

    /**
     * "https" or "http"
     * @var string
     */
    protected static $scheme;

    /**
     * Full URI
     * @var string
     */
    protected static $fullUri;

    /**
     * Base URI
     * @var string
     */
    protected static $baseUri;

    /**
     * URL
     * @var string
     */
    protected static $uri;

    /**
     * The URI query string, excluding leading "?"
     * @var string
     */
    protected static $queryString;

    /**
     * Get Full URI
     *
     * @param   bool    $reload Force reparse the base URI?
     * @return  string
     */
    public static function getFullUri($reload = false)
    {
        if ($reload || is_null(self::$fullUri)) {
            $fullUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']; // Full Request URI
            self::$fullUri = $fullUri;
        }
        return self::$fullUri;
    }

    /**
     * Get Base URI without trailing slash
     *
     * @param   bool    $reload Force reparse the base URI?
     * @return  string
     */
    public static function getBaseUri($reload = false)
    {
        if ($reload || is_null(self::$baseUri)) {
            $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']; // Full Request URI
            $scriptName = $_SERVER['SCRIPT_NAME']; // Script path from docroot
            $baseUri = strpos($requestUri, $scriptName) === 0 ? $scriptName : str_replace('\\', '/', dirname($scriptName));
            self::$baseUri = rtrim($baseUri, '/');
        }
        return self::$baseUri;
    }

    /**
     * Get URI with leading slash
     *
     * @param   bool    $reload     Force reparse the URI?
     * @return  string
     * @throws  RuntimeException    If unable if unable to determine URI
     */
    public static function getUri($reload = false)
    {
        if ($reload || is_null(self::$uri)) {
            $uri = '';
            if (!empty($_SERVER['PATH_INFO'])) {
                $uri = $_SERVER['PATH_INFO'];
            } else {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $uri = parse_url(self::getScheme() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
                } else if (isset($_SERVER['PHP_SELF'])) {
                    $uri = $_SERVER['PHP_SELF'];
                } else {
                    throw new RuntimeException('Unable to detect request URI');
                }
            }
            if (self::getBaseUri() !== '' && strpos($uri, self::getBaseUri()) === 0) {
                $uri = substr($uri, strlen(self::getBaseUri()));
            }
            self::$uri = '/' . ltrim($uri, '/');
        }
        return self::$uri;
    }

    /**
     * Get URI Scheme
     *
     * @param   bool    $reload For reparse the URL scheme?
     * @return  string  "https" or "http"
     */
    public static function getScheme($reload = false)
    {
        if ($reload || is_null(self::$scheme)) {
            self::$scheme = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') ? 'http' : 'https';
        }
        return self::$scheme;
    }

    /**
     * Get URI Query String
     *
     * @param   bool    $reload For reparse the URL query string?
     * @return  string
     */
    public static function getQueryString($reload = false)
    {
        if ($reload || is_null(self::$queryString)) {
            self::$queryString = $_SERVER['QUERY_STRING'];
        }
        return self::$queryString;
    }
}
