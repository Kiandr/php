<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Lenders extends Backend_AuthCheck_App
{

    // Can View Lenders
    public function view()
    {
        return (Settings::getInstance()->MODULES['REW_LENDERS_MODULE'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()) ||
            $this->auth->isAssociate())
        ) ? true : false;
    }

    // Can View Lender Summary
    public function view_summary()
    {
        return (Settings::getInstance()->MODULES['REW_LENDERS_MODULE'] &&
            (($this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->isAgent()) ||
            $this->auth->isAssociate())
        ) ? true : false;
    }

    // Can Manage Lenders
    public function manage()
    {
        return (Settings::getInstance()->MODULES['REW_LENDERS_MODULE'] &&
            $this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()
        ) ? true : false;
    }

    // Can Manage Lenders
    public function manage_self()
    {
        return (Settings::getInstance()->MODULES['REW_LENDERS_MODULE'] &&
            $this->auth->isLender()
        ) ? true : false;
    }

    // Can Manage Action Plans
    public function action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] && Settings::getInstance()->MODULES['REW_LENDERS_MODULE'] &&
                $this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()
        ) ? true : false;
    }

    // Can Manage Own Action Plans
    public function own_action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] && Settings::getInstance()->MODULES['REW_LENDERS_MODULE'] &&
            $this->auth->isLender()
        ) ? true : false;
    }
}
