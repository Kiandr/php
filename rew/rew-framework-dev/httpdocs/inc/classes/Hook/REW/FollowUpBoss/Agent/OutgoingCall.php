<?php

/**
 * Hook_REW_FollowUpBoss_Agent_OutgoingCall
 * Notifies Follow Up Boss when an agent tracks an outgoing call
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss_Agent_OutgoingCall extends Hook_REW_FollowUpBoss
{

    /**
     * Run the hook's code
     * @param array $agent The agent's row from the database
     * @param array $lead The lead's row from the database
     * @param string $outcome The call's outcome
     * @param string $details Call details, as entered by the agent
     */
    protected function invoke($agent, $lead, $outcome, $details)
    {

        // Require partner
        if (!($fub = $this->getPartner())) {
            return;
        }

        // Notify FUB
        $fub->notifyOutgoingCall($lead, $outcome, $details);
    }
}
