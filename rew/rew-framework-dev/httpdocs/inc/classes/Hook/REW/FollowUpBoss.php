<?php

/**
 * Hook_REW_FollowUpBoss
 * Base class for Follow Up Boss hooks
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss extends Hook
{

    /**
     * Get the FUB partner instance (if available)
     * @return Partner_FollowUpBoss|NULL
     */
    protected function getPartner()
    {

        // Require add-on
        if (empty(Settings::getInstance()->MODULES['REW_PARTNERS_FOLLOWUPBOSS'])) {
            return null;
        }

        // Require configured partner
        if (!($fub = Partner_FollowUpBoss::systemInstance())) {
            return null;
        }

        return $fub;
    }
}
