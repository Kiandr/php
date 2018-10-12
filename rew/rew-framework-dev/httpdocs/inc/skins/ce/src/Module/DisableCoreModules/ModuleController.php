<?php

namespace REW\Theme\Enterprise\Module\DisableCoreModules;

use REW\Core\Interfaces\InstallableInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * Disable core modules that are not used for this theme
 * @package REW\Theme\Enterprise\Module\DisableCoreModules
 */
class ModuleController implements InstallableInterface
{

    /**
     * Disabled modules
     * @var string[]
     */
    const DISABLED_MODULES = [
        'REW_SLIDESHOW_MANAGER',
        'REW_FEATURED_LISTINGS',
        'REW_FEATURED_LISTINGS_OVERRIDE'
    ];

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {
        foreach (static::DISABLED_MODULES as $module) {
            $this->settings['MODULES'][$module] = false;
        }
    }
}
