<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\AuthInterface;

/**
 * Class CustomAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CustomAuth extends Auth
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * Create Auth
     * @param SettingsInterface $settings
     * @param AuthInterface     $auth
     */
    public function __construct(SettingsInterface $settings, AuthInterface $auth)
    {
        $this->settings = $settings;
        $this->auth = $auth;
    }

    /**
     * Can Manage Fields
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageFields()
    {
        return $this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_CUSTOM_FIELDS)
            || $this->auth->isAssociate();
    }

    /**
     * Can Delete Fields
     *
     * @param AuthInterface $this->auth Current Authuser
     *
     * @return bool
     */
    public function canDeleteFields()
    {
        return $this->auth->isSuperAdmin();
    }
}
