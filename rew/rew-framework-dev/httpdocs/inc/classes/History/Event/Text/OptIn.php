<?php

/**
 * History_Event_Text_OptIn
 * @package History
 */
class History_Event_Text_OptIn extends History_Event_Text_Incoming
{

    /**
     * @see History_Event::getMessage
     */
    function getMessage(array $options = array())
    {

        // Message view
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

        // Viewing system history
        if ($options['view'] == 'system') {
            if ($agent = $this->getAgent()) {
                $message = ':agent received opt-in message from :lead at :from';
            } else {
                $message = 'Opt-in message received from :lead at :from';
            }

        // Viewing agent's history
        } else if ($options['view'] == 'agent') {
            $message = 'Opt-in message received from :lead';

        // Viewing lead's history
        } else if ($options['view'] == 'lead') {
            if ($agent = $this->getAgent()) {
                $message = ':agent received opt-in message from :from';
            } else {
                $message = 'Opt-in message was received from :from';
            }
        }

        // Return formatted message
        return $this->formatMessage($message);
    }
}
