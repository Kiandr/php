<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\BDX\SettingsInterface as BDXSettingsInterface;
use REW\Backend\Auth\Auth;

/**
 * Class BDXAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class BDXAuth extends Auth
{

    /**
     * @var BDXSettingsInterface
     */
    protected $bdxSettings;

    /**
     * Create Auth
     * @param SettingsInterface    $settings
     * @param BDXSettingsInterface $bdxSettings
     */
    public function __construct(SettingsInterface $settings, BDXSettingsInterface $bdxSettings = null)
    {
        $this->settings = $settings;
        $this->bdxSettings = $bdxSettings;
    }
    
    /**
     * Can Manage Settings
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageSettings(AuthInterface $auth)
    {
        return !empty($this->settings->MODULES['REW_BUILDER'])
            && isset($this->bdxSettings)
            && $this->bdxSettings->STATES
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage IDX Metadata
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageMeta(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_BUILDER']
            && $this->settings->MODULES['REW_IDX_META_INFORMATION']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage IDX Quicksearch
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageQuicksearch(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_IDX_BUILDER']
            && $this->settings->MODULES['REW_IDX_QUICKSEARCH']
            && $auth->isSuperAdmin();
    }
}
