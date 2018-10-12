<?php

/**
 * History_Event_Update_OptOut
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_OptOut extends History_Event_Update
{
    function getMessage(array $options = array())
    {

        // Require 'tpl_date' Function
        require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.Template.php';

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($user->getUser() != $this->getData('admin') || empty($agent)) {
                $agent = $user;
            }
            if ($type == 'History_User_Agent' && $user->getUser() == $this->getData('admin')) {
                $admin = $user;
            }
        }

        // Admin is Agent
        if (!empty($admin) && $admin->getUser() === $agent->getUser()) {
            unset($admin);
        }

        // If Not Set, Make A Dummy Agent
        if (empty($agent)) {
            $agent = new History_User_Agent(0);
        }

        // Auto Opt-Out
        $auto = $this->getData('auto');

        // In-Active Time
        if (!empty($auto)) {
            $inactive = (int) $this->getData('inactive');
            $inactive = (!empty($inactive) ? ' (In-Active for ' . tpl_date($inactive * 60) . ')' : $inactive);
        }

        // System Message
        if ($options['view'] == 'system') {
            if (!empty($auto)) {
                return $agent->displayLink() . ' has been automatically Opted-Out of Auto-Assignment and Auto-Rotation' . $inactive;
            } elseif (!empty($admin)) {
                return $agent->displayLink() . ' has been Opted-Out of Auto-Assignment and Auto-Rotation by ' . $admin->displayLink();
            } else {
                return $agent->displayLink() . ' has Opted-Out of Auto-Assignment and Auto-Rotation';
            }
        }

        // Agent Message
        if ($options['view'] == 'agent') {
            if (!empty($auto)) {
                return 'Automatically Opted-Out of Auto-Assignment and Auto-Rotation' . $inactive;
            } elseif (!empty($admin)) {
                if (!empty($options['user']) && !empty($options['user']) && $options['user'] == $admin->getUser()) {
                    return 'Opted-Out ' . $agent->displayLink() . ' from Auto-Assignment and Auto-Rotation';
                } else {
                    return 'Opted-Out of Auto-Assignment and Auto-Rotation by ' . $admin->displayLink();
                }
            } else {
                return 'Opted-Out of Auto-Assignment and Auto-Rotation';
            }
        }
    }
}
