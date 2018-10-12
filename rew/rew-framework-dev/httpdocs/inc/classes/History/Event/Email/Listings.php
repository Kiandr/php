<?php

/**
 * History_Event_Email_Listings extends History_Event_Email and is used for tracking when a Listing Update is sent to a Lead
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Email_Listings(array(
 *         'subject'   => $mailer->Subject,
 *         'message'   => $mailer->Body
 *     ), array(
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
class History_Event_Email_Listings extends History_Event_Email
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
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead/Agent
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        /* System Message */
        if ($options['view'] == 'system') {
            /* Agent Performed */
            if (!empty($agent)) {
                return $agent->displayLink() . ' sent Listings Email to ' . $lead->displayLink() . ': ' . $this->getData('subject');

            /* Automatic */
            } else {
                return 'Listings Update sent to ' . $lead->displayLink() . ': ' . $this->getData('subject');
            }
        }

        /* System Message */
        if ($options['view'] == 'agent') {
            return 'Sent Listings Email to ' . $lead->displayLink() . ': ' . $this->getData('subject');
        }

        /* Lead Message */
        if ($options['view'] == 'lead') {
            /* Agent Performed */
            if (!empty($agent)) {
                return $agent->displayLink() . ' sent Listings Email: ' . $this->getData('subject');

            /* Automatic */
            } else {
                return 'Listings Update Sent: ' . $this->getData('subject');
            }
        }
    }
}
