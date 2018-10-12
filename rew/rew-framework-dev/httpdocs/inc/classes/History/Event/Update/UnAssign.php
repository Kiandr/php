<?php

/**
 * History_Event_Update_UnAssign
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_UnAssign extends History_Event_Update
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
            if (count($this->users) == 3) {
                if ($type == $user::TYPE_ASSOCIATE) {
                    $admin = $user;
                }
                if ($type == $user::TYPE_AGENT) {
                    if ($this->getData('agent_id') == $user->getUser() && empty($agent)) {
                        $agent = $user;
                    } elseif (empty($admin) || $admin->getType() != $admin::TYPE_ASSOCIATE) {
                        $admin = $user;
                    }
                }
            } else {
                if ($type == $user::TYPE_AGENT) {
                    $agent = $user;
                }
            }
            if ($type == $user::TYPE_LENDER) {
                $lender = $user;
            }
            if ($type == $user::TYPE_LEAD) {
                $lead = $user;
            }
        }

        // Un-Assigned from Lender by Agent
        if (!empty($lender) && !empty($admin)) {
            $agent = $lender;
        } elseif (!empty($lender) && !empty($agent)) {
            $admin = $agent;
            $agent = $lender;
        }

        // Agent or Lender
        $type = '';
        if (!empty($agent)) {
            if ($agent->getType() === $agent::TYPE_AGENT) {
                $type = 'Agent ';
            }
            if ($agent->getType() === $agent::TYPE_LENDER) {
                $type = 'Lender ';
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
            // Admin Performed
            if (!empty($admin)) {
                return $lead->displayLink() . ' Un-Assigned from ' . $type . $agent->displayLink() . ' by ' . $admin->displayLink();

            // Automated
            } else {
                return $lead->displayLink() . ' Un-Assigned from ' . $type . $agent->displayLink();
            }
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            // Admin Performed
            if (!empty($admin)) {
                // Viewing as Admin's History
                if (!empty($options['user']) && $options['user'] == $admin->getUser() && $options['view'] == 'agent') {
                    return 'Un-Assigned ' . $lead->displayLink() . ' from ' . $type . $agent->displayLink();

                // Viewing as Agent's History
                } else {
                    return 'Un-Assigned from ' . $lead->displayLink() . ' by ' . $admin->displayLink();
                }

            // Automated
            } else {
                return 'Un-Assigned from ' . $lead->displayLink();
            }
        }

        // Lead History
        if ($options['view'] == 'lead') {
            // Admin Performed
            if (!empty($admin)) {
                return 'Un-Assigned from ' . $type . $agent->displayLink() . ' by ' . $admin->displayLink();

            // Automated
            } else {
                return 'Un-Assigned from ' . $type . $agent->displayLink();
            }
        }
    }
}
