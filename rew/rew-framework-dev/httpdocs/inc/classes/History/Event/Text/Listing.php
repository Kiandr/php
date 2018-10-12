<?php

/**
 * History_Event_Text_Listing
 * @package History
 */
class History_Event_Text_Listing extends History_Event_Text_Outgoing
{
    use History_Trait_HasListing;

    /**
     * @see History_Event::getMessage()
     */
    public function getMessage(array $options = array())
    {

        // Message view
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        // Viewing system history
        if ($options['view'] == 'system') {
            $message = 'Listing sent via text message to :lead at :to';

        // Viewing lead's history
        } else if ($options['view'] == 'lead') {
            $message = 'Listing sent via text message to :to';
        }

        // Return formatted message
        return $this->formatMessage($message);
    }
    /**
     * @see History_Event_Text::getDetails()
     */
    public function getDetails()
    {
        $html = parent::getDetails();
        $preview = $this->getListingPreview();
        if (!empty($preview)) {
            $html .= '<br><br>' . $preview;
        }
        return $html;
    }
}
