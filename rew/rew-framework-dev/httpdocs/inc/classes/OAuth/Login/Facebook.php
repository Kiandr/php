<?php

/**
 * OAuth_Login_Facebook is used for connecting to Facebook via OAuth
 *
 * @link https://developers.facebook.com/apps
 * @package OAuth
 */
class OAuth_Login_Facebook extends OAuth_Login
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
    protected $name = 'Facebook';

    /**
     * URL to Request Profile
     * @var string
     */
    protected $url_profile = 'https://graph.facebook.com/v2.8/me';

    /**
     * URL to Request Token
     * @var string
     */
    protected $url_request_token = 'https://www.facebook.com/v2.8/dialog/oauth';

    /**
     * URL to Access Token
     * @var string
     */
    protected $url_access_token = 'https://graph.facebook.com/v2.8/oauth/access_token';

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
     * @param boolean $full flag to return the full response or not
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

        // Decode Response
        $response = json_decode($response, true);

        // Require Access Token
        if (empty($response['access_token'])) {
            return false;
        }

        // Set Access Token
        $this->access_token = $response['access_token'];

        if ($full) {
            // Return response
            return $response;
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
            'access_token'  => $token,
            'fields'        => 'first_name,last_name,email,link,picture.type(large)'
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
            'token'           => $token,
            'first_name'      => $profile['first_name'],
            'last_name'       => $profile['last_name'],
            'email'           => $profile['email'],
            'image'           => $profile['picture']['data']['url'],
            'image_default'   => $profile['picture']['data']['is_silhouette'],
            'link'            => $profile['link'],
            'full'            => $profile
        ) : false;
    }
}
