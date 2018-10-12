<?php

/**
 * OAuth_Login is an abstract class used for connecting to OAuth Servers
 *
 * @package OAuth
 */
abstract class OAuth_Login
{

    /**
     * OAuth Version
     * @var float
     */
    protected $version = 2.0;

    /**
     * OAuth Provider Name
     * @var string
     */
    protected $name;

    /**
     * Consumer API Key
     * @var string
     */
    protected $apikey;

    /**
     * Consumer Secret
     * @var string
     */
    protected $secret;

    /**
     * Request Token
     * @var string
     */
    protected $request_token;

    /**
     * Access Token
     * @var string
     */
    protected $access_token;

    /**
     * Verify Token
     * @var string
     */
    protected $verify_token;

    /**
     * URL to Request Profile
     * @var string
     */
    protected $url_profile;

    /**
     * URL to Request Token
     * @var string
     */
    protected $url_request_token;

    /**
     * URL to Access Token
     * @var string
     */
    protected $url_access_token;

    /**
     * URL to Verify Token
     * @var string
     */
    protected $url_verify_token;

    /**
     * Callback URL
     * @var string
     */
    protected $url_redirect;

    /**
     * Create OAuth_Login Client
     *
     * @param string $apikey Consumer ID
     * @param string $secret Consumer Secret
     * @param string $url_redirect Callback URL
     * @return void
     */
    public function __construct($apikey, $secret, $url_redirect = false)
    {

        // API Settings
        $this->apikey = $apikey;
        $this->secret = $secret;

        // Callback URL
        if (!empty($url_redirect)) {
            $this->url_redirect = $url_redirect;
        } else {
            // OAuth 2.0+
            if ($this->version >= 2.0) {
                $this->url_redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . '/oauth';

            // OAuth 1.0
            } else {
                $this->url_redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . '/oauth?state=' . get_class($this);
            }
        }
    }

    /**
     * Get Login URL to Connect Client with Server
     *
     * @param array $params Key-value array of parameters
     * @return string Login URL
     */
    abstract public function getLoginUrl($params = array());

    /**
     * Get Profile Information for Connected Account
     *
     * @param string $token Access token
     * @return array
     */
    abstract public function getProfile($token);

