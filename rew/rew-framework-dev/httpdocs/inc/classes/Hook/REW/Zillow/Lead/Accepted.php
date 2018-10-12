<?php

/**
 * Hook_REW_Zillow_Lead_Accepted
 * Notifies Zillow when a lead is accepted
 *
 * @package Hooks
 */
class Hook_REW_Zillow_Lead_Accepted extends Hook_REW_Zillow
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     */
    protected function invoke($lead)
    {

        // Require Lead Agent
        if (!isset($lead['agent'])) {
            return;
        }

        // Require partner
        if (!($zillow = $this->getPartner($lead['agent']))) {
            return;
        }

        // Notify Zillow
        $zillow->updateLeads($lead['email'], Partner_Zillow::ZILLOW_STATUS_ACTIVE);
    }
}
