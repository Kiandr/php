<?php

/**
 * History_Event_Email_FBL extends History_Event_Email and is used for tracking when an email is reported as spam
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Email_FBL(array(
 *         'subject'   => $Subject,
 *         'message'   => $_POST['body']
 *     ), array(
 *         new History_User_Lead($lead['id'])
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
class History_Event_Email_FBL extends History_Event_Email
{
    function getMessage(array $options = array())
    {

        /* Message View */
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        /* History Event Users */
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        /* System Message */
        if ($options['view'] == 'system') {
            return $lead->displayLink() . ' reported an email as SPAM: ' . $this->getData('subject');
        }

        /* Agent Message */
        if ($options['view'] == 'lead') {
            return 'Reported an email as SPAM: ' . $this->getData('subject');
        }
    }
}
