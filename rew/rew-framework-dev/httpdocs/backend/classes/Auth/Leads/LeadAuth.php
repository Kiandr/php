<?php

namespace REW\Backend\Auth\Leads;

use REW\Backend\Auth\Auth;
use REW\Backend\Auth\Lead\TeamLeadAuth;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\AuthInterface;
use Backend_Lead;
use Container;
use REW\Backend\Partner\Firstcallagent;

/**
 * Class LeadAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class LeadAuth extends Auth
{

    /**
     * No Ownership of this lead
     * @var int
     */
    const UNOWNED = 0;

    /**
     * Team Ownership of this lead (View Only)
     * @var int
     */
    const VIEW = 1;

    /**
     * Team Ownership of this lead (Partial Edit Only)
     * @var int
     */
    const EDIT = 2;

    /**
     * Own Lead, Permission Access to Every Lead or Team Ownership of this lead (Full Access)
     * @var int
     */
    const FULL = 3;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * Lead Ownership
     * @var int
     */
    protected $ownership;

    /**
     * First Call Agent
     * @var Firstcallagent
     */
    protected $fca;

    /**
     * Create Auth
     * @param SettingsInterface $settings
     * @param AuthInterface     $auth
     * @param Backend_Lead      $lead
     */
    public function __construct(SettingsInterface $settings, AuthInterface $auth, Backend_Lead $lead)
    {
        $this->settings = $settings;
        $this->auth = $auth;
        $this->lead = $lead;
        $this->fca = Container::getInstance()->get(Firstcallagent::class);
    }

    /**
     * @return bool
     */
    public function canViewLead()
    {
        return $this->getOwnership() >= self::VIEW;
    }

    /**
     * @return bool
     */
    public function canEditLead()
    {
        return $this->getOwnership() >= self::EDIT;
    }

    /**
     * @return bool
     */
    public function canManageLead()
    {
        return $this->getOwnership() == self::FULL;
    }

    /**
     * @return bool
     */
    public function canAssignAgentToLead()
    {
        return $this->getOwnership() == self::FULL
            && ($this->auth->isSuperAdmin()
            || $this->auth->isAssociate()
            || $this->auth->adminPermission(AuthInterface::PERM_LEADS_ASSIGN));
    }

    /**
     * @return bool
     */
    public function canAssignLenderToLead()
    {
        return !empty($this->settings->MODULES['REW_LENDERS_MODULE'])
            && $this->canAssignAgentToLead();
    }

    /**
     * @return bool
     */
    public function canDeleteLead()
    {
        return $this->getOwnership() == self::FULL
            && ($this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_LEADS_DELETE));
    }

    /**
     * @return bool
     */
    public function canEmailLead()
    {
        return $this->getOwnership(AuthInterface::PERM_LEADS_EMAILS) >= self::VIEW
            || ($this->auth->isSuperAdmin()
                || $this->auth->isLender()
                || $this->auth->isAssociate()
                || $this->auth->hasPermission(AuthInterface::PERM_LEADS_EMAILS)
            );
    }

    /**
     * @return bool
     */
    public function canTextLead()
    {
        return !empty($this->settings->MODULES['REW_PARTNERS_TWILIO'])
            && $this->getOwnership() >= self::VIEW
            && ($this->auth->isSuperAdmin()
            || $this->auth->hasPermission(AuthInterface::PERM_LEADS_TEXT));
    }

    /**
     * @return bool
     */
    public function canViewActionPlans()
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && $this->getOwnership() >= self::VIEW
            && ($this->auth->isAgent()
                || $this->auth->isAssociate()
                || $this->auth->adminPermission(AuthInterface::PERM_LEADS_ALL));
    }

    /**
     * Can Assign Action Plans
     * @return bool
     */
    public function canAssignActionPlans()
    {
        return (
            $this->settings->MODULES['REW_ACTION_PLANS']
            && (
                $this->getOwnership(AuthInterface::PERM_ACTION_PLAN_ASSIGNMENTS) == self::FULL
                || $this->auth->hasPermission(AuthInterface::PERM_LEADS_ACTION_PLAN_ASSIGNMENTS)
            )
            && ($this->auth->isSuperAdmin()
                || $this->auth->isAssociate()
                || $this->auth->isAgent()
            )
        );
    }

    /**
     * Can Authuser View Content from Other Backend users regarding this lead
     * @return boolean
     */
    public function canViewAllLeadContent()
    {
        return $this->auth->isSuperAdmin()
            || $this->auth->adminPermission(AuthInterface::PERM_LEADS_ALL)
            || $this->auth->isAssociate();
    }

    /**
     * Can Add Lead Transaction
     * @return boolean
     */
    public function canAddTransaction()
    {
        return $this->auth->isAgent();
    }

    /**
     * Can Authuser View Transactions
     * @return boolean
     */
    public function canViewTransactions()
    {
        return ($this->auth->isAgent() || $this->auth->isAssociate());
    }

    /**
     * Can Authuser View Reminders
     * @return boolean
     */
    public function canViewReminders()
    {
        return ($this->auth->isAgent() || $this->auth->isAssociate());
    }

    /**
     * Can Authuser View Messages
     * @return boolean
     */
    public function canViewMessages()
    {
        return ($this->auth->isAgent() && $this->settings->MODULES['REW_IDX_CP']);
    }

    /**
     * Can Authuser View Forms
     * @return boolean
     */
    public function canViewForms()
    {
        return (
            $this->auth->isSuperAdmin()
            || $this->auth->isAgent()
            || $this->auth->isLender()
            || $this->auth->isAssociate()
        );
    }

    /**
     * Is FCA on with an API key, both admin and assigned agent can access
     * @return bool
     */
    public function canManageFirstcallagent()
    {
        return (
            (
                $this->auth->isSuperAdmin()
                || ($this->auth->isAgent() && $this->lead['agent'] == $this->auth->info('id'))
            )
            && $this->settings->MODULES['REW_PARTNERS_FIRSTCALLAGENT']
            && $this->fca->hasAPIKey()
        );
    }

    /**
     * Get Integer Indicating Ownership of Lead
     * @return int
     */
    protected function getOwnership($masterPermission = AuthInterface::PERM_LEADS_ALL)
    {
        // Manage Every Lead
        if ($this->auth->isSuperAdmin() || $this->auth->adminPermission($masterPermission) || $this->auth->isAssociate()) {
            return self::FULL;

        // Manage Own Lead (Agent)
        } else if ($this->auth->isAgent() && $this->lead['agent'] == $this->auth->info('id')) {
            return self::FULL;

        // View Own Lead (Lender)
        } else if ($this->auth->isLender() && $this->lead['lender'] == $this->auth->info('id')) {
            return self::VIEW;

        // Check for access to lead through team
        } else if ($this->auth->isAgent() && $this->settings->MODULES['REW_TEAMS'] && $this->lead['share_lead']) {
            $teamChecker = new TeamLeadAuth($this->settings);

            // Full Access to lead through team
            if ($teamChecker->checkFullyEditableAgent($this->auth, $this->lead['agent'])) {
                return self::FULL;

            // Partial Access to lead through team
            } else if ($teamChecker->checkEditableAgent($this->auth, $this->lead['agent'])) {
                return self::EDIT;

            // View-Only Access to lead through team
            } else if ($teamChecker->checkViewableAgent($this->auth, $this->lead['agent'])) {
                return self::VIEW;
            }
        }

        // No Access To Lead
        return self::UNOWNED;
    }
}
