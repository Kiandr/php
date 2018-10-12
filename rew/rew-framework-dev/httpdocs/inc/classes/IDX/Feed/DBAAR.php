<?php

/**
 * DBAAR
 * @package IDX
 */
abstract class IDX_Feed_DBAAR extends IDX_Feed
{

    /**
     * Add extra search panels for this IDX feed
     * @param array $defaults
     * @return array
     */
    public static function getPanels(array $defaults = array())
    {
        // Add CondoName panel below Subdivision panel
        $key_location = array_search('subdivision', array_keys($defaults));
        $first_array = array_splice($defaults, 0, $key_location + 1);
        return array_merge($first_array, array(
            'CondoName' => array('display' => true)
        ), $defaults);
    }
}
