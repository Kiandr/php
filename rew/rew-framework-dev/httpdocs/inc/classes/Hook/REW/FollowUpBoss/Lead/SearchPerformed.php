<?php

/**
 * Hook_REW_FollowUpBoss_Lead_SearchPerformed
 * Notifies Follow Up Boss when an IDX search is performed
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss_Lead_SearchPerformed extends Hook_REW_FollowUpBoss
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param IDX $idx The IDX instance
     * @param array $criteria The search criteria
     * @param string $title The search title
     */
    protected function invoke($lead, IDX $idx, $criteria, $title)
    {

        // Require partner
        if (!($fub = $this->getPartner())) {
            return;
        }

        // Notify FUB
        $fub->notifyPropertySearch($lead, $criteria, $title);
    }
}
