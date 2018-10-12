<?php

/**
 * History_Event_Update_Rejected
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_Rejected extends History_Event_Update
{
    function getMessage(array $options = array())
    {

        /* Message View */
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

        /* History Event Users */
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($type == 'History_User_Agent') {
                $agent = $user;
            }
            if ($type == 'History_User_Associate') {
                $agent = $user;
            }
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead/Agent
        if (empty($agent)) {
            $agent = new History_User_Agent(0);
        }
        if (empty($lead)) {
            $lead  = new History_User_Lead(0);
        }

        /* System History */
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' Rejected ' . $lead->displayLink() . ': ' . $this->getData('reason');
        }

        /* Agent History */
        if ($options['view'] == 'agent') {
            return 'Rejected ' . $lead->displayLink() . ': ' . $this->getData('reason');
        }

        /* Lead History */
        if ($options['view'] == 'lead') {
            return 'Rejected by ' . $agent->displayLink() . ': ' . $this->getData('reason');
        }
    }
}
