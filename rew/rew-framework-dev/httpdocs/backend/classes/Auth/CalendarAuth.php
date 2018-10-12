<?php

namespace REW\Backend\Auth;

use REW\Core\Interfaces\AuthInterface;

/**
 * Class CalendarAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CalendarAuth extends Auth
{

    /**
     * Can manage calendars
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canManageCalendars(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CALENDAR_MANAGE);
    }

    /**
     * Can manage personal calendar
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canManageOwnCalendars(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->hasPermission($auth::PERM_CALENDAR_AGENT);
    }

    /**
     * Can delete calendars
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canDeleteCalendars(AuthInterface $auth)
    {
        return $auth->isSuperAdmin()
            || $auth->adminPermission($auth::PERM_CALENDAR_DELETE);
    }

    /**
     * Can sync with google calender
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canSyncWithGoogleCalander(AuthInterface $auth)
    {

        return $this->settings->MODULES['REW_GOOGLE_CALENDAR'] &&
            $auth->info('google_calendar_sync') == 'true';
    }

    /**
     * Can sync with google calender
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canSyncWithOutlookCalander(AuthInterface $auth)
    {

        return $this->settings->MODULES['REW_OUTLOOK_CALENDAR'] &&
            $auth->info('microsoft_calendar_sync') == 'true';
    }

    /**
     * Check if authorized to push to google calender
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canPushToGoogleCalander(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_GOOGLE_CALENDAR']
            && ($auth->isSuperAdmin() || $auth->hasPermission($auth::CALENDAR_GOOGLE_PUSH));
    }

    /**
     * Check if authorized to push to outlook calender
     *
     * @param AuthInterface $auth User Authorization to Check Against
     *
     * @return bool
     */
    public function canPushToOutlookCalander(AuthInterface $auth)
    {
        return $this->settings->MODULES['REW_OUTLOOK_CALENDAR']
            && ($auth->isSuperAdmin() || $auth->hasPermission($auth::CALENDAR_OUTLOOK_PUSH));
    }
}
