<?php

namespace REW\Backend\Auth\Associates;

use REW\Backend\Auth\Auth;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\AuthInterface;
use Backend_Associate;

/**
 * Class AssociateAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class AssociateAuth extends Auth
{

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var Backend_Associate
     */
    protected $associate;

    /**
     * Create Auth
     * @param SettingsInterface $settings
     * @param AuthInterface     $auth
     * @param Backend_Associate     $associate
     */
    public function __construct(SettingsInterface $settings, AuthInterface $auth, Backend_Associate $associate)
    {
        $this->settings = $settings;
        $this->auth = $auth;
        $this->associate = $associate;
    }

    /**
     * Can View Associate Content
     * @return boolean
     */
    public function canViewAssociate()
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && ($this->auth->isSuperAdmin()
            || $this->auth->isAgent()
            || $this->isSelf());
    }

    /**
     * Can Edit Associates
     * @return boolean
     */
    public function canEditAssociate()
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && ($this->auth->isSuperAdmin()
            || $this->isSelf());
    }

    /**
     * Check if authorized to delete associates
     *
     * @param AuthInterface $auth Auth User
     *
     * @return bool
     */
    public function canDeleteAssociate()
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && $this->auth->isSuperAdmin();
    }

    /**
     * Can Manage Associate Action Plan Tasks
     * @return boolean
     */
    public function canSetTasks()
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && $this->settings->MODULES['REW_ACTION_PLANS']
            && ($this->auth->isSuperAdmin()
            || $this->isSelf());
    }

    /**
     * Can View Associate History
     * @return boolean
     */
    public function canViewHistory()
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && ($this->auth->isSuperAdmin()
            || $this->isSelf());
    }

    /**
     * Can Email Associates
     * @return boolean
     */
    public function canEmailAssociate()
    {
        return $this->settings->MODULES['REW_ISA_MODULE']
            && ($this->auth->isSuperAdmin()
            || $this->auth->isAgent());
    }

    /**
     * Is the current authuser this associate
     * @return boolean
     */
    public function isSelf()
    {
        return $this->auth->isAssociate() && $this->auth->info('id') == $this->associate->getId();
    }
}
