<?php

/**
 * Hook_REW_FollowUpBoss_Lead_ListingViewed
 * Notifies Follow Up Boss when an IDX listing is viewed
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss_Lead_ListingViewed extends Hook_REW_FollowUpBoss
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param IDX $idx The IDX instance
     * @param array $listing The listing row of the property being viewed
     */
    protected function invoke($lead, IDX $idx, $listing)
    {

        // Require partner
        if (!($fub = $this->getPartner())) {
            return;
        }

        // Notify FUB
        $fub->notifyPropertyViewed($lead, $listing);
    }
}
