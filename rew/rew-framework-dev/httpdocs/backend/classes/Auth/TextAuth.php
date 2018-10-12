<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class TextAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class TextAuth extends Auth
{

    /**
     * Can Text Any Lead using Twillio
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canTextLeads(AuthInterface $auth)
    {
        return !empty($this->settings->MODULES['REW_PARTNERS_TWILIO'])
            && $auth->isSuperAdmin();
    }

    /**
     * Can Text Own Leads using Twillio
     *
     * @param AuthInterface $auth Current Authuser
     *
     * @return bool
     */
    public function canTextOwnLeads(AuthInterface $auth)
    {
        return !empty($this->settings->MODULES['REW_PARTNERS_TWILIO']) &&
            $auth->hasPermission($auth::PERM_LEADS_TEXT);
    }
}
