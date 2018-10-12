<?php

/* Feed Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 41.649297;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -83.569221;
Settings::getInstance()->SETTINGS['map_state'] = 'OH';

/* Feed Map Bounds (Don't Generate Geo Points outside of this Polygon) */
/* The Polygon Defined Below includes the Entire State (w/ Padding) */
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>