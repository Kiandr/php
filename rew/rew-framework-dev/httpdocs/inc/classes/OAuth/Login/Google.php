<?php

/**
 * OAuth_Login_Google is used for connecting to Google via OAuth
 *
 * @link https://code.google.com/apis/console/#access
 * @package OAuth
 */
class OAuth_Login_Google extends OAuth_Login
{

    /**
     * OAuth Version 2.0
     * @var float
     */
    protected $version = 2.0;

    /**
     * OAuth Provider Name
     * @var string
     */
    protected $name = 'Google';

    /**
     * URL to Request Profile
     * @var string
     */
    protected $url_profile = 'https://www.googleapis.com/oauth2/v2/userinfo';

    /**
     * URL to Request Token
     * @var string
     */
    protected $url_request_token = 'https://accounts.google.com/o/oauth2/auth';

    /**
     * URL to Access Token
     * @var string
     */
    protected $url_access_token = 'https://accounts.google.com/o/oauth2/token';

    /**
     * URL to Validate Token
     * @var string
     */
    protected $url_validate_token = 'https://www.googleapis.com/oauth2/v1/tokeninfo';

    /**
     * Get Login URL to Connect Client with Server
     *
     * @param array $params Key-value array of parameters
     * @return string Login URL
     */
    public function getLoginUrl($params = array())
    {

        // Merge Params
        $params = array_merge(array(
            'state'         => __CLASS__,
            'redirect_uri'  => $this->url_redirect,
            'client_id'     => $this->apikey,
            'response_type' => 'code',
        ), $params);

        // Return URL
        return $this->url_request_token . '?' . OAuth_Request::build_http_query($params);
    }

    /**
     * Verify Token Code, Request Access Token
     * @param string $code Verification Token
     * @param boolean $full flag to return the full response or not
     * @return Access Token | Request response
     * @uses OAuth_Request::__construct()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function verifyToken($code, $full = false)
    {

        // Set Verification Token
        $this->verify_token = $code;

        // OAuth Request
        $request = new OAuth_Request('POST', $this->url_access_token, array(
            'redirect_uri'  => $this->url_redirect,
            'client_id'     => $this->apikey,
            'client_secret' => $this->secret,
            'code'          => $this->verify_token,
            'grant_type'    => 'authorization_code',
        ));

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
        $parts = json_decode($response, true);

        // Require Access Token
        if (empty($parts['access_token'])) {
            return false;
        }

        // Set Access Token
        $this->access_token = $parts['access_token'];

        if ($full) {
            // Return response
            return $parts;
        } else {
            // Return Access Token
            return $this->access_token;
        }
    }

    /**
     * Use Refresh token to get a new access token
     *
     * @param string $refresh_token Refresh Token
     * @param boolean $full flag to return the full response or not
     * @return Access Token | Request response
     * @uses OAuth_Request::__construct()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function refreshToken($refresh_token, $full = false)
    {

        // Set Verification Token
        $this->verify_token = $code;

        // OAuth Request
        $request = new OAuth_Request('POST', $this->url_access_token, array(
            'redirect_uri'  => $this->url_redirect,
            'client_id'     => $this->apikey,
            'client_secret' => $this->secret,
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token',
        ));

        // Request Token
        $response = $request->execute();

        // Require Response
        if (empty($response)) {
            throw new Exception_OAuthLoginError('No Response');
        }
        // Parse Response
        $parts = json_decode($response, true);

        // Error Occurred
        if ($request->getCode() != 200) {
            throw new Exception_OAuthLoginError(isset($parts['error'])?$parts['error']:'Unknown Error');
        }

        // Require Access Token
        if (empty($parts['access_token'])) {
            throw new Exception_OAuthLoginError('Missing Access Token');
        }

        // Set Access Token
        $this->access_token = $parts['access_token'];

        if ($full) {
            // Return response
            return $parts;
        } else {
            // Return Access Token
            return $this->access_token;
        }
    }

    /**
     * @see OAuth_Login::getProfile()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function getProfile($token)
    {

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_profile, array(
            'access_token'  => $token
        ));

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

        // Return Profile
        return is_array($profile) ? array(
            'token'      => $token,
            'first_name' => $profile['given_name'],
            'last_name'  => $profile['family_name'],
            'email'      => $profile['email'],
            'image'      => (!empty($profile['picture']) ? $profile['picture'] : null),
            'link'       => $profile['link'],
            'full'       => $profile
        ) : false;
    }

    /**
     * Validate Access Token
     * @return boolean
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function validateToken($token)
    {

        // OAuth Request
        $request = new OAuth_Request('POST', $this->url_validate_token, array(
            'access_token' => $token
        ));

        // Request Token
        $response = $request->execute();

        // Invalid token
        if ($request->getCode() == 400) {
            return false;
        }

        // Require Response
        if (empty($response)) {
            return false;
        }

        // Parse Response
        $parts = json_decode($response, true);

        // Require Access Token
        if (!empty($parts['error'])) {
            return false;
        }

        // Valid token
        return true;
    }
}
