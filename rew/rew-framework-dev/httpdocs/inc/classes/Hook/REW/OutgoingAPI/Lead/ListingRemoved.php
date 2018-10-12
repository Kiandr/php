<?php

/**
 * Hook_REW_OutgoingAPI_Lead_ListingRemoved
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_ListingRemoved extends Hook_REW_OutgoingAPI
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param array $row The row that was removed
     */
    protected function invoke($lead, $row)
    {
        $this->sendOutgoingEvent($this->getName(), array(
            'lead'  => $lead,
            'row'   => $row,
        ));
    }
}
