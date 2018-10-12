<?php

/**
 * History_Event_Update_GroupAdd
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_GroupAdd extends History_Event_Update
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

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead  = new History_User_Lead(0);
        }

        // System History
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Added ' . $lead->displayLink() . ' to Group: ' . $this->getData('group');
            } else {
                return $lead->displayLink() . ' Added to Group: ' . $this->getData('group');
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Added ' . $lead->displayLink() . ' to Group: ' . $this->getData('group');
        }

        // Lead History
        if ($options['view'] == 'lead') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Added to Group: ' . $this->getData('group');
            } else {
                return 'Added to Group: ' . $this->getData('group');
            }
        }
    }
}
