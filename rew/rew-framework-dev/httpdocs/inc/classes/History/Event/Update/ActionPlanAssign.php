<?php

/**
 * History_Event_Update_ActionPlanAssign
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_ActionPlanAssign extends History_Event_Update
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'associate', 'lead')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if (in_array($type, array($user::TYPE_AGENT, $user::TYPE_ASSOCIATE))) {
                $agent = $user;
            }
            if ($type == $user::TYPE_LEAD) {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead/Agent
        if (empty($agent)) {
            $agent = new History_User_Generic(0);
        }
        if (empty($lead)) {
            $lead  = new History_User_Lead(0);
        }

        // System History
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' Assigned Action Plan to ' . $lead->displayLink() . ': ' . $this->getData('action_plan');
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            return 'Assigned Action Plan to ' . $lead->displayLink() . ': ' . $this->getData('action_plan');
        }

        // Lead History
        if ($options['view'] == 'lead') {
            return $agent->displayLink() . ' Assigned Action Plan: ' . $this->getData('action_plan');
        }
    }
}
