<?php

namespace REW\Backend\Auth\Agents;

use REW\Backend\Auth\Auth;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\AuthInterface;
use Backend_Agent;

/**
 * Class AgentAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class AgentAuth extends Auth
{

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var Backend_Agent
     */
    protected $agent;

    /**
     * Create Auth
     * @param SettingsInterface $settings
     * @param AuthInterface     $auth
     * @param Backend_Agent     $agent
     */
    public function __construct(SettingsInterface $settings, AuthInterface $auth, Backend_Agent $agent)
    {
        $this->settings = $settings;
        $this->auth = $auth;
        $this->agent = $agent;
    }

    public function canViewAgent()
    {
        return $this->auth->isSuperAdmin()
            || $this->auth->hasPermission(AuthInterface::PERM_AGENTS_VIEW)
            || $this->auth->adminPermission(AuthInterface::PERM_AGENTS_MANAGE)
            || $this->auth->isAssociate()
            || $this->auth->isLender()
            || $this->canManageSelf();
    }

    public function canEditAgent()
    {
        if ($this->agent->getId() == 1) {
            return $this->auth->isSuperAdmin();
        }

        return $this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_AGENTS_MANAGE)
            || $this->canManageSelf();
    }

    public function canManageAgent()
    {
        if ($this->agent->getId() == 1) {
            return $this->auth->isSuperAdmin();
        }

        return $this->auth->isSuperAdmin()
        || $this->auth->adminPermission(AuthInterface::PERM_AGENTS_MANAGE);
    }

    public function canDeleteAgent()
    {
        if ($this->agent->getId() == 1) {
            return false;
        }
        if ($this->agent->getId() == $this->auth->info('id')) {
            return false;
        }
        return $this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_AGENTS_DELETE);
    }

    public function canSetAutoresponders()
    {
        if ($this->agent->getId() == 1) {
            return $this->auth->isSuperAdmin();
        }
        return ($this->auth->adminPermission(AuthInterface::PERM_LEADS_AUTO_RESPONDERS))
            || $this->canManageSelf()
            || $this->auth->isSuperAdmin();
    }

    public function canSetTasks()
    {
        if ($this->agent->getId() == 1) {
            return $this->auth->isSuperAdmin();
        }
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && ($this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_AGENTS_MANAGE)
            || $this->auth->isAssociate()
            || $this->canManageSelf());
    }


    public function canViewHistory()
    {
        if ($this->agent->getId() == 1) {
            return $this->auth->isSuperAdmin();
        }
        return $this->auth->isSuperAdmin()
           || $this->canManageSelf();
    }

    public function canManagePermissions()
    {
        return $this->auth->isSuperAdmin();
    }

    public function canManageApp()
    {
        return !empty($this->settings->MODULES['REW_CRM_API'])
            && $this->auth->isSuperAdmin();
    }

    public function canEmailAgent()
    {
        return $this->auth->isSuperAdmin()
            || $this->auth->hasPermission(AuthInterface::PERM_AGENTS_EMAIL)
            || $this->auth->isAssociate()
            || $this->auth->isLender()
            || $this->canManageSelf();
    }

    public function isSelf()
    {
        return $this->canManageSelf();
    }

    protected function canManageSelf()
    {
        return $this->auth->isAgent() && $this->auth->info('id') == $this->agent->getId();
    }
}
