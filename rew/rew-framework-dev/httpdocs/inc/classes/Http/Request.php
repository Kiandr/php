<?php

/**
 * Http_Request
 *
 * Object-oriented representation of an HTTP request. This class
 * is responsible for parsing the raw HTTP request into a format
 * usable by the Application.
 *
 * This class will automatically remove slashes from GET, POST, PUT,
 * and Cookie data if magic quotes are enabled.
 *
 */
class Http_Request
{

    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_OVERRIDE = '_METHOD';

    /**
     * Request method (ie. "GET", "POST", "PUT", "DELETE", "HEAD")
     * @var string
     */
    protected $method;

    /**
     * Key-value array of HTTP request headers
     * @var array
     */
    protected $headers;

    /**
     * Names of additional headers to parse from the current HTTP request that are not prefixed with "HTTP_"
     * @var array
     */
    protected $additionalHeaders = array('content-type', 'content-length', 'php-auth-user', 'php-auth-pw', 'auth-type', 'x-requested-with');

    /**
     * Key-value array of cookies sent with the current HTTP request
     * @var array
     */
    protected $cookies;

    /**
     * Key-value array of HTTP GET parameters
     * @var array
     */
    protected $get;

    /**
     * Key-value array of HTTP POST parameters
     * @var array
     */
    protected $post;

    /**
     * Key-value array of HTTP PUT parameters
     * @var array
     */
    protected $put;

    /**
     * Raw body of HTTP request
     * @var string
     */
    protected $body;

    /**
     * Content type of HTTP request
     * @var string
     */
    protected $contentType;

    /**
     * Resource URI (ie. "/person/1")
     * @var string
     */
    protected $resource;

