<?php

/**
 * Hook_REW_OutgoingAPI_Lead_Visit
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_Visit extends Hook_REW_OutgoingAPI
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
        $this->sendOutgoingEvent($this->getName(), array(
            'lead_id'       => $lead_id,
            'num_visits'    => $num_visits,
            'referer'       => $referer,
            'keywords'      => $keywords,
        ));
    }
}
