<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class DashboardAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class DashboardAuth
{

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * Create Auth
     * @param AuthInterface     $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Can View Dashboard
     * @return bool
     */
    public function canViewDashboard()
    {
        return ($this->auth->isSuperAdmin()
            || $this->auth->isAgent());
    }
}
