<?php

/**
 * History_Event_Email_Direct extends History_Event_Email_Sent and is used for tracking when an Email is sent directly to a single Lead
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Email_Direct(array(
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
class History_Event_Email_Direct extends History_Event_Email_Sent
{
 
}
