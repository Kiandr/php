<?php

/**
 * History_Event_Create_LeadNote extends History_Event_Create and is used for tracking when an Agent or Lender creates a lead note.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Create_LeadNote(array(
 *         'details' => 'Note details here'
 *         'share'   => true
 *         'type'    => ''
 *     ), array(
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
class History_Event_Create_LeadNote extends History_Event_Create implements History_IExpandable
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

        // Note Type
        $type = $this->getData('type');

        // Shared Note
        $shared = $this->getData('share') ? true : false;

        // System History
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' added ' . $type . ' Note for ' . $lead->displayLink();
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            return $type . ' Note added for ' . $lead->displayLink();
        }

        // Lead History
        if ($options['view'] == 'lead') {
            return $type . ' Note added by ' . $agent->displayLink();
        }
    }

    /**
     * @see History_IExpandable::getDetails()
     */
    function getDetails()
    {
        $message = $this->getData('details');
        $message = nl2br($message);
        return $message;
    }
}
