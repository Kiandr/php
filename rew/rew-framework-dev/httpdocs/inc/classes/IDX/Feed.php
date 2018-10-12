<?php

/**
 * IDX Feed
 * @package IDX
 */
abstract class IDX_Feed
{

    /**
     * CDN Hostname for Cloud Images
     * @var string
     */
    const IMAGE_CDN_HOSTNAMES = ['rackcdn.com', 'rew-feed-images.global.ssl.fastly.net'];

    /**
     * Small Image (240x240)
     * @var string
     */
    const IMAGE_SIZE_SMALL = 's';

    /**
     * Medium Image (640x640)
     * @var string
     */
    const IMAGE_SIZE_MEDIUM = 'm';

    /**
     * Large Image (1024x1024)
     * @var string
     */
    const IMAGE_SIZE_LARGE = 'l';

    /**
     * Original Image Size
     * @var string
     */
    const IMAGE_SIZE_ORIGINAL = 'o';

    /**
     * Get Class Name for IDX Feed
     * @param string $feed
     * @return string
     */
    public static function getClass($feed = 'default')
    {
        // Default Feed
        if ($feed === 'default') {
            $feed = Settings::getInstance()->IDX_FEED;
        }
        // Return Class Name
        return __CLASS__ . '_' . strtoupper(str_replace(array('-', '_'), '', $feed));
    }

    /**
     * Modify default search panels
     * @param array $defaults
     * @return array
     */
    public static function getPanels(array $defaults = array())
    {
        return $defaults;
    }

    /**
     * Return image url with correct size (for REW Cloud Images)
     * @param string $url
     * @param string $size
     * @return string
     */
    public static function thumbUrl($url, $size = self::IMAGE_SIZE_ORIGINAL)
    {
        foreach (self::IMAGE_CDN_HOSTNAMES as $hostname) {
            if (stristr($url, $hostname) !== false) {
                return preg_replace('/\-['
                    . self::IMAGE_SIZE_SMALL
                    . self::IMAGE_SIZE_MEDIUM
                    . self::IMAGE_SIZE_LARGE
                    . self::IMAGE_SIZE_ORIGINAL
                    . '].jpg$/', '-' . $size . '.jpg', $url);
            }
        }
        return $url;
    }
}