    /**
     * Get OAuth Provider
     *
     * @return string Provider Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get OAuth Version
     *
     * @return string OAuth Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sign OAuth Request
     *
     * @param OAuth_Request $request
     * @param string $token_secret
     * @return OAuth_Request Signed Request
     */
    protected function signRequest(OAuth_Request $request, $token_secret = null)
    {

        // HTTP Method
        $http_method = strtoupper($request->getMethod());

        // HTTP Url
        $http_url = $request->getUrl();

        // Request Parameters
        $params = array_merge($request->getParams(), array(
            'oauth_signature_method' => 'HMAC-SHA1'
        ));

        // HTTP Query
        $http_query = OAuth_Request::build_http_query($params);

        // Signature Key
        $signature_key = OAuth_Request::urlencode_rfc3986($this->secret) . '&' . OAuth_Request::urlencode_rfc3986($token_secret);

        // Signature Key
        $base_string = implode('&', array($http_method, OAuth_Request::urlencode_rfc3986($http_url), OAuth_Request::urlencode_rfc3986($http_query)));

        // Generate Signature
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_string, $signature_key, true));

        // OAuth Signature
        $request->setParams(array_merge(array(
            'oauth_signature' => $oauth_signature
        ), $params));

        // Return Signed Request
        return $request;
    }

    /**
     * Get Available OAuth Providers
     *
     * @return array
     */
    public static function getProviders()
    {

        // Social Networks
        $networks = array();

        // Disable on Agent Subdomains (OAuth Wont Work, Redirect URL Error Occurs)
        if (Settings::getInstance()->SETTINGS['agent'] !== 1) {
            return $networks;
        }

        // URL to OAuth Bridge
        $url = Settings::getInstance()->SETTINGS['URL_RAW'] . '/oauth/connect.php?provider=';

        // Connect using Facebook API
        if (!empty(Settings::getInstance()->SETTINGS['facebook_apikey']) && !empty(Settings::getInstance()->SETTINGS['facebook_secret'])) {
            $networks['facebook'] = array(
                'title'     => 'Facebook',
                'image'     => 'facebook.png',
                'connect'   => $url . 'facebook'
            );
        }

        // Connect using Google API
        if (!empty(Settings::getInstance()->SETTINGS['google_apikey']) && !empty(Settings::getInstance()->SETTINGS['google_secret'])) {
            $networks['google'] = array(
                'title'     => 'Google',
                'image'     => 'google.png',
                'connect'   => $url . 'google'
            );
        }

        // Connect using Windows Live API
        if (strtolower(Http_Uri::getScheme()) === 'https' && !empty(Settings::getInstance()->SETTINGS['microsoft_apikey']) && !empty(Settings::getInstance()->SETTINGS['microsoft_secret'])) {
            $networks['microsoft'] = array(
                'title'     => 'Windows Live',
                'image'     => 'microsoft.png',
                'connect'   => $url . 'microsoft'
            );
        }

        // Connect using Twitter
        if (!empty(Settings::getInstance()->SETTINGS['twitter_apikey']) && !empty(Settings::getInstance()->SETTINGS['twitter_secret'])) {
            $networks['twitter'] = array(
                'title'     => 'Twitter',
                'image'     => 'twitter.png',
                'connect'   => $url . 'twitter'
            );
        }

        // Connect using Yahoo
        if (!empty(Settings::getInstance()->SETTINGS['yahoo_apikey']) && !empty(Settings::getInstance()->SETTINGS['yahoo_secret'])) {
            $networks['yahoo'] = array(
                'title'     => 'Yahoo!',
                'image'     => 'yahoo.png',
                'connect'   => $url . 'yahoo'
            );
        }

        // Connect using LinkedIn
        if (!empty(Settings::getInstance()->SETTINGS['linkedin_apikey']) && !empty(Settings::getInstance()->SETTINGS['linkedin_secret'])) {
            $networks['linkedin'] = array(
                'title'     => 'LinkedIn',
                'image'     => 'linkedin.png',
                'connect'   => $url . 'linkedin'
            );
        }

        // Return
        return $networks;
    }

    /**
     * Get Available OAuth Calendar Providers
     *
     * @return array
     */
    public static function getCalendarProviders()
    {

        // Social Networks
        $networks = array();

        // Connect using Google API
        $google = null;
        if (!empty(Settings::getInstance()->SETTINGS['google_apikey']) && !empty(Settings::getInstance()->SETTINGS['google_secret'])) {
            try {
                // OAuth_Login_Google
                $google = new OAuth_Login_Google(Settings::getInstance()->SETTINGS['google_apikey'], Settings::getInstance()->SETTINGS['google_secret']);
                // Add to Networks
                $networks['Google'] = array('title' => 'Google', 'image' => 'google.png', 'connect' => $google->getLoginUrl(array('scope' => 'https://www.googleapis.com/auth/calendar', 'access_type'  => 'offline')));
            } catch (Exception_OAuthLoginError $e) {
                Log::error($e);
            }
        }

        // Connect using Windows Live API
        $microsoft = null;
        if (!empty(Settings::getInstance()->SETTINGS['microsoft_apikey']) && !empty(Settings::getInstance()->SETTINGS['microsoft_secret'])) {
            try {
                // OAuth_Login_Microsoft
                $microsoft = new OAuth_Login_Microsoft(Settings::getInstance()->SETTINGS['microsoft_apikey'], Settings::getInstance()->SETTINGS['microsoft_secret']);
                // Add to Networks
                $networks['Microsoft'] = array('title' => 'Windows Live', 'image' => 'microsoft.png', 'connect' => $microsoft->getLoginUrl(array('scope' => 'user.read')));
            } catch (Exception_OAuthLoginError $e) {
                Log::error($e);
            }
        }

        // Return
        return $networks;
    }

    /**
     * Get Available OAuth Analytics Providers
     *
     * @return array
     */
    public static function getAnalyticsProviders()
    {

        // Social Networks
        $networks = array();

        // Connect using Google API
        $google = null;
        if (!empty(Settings::getInstance()->SETTINGS['google_apikey']) && !empty(Settings::getInstance()->SETTINGS['google_secret'])) {
            try {
                // OAuth_Login_Google
                $google = new OAuth_Login_Google(Settings::getInstance()->SETTINGS['google_apikey'], Settings::getInstance()->SETTINGS['google_secret']);
                // Add to Networks
                $networks['Google'] = array('title' => 'Google', 'image' => 'google.png', 'connect' => $google->getLoginUrl(array('scope' => 'https://www.googleapis.com/auth/analytics.readonly', 'access_type'    => 'offline', 'approval_prompt' => 'force')));
            } catch (Exception_OAuthLoginError $e) {
                Log::error($e);
            }
        }

        // Return
        return $networks;
    }
}
