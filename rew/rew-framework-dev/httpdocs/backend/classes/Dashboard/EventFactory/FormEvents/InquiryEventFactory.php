<?php

namespace REW\Backend\Dashboard\EventFactory\FormEvents;

use REW\Backend\Dashboard\EventFactory\AbstractFormEventFactory;

/**
 * Class InquiryEventFactory
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class InquiryEventFactory extends AbstractFormEventFactory
{

    /**
     * Inquiry Mode
     * @var string
     */
    const MODE = 'inquiry';

    /**
     * Get Event Mode
     * @return string
     */
    public function getMode()
    {
        return self::MODE;
    }

    /**
     * Parse Inquiry Event
     * @param array $event
     * @param array $eventData
     * @return array
     */
    protected function parseEvent(array $event, array $eventData)
    {

        // Set Event Lead
        $event['data']['lead'] = $this->parseEventLead($eventData);

        // Set Event Form
        $data = unserialize($eventData['data']);
        $form = $this->parseFormData($eventData['form_id'], $eventData['form'], $data);
        $event['data']['form'] = $form;

        // Set Event Listing
        if (!empty($form['mls_number'])) {
            $listing = $this->getListing($form['mls_number'], $form['type'], $form['feed']);
            if (isset($listing)) {
                $event['data']['listing'] = $listing;
            }
        }

        return $event;
    }
}
