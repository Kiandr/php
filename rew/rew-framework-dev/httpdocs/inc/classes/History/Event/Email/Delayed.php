<?php

/**
 * History_Event_Email_Delayed extends History_Event_Email and is used for tracking when a Delayed Email is sent to a Lead
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Email_Delayed(array(
 *         'timestamp' => strtotime($delayed),
 *         'subject'   => $mailer->Subject,
 *         'message'   => $mailer->Body
 *     ), array(
 *         new History_User_Lead(1),
 *         new History_User_Agent(1)
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
class History_Event_Email_Delayed extends History_Event_Email
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

        // System Message
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' set Delayed Email to ' . $lead->displayLink() . ' on ' . date('F\, jS Y \a\t g\:ia', $this->getData('timestamp')) . ': ' . $this->getData('subject');
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            return 'Set Delayed Email to send to ' . $lead->displayLink() . ' on ' . date('F\, jS Y \a\t g\:ia', $this->getData('timestamp')) . ': ' . $this->getData('subject');
        }

        // Lead Message
        if ($options['view'] == 'lead') {
            return $agent->displayLink() . ' set Delayed Email to send on ' . date('F\, jS Y \a\t g\:ia', $this->getData('timestamp')) . ': ' . $this->getData('subject');
        }
    }
}
