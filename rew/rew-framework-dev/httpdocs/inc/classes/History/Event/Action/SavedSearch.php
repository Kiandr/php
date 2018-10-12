<?php

/**
 * History_Event_Action_SavedSearch extends History_Event_Action and is used for tracking when a Lead has saved a search, or an Agent or Associatehas saved a search for a lead, or an Auto 'Smart Search' is created for a lead.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_SavedSearch(array(
 *         'search' => array('title' => 'Search Title'),
 *         'auto'   => false // True, if set up automatically created
 *     ), array(
 *         new History_User_Lead(1)
 *         // new History_User_Agent(1)
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
class History_Event_Action_SavedSearch extends History_Event_Action implements History_IExpandable
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

        // Automatic Search
        $auto = $this->getData('auto') ? true : false;

        // Saved Search
        $search = $this->getData('search');

        // Suggested by Agent
        if (!empty($search['agent_id'])) {
            // Get Agent
            $suggested = $this->db->agents->getRow($search['agent_id']);

            // Suggested By...
            $suggested = !empty($suggested) ? ' (Suggested by <strong>' . $suggested['first_name'] . ' ' . $suggested['last_name'] . '</strong>)' : '';
        }

        // System History
        if ($options['view'] == 'system') {
            if (!empty($auto)) {
                return 'Auto-Generated Saved Search: ' . $search['title'];
            } else if (!empty($agent)) {
                return $agent->displayLink() . ' Created Saved Search for ' . $lead->displayLink() . ': ' . $search['title'];
            } else {
                return $lead->displayLink() . ' Created Saved Search: ' . $search['title'] . $suggested;
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Created Saved Search for ' . $lead->displayLink() . ': ' . $search['title'];
        }

        // Lead History
        if ($options['view'] == 'lead') {
            if (!empty($auto)) {
                return 'Auto-Generated Saved Search: ' . $search['title'];
            } else if (!empty($agent)) {
                return $agent->displayLink() . ' Created Saved Search: ' . $search['title'];
            } else {
                return 'Created Saved Search: ' . $search['title'] . $suggested;
            }
        }
    }

    /**
     * Get Saved Search Details
     * @see History_IExpandable::getDetails()
     */
    function getDetails()
    {

        // Saved Search
        $search = $this->getData('search');

        // Search Criteria
        $criteria = Format::unserialize($search['criteria']);

        // Parse Search Criteria
        return Util_IDX::parseCriteria($criteria, $search['idx']) ?: 'No Search Criteria';
    }
}
