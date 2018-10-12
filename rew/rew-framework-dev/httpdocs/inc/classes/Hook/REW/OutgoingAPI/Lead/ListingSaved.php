<?php

/**
 * Hook_REW_OutgoingAPI_Lead_ListingSaved
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_ListingSaved extends Hook_REW_OutgoingAPI
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param IDX $idx The IDX instance
     * @param array $listing The listing row of the property being saved
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
