<?php

/**
 * RAGFMB
 * @package IDX
 */
abstract class IDX_Feed_RAGFMB extends IDX_Feed
{

    /**
     * Add extra search panels for this IDX feed
     * @param array $defaults
     * @return array
     */
    public static function getPanels(array $defaults = array())
    {
        // Add GulfAccess panel below Waterfront panel
        $key_location = array_search('waterfront', array_keys($defaults));
        $first_array = array_splice($defaults, 0, $key_location + 1);
        return array_merge($first_array, array(
            'GulfAccess' => array('display' => true)
        ), $defaults);
    }
}
