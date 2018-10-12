<?php

/* Feed Settings */
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 47.6062095;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -122.3320708;
Settings::getInstance()->SETTINGS['map_state'] = 'WA';

/* Feed Map Bounds (Don't Generate Geo Points outside of this Polygon) */
/* The Polygon Defined Below includes the Entire State of Washington (w/ Padding) */
Settings::getInstance()->SETTINGS['bounds'] = '49.57177772385043 -115.51025390625,45.015560807274014 -115.59814453125,45.20165098612332 -124.95849609375,48.65129883255621 -126.10107421875,49.45765133599128 -122.89306640625,49.57177772385043 -115.51025390625';

?>