    /**
     * The root URI of the Application without trailing slash.
     * This will be "" if the app is installed at the web document root.
     * If the app is installed in a sub-directory "/foo", this will be "/foo".
     * @var string
     */
    protected $root;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;
        $this->headers = $this->loadHttpHeaders();
        $this->body = @file_get_contents('php://input');
        $this->get = self::stripSlashesIfMagicQuotes($_GET);
        $this->post = self::stripSlashesIfMagicQuotes($_POST);
        $this->put = self::stripSlashesIfMagicQuotes($this->loadPutParameters());
        $this->cookies = self::stripSlashesIfMagicQuotes($_COOKIE);
        $this->root = Http_Uri::getBaseUri(true);
        $this->resource = Http_Uri::getUri(true);
        $this->checkForHttpMethodOverride();
    }

    /**
     * Is this a GET Request?
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->method === self::METHOD_GET;
    }

    /**
     * Is this a POST Request?
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->method === self::METHOD_POST;
    }

    /**
     * Is this a PUT Request?
     *
     * @return bool
     */
    public function isPut()
    {
        return $this->method === self::METHOD_PUT;
    }

    /**
     * Is this a DELETE Request?
     *
     * @return bool
     */
    public function isDelete()
    {
        return $this->method === self::METHOD_DELETE;
    }

    /**
     * Is this a HEAD Request?
     *
     * @return bool
     */
    public function isHead()
    {
        return $this->method === self::METHOD_HEAD;
    }

    /**
     * Is this a OPTIONS Request?
     *
     * @return bool
     */
    public function isOptions()
    {
        return $this->method === self::METHOD_OPTIONS;
    }

    /**
     * Is this a XHR Request?
     * @return bool
     */
    public function isAjax()
    {
        return ($this->params('isajax') || $this->headers('X_REQUESTED_WITH') === 'XMLHttpRequest');
    }

    /**
     * Fetch a PUT|POST|GET parameter value
     *
     * The preferred method to fetch the value of a PUT, POST, or GET parameter (searched in that order).
     *
     * @param   string $key    The paramter name
     * @return  string|null    The value of parameter, or NULL if parameter not found
     */
    public function params($key)
    {
        foreach (array('put', 'post', 'get') as $dataSource) {
            $source = $this->$dataSource;
            if (isset($source[(string)$key])) {
                return $source[(string)$key];
            }
        }
        return null;
    }

    /**
     * Fetch GET Parameter(s)
     *
     * @param   string $key          Name of parameter
     * @return  array|string|null    All parameters, parameter value if $key and parameter exists, or NULL if $key and parameter does not exist.
     */
    public function get($key = null)
    {
        return $this->arrayOrArrayValue($this->get, $key);
    }

    /**
     * Fetch POST Parameter(s)
     *
     * @param   string $key          Name of parameter
     * @return  array|string|null    All parameters, parameter value if $key and parameter exists, or NULL if $key and parameter does not exist.
     */
    public function post($key = null)
    {
        return $this->arrayOrArrayValue($this->post, $key);
    }

    /**
     * Fetch PUT Parameter(s)
     *
     * @param   string $key          Name of parameter
     * @return  array|string|null    All parameters, parameter value if $key and parameter exists, or NULL if $key and parameter does not exist.
     */
    public function put($key = null)
    {
        return $this->arrayOrArrayValue($this->put, $key);
    }

    /**
     * Fetch COOKIE Value(s)
     *
     * @param   string $key          The cookie name
     * @return  array|string|null    All parameters, parameter value if $key and parameter exists, or NULL if $key and parameter does not exist.
     */
    public function cookies($key = null)
    {
        return $this->arrayOrArrayValue($this->cookies, $key);
    }

    /**
     * Get HTTP Request Header
     *
     * @param   string $key          The header name
     * @return  array|string|null    All parameters, parameter value if $key and parameter exists, or NULL if $key and parameter does not exist.
     */
    public function headers($key = null)
    {
        return is_null($key) ? $this->headers : $this->arrayOrArrayValue($this->headers, $this->convertHttpHeaderName($key));
    }

    /**
     * Get HTTP Request Body
     *
     * @return string|false String, or FALSE if body could not be read
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get HTTP Method
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get HTTP Request Content-Type
     * @return string
     */
    public function getContentType()
    {
        if (!isset($this->contentType)) {
            $contentType = 'application/x-www-form-urlencoded';
            $header = $this->headers('CONTENT_TYPE');
            if (!is_null($header)) {
                $headerParts = preg_split('/\s*;\s*/', $header);
                $contentType = $headerParts[0];
            }
            $this->contentType = $contentType;
        }
        return $this->contentType;
    }

    /**
     * Get HTTP request resource URI
     *
     * @return string
     */
    public function getResourceUri()
    {
        return $this->resource;
    }

    /**
     * Get HTTP request root URI
     *
     * @return string
     */
    public function getRootUri()
    {
        return $this->root;
    }

    /**
     * Fetch array or array value
     *
     * @param   array           $array
     * @param   string          $key
     * @return  array|mixed     Array if key is null, else array value
     */
    protected function arrayOrArrayValue(array &$array, $key = null)
    {
        return is_null($key) ? $array : $this->arrayValueForKey($array, $key);
    }

    /**
     * Fetch value from array
     *
     * @return mixed|null
     */
    protected function arrayValueForKey(array &$array, $key)
    {
        return isset($array[(string) $key]) ? $array[(string)$key] : null;
    }

    /**
     * Strip slashes from string or array of strings
     *
     * @param   array|string $rawData
     * @return  array|string
     */
    public static function stripSlashesIfMagicQuotes($rawData)
    {
        if (get_magic_quotes_gpc()) {
            return is_array($rawData) ? array_map(array('self', 'stripSlashesIfMagicQuotes'), $rawData) : stripslashes($rawData);
        } else {
            return $rawData;
        }
    }

    /**
     * Get PUT parameters
     *
     * @return array Key-value array of HTTP request PUT parameters
     */
    protected function loadPutParameters()
    {
        if ($this->getContentType() === 'application/x-www-form-urlencoded') {
            $input = is_string($this->body) ? $this->body : '';
            if (function_exists('mb_parse_str')) {
                mb_parse_str($input, $output);
            } else {
                parse_str($input, $output);
            }
            return $output;
        } else {
            return array();
        }
    }

    /**
     * Get HTTP request headers
     *
     * @return array Key-value array of HTTP request headers
     */
    protected function loadHttpHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            $key = $this->convertHttpHeaderName($key);
            if (strpos($key, 'http-') === 0 || in_array($key, $this->additionalHeaders)) {
                $name = str_replace('http-', '', $key);
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    /**
     * Convert HTTP header name
     *
     * @return string
     */
    protected function convertHttpHeaderName($name)
    {
        return str_replace('_', '-', strtolower($name));
    }

    /**
     * Check for HTTP request method override
     *
     * Because traditional web browsers do not support PUT and DELETE
     * HTTP methods, we use a hidden form input field to
     * mimic PUT and DELETE requests. We check for this override here.
     *
     * @return  void
     */
    protected function checkForHttpMethodOverride()
    {
        if (isset($this->post[self::METHOD_OVERRIDE])) {
            $this->method = $this->post[self::METHOD_OVERRIDE];
            unset($this->post[self::METHOD_OVERRIDE]);
            if ($this->isPut()) {
                $this->put = $this->post;
            }
        }
    }
}
