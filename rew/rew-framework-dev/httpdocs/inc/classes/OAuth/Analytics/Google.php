<?php

/**
 * OAuth_Analytics_Google is used for connecting to Google Analytics via OAuth
 *
 * @link https://code.google.com/apis/console/#access
 */
class OAuth_Analytics_Google extends OAuth_Analytics
{

    /**
     * Provider Name
     * @var string
     */
    protected $name = 'Google';

    /**
     * Base HTTP Request URL
     * @var string
     */
    protected $url_analytics = 'https://www.googleapis.com/analytics/';

    /**
     * Request XML Data from Google
     * @param string $url
     * @param array $params
     * @throws Exception If Non-200 Status Code Returned
     * @return SimpleXMLElement
     */
    public function getXML($url, $params = array())
    {

        // Setup OAuth Request
        // OAuth Request
        $request = new OAuth_Request('GET', $url, array(
                'access_token'  => $this->access_token,
        ));

        // Execute Request
        $response = $request->execute();

        // Require 200 HTTP Code
        if ($request->getCode() == 200) {
            // Require Response
            if (empty($response)) {
                return false;
            }

            // Load XML
            $xml = @new SimpleXMLElement($response);

            // XML Namespaces
            $namespaces = $xml->getNamespaces(true);

            // Register XML Namespaces
            foreach ($namespaces as $prefix => $ns) {
                $xml->registerXPathNamespace($prefix, $ns);
            }

            // Return XML
            return $xml;
        } else {
            // Throw Exception
            throw new Exception_OAuthAnalyticsError($response);
        }
    }

    /**
     * Validates access token
     * @return boolean
     */
    public function validateToken()
    {

        // Get authusers data
        $network_google = $this->authuser->info('network_google');

        // Set Valid Token Flag
        $valid_token = false;

        // Get Analytics providers
        $providers = OAuth_Login::getAnalyticsProviders();

        // Ensure we have the network set
        if (!empty($network_google)) {
            // Decode network data
            $network_google = get_object_vars(json_decode($network_google));

            if (!empty($network_google['access_token'])) {
                $valid_token = true;
                if (!empty($network_google['refresh_token']) && !empty($network_google['expire_time']) && $this->isExpired($network_google['expire_time'])) {
                    $google_oauth = new OAuth_Login_Google(Settings::getInstance()->SETTINGS['google_apikey'], Settings::getInstance()->SETTINGS['google_secret']);
                    $token = $google_oauth->refreshToken($network_google['refresh_token'], true);
                    if (!empty($token)) {
                        $network_google['access_token'] = $token['access_token'];
                        $this->access_token = $token['access_token'];

                        // Calculate expiry time
                        if (!empty($response['expires_in'])) {
                            $response['expire_time'] = time() + $response['expires_in'];
                        }

                        $network_google = json_encode($network_google);

                        // App DB
                        $db = \Container::getInstance()->get(\REW\Core\Interfaces\DBInterface::class);

                        if ($db->fetch("UPDATE `" . LM_TABLE_AGENTS . "` SET `network_google` = :network_google WHERE `id` = :id", [
                            'network_google' => $network_google,
                            'id' => $this->authuser->info('id')
                        ])) {
                            $this->authuser->info('network_google', $network_google);
                        }
                    } else {
                        $valid_token = false;
                    }
                } else {
                    $this->access_token = $network_google['access_token'];
                }
            }
        }

        // Open a popup if we don't have a valid token
        if (!$valid_token) {
            $providerUrl = $providers['Google']['connect'];
            if ($providerUrl) {
                $this->page->writeJS("var popup = (window.open(" . json_encode($providerUrl) . ", 'rewanalytics', 'height=600,width=850,scrollbars=1,location=no,toolbar=no,resizable=yes')); if (popup) popup.focus();");
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * Gets the timezone associated with the current calendar
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
