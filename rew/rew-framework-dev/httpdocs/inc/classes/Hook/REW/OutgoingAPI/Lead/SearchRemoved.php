<?php

/**
 * Hook_REW_OutgoingAPI_Lead_SearchRemoved
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_SearchRemoved extends Hook_REW_OutgoingAPI
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
