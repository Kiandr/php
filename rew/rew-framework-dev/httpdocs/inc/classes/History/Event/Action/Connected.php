<?php

/**
 * History_Event_Action_Connected
 *
 * <code>
 * </code>
 *
 */
class History_Event_Action_Connected extends History_Event_Action implements History_IExpandable
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead/Agent
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        // Network Name
        $name = $this->getData('name');

        // Profile Data
        $data = $this->getData('data');

        // IP Address
        $ip = $this->getData('ip');

        // Connect Type
        $type = $this->getData('type');
        if ($type == 'login') {
            $type = 'Logged In';
        } else if ($type == 'register') {
            $type = 'Registered';
        } else {
            $type = 'Connected';
        }

        // System Message
        if ($options['view'] == 'system') {
            return $type . ' via <strong>' . (!empty($data['link']) ? '<a href="' . $data['link'] . '" target="_blank">' . $name . '</a>' : $name) . '</strong>: ' . $lead->displayLink() . (!empty($ip) ? ' (' . $ip . ')' : '');
        }

        // Lead Message
        if ($options['view'] == 'lead') {
            return $type . ' via <strong>' . (!empty($data['link']) ? '<a href="' . $data['link'] . '" target="_blank">' . $name . '</a>' : $name) . '</strong>:' . (!empty($ip) ? ' (' . $ip . ')' : '');
        }
    }

    /**
     * Get Event Details
     * @return string Email Message
     */
    function getDetails()
    {

        // Profile Data
        $data = $this->getData('data');

        // Require Data
        if (!empty($data)) {
            // Return Profile
            return '<div class="item_content_summary social' . (empty($data['image']) ? ' noimg' : '') . '">'
                . '<h4 class="item_content_title">'
                . (!empty($data['link']) ? '<a href="' . $data['link'] . '" target="_blank">' . $data['first_name'] . ' ' . $data['last_name'] . '</a>' : $data['first_name'] . ' ' . $data['last_name'])
                . '</h4>'
                . (!empty($data['image']) ? '<div class="item_content_thumb"><img src="' . $data['image'] . '" alt=""></div>' : '')
                . (!empty($data['link']) ? '<div class="actions"><a class="button view" href="' . $data['link'] . '" target="_blank">View</a></div>' : '')
            . '</div>';
        }
    }
}
