<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class LeadsAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class LeadsAuth extends Auth
{

    /**
     * Can Manage Every Lead
     *
     * @param AuthInterface $auth Current authuser
     *
     * @return bool
     */
    public function canManageLeads(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LEADS_ALL)
            || $auth->isAssociate();
    }

    /**
     * Can Manage Leads
     *
     * @param AuthInterface $auth Current authuser
     *
     * @return bool
     */
    public function canManageOwn(AuthInterface $auth)
    {
        return $auth->isAgent();
    }

    /**
     * Can View Leads
     *
     * @param AuthInterface $auth Current authuser
     *
     * @return bool
     */
    public function canViewOwn(AuthInterface $auth)
    {
        return $auth->isAgent()
        || $auth->isLender();
    }

    /**
     * Can Delete Leads
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canDeleteLeads(AuthInterface $auth)
    {

        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LEADS_DELETE);
    }

    /**
     * Can Assign Leads
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canAssignLeads(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->isAssociate()
            || $auth->adminPermission($auth::PERM_LEADS_ASSIGN);
    }

    /**
     * Can Be Assigned Leads.
     * @param AuthInterface $auth
     * @return bool
     */
    public function canBeAssignedLeads(AuthInterface $auth)
    {
        return !($auth->isAssociate());
    }

    /**
     * Can Mass Assign Action Plans
     * @param AuthInterface $auth
     * @return bool
     */
    public function canAssignActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && ($auth->isSuperAdmin()
            || $auth->isAssociate()
            || $auth->hasPermission($auth::PERM_LEADS_ACTION_PLAN_ASSIGNMENTS));
    }

    /**
     * Can Assign Action Plans to Own Leads
     * @param AuthInterface $auth
     * @return bool
     */
    public function canAssignOwnActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && ($auth->isSuperAdmin()
                || $auth->isAssociate()
                || $auth->hasPermission(AuthInterface::PERM_ACTION_PLAN_ASSIGNMENTS));
    }

    /**
     * Can Manage Action Plans
     * @param AuthInterface $auth
     * @return bool
     */
    public function canManageActionPlans(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_ACTION_PLANS']
            && ($auth->isSuperAdmin()
            || $auth->isAssociate());
    }

    /**
     * Can Share Leads
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canShareLeads(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->isAssociate();
    }


    /**
     * Can Email Leads
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canEmailLeads(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->isAssociate()
            || $auth->isLender()
            || $auth->hasPermission($auth::PERM_LEADS_EMAILS);
    }

    /**
     * Can Text Leads
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canTextLeads(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_TWILIO']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_LEADS_TEXT)
            || $auth->isAssociate());
    }


    /**
     * Can Manage API
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageAPI(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_CRM_API']
            && $auth->isSuperAdmin();
    }

    /**
     * Can Manage Autoreponders
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageAutoresponders(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_LEADS_AUTO_RESPONDERS);
    }

    /**
     * Can Manage Campaigns
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageCampaigns(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Campaigns
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageOwnCampaigns(AuthInterface $auth)
    {
        return $auth->hasPermission($auth::PERM_LEADS_CAMPAIGNS);
    }

    /**
     * Can Manage Documents
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageDocuments(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Social Media
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageSocialNetworks(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Groups
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageGroups(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Own Groups
     *
     * @param AuthInterface $auth
     *
     * @return bool
     */
    public function canManageOwnGroups(AuthInterface $auth)
    {
        return $auth->isAgent()
            || $auth->isAssociate();
    }

    /**
     * Can Export AS CSV
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canExportLeads(AuthInterface $auth)
    {
        return $auth->isSuperAdmin() ||
            $auth->adminPermission($auth::PERM_LEADS_ALL_BACKUP);
    }

    /**
     * Can Export Own Leads as CSV
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canExportOwn(AuthInterface $auth)
    {
        return $this->canExportLeads($auth)
            || $auth->hasPermission($auth::PERM_LEADS_BACKUP);
    }


    /**
     * Can Manage Lead Files
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewFiles(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CMS_FILES);
    }

    /**
     * Can Manage Lead Files
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageFiles(AuthInterface $auth)
    {
        return $auth->isSuperAdmin();
    }

    /**
     * Can Manage Lead Files
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canManageOwnFiles(AuthInterface $auth)
    {
        return $auth->hasPermission($auth::PERM_LEAD_FILES);
    }

    /**
     * Can View Tools
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canViewTools(AuthInterface $auth)
    {
        return $this->canManageSocialNetworks($auth)
            || $this->canExportLeads($auth)
            || $this->canExportOwn($auth);
    }

    /**
     * Can Quicksend to Bomb Bomb
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canSendToBombbomb(AuthInterface $auth)
    {
        if ($this->settings->MODULES['REW_PARTNERS_BOMBBOMB']
            && ($auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_PARTNERS_BOMBBOMB_AGENT))
        ) {
            //Check for Bomb-Bomb keys
            $bb = $auth->info('partners.bombbomb');
            if (!empty($bb['api_key'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can use Espresso
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canSentToEspresso(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_PARTNERS_ESPRESSO']
            && ($auth->isSuperAdmin()
            || $auth->isAssociate()
            || ($auth->isAgent() && $auth->hasPermission($auth::PERM_PARTNERS_ESPRESSO)));
    }

    /**
     * Can Access Shark Tank Leads
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canAccessSharkTank(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_SHARK_TANK']
            && ($auth->isSuperAdmin()
            || ($auth->isAgent() && $auth->hasPermission($auth::PERM_SHARK_TANK)));
    }
}
