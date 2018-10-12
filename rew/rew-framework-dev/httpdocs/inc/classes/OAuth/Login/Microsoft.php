<?php

/**
 * OAuth_Login_Microsoft is used for connecting to Microsoft Live via OAuth
 *
 * @link https://manage.dev.live.com/
 * @package OAuth
 */
class OAuth_Login_Microsoft extends OAuth_Login
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
    protected $name = 'Windows Live';

    /**
     * URL to Request Profile
     * @var string
     */
    protected $url_profile = 'https://graph.microsoft.com/v1.0/me';

    /**
     * URL to Request Token
     * @var string
     */
    protected $url_request_token = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';

    /**
     * URL to Access Token
     * @var string
     */
    protected $url_access_token = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

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
            'response_type' => 'code'
        ), $params);

        // Return URL
        return $this->url_request_token . '?' . OAuth_Request::build_http_query($params);
    }

    /**
     * Verify Token Code, Request Access Token
     *
     * @param string $code Verification Token
     * @return Access Token
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
            'grant_type'    => 'authorization_code'
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
     * @see OAuth_Login::getProfile()
     * @throws Exception_OAuthLoginError If Not 200 Status Code
     */
    public function getProfile($token)
    {

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_profile, array(), array("Authorization: Bearer " . $token));

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
            'first_name' => $profile['givenName'],
            'last_name'  => $profile['surname'],
            'email'      => $profile['userPrincipalName'],
            'image'      => "https://apis.live.net/v5.0/".$profile['id']."/picture?type=large",
            'link'       => "https://profile.live.com/cid-".$profile['id']."/",
            'full'       => $profile
        ) : false;
    }
}
