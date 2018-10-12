<?php

/**
 * History_Event_Action_Login extends History_Event_Action and is used for tracking when an Agent, Lender, or Lead logs into their account.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_Login(array(
 *         'ip' => $_SERVER['REMOTE_ADDR']
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
class History_Event_Action_Login extends History_Event_Action
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
            if ($type == $user::TYPE_AGENT) {
                $agent = $user;
            }
            if ($type == $user::TYPE_ASSOCIATE) {
                $associate = $user;
            }
            if ($type == $user::TYPE_LENDER) {
                $lender = $user;
            }
            if ($type == $user::TYPE_LEAD) {
                $lead = $user;
            }
        }

        // IP Address
        $ip = $this->getData('ip');

        // Track Special External Logins
        $via_check = $this->getData('via');
        switch ($via_check) {
            // RE/MAX Launchpad SSO Login
            case 'remax_sso':
                $via = ' via <a href="http://iconnect.integra.stage.gryphtech.com/Launchpad.aspx" target="_blank">RE/MAX Launchpad</a>';
                break;
            default:
                $via = '';
                break;
        }

        // System History
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return 'Agent Logged In' . $via . ': ' . $agent->displayLink() . (!empty($ip) ? ' (' . $ip . ')' : '');
            }
            if (!empty($associate)) {
                return 'Associate Logged In' . $via . ': ' . $associate->displayLink() . (!empty($ip) ? ' (' . $ip . ')' : '');
            }
            if (!empty($lender)) {
                return 'Lender Logged In' . $via . ': ' . $lender->displayLink() . (!empty($ip) ? ' (' . $ip . ')' : '');
            }
            if (!empty($lead)) {
                return 'Lead Logged In' . $via . ': ' . $lead->displayLink() . (!empty($ip) ? ' (' . $ip . ')' : '');
            }
        }

        // Agent/Lender/Lead History
        if (in_array($options['view'], array('agent', 'associate', 'lender', 'lead'))) {
            return 'Logged In' . $via . (!empty($ip) ? ' (' . $ip . ')' : '');
        }
    }
}
