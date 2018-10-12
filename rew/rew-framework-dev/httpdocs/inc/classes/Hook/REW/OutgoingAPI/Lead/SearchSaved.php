<?php

/**
 * Hook_REW_OutgoingAPI_Lead_SearchSaved
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_SearchSaved extends Hook_REW_OutgoingAPI
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
        $this->sendOutgoingEvent($this->getName(), array(
            'lead'      => $lead,
            'idx'       => $idx,
            'criteria'  => $criteria,
            'title'     => $title,
            'frequency' => $frequency,
            'suggested' => $suggested,
        ));
    }
}
