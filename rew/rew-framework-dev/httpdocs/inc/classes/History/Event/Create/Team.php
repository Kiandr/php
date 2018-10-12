<?php

/**
 * History_Event_Create_Team extends History_Event_Create and is used for tracking when a new team is created (by Agent)
 *
 * <code>
 * try {
 *
 *      $event = new History_Event_Create_Team(array(
 *          'id' => $insert_id,
 *          'name' => $_POST['name']
 *      ),array (
 *          new History_User_Agent($agent_id)
 *      ));
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
class History_Event_Create_Team extends History_Event_Create
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent')) ? $options['view'] : 'system';

        //Distinguish Creating Agent from Primary Team Agent
        $creator_id = $this->getData('creating_agent');
        $owner_id = $this->getData('primary_agent');

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if ($type == $user::TYPE_AGENT) {
                if (isset($creator_id) && $user->getUser() == $creator_id) {
                    $agent = $user;
                }
                if (isset($owner_id) && $user->getUser() == $owner_id) {
                    $owner = $user;
                }
            }
        }

        // Team Link
        $team_id = $this->getData('id');
        $team_name = $this->getData('name');
        $team = Backend_Team::load($team_id);
        $team_link = !empty($team) ?
            '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/teams/members/?id=' . $team->getId() . '">'
                . '#' . $team_id . ' - ' . $team_name
            . '</a>'
        : '#' . $team_id . ' - ' . $team_name;

        // System History
        if ($options['view'] == 'system') {
            return ((!empty($agent)) ? $agent->displayLink() . ' ' : '') . ' Created New Team: ' . $team_link . ((!empty($owner) && $creator_id != $owner_id) ? ', Primary Agent: ' . $owner->displayLink() : '');
        }

        // Agent/Associate/Lender History
        if ($options['view'] == 'agent') {
            if ($creator_id == $owner_id) {
                return 'Created New Team: ' . $team_link;
            } else {
                if ($options['user'] == $creator_id) {
                    return 'Created New Team: ' . $team_link . ((!empty($owner)) ? ', with Primary Agent: ' . $owner->displayLink() : '');
                } else {
                    return ((!empty($agent)) ? $agent->displayLink() . ' ' : '') . 'Created New Team: ' . $team_link;
                }
            }
        }
    }
}
