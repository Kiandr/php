<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\SettingsInterface;

/**
 * Class Auth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class Auth
{

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Create Auth
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }
}
