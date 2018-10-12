<?php

/**
 * Hook_REW_FollowUpBoss_Lead_Visit
 * Notifies Follow Up Boss when a lead visits the site
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss_Lead_Visit extends Hook_REW_FollowUpBoss
{

    /**
     * Run the hook's code
     * @param integer $lead_id The lead's user ID
     * @param integer $num_visits The total number of visits, including this one
     * @param string $referer The visit's referer
     * @param string $keywords The search engine keywords for the visit
     */
    protected function invoke($lead_id, $num_visits, $referer = null, $keywords = null)
    {

        // Require partner
        if (!($fub = $this->getPartner())) {
            return;
        }

        // Notify FUB
        $fub->notifyWebsiteVisit($lead_id, $referer, $keywords);
    }
}
