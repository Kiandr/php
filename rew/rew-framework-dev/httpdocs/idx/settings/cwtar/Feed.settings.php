<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 35.6145169;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -88.8139469;
Settings::getInstance()->SETTINGS['map_state'] = 'TN';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>
