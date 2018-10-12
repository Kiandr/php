<?php

/**
 * Hook_REW_FirstCallAgent_Lead_Created
 * This hook is invoked on lead register/connect for new leads only
 * @package Hooks
 */
class Hook_REW_FirstCallAgent_Lead_Created extends Hook_REW_FirstCallAgent
{
    /**
     * Invoke this hook
     *
     * @param Backend_Lead $lead
     * @param bool $manually_created
     * @return void
     */
    protected function invoke($lead, $manually_created = false)
    {
        $firstcallagent = $this->getPartner();
        if (!empty($firstcallagent)) {
            $firstcallagent->sendLead($lead);
        }
    }
}
