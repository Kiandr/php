<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Associates extends Backend_AuthCheck_App
{

    // Can View Associates
    function view()
    {
        return (Settings::getInstance()->MODULES['REW_ISA_MODULE'] &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->isAgent()))
        ) ? true : false;
    }

    // Can Manage Associates
    function manage()
    {
        return (Settings::getInstance()->MODULES['REW_ISA_MODULE'] &&
            $this->is_super_admin_as_admin()
        ) ? true : false;
    }

    // Can Delete Associates
    function manage_self()
    {
        return (Settings::getInstance()->MODULES['REW_ISA_MODULE'] &&
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can Send Email
    function email()
    {
        return ($this->manage() || ($this->auth->info('mode') == 'agent' && $this->auth->isAgent())) ? true : false;
    }

    // Can Manage Action Plans
    function action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] && $this->manage()) ? true : false;
    }

    // Can Manage Action Plans
    function own_action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] && $this->manage_self()) ? true : false;
    }
}
