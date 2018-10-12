<?php

/**
 * Hook_REW_FirstCallAgent_Agent_Lead_Reassign
 * This hook is invoked on Backend_Lead::assignAgent()
 * @package Hooks
 */
class Hook_REW_FirstCallAgent_Agent_Lead_Reassign extends Hook_REW_FirstCallAgent
{

    protected function invoke($lead)
    {

        $lead = Backend_Lead::load($lead['id']);

        $firstcallagent = $this->getPartner();
        if (!empty($firstcallagent)) {
            $fcaLead = $firstcallagent->getLeadSettings($lead);

            if (!empty($fcaLead) && $fcaLead['sent'] == 'true') {
                $agent = Backend_Agent::load($lead->info('agent'));

                $values = [
                    'agent_name'  => $agent->getName(),
                    'agent_email' => $agent['email'],
                    'agent_phone' => !empty($agent['cell_phone']) ? $agent['cell_phone'] : $agent['office_phone'],
                ];

                $firstcallagent->updateLead($lead, $values);
            }
        }
    }
}
