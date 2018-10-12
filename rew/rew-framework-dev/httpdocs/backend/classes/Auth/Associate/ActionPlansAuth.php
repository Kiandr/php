<?php

namespace REW\Backend\Auth\Associate;

use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Auth;

/**
 * Class ActionPlansAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth\Associate
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ActionPlansAuth extends Auth
{

    /**
     * Check if authorized to manage Agent Action Plans
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && $auth->isSuperAdmin();
    }

    /**
     * Check if authorized to manage the authusers own action plans
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwnActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && $auth->isAssociate();
    }
}
