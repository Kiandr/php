<?php

namespace REW\Theme\Enterprise\Provider;

use REW\Core\Interfaces\ProviderInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\SettingsFileMergerInterface;

/**
 * Settings Override Provider
 * @package REW\Theme\Enterprise\Provider
 */
class SettingsOverrideProvider implements ProviderInterface
{

    /**
     * @const string
     */
    const SETTINGS_FILE = __DIR__ . '/../../config/settings.yml';

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var SettingsFileMergerInterface
     */
    protected $merger;

    /**
     * @param SettingsInterface $settings
     * @param SettingsFileMergerInterface $merger
     */
    public function __construct(SettingsInterface $settings, SettingsFileMergerInterface $merger)
    {
        $this->settings = $settings;
        $this->merger = $merger;
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $file = static::SETTINGS_FILE;
        if (file_exists($file)) {
            $config = $this->settings->getConfig();
            $config = $this->merger->merge($file, $config);
            foreach ($config as $key => $value) {
                $this->settings[$key] = $value;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [];
    }
}
