<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 32.7153292;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -117.1572551;
Settings::getInstance()->SETTINGS['map_state'] = 'CA';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
// The Polygon Defined Below includes the Entire State of Texas (w/ Padding)
Settings::getInstance()->SETTINGS['bounds'] = '42.368826185895635 -124.947509765625,42.320105654646134 -119.520263671875,39.1408542479868 -119.388427734375,34.68080292218987 -113.236083984375,32.09436457148002 -113.961181640625,31.683926154519607 -118.157958984375,34.753046883357214 -123.936767578125,40.54107791554274 -125.760498046875,42.368826185895635 -124.947509765625';

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;
