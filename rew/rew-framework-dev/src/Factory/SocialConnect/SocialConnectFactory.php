<?php

namespace REW\Factory\SocialConnect;

use REW\Core\Interfaces\SettingsInterface;
use REW\Model\SocialConnect\SocialConnectModel;

/**
 * SocialConnectFactory
 * @package REW\Factory\SocialConnect
 */
class SocialConnectFactory
{

    /**
     * @var string
     */
    const SOCIAL_CONNECT_LINK = '%s/oauth/connect.php?provider=%s';

    /**
     * @var string
     */
    const SOCIAL_CONNECT_KEY_ID = 'id';

    /**
     * @var string
     */
    const SOCIAL_CONNECT_KEY_TITLE = 'title';

    /**
     * @var string
     */
    const SOCIAL_CONNECT_KEY_CONNECT = 'connect';

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings) {
        $this->settings = $settings;
    }

    /**
     * @param array $data
     * @return SocialConnectModel
     */
    public function createFromArray(array $data)
    {
        $socialConnectModel = new SocialConnectModel();
        $socialConnectModel= $socialConnectModel
            ->withId(!empty($data[self::SOCIAL_CONNECT_KEY_ID]) ? $data[self::SOCIAL_CONNECT_KEY_ID] : null)
            ->withTitle(!empty($data[self::SOCIAL_CONNECT_KEY_TITLE]) ? $data[self::SOCIAL_CONNECT_KEY_TITLE] : null);

        // Generate connext url to connect
        if (!empty($data[self::SOCIAL_CONNECT_KEY_CONNECT])) {
            $socialConnectModel = $socialConnectModel->withConnectUrl(
                sprintf(static::SOCIAL_CONNECT_LINK,
                    $this->settings->SETTINGS['URL_RAW'],
                    $data[self::SOCIAL_CONNECT_KEY_CONNECT]
                )
            );
        }

        return $socialConnectModel;
    }
}
