<?php

/**
 * History_Event_Text_AutoResponder
 * @package History
 */
class History_Event_Text_AutoResponder extends History_Event_Text_Outgoing
{

    /**
     * @see History_Event::getMessage()
     */
    public function getMessage(array $options = array())
    {

        // Message view
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

        // Viewing system history
        if ($options['view'] == 'system') {
            if ($agent = $this->getAgent()) {
                $message = ':agent\'s text message auto-responder sent to :lead at :to';
            } else {
                $message = 'Text message auto-responder sent to :lead at :to';
            }

        // Viewing agent's history
        } else if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            $message = 'Text message auto-responder sent to :lead at :to';

        // Viewing lead's history
        } else if ($options['view'] == 'lead') {
            if ($agent = $this->getAgent()) {
                $message = ':agent\'s text message auto-responder sent to :to';
            } else {
                $message = 'Text message auto-responder was sent to :to';
            }
        }

        // Return formatted message
        return $this->formatMessage($message);
    }
}
