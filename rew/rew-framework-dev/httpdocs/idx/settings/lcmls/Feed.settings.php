<?php

/* Feed Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 44.958164;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -124.017891;
Settings::getInstance()->SETTINGS['map_state'] = 'OR';

/* Feed Map Bounds (Don't Generate Geo Points outside of this Polygon) */
/* The Polygon Defined Below includes the Entire State (w/ Padding) */
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>