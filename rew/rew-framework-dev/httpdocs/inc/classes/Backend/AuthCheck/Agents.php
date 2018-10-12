<?php

/**
 * @package Backend
 */
class Backend_AuthCheck_Agents extends Backend_AuthCheck_App
{

    // Can View Agents
    public function view()
    {
        return ($this->auth->isSuperAdmin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_AGENTS_VIEW)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_AGENTS_MANAGE)) ||
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Can Add/Edit Agents
    public function manage()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_AGENTS_MANAGE))) ? true : false;
    }

    // Can Add/Edit Self
    public function manage_own()
    {
        return $this->auth->isAgent() ? true : false;
    }

    // Can Delete Agents
    public function delete()
    {
        return ($this->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_AGENTS_DELETE))
        ) ? true : false;
    }

    // Can Send Email
    public function email()
    {
        return ($this->auth->isSuperAdmin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_AGENTS_EMAIL)) ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_AGENTS_MANAGE) && $this->auth->hasPermission(Auth::PERM_AGENTS_EMAIL)) ||
            $this->auth->isAssociate()
        ) ? true : false;
    }

    // Manage Agent Autoresponders
    public function autoresponder()
    {
        return $this->is_super_admin_as_admin();
    }

    // Manage Agent History
    public function history()
    {
        return $this->is_super_admin_as_admin();
    }

    // Manage Agent Permissions
    public function permissions()
    {
        return $this->is_super_admin_as_admin();
    }

    // Manage Agent Offices
    public function offices()
    {
        return $this->is_super_admin_as_admin();
    }

    // Can Manage Action Plans
    public function action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] &&
            ($this->auth->is_super_admin() ||
            ($this->auth->info('mode') == 'admin' && $this->auth->adminPermission(Auth::PERM_AGENTS_MANAGE)) ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::PERM_AGENTS_VIEW)) ||
            $this->auth->isAssociate())
        ) ? true : false;
    }

    // Can Manage Own Action Plans
    public function own_action_plans()
    {
        return (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] &&
            $this->auth->isAgent()
        ) ? true : false;
    }

    // Can Push to Google Calander
    public function google_calander_push()
    {
        return (Settings::getInstance()->MODULES['REW_GOOGLE_CALENDAR'] &&
            ($this->auth->is_super_admin_as_admin() ||
            ($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::CALENDAR_GOOGLE_PUSH)))
        ) ? true : false;
    }

    // Can  Push to Outlook Calander
    public function outlook_calander_push()
    {
        return (Settings::getInstance()->MODULES['REW_OUTLOOK_CALENDAR'] &&
            (($this->auth->info('mode') == 'agent' && $this->auth->hasPermission(Auth::CALENDAR_OUTLOOK_PUSH)) ||
            $this->is_super_admin_as_admin())
        ) ? true : false;
    }
}
