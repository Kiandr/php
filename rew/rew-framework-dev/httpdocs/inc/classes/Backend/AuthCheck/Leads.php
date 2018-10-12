<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Leads extends Backend_AuthCheck_App
{

    // Can View Leads
    public function view()
    {
        return ($this->manage() || $this->auth->isLender());
    }

    // Can Add/Edit Leads
    public function manage()
    {
        return (($this->auth->info('mode') == 'admin' && $this->auth->isSuperAdmin()) ||
                ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LEADS_ALL)) ||
                ($this->auth->info('mode') == 'agent') ||
                $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can Delete Leads
    public function delete()
    {
        return (
            $this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LEADS_DELETE)) ||
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can View Reminders
    public function reminders()
    {
        return $this->manage();
    }

    // Can View Transactions
    public function transactions()
    {
        return $this->manage();
    }

    // Can View Messages
    public function messages()
    {
        return $this->manage();
    }

    // Can Assign Leads
    public function assign()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LEADS_ASSIGN)) ||
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can Share Leads
    public function share()
    {
        return (
            $this->auth->isSuperAdmin() ||
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can Email Leads
    public function email()
    {
        return (
            $this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_LEADS_EMAILS) ||
            $this->auth->isAssociate() ||
            $this->auth->isLender()
        ) ? true : false;
    }

    // Can Email Leads
    public function text()
    {
        return (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'] && (
            $this->auth->isSuperAdmin() ||
            $this->auth->hasPermission(Auth::PERM_LEADS_TEXT) ||
            $this->auth->isAssociate()
        )) ? true : false;
    }

    // Can View Action Plans
    public function view_action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] &&
            ($this->manage() || $this->auth->isAssociate() || $this->auth->isLender())
        ) ? true : false;
    }

    // Can Assign Action Plans
    public function assign_action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] &&
            ($this->auth->isSuperAdmin() || $this->auth->isAssociate() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_ACTION_PLAN_ASSIGNMENTS)))
        ) ? true : false;
    }

    // Can Manage Action Plans
    public function action_plans()
    {
        return (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS']) && $this->auth->isSuperAdmin()) ? true : false;
    }

    // Can Manage API
    public function api()
    {
        return (Settings::getInstance()->MODULES['REW_CRM_API'] &&
            $this->is_super_admin_as_admin()
         ) ? true : false;
    }

    // Can Manage Autoresponders
    public function autoresponders()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LEADS_AUTO_RESPONDERS))
        ) ? true : false;
    }

    // Can Manage Campaigns
    public function campaigns()
    {
        return (($this->auth->info('mode') == 'agent' && $this->auth->isSuperAdmin()) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_LEADS_CAMPAIGNS)) ||
            $this->is_super_admin_as_admin()
        ) ? true : false;
    }

    // Can Manage Documents
    public function docs()
    {
        return ($this->auth->info('mode') == 'agent' || $this->auth->isSuperAdmin()) ? true : false;
    }

    // Can Manage Social Media
    public function social()
    {
        return $this->is_super_admin_as_admin() ? true : false;
    }

    // Can Manage Groups
    public function groups()
    {
        return ($this->auth->isSuperAdmin() ||
            $this->auth->isAgent() ||
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can Export AS CSV
    public function export()
    {
        return ($this->auth->isSuperAdmin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_LEADS_BACKUP)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_LEADS_ALL_BACKUP))
        ) ? true : false;
    }

    // Can Export AS CSV
    public function files()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_CMS_FILES)) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_LEAD_FILES))
        ) ? true : false;
    }

    // Can Quicksend Bomb Bomb
    public function bombbomb()
    {
        if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_BOMBBOMB']) &&
            ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_PARTNERS_BOMBBOMB_AGENT)))
        ) {
            //Check for Bomb-Bomb keys
            $bb = $this->auth->info('partners.bombbomb');
            if (!empty($bb['api_key'])) {
                return true;
            }
        }
        return false;
    }

    // Can use Espresso
    public function espresso()
    {
        return (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) &&
            ($this->auth->isSuperAdmin() || $this->auth->isAssociate() ||
            ($this->auth->isAgent() && $this->auth->hasPermission(Auth::PERM_PARTNERS_ESPRESSO)))
        ) ? true : false;
    }
}
