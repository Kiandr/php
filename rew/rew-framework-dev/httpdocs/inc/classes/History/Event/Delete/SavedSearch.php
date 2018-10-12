<?php

/**
 * History_Event_Delete_SavedSearch extends History_Event_Delete and is used for tracking when an Agent, Associate or Lead removed a saved search.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Delete_SavedSearch(array(
 *         'search' => array('title' => 'Search Title')
 *     ), array(
 *         new History_User_Lead(1)
 *         //new History_User_Agent(1)
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
class History_Event_Delete_SavedSearch extends History_Event_Delete
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'associate', 'lead')) ? $options['view'] : 'system';

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
            $lead = new History_User_Lead(0);
        }

        // Saved Search
        $search = $this->getData('search');

        // System Message
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Removed ' . $lead->displayLink() . '\'s Saved Search: ' . $search['title'];
            } else {
                return $lead->displayLink() . ' Removed Saved Search: ' . $search['title'];
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Removed ' . $lead->displayLink() . '\'s Saved Search: ' . $search['title'];
        }

        // Lead History
        if ($options['view'] == 'lead') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Removed Saved Search: ' . $search['title'];
            } else {
                return 'Removed Saved Search: ' . $search['title'];
            }
        }
    }
}
