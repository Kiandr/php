<?php

/* Feed Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 39.037249;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -84.492416;
Settings::getInstance()->SETTINGS['map_state'] = 'KY';

/* Feed Map Bounds (Don't Generate Geo Points outside of this Polygon) */
/* The Polygon Defined Below includes the Entire State (w/ Padding) */
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>