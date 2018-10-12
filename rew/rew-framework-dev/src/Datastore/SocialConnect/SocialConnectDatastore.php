<?php
namespace REW\Datastore\SocialConnect;

use REW\Core\Interfaces\SettingsInterface;
use REW\Factory\SocialConnect\SocialConnectFactory;
use \Http_Uri;

/**
 * Class SocialConnectDatastore
 * @package REW\Datastore\SocialConnect
 */
class SocialConnectDatastore
{
    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var SocialConnectFactory
     */
    protected $socialConnectFactory;

    /**
     * @param SettingsInterface $settings
     * @param SocialConnectFactory $socialConnectFactory
     */
    public function __construct(SettingsInterface $settings, SocialConnectFactory $socialConnectFactory)
    {
        $this->settings = $settings;
        $this->socialConnectFactory = $socialConnectFactory;
    }

    /**
     * @return SocialConnectResult[]
     */
    public function getSocialConnects()
    {
        // Social SocialConnects
        $socialConnects = [];

        // Disable on Agent Subdomains (OAuth Wont Work, Redirect URL Error Occurs)
        if ($this->settings->SETTINGS['agent'] !== 1) {
            return $socialConnects;
        }

        // Connect using Facebook API
        if (!empty($this->settings->SETTINGS['facebook_apikey']) && !empty($this->settings->SETTINGS['facebook_secret'])) {
            $socialConnects[] = $this->socialConnectFactory->createFromArray([
                'id'      => 'facebook',
                'title'   => 'Facebook',
                'connect' => 'facebook'
            ]);
        }

        // Connect using Google API
        if (!empty($this->settings->SETTINGS['google_apikey']) && !empty($this->settings->SETTINGS['google_secret'])) {
            $socialConnects[] = $this->socialConnectFactory->createFromArray([
                'id'      => 'google',
                'title'   => 'Google',
                'connect' => 'google'
            ]);
        }

        // Connect using Windows Live API
        if (strtolower(Http_Uri::getScheme()) === 'https' && !empty($this->settings->SETTINGS['microsoft_apikey']) && !empty($this->settings->SETTINGS['microsoft_secret'])) {
            $socialConnects[] = $this->socialConnectFactory->createFromArray([
                'id'      => 'microsoft',
                'title'   => 'Windows Live',
                'connect' => 'microsoft'
            ]);
        }

        // Connect using Twitter
        if (!empty($this->settings->SETTINGS['twitter_apikey']) && !empty($this->settings->SETTINGS['twitter_secret'])) {
            $socialConnects[] = $this->socialConnectFactory->createFromArray([
                'id'      => 'twitter',
                'title'   => 'Twitter',
                'connect' => 'twitter'
            ]);
        }

        // Connect using Yahoo
        if (!empty($this->settings->SETTINGS['yahoo_apikey']) && !empty($this->settings->SETTINGS['yahoo_secret'])) {
            $socialConnects[] = $this->socialConnectFactory->createFromArray([
                'id'      => 'yahoo',
                'title'   => 'Yahoo!',
                'connect' => 'yahoo'
            ]);
        }

        // Connect using LinkedIn
        if (!empty($this->settings->SETTINGS['linkedin_apikey']) && !empty($this->settings->SETTINGS['linkedin_secret'])) {
            $socialConnects[] = $this->socialConnectFactory->createFromArray([
                'id'      => 'linkedin',
                'title'   => 'LinkedIn',
                'connect' => 'linkedin'
            ]);
        }

        // Return
        return $socialConnects;
    }
}
