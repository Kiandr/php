<?php

/**
 * OAuth_Request is a class used to make cURL Requests
 *
 * @package OAuth
 */
class OAuth_Request
{

    /**
     * Request Method
     * @var string
     */
    protected $method;

    /**
     * Url to Request
     * @var string
     */
    protected $url;

    /**
     * Request Parameters
     * @var array
     */
    protected $params;

    /**
     * HTTP Headers
     * @var array
     */
    protected $headers;

    /**
     * cURL Resource
     * @var resource
     */
    protected $ch;

    /**
     * Request Response
     * @var string
     */
    protected $response;

    /**
     * HTTP Status Code
     * @var int
     */
    protected $http_code;

    /**
     * HTTP Information from last request
     * @var array
     */
    protected $http_info;
    
    /**
     * Send Request as JSON flag
     * @var boolean
     */
    protected $json;
    
    /**
     * Create New OAuth_Request
     *
     * @param string $method HTTP Method ("POST", "GET", "PUT", "DELETE")
     * @param string $url URL to Request
     * @param mixed $params Request Params
     * @param array $headers HTTP Headers
     * @return void
     */
    public function __construct($method, $url, $params = array(), $headers = array(), $json = false)
    {

        // Set HTTP Method
        $this->method = strtoupper($method);

        // Set Request URL
        $this->url = $url;

        // Set Request Params
        $this->params = $params;

        // Set HTTP Headers
        $this->headers = $headers;
        
        // Set JSON flag
        $this->json = $json;
    }

    /**
     * Execute Request
     *
     * @return string Response
     * @uses Profile
     */
    public function execute()
    {

        // Start timer
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();
        $timer->setDetails($this->url);

        // Request Headers
        $headers = $this->getHeaders();

        // Setup cURL Handle
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        if (ini_get('open_basedir') === '') {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true); // handle redirects
        }
        curl_setopt($this->ch, CURLOPT_ENCODING, ''); // handle http compression
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_FAILONERROR, false);

        // Handle Request Method
        switch ($this->method) {
            case 'POST':
                if ($this->json) {
                    $headers[] = 'Content-Type: application/json';
                    $params = json_encode($this->params);
                } else {
                    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                    $params = self::build_http_query($this->params);
                }
                
                curl_setopt($this->ch, CURLOPT_URL, $this->url);
                curl_setopt($this->ch, CURLOPT_POST, 1);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'PUT':
                if ($this->json) {
                    $headers[] = 'Content-Type: application/json';
                    $params = json_encode($this->params);
                } else {
                    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                    $params = self::build_http_query($this->params);
                }
                curl_setopt($this->ch, CURLOPT_URL, $this->url);
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'DELETE':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($this->ch, CURLOPT_URL, $this->url . ($this->params ? (stristr($this->url, '?') ? '&' : '?') . self::build_http_query($this->params) : ''));
                break;
            case 'GET':
            default:
                curl_setopt($this->ch, CURLOPT_URL, $this->url . ($this->params ? (stristr($this->url, '?') ? '&' : '?') . self::build_http_query($this->params) : ''));
                break;
        }
        
        // Set HTTP Headers
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        // Execute Request
        $this->response = curl_exec($this->ch);

        // Store HTTP Information
        $this->http_code = (int) curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $this->http_info = curl_getinfo($this->ch);
        curl_close($this->ch);

        // Stop timer
        $timer->stop();

        // Return Response
        return $this->response;
    }

    /**
     * Set Request URL
     *
     * @param string $url URL to Request
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Set Request Parameters
     *
     * @param array $params Params to Send
     * @return void
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Set HTTP Method
     *
     * @param string $method HTTP Method
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * Set HTTP Headers
     *
     * @param array $headers HTTP Headers
     * @return void
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Get Request URL
     *
     * @return string URL to Request
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get Request Parameters
     *
     * @return array Params to Send
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get HTTP Method
     *
     * @return string HTTP Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get HTTP Headers
     *
     * @return array HTTP Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get Response
     *
     * @return string Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get Response Code
     *
     * @return int HTTP Code
     */
    public function getCode()
    {
        return $this->http_code;
    }

    /**
     * Get Response Info
     *
     * @return array HTTP Info
     */
    public function getInfo()
    {
        return $this->http_info;
    }

    /**
     * URL Encode RFC 3968
     *
     * @param mixed $input
     * @return string
     */
    public static function urlencode_rfc3986($input)
    {
        if (is_array($input)) {
            return array_map(array(__CLASS__, 'urlencode_rfc3986'), $input);
        } else if (is_scalar($input)) {
            return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($input)));
        } else {
            return '';
        }
    }

    /**
     * Build HTTP Query String, Params sorted by name to match Specs.
     *
     * @param array $params
     */
    public static function build_http_query($params = array())
    {
        if (!$params) {
            return '';
        }
        // Urlencode both keys and values
        $keys = self::urlencode_rfc3986(array_keys($params));
        $values = self::urlencode_rfc3986(array_values($params));
        $params = array_combine($keys, $values);
        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($params, 'strcmp');
        $pairs = array();
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                // If two or more parameters share the same name, they are sorted by their value
                // Ref: Spec: 9.1.1 (1)
                // June 12th, 2010 - changed to sort because of issue 164 by hidetaka
                sort($value, SORT_STRING);
                foreach ($value as $duplicate_value) {
                    $pairs[] = $parameter . '=' . $duplicate_value;
                }
            } else {
                $pairs[] = $parameter . '=' . $value;
            }
        }
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
        // Each name-value pair is separated by an '&' character (ASCII code 38)
        return implode('&', $pairs);
    }
}
