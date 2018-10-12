<?php

/**
 * History_Event_Email_Reminder extends History_Event_Email and is used for tracking when a Reminder Email is Sent to an Agent
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Email_Reminder(array(
 *         'subject'   => $mailer->Subject,
 *         'message'   => $mailer->Body
 *     ), array(
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
class History_Event_Email_Reminder extends History_Event_Email
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'associate', 'agent')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            if (in_array($type, array($user::TYPE_AGENT, $user::TYPE_ASSOCIATE))) {
                $agent = $user;
            }
        }

        // If Not Set, Make A Dummy Agent
        if (empty($agent)) {
            $agent = new History_User_Generic(0);
        }

        // System Message
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' was sent Reminder Email: ' . $this->getData('subject');
        }

        // Agent / Associate Message
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Sent Reminder Email: ' . $this->getData('subject');
        }
    }
}
