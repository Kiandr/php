<?php

/* Feed Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 30.267153;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -97.743061;
Settings::getInstance()->SETTINGS['map_state'] = 'TX';

/* Feed Map Bounds (Don't Generate Geo Points outside of this Polygon) */
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>