<?php

/**
 * OAuth_Login_Twitter is used for connecting to Twitter via OAuth
 *
 * @global $_SESSION[__CLASS__] Stores OAuth Secret
 * @link https://dev.twitter.com/apps
 * @package OAuth
 */
class OAuth_Login_Twitter extends OAuth_Login
{

    /**
     * OAuth Version 1.0
     * @var float
     */
    protected $version = 1.0;

    /**
     * OAuth Provider
     * @var string
     */
    protected $name = 'Twitter';

    /**
     * URL to Request Profile
     * @var string
     */
    protected $url_profile = 'https://api.twitter.com/1.1/account/verify_credentials.json';

    /**
     * URL to Request Token
     * @var string
     */
    protected $url_request_token = 'https://api.twitter.com/oauth/request_token';

    /**
     * URL to Access Token
     * @var string
     */
    protected $url_access_token = 'https://api.twitter.com/oauth/access_token';

    /**
     * URL to Verify Token
     * @var string
     */
    protected $url_verify_token = 'https://twitter.com/oauth/authenticate';

    /**
     * Get Login URL to Connect Client with Server
     *
     * @param array $params Key-value array of parameters
     * @return string|false Login URL
     * @uses OAuth_Request::__construct()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function getLoginUrl($params = array())
    {

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_request_token, array_merge($params, array(
            'oauth_version'   => '1.0',
            'oauth_timestamp' => time(),
            'oauth_nonce'     => md5(microtime() . mt_rand()),
            'oauth_callback' => $this->url_redirect,
            'oauth_consumer_key' => $this->apikey
        )));

        // Sign Request
        $request = $this->signRequest($request);

        // Request Token
        $response = $request->execute();

        // Error Occurred
        if ($request->getCode() != 200) {
            throw new Exception_OAuthLoginError;
        }

        // Require Request Token
        if (empty($response)) {
            return false;
        }

        // Parse Response
        parse_str($response, $parts);

        // Require Token & Secret
        if (empty($parts['oauth_token']) || empty($parts['oauth_token_secret'])) {
            return false;
        }

        // Set Request Token
        $this->request_token = $parts['oauth_token'];

        // Set Token Secret
        $_SESSION[__CLASS__] = $parts['oauth_token_secret'];

        // Return Request URL
        return $this->url_verify_token . '?' . OAuth_Request::build_http_query(array(
            'oauth_token' => $this->request_token
        ));
    }

    /**
     * Verify Token Code, Request Access Token
     *
     * @param string $request_token Request Token
     * @param string $verify_token Verification Token
     * @return Access Token
     * @uses OAuth_Request::__construct()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function verifyToken($request_token, $verify_token)
    {

        // Set Request Token
        $this->request_token = $request_token;

        // Set Verification Token
        $this->verify_token = $verify_token;

        // Set Token Secret
        $token_secret = $_SESSION[__CLASS__];

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_access_token, array(
            'oauth_version'      => '1.0',
            'oauth_timestamp'    => time(),
            'oauth_nonce'        => md5(microtime() . mt_rand()),
            'oauth_callback'     => $this->url_redirect,
            'oauth_consumer_key' => $this->apikey,
            'oauth_token'        => $this->request_token,
            'oauth_verifier'     => $this->verify_token
        ));

        // Sign Request
        $request = $this->signRequest($request, $token_secret);

        // Request Token
        $response = $request->execute();

        // Error Occurred
        if ($request->getCode() != 200) {
            throw new Exception_OAuthLoginError;
        }

        // Require Response
        if (empty($response)) {
            return false;
        }

        // Parse Response
        parse_str($response, $parts);
        //$parts['oauth_token'], $parts['oauth_token_secret'], $parts['user_id'], $parts['screen_name']

        // Require Access Token
        if (empty($parts['oauth_token'])) {
            return false;
        }

        // Set Access Token
        $this->access_token = $parts['oauth_token'];

        // Set Token Secret
        $_SESSION[__CLASS__] = $parts['oauth_token_secret'];

        // Return Access Token
        return $this->access_token;
    }

    /**
     * @see OAuth_Login::getProfile()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function getProfile($token)
    {

        // Set Access Token
        $this->access_token = $token;

        // Set Token Secret
        $token_secret = $_SESSION[__CLASS__];

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_profile, array(
            'oauth_version'      => '1.0',
            'oauth_timestamp'    => time(),
            'oauth_nonce'        => md5(microtime() . mt_rand()),
            'oauth_consumer_key' => $this->apikey,
            'oauth_token'        => $this->access_token
        ));

        // Sign Request
        $request = $this->signRequest($request, $token_secret);

        // Request Token
        $response = $request->execute();

        // Error Occurred
        if ($request->getCode() != 200) {
            throw new Exception_OAuthLoginError;
        }

        // Require Response
        if (empty($response)) {
            return false;
        }

        // Decode JSON
        $profile = json_decode($response, true);

        // Split Name
        list ($first_name, $last_name) = explode(' ', $profile['name']);

        // Return Profile
        return is_array($profile) ? array(
            'token'      => $token,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => null,
            'image'      => $profile['profile_image_url'],
            'link'       => (!empty($profile['screen_name']) ? 'https://twitter.com/' . $profile['screen_name'] : ''),
            'full'       => $profile
        ) : false;
    }
}
