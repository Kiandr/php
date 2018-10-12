<?php

namespace REW\Backend\Auth\Lender;

use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\LendersAuth;

/**
 * Class ActionPlansAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth\Agent
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class LenderActionPlansAuth extends LendersAuth
{

    /**
     * Check if authorized to manage Agent Action Plans
     *
     * @param Auth $auth Current Authuser
     *
     * @return bool
     */
    public function canManageActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && $this->settings->MODULES['REW_LENDERS_MODULE']
            && $auth->info('mode') == 'admin' && $auth->isSuperAdmin();
    }

    /**
     * Check if authorized to manage the authusers own action plans
     *
     * @param Auth $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwnActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && $this->settings->MODULES['REW_LENDERS_MODULE']
            && $auth->isLender();
    }
}
