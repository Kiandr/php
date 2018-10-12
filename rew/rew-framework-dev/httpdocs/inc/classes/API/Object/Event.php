<?php

/**
 * API_Object_Event
 *
 */
class API_Object_Event extends API_Object
{

    /**
     * Get the object's data
     * @return array
     */
    public function getData()
    {
        $db = $this->_db;
        $event = $this->_row;
        $data = array(
            'type' => $event->getType(),
            'subtype' => $event->getSubtype(),
            'details' => $event->getData('data'),
            'timestamp' => $event->getTimestamp(),
        );
        return $data;
    }
}
