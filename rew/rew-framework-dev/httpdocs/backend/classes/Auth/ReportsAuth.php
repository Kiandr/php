<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class ReportsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ReportsAuth extends Auth
{

    /**
     * Can View Reports
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewReports(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_REPORTS_GOOGLE_ANALYTICS)
            || $auth->adminPermission($auth::PERM_REPORTS_AGENT_ALL)
            || $auth->adminPermission($auth::PERM_REPORTS_LISTINGS)
            || $auth->adminPermission($auth::PERM_REPORTS_AGENT_ALL)
            || ($auth->isAgent() && $auth->hasPermission($auth::PERM_REPORTS_AGENT));
    }

    /**
     * Can View Agent Response Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewResponseReport(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_REPORTS_AGENT_ALL);
    }

    /**
     * Can View Own Response Reports
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewOwnResponseReports(AuthInterface $auth)
    {
        return $auth->isAgent() && $auth->hasPermission($auth::PERM_REPORTS_AGENT);
    }

    /**
     * Can View Analytics Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewAnalyticsReport(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
        || $auth->adminPermission($auth::PERM_REPORTS_GOOGLE_ANALYTICS);
    }

    /**
     * Can View Analytics Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canConnectToAnalytics(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can View Listings Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewListingReport(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_REPORTS_LISTINGS);
    }

    /**
     * Can View Dialer Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewDialerReport(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_ESPRESSO']
            && ($auth->isSuperAdmin()
            || $auth->isAssociate());
    }

    /**
     * Can View Own Dialer Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewOwnDialerReport(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_ESPRESSO']
            && $auth->hasPermission($auth::PERM_REPORTS_ESPRESSO);
    }

    /**
     * Can View Tasks Report
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewActionPlanReports(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && $auth->isSuperAdmin();
    }
}
