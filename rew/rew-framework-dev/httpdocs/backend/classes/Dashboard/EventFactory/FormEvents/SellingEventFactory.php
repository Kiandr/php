<?php

namespace REW\Backend\Dashboard\EventFactory\FormEvents;

use REW\Backend\Dashboard\EventFactory\AbstractFormEventFactory;
use \Util_Curl;

/**
 * Class SellingEventFactory
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class SellingEventFactory extends AbstractFormEventFactory
{

    /**
     * Selling Mode
     * @var string
     */
    const MODE = 'selling';

    /**
     * Get Event Mode
     * @return string
     */
    public function getMode()
    {
        return self::MODE;
    }

    /**
     * Parse Selling Event
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

        // Set Event Map
        if (!empty($this->settings->MODULES['REW_IDX_STREETVIEW'])) {
            $address = $event['data']['form']['address'] . ' '
                . $event['data']['form']['city'] . ' '
                . $event['data']['form']['state'];
            $map = $this->parseMap($address);
            if (isset($map)) {
                $event['data']['map'] = $map;
            }
        }

        return $event;
    }

    /**
     * Map Event
     * @param string $address
     * @return array
     */
    protected function parseMap($address)
    {

        // Geocoder API URL
        $apiKey = $this->settings->get('google.maps.api_key');
        $geocoderRequest = sprintf('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&key=%s&address=%s', $apiKey, rawurlencode($address));

        // Process Request
        try {
            $json = Util_Curl::executeRequest($geocoderRequest);
        } catch (Exception $e) {
            return null;
        }

        // Response Info
        $info = Util_Curl::info();

        // If valid response
        if ($info['http_code'] == 200) {
            $data = json_decode($json, true);
            // Location
            $result = $data['results'][0];
            if (!empty($result)) {
                $location = $result['geometry']['location'];
                if (!empty($location)) {
                    return [
                        'show' => true,
                        'lng'  =>  $location['lng'],
                        'lat'  =>  $location['lat']
                    ];
                }
            }
        }
        return null;
    }
}
