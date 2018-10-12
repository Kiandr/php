<?php

/**
 * History_Event_Phone_Contact
 *
 * <code>
 * </code>
 *
 */
class History_Event_Phone_Contact extends History_Event_Phone
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'associate', 'lender', 'lead')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if (in_array($type, array($user::TYPE_AGENT, $user::TYPE_ASSOCIATE, $user::TYPE_LENDER))) {
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
            return $agent->displayLink() . ' made Phone Call to ' . $lead->displayLink();
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            return 'Phone Call to ' . $lead->displayLink();
        }

        // Lead History
        if ($options['view'] == 'lead') {
            return 'Phone Call from ' . $agent->displayLink();
        }
    }
}
