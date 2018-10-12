<?php

/* wrist Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 40.3736;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -82.7755;
Settings::getInstance()->SETTINGS['map_state'] = 'OH';

/* wrist Map Bounds (Don't Generate Geo Points outside of this Polygon) */
/* The Polygon Defined Below includes the Entire State (w/ Padding) */
//Settings::getInstance()->SETTINGS['bounds'] = '';

?>