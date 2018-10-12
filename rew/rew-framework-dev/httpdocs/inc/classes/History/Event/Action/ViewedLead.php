<?php

/**
 * History_Event_Action_ViewedLead extends History_Event_Action and is used for tracking when an Agent or Lender views a lead.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_ViewedLead(null, array(
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
class History_Event_Action_ViewedLead extends History_Event_Action
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
            return $agent->displayLink() . ' Viewed Lead: ' . $lead->displayLink();
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            return 'Viewed Lead: ' . $lead->displayLink();
        }

        // Lead History
        if ($options['view'] == 'lead') {
            return 'Viewed by ' . $agent->displayLink();
        }
    }
}
