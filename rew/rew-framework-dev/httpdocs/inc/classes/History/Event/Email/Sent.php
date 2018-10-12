<?php

/**
 * History_Event_Email_Sent extends History_Event_Email and is used for tracking when an Email is sent to a Lead
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Email_Sent(array(
 *         'plaintext' =>  true,
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
class History_Event_Email_Sent extends History_Event_Email
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'associate', 'agent', 'lender', 'lead')) ? $options['view'] : 'system';

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

        // Delayed Email was Sent
        $delayed = $this->getData('delayed') ? true : false;

        // System Message
        if ($options['view'] == 'system') {
            // Delayed Email
            if (!empty($delayed)) {
                return $agent->displayLink() . '\'s Delayed Email sent to ' . $lead->displayLink() . ': ' . $this->getData('subject');

            // Direct Email
            } else {
                return $agent->displayLink() . ' sent Email to ' . $lead->displayLink() . ': ' . $this->getData('subject');
            }
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            // Delayed Email
            if (!empty($delayed)) {
                return 'Delayed Email sent to ' . $lead->displayLink() . ': ' . $this->getData('subject');

            // Direct Email
            } else {
                return 'Sent Email to ' . $lead->displayLink() . ': ' . $this->getData('subject');
            }
        }

        // Lead Message
        if ($options['view'] == 'lead') {
            // Delayed Email
            if (!empty($delayed)) {
                return $agent->displayLink() . '\'s Delayed Email Sent: ' . $this->getData('subject');

            // Direct Email
            } else {
                return $agent->displayLink() . ' sent Email: ' . $this->getData('subject');
            }
        }
    }
}
