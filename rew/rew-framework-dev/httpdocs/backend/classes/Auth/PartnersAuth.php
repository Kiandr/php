<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class PartnersAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class PartnersAuth extends Auth
{

    /**
     * Can Conversion Tracking
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewPartners(AuthInterface $auth)
    {
        return $this->canManageBombomb($auth)
            || $this->canManageEspresso($auth)
            || $this->canManageFollowupboss($auth)
            || $this->canManageGrasshopper($auth)
            || $this->canManageWiseagent($auth)
            || $this->canManageZillow($auth)
            || $this->canManageFirstcallagent($auth)
            || $this->canManageDotloop($auth);
    }

    /**
     * Can Manage Bombbomb
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageBombomb(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_BOMBBOMB']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_PARTNERS_BOMBBOMB_AGENT));
    }

    /**
     * Can Manage Espresso
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageEspresso(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_ESPRESSO']
            && ($auth->isSuperAdmin()
            || $auth->isAssociate()
            || $auth->hasPermission($auth::PERM_PARTNERS_ESPRESSO));
    }

    /**
     * Can Manage Followupboss
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageFollowupboss(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_FOLLOWUPBOSS']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage Happy Grasshopper
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageGrasshopper(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_GRASSHOPPER']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_PARTNERS_GRASSHOPPER_AGENT));
    }

    /**
     * Can Manage Wiseagent
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageWiseagent(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_WISEAGENT']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_PARTNERS_WISEAGENT_AGENT));
    }

    /**
     * Can Manage Zillow
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageZillow(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_ZILLOW']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_PARTNERS_ZILLOW));
    }

    /**
     * Can Manage DotLoop
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageDotloop(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_DOTLOOP']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_PARTNERS_DOTLOOP));
    }

    /**
     * Can Manage FirstCallAgent
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageFirstcallagent(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_FIRSTCALLAGENT']
            && ($auth->isSuperAdmin()
                || $auth->hasPermission($auth::PERM_PARTNERS_FIRSTCALLAGENT));
    }
}
