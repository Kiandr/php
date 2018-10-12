<?php

/**
 * Hook_REW_FollowUpBoss_Lead_SearchSaved
 * Notifies Follow Up Boss when an IDX search is saved
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss_Lead_SearchSaved extends Hook_REW_FollowUpBoss
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param IDX $idx The IDX instance
     * @param array $criteria The search criteria
     * @param string $title The search title
     * @param string $frequency The alert frequency
     * @param boolean $suggested Whether this is a suggested search
     */
    protected function invoke($lead, IDX $idx, $criteria, $title, $frequency = 'weekly', $suggested = false)
    {

        // Require partner
        if (!($fub = $this->getPartner())) {
            return;
        }

        // Notify FUB
        $fub->notifyPropertySearchSaved($lead, $criteria, $title);
    }
}
