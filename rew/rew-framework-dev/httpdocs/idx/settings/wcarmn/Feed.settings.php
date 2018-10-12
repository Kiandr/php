<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   =  45.1433;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  =  -95.0381;
Settings::getInstance()->SETTINGS['map_state'] = 'MN';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
// The Polygon Defined Below includes the Entire State (w/ Padding)
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>
