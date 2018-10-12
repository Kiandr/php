<?php

/**
 * Hook_REW_OutgoingAPI_Lead_ListingViewed
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_ListingViewed extends Hook_REW_OutgoingAPI
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param IDX $idx The IDX instance
     * @param array $listing The listing row of the property being viewed
     */
    protected function invoke($lead, IDX $idx, $listing)
    {
        $this->sendOutgoingEvent($this->getName(), array(
            'lead'      => $lead,
            'idx'       => $idx,
            'listing'   => $listing,
        ));
    }
}
