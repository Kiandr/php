<?php

/**
 * History_Event_Action_ViewedSearch extends History_Event_Action and is used for tracking when a Lead has viewed a search.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_ViewedSearch(array(
 *         'search' => array('title' => 'Search Title')
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
class History_Event_Action_ViewedSearch extends History_Event_Action implements History_IExpandable
{

    /**
     * @see History_Event::getMessage()
     */
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

        /* Performed Search */
        $search = $this->getData('search');

        /* System Message */
        if ($options['view'] == 'system') {
            return $lead->displayLink() . ' Performed IDX Search: ' . $search['title'];
        }

        /* Lead Message */
        if ($options['view'] == 'lead') {
            return 'Performed IDX Search: ' . $search['title'];
        }
    }

    /**
     * Get Saved Search Details
     * @see History_IExpandable::getDetails()
     */
    function getDetails()
    {
        $search = $this->getData('search');
        $criteria = Format::unserialize($search['criteria']);
        return Util_IDX::parseCriteria($criteria, $search['idx']) ?: 'No Search Criteria';
    }
}
