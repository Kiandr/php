<?php

/**
 * History_Event_Create_Lead extends History_Event_Create and is used for tracking when a new lead is created (by Agent or System)
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Create_Lead(null, array(
 *         new History_User_Agent(1),
 *         new History_User_Lead(1)
 *     ));
 *
 *     $event->save();
 *
 * } catch (Exception $e) {
 *     echo '<p>' . $e->getMessage() . '</p>';
 * }
 * </code>
 *
 * @package History
 */
class History_Event_Create_Lead extends History_Event_Create
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

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
                return $agent->displayLink() . ' Added New Lead: ' . $lead->displayLink();
            } else {
                return 'New Lead: ' . $lead->displayLink();
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Added New Lead: ' . $lead->displayLink();
        }

        // Lead History
        if ($options['view'] == 'lead') {
            if (!empty($agent)) {
                return 'Lead Added by ' . $agent->displayLink();
            } else {
                return 'Lead Created';
            }
        }
    }
}
