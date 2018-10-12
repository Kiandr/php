<?php

/* Feed Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 29.7628844;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -95.3830615;
Settings::getInstance()->SETTINGS['map_state'] = 'TX';

/* Feed Map Bounds (Don't Generate Geo Points outside of this Polygon) */
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>