<?php

/**
 * Hook_REW_OutgoingAPI_Lead_SearchPerformed
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_SearchPerformed extends Hook_REW_OutgoingAPI
{

    /**
     * Run the hook's code
     * @param array $lead The lead's row from the database
     * @param IDX $idx The IDX instance
     * @param array $criteria The search criteria
     * @param string $title The search title
     */
    protected function invoke($lead, IDX $idx, $criteria, $title)
    {
        $this->sendOutgoingEvent($this->getName(), array(
            'lead'      => $lead,
            'idx'       => $idx,
            'criteria'  => $criteria,
            'title'     => $title,
        ));
    }
}
